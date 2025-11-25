<!-- Navbar Start -->
<nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
    <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
        <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
    </a>
    <a href="#" class="sidebar-toggler flex-shrink-0">
        <i class="fa fa-bars"></i>
    </a>
    <form class="d-none d-md-flex ms-4">
        <input class="form-control bg-dark border-0" type="search" placeholder="Search">
    </form>
    <div class="navbar-nav align-items-center ms-auto">
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-2" style="font-size: 24px;"></i>
                <span class="d-none d-lg-inline-flex">
                    @if(session('user'))
                        {{ session('user')->username }} ({{ ucfirst(session('user')->level) }})
                    @else
                        User
                    @endif
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                <a href="{{ route('profile.index') }}" class="dropdown-item">
                    <i class="fas fa-user me-2"></i>My Profile
                </a>
                <a href="{{ route('profile.index') }}" class="dropdown-item">
                    <i class="fas fa-cog me-2"></i>Settings
                </a>
                <a href="{{ route('logout') }}" class="dropdown-item">
                    <i class="fas fa-sign-out-alt me-2"></i>Log Out
                </a>
            </div>
        </div>
    </div>
</nav>
<!-- Navbar End -->