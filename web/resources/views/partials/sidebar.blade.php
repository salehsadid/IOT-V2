<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        Parkinson's Monitor
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <span style="margin-right: 10px;">📊</span> Dashboard
                </a>
            </li>
            <li>
                <a href="#">
                    <span style="margin-right: 10px;">👥</span> Patients
                </a>
            </li>
            <li>
                <a href="#">
                    <span style="margin-right: 10px;">📱</span> Devices
                </a>
            </li>
            <li>
                <a href="#">
                    <span style="margin-right: 10px;">📈</span> Detection Events
                </a>
            </li>
            <li>
                <a href="#">
                    <span style="margin-right: 10px;">⚡</span> Commands
                </a>
            </li>
            <li style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <a href="#">
                    <span style="margin-right: 10px;">⚙️</span> Settings
                </a>
            </li>
        </ul>
    </nav>
</aside>
