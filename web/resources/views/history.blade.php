<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detection Event History</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #2c3e50; font-weight: 600; }
        tr:hover { background-color: #f1f1f1; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; }
        .badge.tremor { background-color: #e74c3c; }
        .badge.fog { background-color: #f39c12; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; }
        .pagination nav { display: flex; gap: 5px; }
        .pagination a, .pagination span { padding: 8px 12px; margin: 0; border: 1px solid #ddd; text-decoration: none; color: #3498db; border-radius: 4px; }
        .pagination .active { background-color: #3498db; color: white; border-color: #3498db; }
        .pagination svg { width: 20px; height: 20px; }
        .empty-state { text-align: center; padding: 40px; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detection Event History</h1>
        
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
            <p>Connect the ESP32 to WiFi to start logging events.</p>
        </div>
        @endif
    </div>
</body>
</html>
