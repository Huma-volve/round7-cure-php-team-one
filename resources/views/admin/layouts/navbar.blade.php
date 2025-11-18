<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow {{ app()->getLocale()=='ar' ? 'rtl-navbar' : '' }}">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                 aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small"
                               placeholder="Search for..." aria-label="Search"
                               aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Language Dropdown -->
        <li class="nav-item dropdown no-arrow mx-1">
            @php
                $currentLocale = app()->getLocale();
                $currentLanguage = $currentLocale === 'ar' ? 'العربية' : 'English';
                $currentFlag = $currentLocale === 'ar'
                    ? 'https://flagcdn.com/w20/sa.png'
                    : 'https://flagcdn.com/w20/us.png';
                $currentShort = strtoupper($currentLocale);
            @endphp

            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="languageDropdown"
               role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ $currentFlag }}" alt="{{ $currentLanguage }}" class="rounded" width="20" height="20">
                <span class="text-gray-600 small d-none d-sm-inline">{{ $currentLanguage }}</span>
                <span class="text-gray-600 small d-inline d-sm-none">{{ $currentShort }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="languageDropdown">
                <a class="dropdown-item d-flex align-items-center py-2 {{ app()->getLocale() == 'ar' ? 'active font-weight-bold' : '' }}"
                   href="{{ route('change.language', 'ar') }}">
                    <img src="https://flagcdn.com/w20/sa.png" alt="Arabic" class="mr-2 rounded" width="20" height="20">
                    <span class="ms-1">العربية</span>
                </a>
                <a class="dropdown-item d-flex align-items-center py-2 {{ app()->getLocale() == 'en' ? 'active font-weight-bold' : '' }}"
                   href="{{ route('change.language', 'en') }}">
                    <img src="https://flagcdn.com/w20/us.png" alt="English" class="mr-2 rounded" width="20" height="20">
                    <span class="ms-1">English</span>
                </a>
            </div>
        </li>

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>

                <span class="badge badge-danger badge-counter">{{ $unreadCount ?? 0 }}</span>
            </a>

            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Alerts Center</h6>

                <!-- START Scrollable area -->
                <div class="notifications-scroll">
                    @forelse (($notifications ?? collect()) as $notification)
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">{{ $notification->created_at }}</div>
                                <span class="{{ $notification->is_read ? '' : 'font-weight-bold' }}">
                                    {{ $notification->body }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <a class="dropdown-item text-center small text-gray-500">No notifications found</a>
                    @endforelse
                </div>
                <!-- END Scrollable area -->

                <a class="dropdown-item text-center small text-gray-500"
                   href="{{ route('admin.notifications.index') }}">
                    Show All Alerts
                </a>
            </div>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <span class="badge badge-danger badge-counter">{{ $ticketsCount ?? 0 }}</span>
            </a>

            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">Tickets Center</h6>

                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.tickets.index') }}">
                    <div class="dropdown-list-image mr-3">
                        <div class="status-indicator bg-success"></div>
                        <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                    </div>
                    <div class="font-weight-bold">
                        <div class="text-truncate">You have {{ $ticketsCount ?? 0 }} open/pending tickets.</div>
                        <div class="small text-gray-500">Go to tickets to view details</div>
                    </div>
                </a>

                <a class="dropdown-item text-center small text-gray-500"
                   href="{{ route('admin.tickets.index') }}">
                    Read More Messages
                </a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Menu -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ Auth::user()->name }}
                </span>
                <img class="img-profile rounded-circle"
                     src="{{ $avatarUrl ?? asset('img/undraw_profile.svg') }}">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('admin.account.profile') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="{{ route('admin.account.settings') }}">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Settings
                </a>
                <a class="dropdown-item" href="{{ route('admin.account.activity-log') }}">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                    Activity Log
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>

<!-- RTL Styles -->
@if(app()->getLocale()=='ar')
<style>
    /* .rtl-navbar { direction: rtl; }

    .rtl-navbar .navbar-nav.ml-auto {
        margin-left: 0 !important;
        margin-right: auto !important;
    }

    .rtl-navbar .dropdown-menu {
        text-align: right;
        direction: rtl;
        right: auto;
        left: 0;
    }

    .rtl-navbar .dropdown-item .mr-2 {
        margin-right: 0 !important;
        margin-left: .5rem !important;
    }

    .rtl-navbar #userDropdown .mr-2 {
        margin-right: 0 !important;
        margin-left: .5rem !important;
    } */
</style>
@endif

<style>
/* Scroll area inside notifications */
/* .notifications-scroll {
    max-height: 250px;
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 5px;
} */
</style>

<script>
document.getElementById('alertsDropdown').addEventListener('click', function () {

    fetch('/notifications/{{ Auth::id() }}/markAllAsRead', {
        method: 'POST',
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {

            // صفر العداد
            let counter = document.querySelector('.badge-counter');
            if (counter) counter.textContent = 0;

            // شيل الخط العريض من الإشعارات
            document.querySelectorAll('.notifications-scroll span.font-weight-bold')
                .forEach(el => el.classList.remove('font-weight-bold'));
        }
    });
});
</script>


