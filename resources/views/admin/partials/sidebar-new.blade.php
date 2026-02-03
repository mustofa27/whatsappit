<div class="sidebar d-flex flex-column" id="sidebar">
    <div class="sidebar-header border-bottom border-secondary p-3 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 sidebar-title">WAIt</h5>
        <button class="btn btn-link text-white d-none d-lg-block p-0" id="sidebarToggle" type="button">
            <i class="bi bi-list fs-4"></i>
        </button>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas"></button>
    </div>
    
    <div class="flex-grow-1 overflow-auto">
        <nav class="nav flex-column p-3">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'bg-primary' : '' }}" title="Dashboard">
                <i class="bi bi-speedometer2 fs-5"></i>
                <span class="sidebar-text ms-3">Dashboard</span>
            </a>
            
            <a href="{{ route('admin.accounts.index') }}" class="nav-link text-white {{ request()->routeIs('admin.accounts.*') ? 'bg-primary' : '' }}" title="WhatsApp Accounts">
                <i class="bi bi-phone fs-5"></i>
                <span class="sidebar-text ms-3">WhatsApp Accounts</span>
            </a>
            
            <a href="{{ route('admin.messages.index') }}" class="nav-link text-white {{ request()->routeIs('admin.messages.*') ? 'bg-primary' : '' }}" title="Messages">
                <i class="bi bi-envelope fs-5"></i>
                <span class="sidebar-text ms-3">Messages</span>
            </a>
        </nav>
    </div>
</div>
