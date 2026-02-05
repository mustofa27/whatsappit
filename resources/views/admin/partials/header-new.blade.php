<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container-fluid">
        <button class="btn btn-link d-lg-none" type="button" id="mobileSidebarToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
        
        <span class="navbar-brand mb-0 h1">@yield('page-title', 'Dashboard')</span>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=0D6EFD&color=fff" 
                         class="rounded-circle" width="32" height="32" alt="{{ auth()->user()->name }}">
                    <span class="ms-2 text-dark">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ url('admin/profile') }}">
                            <i class="bi bi-person me-2"></i> My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
