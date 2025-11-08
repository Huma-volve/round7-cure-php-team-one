<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('asset/img/favicon.png') }}">


    <title>@yield('title')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('asset/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('asset/css/sb-admin-2.min.css')}}" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}



</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion   " id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SB Admin {{app()->getlocale()}}<sup>2</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">
   <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        @if(app()->getLocale() == 'ar')
            <img src="https://flagcdn.com/w20/sa.png" alt="Arabic" class="me-2 rounded">
            العربية
        @else
            <img src="https://flagcdn.com/w20/us.png" alt="English" class="me-2 rounded">
            English
        @endif
    </a>

    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="languageDropdown">
        <li>
            <a class="dropdown-item d-flex align-items-center py-2 {{ app()->getLocale() == 'ar' ? 'active fw-bold' : '' }}"
               href="{{ route('change.language', 'ar') }}">
                <img src="https://flagcdn.com/w20/sa.png" alt="Arabic" class="me-2 rounded">
                العربية
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center py-2 {{ app()->getLocale() == 'en' ? 'active fw-bold' : '' }}"
               href="{{ route('change.language', 'en') }}">
                <img src="https://flagcdn.com/w20/us.png" alt="English" class="me-2 rounded">
                English
            </a>
        </li>
    </ul>
</li>


        <li class="nav-item active  ms-auto">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>{{ __('sidebar.Dashboard') }}</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        {{ __('sidebar.Management') }}
    </div>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
            <i class="fas fa-users"></i>
            <span>{{ __('sidebar.Users') }}</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.doctors.index') }}">
            <i class="fas fa-user-md"></i>
            <span>{{ __('sidebar.Doctors') }}</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.patients.index') }}">
            <i class="fas fa-user-injured"></i>
            <span>{{ __('sidebar.Patients') }}</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.bookings.index') }}">
            <i class="fas fa-calendar-check"></i>
            <span>{{ __('sidebar.Bookings') }}</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.payments.index') }}">
            <i class="fas fa-credit-card"></i>
            <span>{{ __('sidebar.Payments') }}</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.disputes.index') }}">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ __('sidebar.Disputes') }}</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.tickets.index') }}">
            <i class="fas fa-ticket-alt"></i>
            <span>{{ __('sidebar.Tickets') }}</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages1"
            aria-expanded="true" aria-controls="collapsePages1">
            <i class="fas fa-fw fa-folder"></i>
            <span>{{ __('sidebar.Settings') }}</span>
        </a>
        <div id="collapsePages1" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">{{ __('sidebar.Settings Screens:') }}</h6>
            </div>
        </div>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

        </ul>
