<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\WifiUser;
use App\Models\Voucher;
use App\Models\VoucherBatch;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function index()
    {
        $packages = Package::where('is_active', true)->get();
        // Get all manual voucher batches
        $batches = VoucherBatch::with('package')->orderBy('created_at', 'desc')->paginate(15);
                    
        return view('admin.vouchers.index', compact('packages', 'batches'));
    }

    public function generateSingleSms(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'phone_number' => 'required|string|min:9',
        ]);

        $package = Package::find($request->package_id);
        $phone = trim($request->phone_number);
        $batchRef = 'SMS-' . strtoupper(uniqid());

        DB::beginTransaction();
        try {
            $batch = VoucherBatch::create([
                'batch_reference' => $batchRef,
                'package_id' => $package->id,
                'created_by' => auth()->id(),
                'quantity' => 1,
                'notes' => "SMS Vocha: {$phone}",
            ]);

            $voucherCode = (string) mt_rand(100000, 999999);
            while (Voucher::where('code', $voucherCode)->exists()) {
                $voucherCode = (string) mt_rand(100000, 999999);
            }

            $expiresAt = now()->addMinutes($package->duration_minutes);

            $wifiUser = WifiUser::create([
                'phone_number' => $phone,
                'mikrotik_username' => $voucherCode,
                'mikrotik_password' => $voucherCode,
                'package_id' => $package->id,
                'payment_type' => 'cash',
                'expires_at' => $expiresAt,
                'activated_at' => now(),
                'is_online' => false,
            ]);

            $transaction = Transaction::create([
                'transaction_reference' => 'SMS-' . strtoupper(uniqid()),
                'palm_pesa_order_id' => $batchRef,
                'phone_number' => $phone,
                'amount' => $package->price,
                'status' => 'success',
                'payment_method' => 'cash',
                'package_id' => $package->id,
                'wifi_user_id' => $wifiUser->id,
                'mikrotik_voucher' => $voucherCode,
            ]);

            $fallbackCust = \App\Models\Customer::withoutGlobalScopes()->first();
            \App\Models\Subscription::create([
                'customer_id' => $fallbackCust ? $fallbackCust->id : 1,
                'wifi_user_id' => $wifiUser->id,
                'package_id' => $package->id,
                'transaction_id' => $transaction->id,
                'type' => 'new',
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => $expiresAt,
                'notes' => "SMS Vocha: {$phone}"
            ]);

            Voucher::create([
                'batch_id' => $batch->id,
                'code' => $voucherCode,
                'wifi_user_id' => $wifiUser->id,
                'transaction_id' => $transaction->id,
                'status' => 'available'
            ]);

            DB::commit();

            return redirect()->route('admin.vouchers.index')
                             ->with('single_sms_success', true)
                             ->with('sms_code', $voucherCode)
                             ->with('sms_phone', $phone)
                             ->with('sms_package_name', $package->name)
                             ->with('sms_package_price', $package->price)
                             ->with('sms_batch_id', $batchRef);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Kuna tatizo limetokea: ' . $e->getMessage());
        }
    }

    public function generateBatch(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'quantity' => 'required|integer|min:1|max:500',
        ]);

        $package = Package::find($request->package_id);
        $quantity = (int) $request->quantity;
        $batchRef = 'BATCH-' . strtoupper(uniqid());

        DB::beginTransaction();
        try {
            // Create Batch
            $batch = VoucherBatch::create([
                'batch_reference' => $batchRef,
                'package_id' => $package->id,
                'created_by' => auth()->id(),
                'quantity' => $quantity,
                'notes' => "Generated $quantity vouchers for cash sales",
            ]);

            $isSingle = ($quantity === 1);
            $phone = $isSingle ? 'Mteja wa Cash' : 'Batch Print';
            $expiresAt = $isSingle ? now()->addMinutes($package->duration_minutes) : null;
            $activatedAt = $isSingle ? now() : null;
            $fallbackCust = \App\Models\Customer::withoutGlobalScopes()->first();

            for ($i = 0; $i < $quantity; $i++) {
                $voucherCode = (string) mt_rand(100000, 999999);
                
                // Ensure code uniqueness (rare but possible collision)
                while (Voucher::where('code', $voucherCode)->exists() || WifiUser::where('mikrotik_username', $voucherCode)->exists()) {
                    $voucherCode = (string) mt_rand(100000, 999999);
                }
                
                // Create WifiUser record
                $wifiUser = WifiUser::create([
                    'phone_number' => $phone,
                    'mikrotik_username' => $voucherCode,
                    'mikrotik_password' => $voucherCode,
                    'package_id' => $package->id,
                    'payment_type' => 'cash',
                    'expires_at' => $expiresAt,
                    'activated_at' => $activatedAt,
                    'is_online' => false,
                ]);

                // Create Transaction record
                $transaction = Transaction::create([
                    'transaction_reference' => 'MANUAL-' . strtoupper(uniqid()),
                    'palm_pesa_order_id' => $batchRef, 
                    'phone_number' => $phone,
                    'amount' => $package->price,
                    'status' => 'success',
                    'payment_method' => 'cash',
                    'package_id' => $package->id,
                    'wifi_user_id' => $wifiUser->id,
                    'mikrotik_voucher' => $voucherCode,
                ]);

                if ($isSingle) {
                    \App\Models\Subscription::create([
                        'customer_id' => $fallbackCust ? $fallbackCust->id : 1,
                        'wifi_user_id' => $wifiUser->id,
                        'package_id' => $package->id,
                        'transaction_id' => $transaction->id,
                        'type' => 'new',
                        'status' => 'active',
                        'starts_at' => now(),
                        'expires_at' => $expiresAt,
                        'notes' => 'Mauzo ya Vocha (Manual Cash)'
                    ]);
                }

                // Create Voucher record
                Voucher::create([
                    'batch_id' => $batch->id,
                    'code' => $voucherCode,
                    'wifi_user_id' => $wifiUser->id,
                    'transaction_id' => $transaction->id,
                    'status' => 'available'
                ]);
            }

            DB::commit();

            $createdVouchers = Voucher::where('batch_id', $batch->id)->get(['code']);
            $voucherCodes = $createdVouchers->pluck('code')->toArray();

            return redirect()->route('admin.vouchers.index')
                             ->with('voucher_success', true)
                             ->with('voucher_quantity', $quantity)
                             ->with('single_code', $quantity == 1 ? ($voucherCodes[0] ?? null) : null)
                             ->with('batch_id', $batch->batch_reference)
                             ->with('package_name', $package->name)
                             ->with('package_price', $package->price)
                             ->with('package_duration', $package->human_duration ?? ($package->duration_minutes . ' min'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Kuna tatizo limetokea: ' . $e->getMessage());
        }
    }

    public function batchJson($batchId)
    {
        $batch = VoucherBatch::where('batch_reference', $batchId)->with('package')->first();
        if (!$batch) {
            return response()->json(['error' => 'Kundi halijapatikana.'], 404);
        }

        $vouchers = Voucher::where('batch_id', $batch->id)->get(['id', 'code', 'status']);
        $package = $batch->package;

        return response()->json([
            'batch_reference' => $batch->batch_reference,
            'package_name' => $package ? $package->name : 'Wi-Fi Package',
            'package_price' => $package ? $package->price : 0,
            'package_duration' => $package ? ($package->human_duration ?? ($package->duration_minutes . ' min')) : '',
            'quantity' => $batch->quantity,
            'vouchers' => $vouchers,
        ]);
    }

    public function printPdf($batchId)
    {
        // Now fetch from vouchers instead of transactions
        $batch = VoucherBatch::where('batch_reference', $batchId)->with('package')->first();
        if (!$batch) {
            return redirect()->back()->with('error', 'Kundi halijapatikana.');
        }

        $vouchers = Voucher::where('batch_id', $batch->id)->with(['transaction', 'wifiUser'])->get();

        if ($vouchers->isEmpty()) {
            return redirect()->back()->with('error', 'Fungu hili halina vocha zozote.');
        }

        return view('admin.vouchers.print', compact('vouchers', 'batch'));
    }
}
