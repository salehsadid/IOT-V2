<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detection Event History</title>
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
            max-width: 1100px; margin: 40px auto; padding: 0 20px; 
        }

        .card {
            background: rgba(30, 41, 59, 0.5); 
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 30px; 
            border-radius: 16px; 
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5); 
        }
        
        h1 { margin-top: 0; color: #f8fafc; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; font-weight: 800; font-size: 24px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 15px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        th { color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        tr:hover { background: rgba(255,255,255,0.02); }
        
        .badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; letter-spacing: 1px; }
        .badge.tremor { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #f87171; }
        .badge.fog { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid #fbbf24; }
        
        .pagination { margin-top: 30px; display: flex; justify-content: center; }
        .pagination nav { display: flex; gap: 5px; }
        .pagination a, .pagination span { 
            padding: 8px 15px; margin: 0; border: 1px solid rgba(255,255,255,0.1); 
            text-decoration: none; color: #cbd5e1; border-radius: 8px; background: rgba(15, 23, 42, 0.5);
        }
        .pagination a:hover { background: rgba(56, 189, 248, 0.2); color: #38bdf8; border-color: #38bdf8; }
        .pagination .active { background: #38bdf8; color: #0f172a; border-color: #38bdf8; font-weight: bold; }
        .pagination svg { width: 20px; height: 20px; }
        
        .empty-state { text-align: center; padding: 60px 20px; color: #64748b; }
        .empty-state h3 { color: #94a3b8; margin-bottom: 10px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="brand">Parkinson Monitor System</div>
        <div>
            <a href="{{ route('dashboard') }}">Back to Dashboard</a>
            @auth
            <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left: 15px;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#f87171; font-weight:800; font-size:16px; cursor:pointer; font-family:'Inter', sans-serif;">Logout</button>
            </form>
            @endauth
        </div>
    </div>
    
    <div class="container">
        <div class="card">
            <h1>Detection Event History {{ request('type') ? ' - ' . strtoupper(request('type')) : '' }}</h1>
            
            @if($events->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Event Type</th>
                        <th>Duration (s)</th>
                        <th>Start Lvl</th>
                        <th>Max Lvl</th>
                        <th>Device ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr>
                        <td>{{ $event->start_time->format('Y-m-d') }}</td>
                        <td>{{ $event->start_time->format('H:i:s') }}</td>
                        <td>{{ $event->end_time->format('H:i:s') }}</td>
                        <td>
                            <span class="badge {{ strtolower($event->event_type) }}">
                                {{ $event->event_type }}
                            </span>
                        </td>
                        <td>{{ number_format($event->duration_ms / 1000, 1) }}</td>
                        <td>{{ $event->isTremor() ? $event->start_level : '-' }}</td>
                        <td>{{ $event->isTremor() ? $event->max_level : '-' }}</td>
                        <td>{{ $event->device_id }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="pagination">
                {{ $events->links() }}
            </div>
            @else
            <div class="empty-state">
                <h3>No events recorded yet.</h3>
                <p>Wait for the ESP32 to detect and sync medical events.</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
