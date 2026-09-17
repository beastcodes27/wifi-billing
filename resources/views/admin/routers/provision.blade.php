@extends('layouts.app')

@section('title', 'Unganisha Router')

@section('content')
<style>
    /* Responsive Provisioning UI */
    .provision-wrapper {
        max-width: 820px;
        margin: 0 auto;
        width: 100%;
        padding-bottom: 2.5rem;
    }

    .provision-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.85rem;
        gap: 0.75rem;
    }

    .provision-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.85rem;
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: var(--shadow-xs);
        transition: all var(--transition);
    }

    .provision-back-btn:hover {
        color: #FF3300;
        border-color: #FF3300;
        background: #FFF5F2;
    }

    .provision-breadcrumb {
        font-size: 0.78rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .provision-breadcrumb .active {
        font-weight: 700;
        color: var(--text-main);
    }

    /* Header Card */
    .provision-header-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin-bottom: 0.85rem;
        box-shadow: var(--shadow-xs);
    }

    .provision-router-identity {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .provision-terminal-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 51, 0, 0.1);
        color: #FF3300;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .provision-router-info {
        flex: 1;
        min-width: 0;
    }

    .provision-router-name {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .provision-meta-strip {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.3rem;
    }

    .meta-tag {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--text-muted);
        background: var(--surface-soft);
        border: 1px solid var(--border-color);
        padding: 0.15rem 0.55rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Script Card */
    .provision-script-card {
        background: #FFFFFF;
        border: 1px solid #BBF7D0;
        border-radius: 16px;
        padding: 1.15rem 1.25rem;
        margin-bottom: 0.85rem;
        box-shadow: var(--shadow-xs);
    }

    .script-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.55rem;
        gap: 0.5rem;
    }

    .script-card-title {
        font-size: 0.98rem;
        font-weight: 800;
        color: #166534;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .script-card-title i {
        color: #16A34A;
    }

    .countdown-pill {
        font-size: 0.76rem;
        font-weight: 800;
        color: #DC2626;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        padding: 0.18rem 0.6rem;
        border-radius: var(--radius-pill);
        white-space: nowrap;
    }

    .script-instruction {
        font-size: 0.86rem;
        color: var(--text-secondary);
        line-height: 1.45;
        margin-bottom: 0.85rem;
    }

    .terminal-action-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.55rem;
        gap: 0.5rem;
    }

    .terminal-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-copy-script {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.5rem 1.05rem;
        background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%);
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(255, 51, 0, 0.3);
        transition: all var(--transition);
        white-space: nowrap;
    }

    .btn-copy-script:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 51, 0, 0.4);
    }

    .btn-copy-script:active {
        transform: scale(0.96);
    }

    /* Dark Terminal Box */
    .terminal-code-box {
        background: #0B132B;
        border: 1px solid #1E293B;
        border-radius: 12px;
        padding: 0.95rem;
        overflow: hidden;
    }

    .terminal-pre {
        margin: 0;
        color: #38BDF8;
        font-family: 'Fira Code', 'Courier New', monospace;
        font-size: 0.78rem;
        line-height: 1.6;
        word-break: break-all;
        white-space: pre-wrap;
        max-height: 240px;
        overflow-y: auto;
    }

    .terminal-tip {
        font-size: 0.78rem;
        color: #15803D;
        font-weight: 600;
        margin-top: 0.65rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    /* Monitor Card */
    .provision-monitor-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.15rem 1.25rem;
        box-shadow: var(--shadow-xs);
    }

    .monitor-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        gap: 0.5rem;
    }

    .monitor-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .monitor-title i {
        color: #FF3300;
    }

    .btn-check-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        background: var(--surface-soft);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all var(--transition);
    }

    .btn-check-status:hover {
        background: #F1F5F9;
        color: var(--text-main);
    }

    .monitor-status-box {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.85rem 1rem;
        background: var(--surface-soft);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        margin-bottom: 0.65rem;
    }

    .status-pulse-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #DC2626;
        box-shadow: 0 0 10px #DC2626;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .monitor-status-text-wrap {
        flex: 1;
        min-width: 0;
    }

    .status-text-waiting {
        font-size: 0.95rem;
        font-weight: 800;
        color: #DC2626;
        line-height: 1.25;
    }

    .status-last-sync {
        font-size: 0.76rem;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .monitor-footnote {
        font-size: 0.75rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    /* Feedback Card */
    .provision-feedback-card {
        text-align: center;
        padding: 2.5rem 1.5rem;
        border-radius: 16px;
        border: 1px solid #BBF7D0;
        background: #F0FDF4;
    }

    .feedback-icon {
        font-size: 3rem;
        color: #16A34A;
        margin-bottom: 0.85rem;
        display: block;
    }

    .feedback-title {
        margin: 0 0 0.4rem 0;
        color: #166534;
        font-size: 1.35rem;
        font-weight: 800;
    }

    .feedback-text {
        color: #1E293B;
        font-size: 0.9rem;
        max-width: 480px;
        margin: 0 auto 1.25rem auto;
    }

    .feedback-actions {
        display: flex;
        justify-content: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .btn-feedback-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.6rem 1.25rem;
        background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%);
        color: #FFFFFF;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(255, 51, 0, 0.3);
    }

    .btn-feedback-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.6rem 1.25rem;
        background: #FFFFFF;
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 800;
        cursor: pointer;
    }

    /* Mobile Responsiveness & Edge-to-Edge Utilization */
    @media (max-width: 768px) {
        .provision-wrapper {
            padding-left: 0 !important;
            padding-right: 0 !important;
            padding-bottom: 5rem !important;
        }
        .provision-header-card,
        .provision-script-card,
        .provision-monitor-card {
            padding: 0.9rem 0.95rem !important;
            border-radius: 14px !important;
            margin-bottom: 0.75rem !important;
        }
        .provision-router-name {
            font-size: 1.05rem !important;
        }
        .terminal-action-row {
            flex-direction: column;
            align-items: stretch;
            gap: 0.45rem;
        }
        .btn-copy-script {
            width: 100%;
            justify-content: center;
            padding: 0.65rem 1rem;
            font-size: 0.86rem;
        }
        .terminal-code-box {
            padding: 0.75rem;
        }
        .terminal-pre {
            font-size: 0.74rem;
            max-height: 200px;
        }
    }
