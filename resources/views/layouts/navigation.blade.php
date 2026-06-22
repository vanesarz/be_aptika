<nav class="sj-sidebar">
    <div class="sidebar-header">
        <div class="brand-block">
            <div class="brand-raptika">
                <span class="brand-r">R</span><span class="brand-rest">APTIKA</span>
            </div>
            <span class="brand-sub">Rekap Data Aptika</span>
        </div>
    </div>

    <div class="sidebar-content">
        <div class="nav-group">
            <p class="group-title">Menu Utama</p>
            
            {{-- Link SMART Jabar --}}
            <a href="{{ url('/smartjabar/joined-apps') }}" 
               class="nav-link {{ Request::is('smartjabar/joined') ? 'active' : '' }}">
             
                <span>SMART Jabar</span>
            </a>

            {{-- Link Integrasi --}}
            <a href="{{ url('/sadajabar') }}" 
               class="nav-link {{ Request::is('sadajabar*') ? 'active' : '' }}">
              
                <span>SADA Jabar</span>
            </a>

            {{-- Link Pengelolaan --}}
            <a href="{{ url('/pengelolaan') }}" 
               class="nav-link {{ Request::is('pengelolaan*') ? 'active' : '' }}">
           
                <span>Pengelolaan Aplikasi</span>
            </a>
            
            {{-- Link Rekayasa --}}
            <a href="{{ url('/rekayasa/application-replications') }}" 
               class="nav-link {{ Request::is('rekayasa*') ? 'active' : '' }}">
             
                <span>Rekayasa Aplikasi</span>
            </a>
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
            <div>
                <p class="user-name">{{ Auth::user()->name ?? 'User' }}</p>
                <a href="/profile" class="user-action">Pengaturan</a>
            </div>
        </div>
        
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="submit" class="logout-btn" title="Logout" style="border:none; cursor:pointer;">
                <svg viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </button>
        </form>
    </div>
</nav>

<style>
    /* CSS tetap sama dengan yang kamu miliki, namun saya tambahkan sedikit perbaikan */
    .sj-sidebar {
        width: 280px; height: 100vh; background: #1E74BC;
        display: flex; flex-direction: column; position: fixed;
        left: 0; top: 0; overflow: hidden; z-index: 100;
    }

    .sidebar-header { padding: 1.35rem 1.5rem 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.12); }
    .brand-raptika { font-family: 'DM Mono', monospace; font-size: 1.7rem; color: #fff; }
    .brand-r { color: #22C55E; font-weight: 700; }
    .brand-sub { font-size: 0.7rem; color: rgba(255,255,255,0.5); font-family: 'DM Mono', monospace; }

    .sidebar-content { flex: 1; padding: 1rem; overflow-y: auto; }
    .group-title { font-size: 0.65rem; text-transform: uppercase; color: rgba(255,255,255,0.4); margin-bottom: 0.5rem; padding-left: 0.5rem; }

    .nav-link {
        display: flex; align-items: center; gap: 0.75rem; padding: 0.7rem 0.85rem;
        border-radius: 10px; text-decoration: none; color: rgba(255,255,255,0.8);
        font-size: 0.875rem; transition: all 0.2s; margin-bottom: 4px;
    }

    .nav-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
    .nav-link.active { background: rgba(255,255,255,0.15); color: #fff; font-weight: 600; }
    .nav-link.active::before {
        content: ''; position: absolute; left: 0; top: 25%; bottom: 25%;
        width: 4px; background: #22C55E; border-radius: 0 4px 4px 0;
    }

    .nav-icon {
        width: 30px; height: 30px; background: rgba(255,255,255,0.1);
        border-radius: 8px; display: flex; align-items: center; justify-content: center;
    }
    .nav-icon svg { width: 16px; height: 16px; stroke: #fff; fill: none; stroke-width: 2; }
    .nav-link.active .nav-icon { background: rgba(34,197,94,0.3); }

    .sidebar-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 1.25rem; border-top: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.1);
    }
    .user-profile { display: flex; align-items: center; gap: 0.7rem; }
    .user-avatar { 
        width: 35px; height: 35px; border-radius: 50%; background: #22C55E; 
        color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;
    }
    .user-name { font-size: 0.85rem; color: #fff; margin: 0; }
    .user-action { font-size: 0.7rem; color: rgba(255,255,255,0.5); text-decoration: none; }
    .logout-btn { background: rgba(255,255,255,0.1); padding: 8px; border-radius: 8px; }
    .logout-btn svg { width: 16px; height: 16px; stroke: #fff; fill: none; stroke-width: 2; }
</style>