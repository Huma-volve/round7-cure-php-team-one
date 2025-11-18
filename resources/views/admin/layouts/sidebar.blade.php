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
    <link href="{{ asset('asset/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}



</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @php($isArabic = app()->getLocale() === 'ar')
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion {{ $isArabic ? 'rtl-sidebar' : '' }}"
            id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center"
                href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon  ">
                     <img style="width: 50px;" src="{{ asset('storage/' . $logo) }}" alt="{{ $appName }}" >
                </div>

             <div class="sidebar-brand-text mx-3 font-weight-bold">
                {{ \App\Models\Setting::getValue('app_name', config('app.name')) }}  </div>


            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <li class="nav-item   {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span style="font-size: large;" >{{ __('sidebar.Dashboard') }}</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                {{ __('sidebar.Management') }}
            </div>

            <li class="nav-item {{request()->routeIs('admin.users.*') ? 'active' : ''}}">
                <a class="nav-link " href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i>
                    <span style="font-size: large;" >{{ __('sidebar.Users') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('admin.doctors.*') ? 'active text-white' : ''}}"
                 href="{{ route('admin.doctors.index') }}">
                    <i class="fas fa-user-md"></i>
                    <span style="font-size: large;"> {{ __('sidebar.Doctors') }}</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <li class="nav-item  {{request()->routeIs('admin.patients.*') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('admin.patients.index') }}">
                    <i class="fas fa-user-injured"></i>
                    <span style="font-size: large;">{{ __('sidebar.Patients') }}</span>
                </a>
            </li>

            <li class="nav-item  {{ request()->routeIs('admin.bookings.*')  ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.bookings.index') }}">
                    <i class="fas fa-calendar-check"></i>
                    <span style="font-size: large;">{{ __('sidebar.Bookings') }}</span>
                </a>
            </li>

            <li class="nav-item  {{request()->routeIs('admin.payments.*') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('admin.payments.index') }}">
                    <i class="fas fa-credit-card"></i>
                    <span style="font-size: large;" >{{ __('sidebar.Payments') }}</span>
                </a>
            </li>

            <li class="nav-item  {{request()->routeIs('admin.disputes.*') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('admin.disputes.index') }}">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span style="font-size: large;" >{{ __('sidebar.Disputes') }}</span>
                </a>
            </li>

            <li class="nav-item {{request()->routeIs('admin.tickets.*') ? 'active' : ''}} ">
                <a class="nav-link" href="{{ route('admin.tickets.index') }}">
                    <i class="fas fa-ticket-alt"></i>
                    <span style="font-size: large;" >{{ __('sidebar.Tickets') }}</span>
                </a>
            </li>
            <li class="nav-item  {{request()->routeIs('admin.settings.*') ? 'active' : ''}} ">
                <a class="nav-link" href="{{ route('admin.settings.index') }}">
                    {{-- <i class="fas fa-ticket-alt"></i> --}}
                  <i class="fas fa-cog"></i>

                    <span style="font-size: large;" >{{ __('sidebar.Settings') }}</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">



            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        @if ($isArabic)
            <style>
                #accordionSidebar.rtl-sidebar .nav-link {
                    direction: rtl;
                    display: flex;
                    flex-direction: row-reverse;
                    justify-content: flex-end;
                    align-items: center;
                    gap: .75rem;
                }

                #accordionSidebar.rtl-sidebar .nav-link i {
                    margin-left: 0;
                    margin-right: 0;
                }

                #accordionSidebar.rtl-sidebar .nav-link span {
                    text-align: right;
                }

                #accordionSidebar.rtl-sidebar .sidebar-heading {
                    text-align: right;
                }
            </style>
        @endif
