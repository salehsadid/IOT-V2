<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detection Event History</title>
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
            --hover-bg: rgba(14, 165, 233, 0.1);
            --table-hover: rgba(0, 0, 0, 0.02);
            --pagination-bg: #f8fafc;
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
            --hover-bg: rgba(56, 189, 248, 0.2);
            --table-hover: rgba(255, 255, 255, 0.02);
            --pagination-bg: rgba(15, 23, 42, 0.5);
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
        
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        .card {
            background: var(--card-bg); 
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            padding: 30px; 
            border-radius: 16px; 
            box-shadow: 0 10px 30px -10px var(--shadow-color); 
        }
        
        h1 { margin-top: 0; color: var(--text-main); border-bottom: 1px solid var(--border-color); padding-bottom: 15px; font-weight: 800; font-size: 24px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 15px 12px; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        tr:hover { background: var(--table-hover); }
        
        .badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; letter-spacing: 1px; }
        .badge.tremor { background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid #f87171; }
        .badge.fog { background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid #fbbf24; }
        
        [data-theme="dark"] .badge.tremor { color: #f87171; }
        [data-theme="dark"] .badge.fog { color: #fbbf24; }
        
        .pagination { margin-top: 30px; display: flex; justify-content: center; }
        .pagination nav { display: flex; gap: 5px; }
        .pagination a, .pagination span { 
            padding: 8px 15px; margin: 0; border: 1px solid var(--border-color); 
            text-decoration: none; color: var(--text-muted); border-radius: 8px; background: var(--pagination-bg);
            transition: all 0.2s ease;
        }
        .pagination a:hover { background: var(--hover-bg); color: var(--brand-color); border-color: var(--brand-color); }
        .pagination .active { background: var(--brand-color); color: #ffffff; border-color: var(--brand-color); font-weight: bold; }
        [data-theme="dark"] .pagination .active { color: #0f172a; }
        .pagination svg { width: 20px; height: 20px; }
        
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state h3 { color: var(--text-main); margin-bottom: 10px; font-weight: 600; }
        
        .filter-bar {
            display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-end; flex-wrap: wrap;
            background: rgba(0,0,0,0.02); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);
        }
        [data-theme="dark"] .filter-bar { background: rgba(255,255,255,0.02); }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-group label { font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .filter-group select, .filter-group input {
            padding: 10px 15px; border-radius: 8px; border: 1px solid var(--border-color); 
            background: var(--card-bg); color: var(--text-main); font-family: 'Inter', sans-serif;
            outline: none; transition: border-color 0.2s;
        }
        .filter-group select:focus, .filter-group input:focus { border-color: var(--brand-color); }
        
        .btn-filter {
            padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;
            background: var(--brand-color); color: #fff; transition: background 0.2s;
        }
        [data-theme="dark"] .btn-filter { color: #0f172a; }
        .btn-filter:hover { filter: brightness(1.1); }
        .btn-clear { background: var(--text-muted); }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="brand">Parkinson Monitor System</div>
        <div>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Theme</button>
            <a href="{{ route('dashboard') }}">Back to Dashboard</a>
            @auth
            <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left: 15px;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#ef4444; font-weight:800; font-size:16px; cursor:pointer; font-family:'Inter', sans-serif;">Logout</button>
            </form>
            @endauth
        </div>
    </div>
    
    <div class="container">
        <div class="card">
            <h1>Detection Event History</h1>
            
            <form class="filter-bar" method="GET" action="{{ route('history') }}">
                <div class="filter-group">
                    <label>Event Type</label>
                    <select name="type">
                        <option value="ALL" {{ request('type') == 'ALL' ? 'selected' : '' }}>All Events</option>
                        <option value="TREMOR" {{ request('type') == 'TREMOR' ? 'selected' : '' }}>Tremor</option>
                        <option value="FOG" {{ request('type') == 'FOG' ? 'selected' : '' }}>FOG</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="filter-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="filter-group">
                    <label>Sort By</label>
                    <select name="sort_by">
                        <option value="start_time" {{ request('sort_by') == 'start_time' ? 'selected' : '' }}>Date</option>
                        <option value="duration_ms" {{ request('sort_by') == 'duration_ms' ? 'selected' : '' }}>Duration</option>
                        <option value="max_level" {{ request('sort_by') == 'max_level' ? 'selected' : '' }}>Tremor Level</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Order</label>
                    <select name="sort_order">
                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="filter-group" style="flex-direction: row; gap: 10px;">
                    <button type="submit" class="btn-filter">Apply Filters</button>
                    <a href="{{ route('history') }}" class="btn-filter btn-clear" style="text-decoration:none;">Clear</a>
                </div>
            </form>
            
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

    <script>
        // Theme Toggle Logic
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
    </script>
</body>
</html>
