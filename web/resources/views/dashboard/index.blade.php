<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); 
            color: #f8fafc; 
            margin: 0; 
            padding: 0;
            min-height: 100vh;
        }
        
        .navbar { 
            background: rgba(15, 23, 42, 0.7); 
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar .brand { font-weight: 800; font-size: 20px; letter-spacing: 1px; color: #38bdf8; }
        .navbar a { 
            color: #cbd5e1; text-decoration: none; font-weight: 600; padding: 8px 16px; 
            border-radius: 8px; transition: all 0.3s ease; 
        }
        .navbar a:hover { background: rgba(56, 189, 248, 0.2); color: #38bdf8; }
        
        .container { 
            max-width: 1200px; margin: 40px auto; padding: 0 20px; 
            display: grid; grid-template-columns: 1fr 1fr; gap: 30px; 
        }
        
        .card { 
            background: rgba(30, 41, 59, 0.5); 
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 30px; 
            border-radius: 16px; 
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5); 
            transition: transform 0.3s ease;
        }
        .card:hover { transform: translateY(-5px); }
        
        .card h2 { 
            margin-top: 0; color: #94a3b8; border-bottom: 1px solid rgba(255,255,255,0.1); 
            padding-bottom: 15px; font-size: 16px; text-transform: uppercase; letter-spacing: 2px; 
        }
        
        .info-row { display: flex; margin-bottom: 15px; font-size: 16px; }
        .info-row strong { width: 100px; color: #38bdf8; }
        
        /* Machine Status Grid */
        .machine-status { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px; }
        .status-box { 
            background: rgba(15, 23, 42, 0.6); 
            padding: 20px 10px; 
            border-radius: 12px; 
            text-align: center; 
            border: 1px solid rgba(255,255,255,0.02);
        }
        .status-box-title { font-size: 14px; color: #94a3b8; margin-bottom: 10px; font-weight: 600; }
        
        .status-badge { 
            display: inline-block; padding: 6px 12px; border-radius: 20px; 
            font-weight: 800; font-size: 12px; letter-spacing: 1px;
        }
        .status-ok { background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid #4ade80; }
        .status-err { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #f87171; }
        .status-off { background: rgba(100, 116, 139, 0.2); color: #94a3b8; border: 1px solid #94a3b8; }
        
        .big-status { font-size: 32px; font-weight: 800; margin: 30px 0; text-align: center; text-shadow: 0 0 20px rgba(0,0,0,0.5); }
        
        .actions { display: flex; gap: 15px; margin-top: 20px; justify-content: center; }
        
        .btn { 
            display: inline-block; padding: 12px 24px; background: #38bdf8; color: #0f172a; 
            text-decoration: none; border-radius: 8px; font-weight: 800; border: none; 
            cursor: pointer; text-align: center; transition: all 0.2s ease;
        }
        .btn:hover { background: #0ea5e9; box-shadow: 0 0 15px rgba(56, 189, 248, 0.4); }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.4); }
        .btn-disabled { background: #475569; color: #94a3b8; cursor: not-allowed; box-shadow: none !important; }
        
        /* Popup Modal */
        #alert-modal { 
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); z-index: 1000; 
            justify-content: center; align-items: center; 
        }
        .modal-content { 
            background: #1e293b; padding: 40px; border-radius: 20px; text-align: center; 
            max-width: 450px; width: 100%; border: 2px solid #ef4444;
            box-shadow: 0 0 40px rgba(239, 68, 68, 0.3);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .modal-content h1 { color: #f87171; margin-top: 0; font-size: 32px; font-weight: 800; }
        .modal-content p { font-size: 18px; color: #cbd5e1; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="brand">Parkinson Monitor System</div>
        <div>
            <a href="{{ route('history') }}">View Full History</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left: 15px;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#f87171; font-weight:800; font-size:16px; cursor:pointer; font-family:'Inter', sans-serif;">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <!-- Patient Profile -->
        <div class="card">
            <h2>Patient Profile</h2>
            @if($patient)
                <div class="info-row"><strong>Name:</strong> {{ $patient->full_name }}</div>
                <div class="info-row"><strong>Age:</strong> {{ $age }} years</div>
                <div class="info-row"><strong>DOB:</strong> {{ $patient->date_of_birth }}</div>
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
            <p style="text-align:center; font-size:12px; color:#64748b; margin-top:0;">Syncing every 3 seconds...</p>
        </div>

        <!-- Tremor Status -->
        <div class="card">
            <h2>Tremor Status (Live)</h2>
            <div id="ui-tremor-status" class="big-status" style="color:#4ade80;">NO TREMOR</div>
            <div class="actions">
                <a href="{{ route('history') }}?type=TREMOR" class="btn">See Tremor History</a>
            </div>
        </div>

        <!-- FOG Status -->
        <div class="card">
            <h2>FOG Status (Live)</h2>
            <div id="ui-fog-status" class="big-status" style="color:#4ade80;">NO FOG</div>
            <div class="actions">
                <button id="btn-stop-buzzer" class="btn btn-danger btn-disabled" disabled onclick="stopBuzzer()">Stop Buzzer</button>
                <a href="{{ route('history') }}?type=FOG" class="btn">See FOG History</a>
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

    <script>
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

            if (!online) {
                powerBadge.className = 'status-badge status-off'; powerBadge.innerText = 'OFFLINE';
                handBadge.className = 'status-badge status-off'; handBadge.innerText = 'N/A';
                legBadge.className = 'status-badge status-off'; legBadge.innerText = 'N/A';
                tremorStatus.innerText = 'OFFLINE'; tremorStatus.style.color = '#64748b';
                fogStatus.innerText = 'OFFLINE'; fogStatus.style.color = '#64748b';
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
                tremorStatus.style.color = '#f87171';
                tremorStatus.style.textShadow = '0 0 20px rgba(248, 113, 113, 0.4)';
                if (lastTremorLevel === 0) showAlert('Tremor Detected!', 'Patient is experiencing a Level ' + status.tremor_level + ' Tremor.');
            } else {
                tremorStatus.innerText = 'NO TREMOR';
                tremorStatus.style.color = '#4ade80';
                tremorStatus.style.textShadow = '0 0 20px rgba(74, 222, 128, 0.4)';
            }
            lastTremorLevel = status.tremor_level;

            // FOG (Matches OLED Text)
            if (status.fog_active) {
                fogStatus.innerText = 'FOG DETECTED';
                fogStatus.style.color = '#f87171';
                fogStatus.style.textShadow = '0 0 20px rgba(248, 113, 113, 0.4)';
                stopBuzzerBtn.classList.remove('btn-disabled'); stopBuzzerBtn.disabled = false;
                if (!lastFogState) showAlert('FOG Detected!', 'Patient is experiencing Freezing of Gait.');
            } else {
                fogStatus.innerText = 'NO FOG';
                fogStatus.style.color = '#4ade80';
                fogStatus.style.textShadow = '0 0 20px rgba(74, 222, 128, 0.4)';
                stopBuzzerBtn.classList.add('btn-disabled'); stopBuzzerBtn.disabled = true;
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
            btn.disabled = true;

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
                        btn.disabled = false;
                    } else {
                        btn.innerText = "Stop Buzzer";
                    }
                }, 3000);
            });
        }

        // Poll every 3 seconds
        setInterval(fetchLiveStatus, 3000);
        // Initial fetch
        fetchLiveStatus();
    </script>
</body>
</html>