</style>

<div class="provision-wrapper">

    {{-- Top Back & Breadcrumb Bar --}}
    <div class="provision-topbar">
        <a href="{{ route('admin.routers.index') }}" class="provision-back-btn">
            <i class="fas fa-arrow-left"></i>
            <span>Rudi kwenye Routers</span>
        </a>
        <div class="provision-breadcrumb desktop-only-header">
            <span>Routers</span>
            <i class="fas fa-chevron-right" style="font-size: 0.65rem;"></i>
            <span>{{ $router->name }}</span>
            <i class="fas fa-chevron-right" style="font-size: 0.65rem;"></i>
            <span class="active">Kuunganisha</span>
        </div>
    </div>

    {{-- Router Identity Summary Card --}}
    <div class="provision-header-card">
        <div class="provision-router-identity">
            <div class="provision-terminal-icon">
                <i class="fas fa-terminal"></i>
            </div>
            <div class="provision-router-info">
                <h2 class="provision-router-name">{{ $router->name }}</h2>
                <div class="provision-meta-strip">
                    <span class="meta-tag"><i class="fas fa-map-marker-alt"></i> {{ $router->site->name ?? 'Site Kuu' }}</span>
                    <span class="meta-tag"><i class="fas fa-network-wired"></i> {{ $router->management_ip ?: '192.168.88.1' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div id="instruction-blocks">
        {{-- 1-Click Setup Script Box --}}
        <div class="provision-script-card">
            <div class="script-card-header">
                <div class="script-card-title">
                    <i class="fas fa-bolt"></i>
                    <span>Script ya Kuunganisha</span>
                </div>
                <span id="countdown-timer" class="countdown-pill">57s</span>
            </div>

            <p class="script-instruction">
                Nakili script hapa chini, kisha Paste kwenye <strong>Winbox &gt; Terminal</strong> ya MikroTik.
            </p>

            <div class="terminal-action-row">
                <span class="terminal-label"><i class="fas fa-code"></i> Kodi ya Terminal:</span>
                <button type="button" onclick="copyScriptCode()" id="copy-btn" class="btn-copy-script">
                    <i class="fas fa-copy"></i>
                    <span>Nakili Script</span>
                </button>
            </div>

            @php
                $serverUrl = config('app.url');
                if (empty($serverUrl) || str_contains($serverUrl, 'localhost') || str_contains($serverUrl, '127.0.0.1')) {
                    $serverUrl = 'https://drinkpoint.store';
                }
                $serverUrl = rtrim($serverUrl, '/');
                $serverHost = parse_url($serverUrl, PHP_URL_HOST);
            @endphp
            {{-- Terminal Dark Code Box --}}
            <div class="terminal-code-box">
                <pre id="provision-code" class="terminal-pre">/ip dns set servers=8.8.8.8,1.1.1.1 allow-remote-requests=yes
do { /ip dns static add name="{{ $serverHost }}" address=66.29.141.97; } on-error={};
/ip dns cache flush
/system clock set time-zone-name=Africa/Dar_es_Salaam
/system ntp client set enabled=yes primary-ntp=129.6.15.28 secondary-ntp=132.163.96.1

do { /system scheduler remove [find name="wifibilling-sync-{{ $router->id }}"]; } on-error={};
/system scheduler add name="wifibilling-sync-{{ $router->id }}" start-time=startup interval=5s on-event="/tool fetch url=\"{{ $serverUrl }}/api/mikrotik/sync/{{ $router->sync_token }}\" mode=https check-certificate=no dst-path=\"wifibilling-sync-{{ $router->id }}.rsc\"; :delay 2s; :if ([:len [/file find name=\"wifibilling-sync-{{ $router->id }}.rsc\"]] > 0) do={ :import wifibilling-sync-{{ $router->id }}.rsc; /file remove [find name=\"wifibilling-sync-{{ $router->id }}.rsc\"]; };"

do { /system scheduler remove [find name="wifibilling-expire-{{ $router->id }}"]; } on-error={};
/system scheduler add name="wifibilling-expire-{{ $router->id }}" start-time=startup interval=5m on-event="/tool fetch url=\"{{ $serverUrl }}/api/mikrotik/expired-users/{{ $router->sync_token }}\" mode=https check-certificate=no dst-path=\"wifibilling-expired-{{ $router->id }}.rsc\"; :delay 3s; :if ([:len [/file find name=\"wifibilling-expired-{{ $router->id }}.rsc\"]] > 0) do={ :import wifibilling-expired-{{ $router->id }}.rsc; /file remove [find name=\"wifibilling-expired-{{ $router->id }}.rsc\"]; };"

do { /system scheduler remove [find name="wifibilling-active-{{ $router->id }}"]; } on-error={};
/system scheduler add name="wifibilling-active-{{ $router->id }}" start-time=startup interval=10s on-event=":local act \"\"; :foreach i in=[/ip hotspot active find] do={ :local u [/ip hotspot active get \$i user]; :local bi [/ip hotspot active get \$i bytes-in]; :local bo [/ip hotspot active get \$i bytes-out]; :local ut [/ip hotspot active get \$i uptime]; :local mc [/ip hotspot active get \$i mac-address]; :local ip [/ip hotspot active get \$i address]; :set act (\$act . \$u . \",\" . \$bi . \",\" . \$bo . \",\" . \$ut . \",\" . \$mc . \",\" . \$ip . \";\"); }; :if ([:len \$act] = 0) do={ :set act \"none\"; }; do { /tool fetch url=\"{{ $serverUrl }}/api/mikrotik/active-users-sync/{{ $router->sync_token }}\" http-method=post http-data=\$act mode=https check-certificate=no keep-result=no; } on-error={};"

do { /system scheduler remove [find name="wifibilling-reconnect-{{ $router->id }}"]; } on-error={};
/system scheduler add name="wifibilling-reconnect-{{ $router->id }}" start-time=startup interval=30s on-event=":delay 5s; do { /tool fetch url=\"{{ $serverUrl }}/api/mikrotik/reconnect/{{ $router->sync_token }}\" mode=https check-certificate=no dst-path=\"wifibilling-reconnect-{{ $router->id }}.rsc\"; :delay 2s; :if ([:len [/file find name=\"wifibilling-reconnect-{{ $router->id }}.rsc\"]] > 0) do={ :import wifibilling-reconnect-{{ $router->id }}.rsc; /file remove [find name=\"wifibilling-reconnect-{{ $router->id }}.rsc\"]; }; } on-error={};"

/ip hotspot profile set [find] login-by=mac,cookie,mac-cookie,http-chap,http-pap http-cookie-lifetime=3d

/ip hotspot user profile set [find] on-login=":if ([:len [/ip hotspot user find name=\$mac-address]] = 0) do={ :local prof [/ip hotspot user get [find name=\$user] profile]; do { /ip hotspot user add name=\$mac-address password=\"\" profile=\$prof comment=(\"Auto-Auth: \" . \$user); } on-error={}; };" idle-timeout=none keepalive-timeout=none session-timeout=0s

/ip hotspot set [find] idle-timeout=none keepalive-timeout=none

/ip hotspot walled-garden add dst-host="{{ $serverHost }}"
/ip hotspot walled-garden add dst-host="*.{{ $serverHost }}"
/ip hotspot walled-garden add dst-host="*drinkpoint.store*"
/ip hotspot walled-garden add dst-host="*fimipay.com*"
/ip hotspot walled-garden add dst-host="fonts.googleapis.com"
/ip hotspot walled-garden add dst-host="fonts.gstatic.com"
/ip hotspot walled-garden add dst-host="cdnjs.cloudflare.com"</pre>
            </div>

            <div class="terminal-tip">
                <i class="fas fa-check-circle"></i>
                <span>Bonyeza Enter kwenye terminal kukamilisha.</span>
            </div>
        </div>

        {{-- Step 2: login.html file card --}}
        <div class="provision-script-card" style="margin-top: 1rem; border-top: 3px solid #3B82F6;">
            <div class="script-card-header">
                <div class="script-card-title">
                    <i class="fas fa-file-code" style="color: #3B82F6;"></i>
                    <span>Hatua ya 2: Weka faili la login.html (Hotspot Portal)</span>
                </div>
                <a href="{{ route('admin.routers.download_login') }}" class="btn-copy-script" style="background: #2563EB; text-decoration: none;" download>
                    <i class="fas fa-download"></i>
                    <span>Pakua login.html</span>
                </a>
            </div>
            <p class="script-instruction">
                Ili simu ya mteja ikishajiunga na WiFi ifungue kiotomatiki ukurasa wa kulipia na kuingiza vocha:
            </p>
            <ol style="font-size: 0.82rem; color: var(--text-secondary); margin: 0.5rem 0 0.5rem 1.25rem; line-height: 1.6;">
                <li>Pakua faili la <strong>login.html</strong> kwa kubonyeza kitufe hapo juu.</li>
                <li>Fungua <strong>Winbox &gt; Files</strong>.</li>
                <li>Tafuta folda inayoitwa <strong>hotspot</strong> (au <code>flash/hotspot</code>).</li>
                <li>Buruta (Drag &amp; Drop) hilo faili la <strong>login.html</strong> na kuliachia ndani ya folda hiyo ya <strong>hotspot</strong>.</li>
            </ol>
        </div>

        {{-- Live Connection Monitor Card --}}
        <div class="provision-monitor-card">
            <div class="monitor-header">
                <div class="monitor-title">
                    <i class="fas fa-satellite-dish"></i>
                    <span>Mawasiliano ya Router</span>
                </div>
                <button type="button" onclick="checkMikrotikStatus()" class="btn-check-status">
                    <i class="fas fa-sync-alt"></i>
                    <span>Kagua</span>
                </button>
            </div>

            <div class="monitor-status-box">
                <div id="status-indicator" class="status-pulse-dot"></div>
                <div class="monitor-status-text-wrap">
                    <div id="status-text" class="status-text-waiting">Inasubiri kuunganishwa...</div>
                    <div class="status-last-sync">Mawasiliano: <strong id="last-sync-time">-</strong></div>
                </div>
            </div>

            <div class="monitor-footnote">
                <i class="fas fa-info-circle"></i>
                <span>Inakagua kila sekunde 5 kiotomatiki.</span>
            </div>
        </div>
    </div>

    {{-- Success / Expiry Container --}}
    <div id="status-message-container" class="provision-feedback-card" style="display:none;">
        <i id="status-message-icon" class="fas fa-check-circle feedback-icon"></i>
        <h3 id="status-message-title" class="feedback-title">Imeunganishwa!</h3>
        <p id="status-message-text" class="feedback-text">
            Router imeunganishwa na ipo hewani sasa!
        </p>
        <div class="feedback-actions">
            <a href="{{ route('admin.routers.index') }}" class="btn-feedback-primary">
                <i class="fas fa-arrow-left"></i>
                <span>Rudi kwenye Routers</span>
            </a>
            <button type="button" onclick="window.location.reload()" class="btn-feedback-secondary">
                <i class="fas fa-redo"></i>
                <span>Rudia Script</span>
            </button>
        </div>
    </div>
</div>

<script>
    let isConnected = false;
    let timeLeft = 57;
    const instructionBlocks = document.getElementById('instruction-blocks');
    const statusContainer = document.getElementById('status-message-container');
    const statusTitle = document.getElementById('status-message-title');
    const statusText = document.getElementById('status-message-text');
    const statusIcon = document.getElementById('status-message-icon');
    const countdownEl = document.getElementById('countdown-timer');

    function copyScriptCode() {
        const text = document.getElementById('provision-code').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('copy-btn');
            btn.innerHTML = '<i class="fas fa-check"></i> <span>Imenakiliwa!</span>';
            btn.style.background = '#16A34A';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-copy"></i> <span>Nakili Script</span>';
                btn.style.background = 'linear-gradient(135deg, #FF3300 0%, #ff5722 100%)';
            }, 2200);
        });
    }

    // Countdown Timer
    const timerInterval = setInterval(() => {
        if (isConnected) {
            clearInterval(timerInterval);
            return;
        }

        timeLeft--;
        if (countdownEl) countdownEl.innerText = timeLeft + 's';

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            instructionBlocks.style.display = 'none';
            statusContainer.style.display = 'block';
            statusContainer.style.background = '#FEF2F2';
            statusContainer.style.borderColor = '#FECACA';
            statusIcon.className = 'fas fa-times-circle feedback-icon';
            statusIcon.style.color = '#EF4444';
            statusTitle.style.color = '#DC2626';
            statusTitle.innerText = 'Muda Umekwisha';
            statusText.innerText = 'Muda wa kunakili umekwisha. Bonyeza hapa chini kurudia.';
        }
    }, 1000);

    function checkMikrotikStatus() {
        if (timeLeft <= 0 && !isConnected) return;

        fetch('{{ route('admin.mikrotik.status') }}')
            .then(response => response.json())
            .then(data => {
                const indicator = document.getElementById('status-indicator');
                const text = document.getElementById('status-text');
                const lastSync = document.getElementById('last-sync-time');

                if (indicator && text && lastSync) {
                    text.innerText = data.message;
                    lastSync.innerText = data.last_sync || 'Bado haijaunganishwa';

                    if (data.connected) {
                        indicator.style.background = '#16A34A';
                        indicator.style.boxShadow = '0 0 10px #16A34A';
                        text.style.color = '#16A34A';
                    } else {
                        indicator.style.background = '#DC2626';
                        indicator.style.boxShadow = '0 0 10px #DC2626';
                        text.style.color = '#DC2626';
                    }
                }

                if (data.connected && !isConnected) {
                    isConnected = true;
                    instructionBlocks.style.display = 'none';
                    statusContainer.style.display = 'block';
                    statusContainer.style.background = '#F0FDF4';
                    statusContainer.style.borderColor = '#BBF7D0';
                    statusIcon.className = 'fas fa-check-circle feedback-icon';
                    statusIcon.style.color = '#16A34A';
                    statusTitle.style.color = '#166534';
                    statusTitle.innerText = 'Imeunganishwa!';
                    statusText.innerText = 'Router ya {{ $router->name }} ipo hewani sasa!';
                }
            })
            .catch(error => console.error('Error fetching Mikrotik status:', error));
    }

    checkMikrotikStatus();
    setInterval(checkMikrotikStatus, 5000);
</script>
@endsection
