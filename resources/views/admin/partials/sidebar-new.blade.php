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

            <a href="{{ route('admin.contacts.index') }}" class="nav-link text-white {{ request()->routeIs('admin.contacts.*') ? 'bg-primary' : '' }}" title="Contacts">
                <i class="bi bi-people fs-5"></i>
                <span class="sidebar-text ms-3">Contacts</span>
            </a>
            
            <a href="{{ route('admin.conversations.index') }}" class="nav-link text-white {{ request()->routeIs('admin.conversations.*') ? 'bg-primary' : '' }}" title="Conversations">
                <i class="bi bi-chat-dots fs-5"></i>
                <span class="sidebar-text ms-3">Conversations</span>
            </a>

            <a href="{{ route('admin.scheduled-messages.index') }}" class="nav-link text-white {{ request()->routeIs('admin.scheduled-messages.*') ? 'bg-primary' : '' }}" title="Message Queue">
                <i class="bi bi-clock-history fs-5"></i>
                <span class="sidebar-text ms-3">Message Queue</span>
            </a>

            <a href="{{ route('admin.templates.index') }}" class="nav-link text-white {{ request()->routeIs('admin.templates.*') ? 'bg-primary' : '' }}" title="Templates">
                <i class="bi bi-file-text fs-5"></i>
                <span class="sidebar-text ms-3">Templates</span>
            </a>

            <a href="{{ route('admin.analytics.index') }}" class="nav-link text-white {{ request()->routeIs('admin.analytics.*') ? 'bg-primary' : '' }}" title="Analytics">
                <i class="bi bi-graph-up fs-5"></i>
                <span class="sidebar-text ms-3">Analytics</span>
            </a>

            <a href="{{ route('admin.team-members.index') }}" class="nav-link text-white {{ request()->routeIs('admin.team-members.*') ? 'bg-primary' : '' }}" title="Team Members">
                <i class="bi bi-people-fill fs-5"></i>
                <span class="sidebar-text ms-3">Team Members</span>
            </a>

            <a href="{{ route('admin.pending-invitations') }}" class="nav-link text-white {{ request()->routeIs('admin.pending-invitations') ? 'bg-primary' : '' }}" title="Pending Invitations">
                <i class="bi bi-inbox fs-5"></i>
                <span class="sidebar-text ms-3">Pending Invitations</span>
            </a>

            <a href="{{ route('subscription.show') }}" class="nav-link text-white {{ request()->routeIs('subscription.*') ? 'bg-primary' : '' }}" title="My Subscription">
                <i class="bi bi-box-seam fs-5"></i>
                <span class="sidebar-text ms-3">My Subscription</span>
            </a>

            @if(auth()->user()->is_admin)
            <hr class="bg-secondary my-2">
            <div class="nav-link text-white-50 px-0 py-1" style="cursor: default;">
                <small class="text-uppercase fw-bold">Administration</small>
            </div>
            
            <a href="{{ route('admin.users.index') }}" class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'bg-primary' : '' }}" title="User Management">
                <i class="bi bi-people fs-5"></i>
                <span class="sidebar-text ms-3">User Management</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="nav-link text-white {{ request()->routeIs('admin.settings.*') ? 'bg-primary' : '' }}" title="Settings">
                <i class="bi bi-gear fs-5"></i>
                <span class="sidebar-text ms-3">Settings</span>
            </a>

            <a href="{{ route('admin.subscription-plans.index') }}" class="nav-link text-white {{ request()->routeIs('admin.subscription-plans.*') ? 'bg-primary' : '' }}" title="Subscription Plans">
                <i class="bi bi-credit-card fs-5"></i>
                <span class="sidebar-text ms-3">Subscription Plans</span>
            </a>
            @endif
        </nav>
    </div>
</div>
