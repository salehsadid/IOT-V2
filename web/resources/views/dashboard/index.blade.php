<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        
        :root {
            --bg-color: #f1f5f9;
            --navbar-bg: rgba(255, 255, 255, 0.85);
            --card-bg: rgba(255, 255, 255, 0.9);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: rgba(0, 0, 0, 0.05);
            --shadow-color: rgba(0, 0, 0, 0.08);
            --brand-color: #0284c7;
            --btn-bg: #0ea5e9;
            --btn-text: #ffffff;
            --status-box-bg: #f8fafc;
            --hover-bg: rgba(14, 165, 233, 0.1);
        }

        [data-theme="dark"] {
            --bg-color: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --navbar-bg: rgba(15, 23, 42, 0.7);
            --card-bg: rgba(30, 41, 59, 0.5);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.05);
            --shadow-color: rgba(0, 0, 0, 0.5);
            --brand-color: #38bdf8;
            --btn-bg: #38bdf8;
            --btn-text: #0f172a;
            --status-box-bg: rgba(15, 23, 42, 0.6);
            --hover-bg: rgba(56, 189, 248, 0.2);
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-color); 
            color: var(--text-main); 
            margin: 0; 
            padding: 0;
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
        }
        
        .navbar { 
            background: var(--navbar-bg); 
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 15px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 15px var(--shadow-color);
        }
        
        .navbar .brand { font-weight: 800; font-size: 20px; letter-spacing: 1px; color: var(--brand-color); }
        .navbar a { 
            color: var(--text-muted); text-decoration: none; font-weight: 600; padding: 8px 16px; 
            border-radius: 8px; transition: all 0.3s ease; 
        }
        .navbar a:hover { background: var(--hover-bg); color: var(--brand-color); }
        
        .theme-toggle {
            background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-main);
            padding: 8px 15px; border-radius: 20px; cursor: pointer; font-weight: 600; font-family: 'Inter', sans-serif;
            margin-right: 15px; transition: all 0.3s ease; box-shadow: 0 2px 5px var(--shadow-color);
        }
        
        .container { 
            max-width: 1200px; margin: 40px auto; padding: 0 20px; 
            display: grid; grid-template-columns: 1fr 1fr; gap: 30px; 
        }
        
        .card { 
            background: var(--card-bg); 
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            padding: 30px; 
            border-radius: 16px; 
            box-shadow: 0 10px 30px -10px var(--shadow-color); 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px -10px var(--shadow-color); }
        
        .card h2 { 
            margin-top: 0; color: var(--text-muted); border-bottom: 1px solid var(--border-color); 
            padding-bottom: 15px; font-size: 16px; text-transform: uppercase; letter-spacing: 2px; 
        }
        
        .info-row { display: flex; margin-bottom: 15px; font-size: 16px; }
        .info-row strong { width: 100px; color: var(--brand-color); }
        
        /* Machine Status Grid */
        .machine-status { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px; }
        .status-box { 
            background: var(--status-box-bg); 
            padding: 20px 10px; 
            border-radius: 12px; 
            text-align: center; 
            border: 1px solid var(--border-color);
        }
        .status-box-title { font-size: 14px; color: var(--text-muted); margin-bottom: 10px; font-weight: 600; }
        
        .status-badge { 
            display: inline-block; padding: 6px 12px; border-radius: 20px; 
            font-weight: 800; font-size: 12px; letter-spacing: 1px;
        }
        .status-ok { background: rgba(34, 197, 94, 0.15); color: #16a34a; border: 1px solid #4ade80; }
        .status-err { background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid #f87171; }
        .status-off { background: rgba(100, 116, 139, 0.15); color: #64748b; border: 1px solid #94a3b8; }
        
        [data-theme="dark"] .status-ok { color: #4ade80; }
        [data-theme="dark"] .status-err { color: #f87171; }
        [data-theme="dark"] .status-off { color: #94a3b8; }
        
        .big-status { font-size: 32px; font-weight: 800; margin: 30px 0; text-align: center; }
        
        .actions { display: flex; gap: 15px; margin-top: 20px; justify-content: center; }
        
        .btn { 
            display: inline-block; padding: 12px 24px; background: var(--btn-bg); color: var(--btn-text); 
            text-decoration: none; border-radius: 8px; font-weight: 800; border: none; 
            cursor: pointer; text-align: center; transition: all 0.2s ease;
        }
        .btn:hover { filter: brightness(1.1); box-shadow: 0 0 15px rgba(2, 132, 199, 0.4); }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.4); }
        .btn-disabled { background: #94a3b8; color: #f8fafc; cursor: not-allowed; box-shadow: none !important; }
        [data-theme="dark"] .btn-disabled { background: #475569; color: #94a3b8; }
        
        /* Popup Modal */
        #alert-modal { 
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); z-index: 1000; 
            justify-content: center; align-items: center; 
        }
        .modal-content { 
            background: var(--card-bg); padding: 40px; border-radius: 20px; text-align: center; 
            max-width: 450px; width: 100%; border: 2px solid #ef4444;
            box-shadow: 0 0 40px rgba(239, 68, 68, 0.3);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .modal-content h1 { color: #ef4444; margin-top: 0; font-size: 32px; font-weight: 800; }
        .modal-content p { font-size: 18px; color: var(--text-main); margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="brand">Parkinson Monitor System</div>
        <div>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Theme</button>
            <a href="{{ route('history') }}">View Full History</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left: 15px;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#ef4444; font-weight:800; font-size:16px; cursor:pointer; font-family:'Inter', sans-serif;">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <!-- Patient Profile -->
        <div class="card" style="position: relative;">
            <h2>Patient Profile</h2>
            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.15); color: #16a34a; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; border: 1px solid #4ade80;">{{ session('success') }}</div>
            @endif
            @if($patient)
                <div class="info-row"><strong>Name:</strong> {{ $patient->full_name }}</div>
                <div class="info-row"><strong>Age:</strong> {{ $age }} years</div>
                <div class="info-row"><strong>DOB:</strong> {{ $patient->date_of_birth->format('Y-M-d') }}</div>
                <button onclick="document.getElementById('edit-patient-modal').style.display='flex'" style="position: absolute; top: 25px; right: 25px; background: none; border: none; color: var(--brand-color); cursor: pointer; font-weight: 600; font-family: 'Inter', sans-serif;">✏️ Edit</button>
            @else
                <p>Patient data not found.</p>
            @endif
        </div>

        <!-- Machine Status -->
        <div class="card">
            <h2>Machine Status</h2>
            <div class="machine-status">
                <div class="status-box">
                    <div class="status-box-title">Device Power</div>
                    <div id="ui-power" class="status-badge status-off">OFFLINE</div>
                </div>
                <div class="status-box">
                    <div class="status-box-title">Hand Sensor</div>
                    <div id="ui-hand" class="status-badge status-off">N/A</div>
                </div>
                <div class="status-box">
                    <div class="status-box-title">Leg Sensor</div>
                    <div id="ui-leg" class="status-badge status-off">N/A</div>
                </div>
            </div>
            <p style="text-align:center; font-size:12px; color:var(--text-muted); margin-top:0;">Syncing every 1 second...</p>
        </div>

        <!-- Tremor Status -->
        <div class="card">
            <h2>Tremor Status (Live)</h2>
            <div id="ui-tremor-status" class="big-status" style="color:#10b981;">NO TREMOR</div>
            <div class="actions">
                <a href="{{ route('history') }}?type=TREMOR" class="btn">See Tremor History</a>
            </div>
        </div>

        <!-- FOG Status -->
        <div class="card">
            <h2>FOG Status (Live)</h2>
            <div id="ui-fog-status" class="big-status" style="color:#10b981;">NO FOG</div>
            <div class="actions">
                <button id="btn-stop-buzzer" class="btn btn-danger btn-disabled" disabled onclick="stopBuzzer()">Stop Buzzer</button>
                <a href="{{ route('history') }}?type=FOG" class="btn">See FOG History</a>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="card" style="grid-column: 1 / -1;">
            <h2>Daily & Lifetime Statistics</h2>
            <div class="machine-status" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 0;">
                <div class="status-box">
                    <div class="status-box-title">Tremor Events (Today)</div>
                    <div class="big-status" style="margin: 10px 0; font-size: 28px; color: var(--brand-color);">{{ $stats['todayTremorCount'] }}</div>
                </div>
                <div class="status-box">
                    <div class="status-box-title">FOG Events (Today)</div>
                    <div class="big-status" style="margin: 10px 0; font-size: 28px; color: var(--brand-color);">{{ $stats['todayFogCount'] }}</div>
                </div>
                <div class="status-box">
                    <div class="status-box-title">Highest Tremor Today</div>
                    <div class="big-status" style="margin: 10px 0; font-size: 28px; color: var(--brand-color);">Lvl {{ $stats['highestTremor'] }}</div>
                </div>
                <div class="status-box">
                    <div class="status-box-title">Total Tremor Events</div>
                    <div class="big-status" style="margin: 10px 0; font-size: 28px; color: var(--text-main);">{{ $stats['totalTremorEvents'] }}</div>
                </div>
                <div class="status-box">
                    <div class="status-box-title">Total FOG Events</div>
                    <div class="big-status" style="margin: 10px 0; font-size: 28px; color: var(--text-main);">{{ $stats['totalFogEvents'] }}</div>
                </div>
                <div class="status-box">
                    <div class="status-box-title">Avg Tremor Duration</div>
                    <div class="big-status" style="margin: 10px 0; font-size: 28px; color: var(--text-muted);">{{ $stats['avgTremorDuration'] }}s</div>
                </div>
                <div class="status-box">
                    <div class="status-box-title">Avg FOG Duration</div>
                    <div class="big-status" style="margin: 10px 0; font-size: 28px; color: var(--text-muted);">{{ $stats['avgFogDuration'] }}s</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <div id="alert-modal">
        <div class="modal-content">
            <h1 id="alert-title">CRITICAL ALERT</h1>
            <p id="alert-message">A medical event is happening.</p>
            <button class="btn" onclick="document.getElementById('alert-modal').style.display='none'">Dismiss Alert</button>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div id="edit-patient-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); z-index: 1000; justify-content: center; align-items: center;">
        <div class="modal-content" style="border: 1px solid var(--border-color); animation: none; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <h2 style="color: var(--text-main); margin-top: 0; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">Edit Patient Details</h2>
            <form action="{{ route('patient.update') }}" method="POST" style="text-align: left; display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
                @csrf
                <div>
                    <label style="color: var(--text-muted); font-size: 14px; font-weight: 600;">Full Name</label><br>
                    <input type="text" name="full_name" value="{{ $patient ? $patient->full_name : '' }}" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-main); font-family: 'Inter', sans-serif; box-sizing: border-box; margin-top: 5px;">
                </div>
                <div>
                    <label style="color: var(--text-muted); font-size: 14px; font-weight: 600;">Date of Birth</label><br>
                    <input type="date" name="date_of_birth" value="{{ $patient ? $patient->date_of_birth->format('Y-m-d') : '' }}" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-main); font-family: 'Inter', sans-serif; box-sizing: border-box; margin-top: 5px;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                    <button type="button" class="btn" style="background: var(--text-muted); color: #fff;" onclick="document.getElementById('edit-patient-modal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn" style="background: var(--brand-color); color: #fff;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Theme Toggle Logic
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Re-render status colors based on new theme
            fetchLiveStatus();
        }

        let isAlertShowing = false;
        let lastFogState = false;
        let lastTremorLevel = 0;

        function fetchLiveStatus() {
            fetch('/api/status')
                .then(response => response.json())
                .then(data => {
                    updateUI(data.online, data.status);
                })
                .catch(error => console.error('Error fetching status:', error));
        }

        function updateUI(online, status) {
            const powerBadge = document.getElementById('ui-power');
            const handBadge = document.getElementById('ui-hand');
            const legBadge = document.getElementById('ui-leg');
            const tremorStatus = document.getElementById('ui-tremor-status');
            const fogStatus = document.getElementById('ui-fog-status');
            const stopBuzzerBtn = document.getElementById('btn-stop-buzzer');
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

            if (!online) {
                powerBadge.className = 'status-badge status-off'; powerBadge.innerText = 'OFFLINE';
                handBadge.className = 'status-badge status-off'; handBadge.innerText = 'N/A';
                legBadge.className = 'status-badge status-off'; legBadge.innerText = 'N/A';
                tremorStatus.innerText = 'OFFLINE'; tremorStatus.style.color = 'var(--text-muted)';
                fogStatus.innerText = 'OFFLINE'; fogStatus.style.color = 'var(--text-muted)';
                stopBuzzerBtn.classList.add('btn-disabled'); stopBuzzerBtn.disabled = true;
                return;
            }

            // Power
            powerBadge.className = 'status-badge status-ok'; powerBadge.innerText = 'ONLINE';
            
            // Sensors
            handBadge.className = status.hand_ok ? 'status-badge status-ok' : 'status-badge status-err';
            handBadge.innerText = status.hand_ok ? 'OK' : 'ERROR';
            legBadge.className = status.leg_ok ? 'status-badge status-ok' : 'status-badge status-err';
            legBadge.innerText = status.leg_ok ? 'OK' : 'ERROR';

            // Tremor (Matches OLED Text)
            if (status.tremor_level > 0) {
                tremorStatus.innerText = 'TREMOR LVL ' + status.tremor_level;
                tremorStatus.style.color = isDark ? '#f87171' : '#ef4444';
                tremorStatus.style.textShadow = isDark ? '0 0 20px rgba(248, 113, 113, 0.4)' : 'none';
                if (status.tremor_level >= 2 && lastTremorLevel < 2) {
                    showAlert('Tremor Detected!', 'Patient is experiencing a Level ' + status.tremor_level + ' Tremor.');
                }
            } else {
                tremorStatus.innerText = 'NO TREMOR';
                tremorStatus.style.color = isDark ? '#4ade80' : '#10b981';
                tremorStatus.style.textShadow = isDark ? '0 0 20px rgba(74, 222, 128, 0.4)' : 'none';
            }
            lastTremorLevel = status.tremor_level;

            // FOG (Matches OLED Text)
            if (status.fog_active) {
                fogStatus.innerText = 'FOG DETECTED';
                fogStatus.style.color = isDark ? '#f87171' : '#ef4444';
                fogStatus.style.textShadow = isDark ? '0 0 20px rgba(248, 113, 113, 0.4)' : 'none';
                stopBuzzerBtn.classList.remove('btn-disabled'); 
                stopBuzzerBtn.removeAttribute('disabled');
                if (!lastFogState) showAlert('FOG Detected!', 'Patient is experiencing Freezing of Gait.');
            } else {
                fogStatus.innerText = 'NO FOG';
                fogStatus.style.color = isDark ? '#4ade80' : '#10b981';
                fogStatus.style.textShadow = isDark ? '0 0 20px rgba(74, 222, 128, 0.4)' : 'none';
                stopBuzzerBtn.classList.add('btn-disabled'); 
                stopBuzzerBtn.setAttribute('disabled', 'true');
            }
            lastFogState = status.fog_active;
        }

        function showAlert(title, message) {
            document.getElementById('alert-title').innerText = title;
            document.getElementById('alert-message').innerText = message;
            document.getElementById('alert-modal').style.display = 'flex';
        }

        function stopBuzzer() {
            const btn = document.getElementById('btn-stop-buzzer');
            btn.innerText = "Queued...";
            btn.classList.add('btn-disabled');
            btn.setAttribute('disabled', 'true');

            fetch('/api/command/stop-buzzer', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            })
            .then(res => res.json())
            .then(data => {
                setTimeout(() => { 
                    if(document.getElementById('ui-fog-status').innerText === 'FOG DETECTED') {
                        btn.innerText = "Stop Buzzer"; 
                        btn.classList.remove('btn-disabled');
                        btn.removeAttribute('disabled');
                    } else {
                        btn.innerText = "Stop Buzzer";
                    }
                }, 3000);
            });
        }

        // Poll every 1 second for faster real-time experience
        setInterval(fetchLiveStatus, 1000);
        // Initial fetch
        fetchLiveStatus();
    </script>
</body>
</html>
