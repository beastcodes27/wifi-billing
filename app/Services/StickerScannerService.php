<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Reads WiFi access point / router sticker labels.
 *
 * Uses a cloud vision model (Gemini) when GEMINI_API_KEY is set, otherwise
 * falls back to the local Tesseract OCR binary.
 */
class StickerScannerService
{
    /**
     * @return array{brand:?string,model:?string,serial_number:?string,device_key:?string,mac_address:?string,confidence:?float,driver:string,raw:mixed}
     */
    public function scan(string $imagePath): array
    {
        $driver = config('services.sticker.driver', 'auto');
        $geminiKey = config('services.gemini.api_key');

        if ($driver === 'gemini' || ($driver === 'auto' && $geminiKey)) {
            if ($geminiKey) {
                return $this->scanWithGemini($imagePath);
            }

            if ($driver === 'gemini') {
                throw new \RuntimeException('GEMINI_API_KEY haipo. Weka key kwenye .env ili kutumia AI scan.');
            }
        }

        return $this->scanWithTesseract($imagePath);
    }

    protected function scanWithGemini(string $imagePath): array
    {
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $prompt = 'You are reading a photograph of a WiFi access point or router sticker/label. '
            . 'Extract the device details and reply with MINIFIED JSON only (no markdown), using exactly these keys: '
            . 'brand, model, serial_number, device_key, mac_address, confidence. '
            . 'brand: one of "MikroTik", "Ubiquiti UniFi", "TP-Link Omada", or the manufacturer name. '
            . 'serial_number: the value labelled Serial Number / Serial No / S/N / SN. '
            . 'device_key: the value labelled "Device Key", "Device Password" or "Device Pwd". '
            . 'mac_address: the MAC address if present. '
            . 'confidence: integer 0-100 of how confident you are. '
            . 'Use null for any field that is not visible.';

        $response = Http::timeout(45)->post($url, [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    ['inline_data' => [
                        'mime_type' => $this->mimeType($imagePath),
                        'data' => base64_encode(file_get_contents($imagePath)),
                    ]],
                ],
            ]],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0,
            ],
        ]);

        if (! $response->successful()) {
            Log::error('Gemini sticker scan failed', [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500),
            ]);

            throw new \RuntimeException('AI scan imeshindikana (Gemini HTTP ' . $response->status() . ').');
        }

        $text = (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        $data = json_decode(trim($text), true);

        if (! is_array($data)) {
            // Sometimes the model wraps JSON in prose; try to extract the first {...}.
            if (preg_match('/\{.*\}/s', $text, $m)) {
                $data = json_decode($m[0], true) ?: [];
            } else {
                $data = [];
            }
        }

        return $this->normalize($data, 'gemini', $response->json());
    }

    protected function scanWithTesseract(string $imagePath): array
    {
        $binary = config('services.sticker.tesseract_binary', 'tesseract');
        $location = trim((string) @shell_exec('command -v ' . escapeshellarg($binary) . ' 2>/dev/null'));

        if ($location === '') {
            throw new \RuntimeException('Hakuna injini ya OCR. Weka GEMINI_API_KEY au sakinisha tesseract-ocr.');
        }

        $text = (string) @shell_exec(escapeshellarg($binary) . ' ' . escapeshellarg($imagePath) . ' stdout 2>/dev/null');

        $data = [
            'brand' => $this->guessBrand($text),
            'model' => $this->matchLabel($text, ['Model', 'Mfano']),
            'serial_number' => $this->matchLabel($text, ['Serial Number', 'Serial No', 'Serial', 'S/N', 'SN']),
            'device_key' => $this->matchLabel($text, ['Device Key', 'Device Password', 'Device Pwd']),
            'mac_address' => $this->matchMac($text),
        ];

        return $this->normalize($data, 'tesseract', ['text' => $text]);
    }

    protected function normalize(array $data, string $driver, $raw): array
    {
        $clean = function ($v) {
            if ($v === null) {
                return null;
            }
            $v = trim((string) $v);

            return $v === '' ? null : $v;
        };

        $confidence = $data['confidence'] ?? null;
        $confidence = is_numeric($confidence) ? min(100, max(0, (float) $confidence)) : null;

        return [
            'brand' => $clean($data['brand'] ?? null),
            'model' => $clean($data['model'] ?? null),
            'serial_number' => $clean($data['serial_number'] ?? null),
            'device_key' => $clean($data['device_key'] ?? null),
            'mac_address' => $clean($data['mac_address'] ?? null),
            'confidence' => $confidence,
            'driver' => $driver,
            'raw' => $raw,
        ];
    }

    protected function mimeType(string $path): string
    {
        $mime = @mime_content_type($path);

        return is_string($mime) && str_starts_with($mime, 'image/') ? $mime : 'image/jpeg';
    }

    protected function guessBrand(string $text): ?string
    {
        $t = strtolower($text);

        if (str_contains($t, 'mikrotik') || str_contains($t, 'routeros')) {
            return 'MikroTik';
        }
        if (str_contains($t, 'unifi') || str_contains($t, 'ubiquiti')) {
            return 'Ubiquiti UniFi';
        }
        if (str_contains($t, 'tp-link') || str_contains($t, 'tplink') || str_contains($t, 'omada') || str_contains($t, 'eap')) {
            return 'TP-Link Omada';
        }

        return null;
    }

    protected function matchLabel(string $text, array $labels): ?string
    {
        foreach ($labels as $label) {
            $pattern = '/' . preg_quote($label, '/') . '\s*[:\-]?\s*([A-Za-z0-9\-\._\/]{4,40})/i';
            if (preg_match($pattern, $text, $m)) {
                return trim($m[1]);
            }
        }

        return null;
    }

    protected function matchMac(string $text): ?string
    {
        if (preg_match('/\b([0-9A-Fa-f]{2}[:-]){5}[0-9A-Fa-f]{2}\b/', $text, $m)) {
            return strtoupper(str_replace('-', ':', $m[0]));
        }

        return null;
    }
}
