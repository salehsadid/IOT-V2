<header class="navbar">
    <div class="navbar-left">
        <button class="mobile-toggle" id="mobileToggle">
            ☰
        </button>
        <span style="font-weight: 600; color: var(--text-muted); display: none;"><!-- Title is in sidebar --></span>
    </div>

    <div class="navbar-right">
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <span class="user-role">{{ Auth::user()->role->label() ?? 'User' }}</span>
        </div>
        
        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</header>
