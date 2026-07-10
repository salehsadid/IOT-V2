@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
    <h1 class="page-title">Dashboard Overview</h1>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card glass-panel">
            <div class="stat-card-title">👥 Total Patients</div>
            <div class="stat-card-value">0</div>
        </div>
        <div class="stat-card glass-panel">
            <div class="stat-card-title">📱 Total Devices</div>
            <div class="stat-card-value">0</div>
        </div>
        <div class="stat-card glass-panel">
            <div class="stat-card-title">📈 Today's Tremor Events</div>
            <div class="stat-card-value">0</div>
        </div>
        <div class="stat-card glass-panel">
            <div class="stat-card-title">⚠️ Today's FOG Events</div>
            <div class="stat-card-value">0</div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="section-card glass-panel">
        <div class="section-header">
            Recent Activity
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Event</th>
                        <th>Patient</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Placeholder Rows -->
                    <tr>
                        <td>10:42 AM</td>
                        <td><span class="badge badge-fog">FOG Detected</span></td>
                        <td>Demo Patient A</td>
                        <td><span class="badge badge-success">Cueing Active</span></td>
                    </tr>
                    <tr>
                        <td>09:15 AM</td>
                        <td><span class="badge badge-tremor">Severe Tremor</span></td>
                        <td>Demo Patient B</td>
                        <td>Logged</td>
                    </tr>
                    <tr>
                        <td>Yesterday</td>
                        <td><span class="badge badge-tremor">Mild Tremor</span></td>
                        <td>Demo Patient C</td>
                        <td>Logged</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
