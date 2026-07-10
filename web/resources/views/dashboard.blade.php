<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Parkinson's Monitoring System</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 2rem;
            color: #111827;
        }
        .dashboard-container {
            background-color: white;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #2563eb;
        }
        .info-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-row {
            margin-bottom: 1rem;
            display: flex;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            width: 100px;
            color: #4b5563;
        }
        .info-value {
            color: #111827;
        }
        .role-badge {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        .logout-form {
            margin: 0;
        }
        .btn-logout {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-logout:hover {
            background-color: #dc2626;
        }
        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Parkinson's Monitor</h1>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>

        <div class="success-message">
            ✅ Authentication Successful
        </div>

        <div class="info-card">
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Role:</div>
                <div class="info-value">
                    <span class="role-badge">{{ $user->role->label() ?? 'Unknown' }}</span>
                </div>
            </div>
        </div>
        
        <p style="color: #6b7280; font-size: 0.875rem;">
            This is a temporary page. The full dashboard UI will be implemented in a future phase.
        </p>
    </div>
</body>
</html>
