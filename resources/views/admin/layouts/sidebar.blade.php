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
        @php
            $isArabic = app()->getLocale() === 'ar';
            $user = auth()->user();
            $isDoctorPortal = $user && $user->hasRole('doctor') && !$user->hasRole('admin');
        @endphp
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion {{ $isArabic ? 'rtl-sidebar' : '' }}"
            id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center mt-2"

            href="{{ $isDoctorPortal ? route('doctor.dashboard') : route('admin.dashboard') }}">
                <div class="sidebar-brand-icon ">
             <img style="width: 50px;" src="{{ asset('storage/' . $logo) }}" alt="{{ $appName }}" >
                </div>

             <div class="sidebar-brand-text mx-3 font-weight-bold">
                {{ \App\Models\Setting::getValue('app_name', config('app.name')) }}  </div>
       </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">



            @if($isDoctorPortal)
                <li class="nav-item {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>{{ __('sidebar.DoctorDashboard') }}</span>
                    </a>
                </li>

                <hr class="sidebar-divider">

                <li class="nav-item {{ request()->routeIs('doctor.bookings.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('doctor.bookings.*') ? 'active' : '' }}" href="{{ route('doctor.bookings.index') }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>{{ __('sidebar.DoctorBookings') }}</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('doctor.patients.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('doctor.patients.*') ? 'active' : '' }}" href="{{ route('doctor.patients.index') }}">
                        <i class="fas fa-user-injured"></i>
                        <span>{{ __('sidebar.DoctorPatients') }}</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('doctor.payments.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('doctor.payments.*') ? 'active' : '' }}" href="{{ route('doctor.payments.index') }}">
                        <i class="fas fa-credit-card"></i>
                        <span>{{ __('sidebar.DoctorPayments') }}</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('doctor.schedule.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('doctor.schedule.*') ? 'active' : '' }}" href="{{ route('doctor.schedule.edit') }}">
                        <i class="fas fa-clock"></i>
                        <span>{{ __('sidebar.DoctorSchedule') }}</span>
                    </a>
                </li>
            @else
            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">

                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span style="font-size: large;" >{{ __('sidebar.Dashboard') }}</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                {{ __('sidebar.Management') }}
            </div>



            <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        @if (app()->getlocale() == 'ar')

                       <span style="font-size: large;" >{{ __('sidebar.Users') }}</span>
                                       <i class="fas fa-users"></i>
                                       @else
                         <i class="fas fa-users"></i>
                    <span style="font-size: large;" >{{ __('sidebar.Users') }}</span>

                        @endif

                </a>
            </li>



            <li class="nav-item {{ request()->routeIs('admin.specialties.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.specialties.*') ? 'active' : '' }}" href="{{ route('admin.specialties.index') }}">
                        @if (app()->getlocale() == 'ar')

                       <span style="font-size: large;" >{{ __('sidebar.Specialties') }}</span>
                              <i class="fas fa-user-md"></i>
                                       @else
                       <i class="fas fa-user-md"></i>
                    <span style="font-size: large;" >{{ __('sidebar.Specialties') }}</span>

                        @endif
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}" href="{{ route('admin.doctors.index') }}">
                        @if (app()->getlocale() == 'ar')

                       <span style="font-size: large;" >{{ __('sidebar.Doctors') }}</span>
                              <i class="fas fa-user-md"></i>
                                       @else
                       <i class="fas fa-user-md"></i>
                    <span style="font-size: large;" >{{ __('sidebar.Doctors') }}</span>

                        @endif
                </a>
            </li>

            <hr class="sidebar-divider">



            <li class="nav-item {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}" href="{{ route('admin.patients.index') }}">

                       @if (app()->getlocale() == 'ar')

                            <span style="font-size: large;" >{{ __('sidebar.Patients') }}</span>
                                <i class="fas fa-user-injured"></i>
                                                @else
                                <i class="fas fa-user-injured"></i>
                            <span style="font-size: large;" >{{ __('sidebar.Patients') }}</span>

                        @endif


                </a>
            </li>



            <li class="nav-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">

                       @if (app()->getlocale() == 'ar')

                            <span style="font-size: large;" >{{ __('sidebar.Bookings') }}</span>
                                    <i class="fas fa-calendar-check"></i>
                                                @else
                             <i class="fas fa-calendar-check"></i>
                            <span style="font-size: large;" >{{ __('sidebar.Bookings') }}</span>

                        @endif

                </a>
            </li>


            <li class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
        @if (app()->getlocale() == 'ar')

                            <span style="font-size: large;" >{{ __('sidebar.Payments') }}</span>
                     <i class="fas fa-credit-card"></i>
                                                @else
                          <i class="fas fa-credit-card"></i>
                            <span style="font-size: large;" >{{ __('sidebar.Payments') }}</span>

                        @endif


                </a>
            </li>



            <li class="nav-item {{ request()->routeIs('admin.disputes.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.disputes.*') ? 'active' : '' }}" href="{{ route('admin.disputes.index') }}">
    @if (app()->getlocale() == 'ar')

                            <span style="font-size: large;" >{{ __('sidebar.Disputes') }}</span>
                     <i class="fas fa-credit-card"></i>
                                                @else
                          <i class="fas fa-credit-card"></i>
                            <span style="font-size: large;" >{{ __('sidebar.Disputes') }}</span>

                        @endif

                </a>
            </li>


            <li class="nav-item {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">
    @if (app()->getlocale() == 'ar')

                            <span style="font-size: large;" >{{ __('sidebar.Tickets') }}</span>
                      <i class="fas fa-ticket-alt"></i>
                                                @else
                           <i class="fas fa-ticket-alt"></i>
                            <span style="font-size: large;" >{{ __('sidebar.Tickets') }}</span>

                        @endif



                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}" href="{{ route('admin.faqs.index') }}">
 @if (app()->getlocale() == 'ar')

                            <span style="font-size: large;" >{{ __('sidebar.Faqs') }}</span>
                       <i class="fas fa-question-circle"></i>
                                                @else
                          <i class="fas fa-question-circle"></i>
                            <span style="font-size: large;" >{{ __('sidebar.Faqs') }}</span>

                        @endif

                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
@if (app()->getlocale() == 'ar')

                            <span style="font-size: large;" >{{ __('sidebar.Settings') }}</span>
                         <i class="fas fa-cog"></i>
                                                @else
                            <i class="fas fa-cog"></i>
                            <span style="font-size: large;" >{{ __('sidebar.Settings') }}</span>

                        @endif


                </a>
            </li>
            @endif

            <hr class="sidebar-divider d-none d-md-block">



            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        <style>
            #accordionSidebar .nav-link {
                display: flex;
                align-items: center;
                gap: .65rem;
                justify-content: flex-start;
            }

            #accordionSidebar .sidebar-heading {
                text-align: left;
            }
        </style>

        @if ($isArabic)
            <style>
                #accordionSidebar.rtl-sidebar {
                    direction: rtl;
                }

                #accordionSidebar.rtl-sidebar .nav-link {
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

                #accordionSidebar.rtl-sidebar .nav-link span,
                #accordionSidebar.rtl-sidebar .sidebar-heading {
                    text-align: right;
                }
            </style>
        @endif
