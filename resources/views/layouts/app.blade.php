<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ConnectCare') }}</title>

    {{-- Bootstrap + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; letter-spacing: .3px; }
        .nav-link.active { font-weight: 600; color: #ffc107 !important; }
        .dropdown-menu a { color: #000000 !important; }

        /* Tighter/more consistent link spacing, nicer alignment */
        .navbar-nav .nav-link { padding: .5rem .75rem; }
        .navbar-nav.gapped > .nav-item { margin-left: .25rem; margin-right: .25rem; }
        .navbar-dark .dropdown-menu { min-width: 16rem; }

        /* Optional thin divider between groups on lg+ */
        @media (min-width: 992px) {
            .nav-sep {
                width: 1px; height: 24px; background: hsla(238, 43%, 25%, 0.251);
                margin: 0 .5rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand me-3" href="{{ route('home') }}">{{ config('app.name', 'ConnectCare') }}</a>

        {{-- Mobile toggle --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapsible content --}}
        <div id="mainNav" class="collapse navbar-collapse">
            {{-- LEFT: primary navigation (aligned left, grows) --}}
            <ul class="navbar-nav gapped me-auto align-items-lg-center">
                @auth
                    @php
                        $role = auth()->user()->role->name ?? '';
                        $is = fn (...$patterns) => request()->routeIs(...$patterns) || request()->is(...$patterns);
                    @endphp

                    {{-- ================= ADMIN ================= --}}
                    @if($role === 'Admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                               class="nav-link {{ $is('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                               class="nav-link {{ $is('admin.users.*') ? 'active' : '' }}">Users</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $is('admin/churches*','admin/districts*','admin/zones*','admin/homecells*','admin/teams*') ? 'active' : '' }}"
                               href="#" id="structureDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Structure
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="structureDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.churches.index') }}">Churches</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.districts.index') }}">Districts</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.zones.index') }}">Zones</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.homecells.index') }}">Homecells</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.teams.index') }}">Teams</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $is('assignments*') ? 'active' : '' }}"
                               href="#" id="assignDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Assignments
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="assignDropdown">
                                <li><a class="dropdown-item" href="{{ route('assignments.index') }}">All Assignments</a></li>
                                <li><a class="dropdown-item" href="{{ route('assignments.standby') }}">Standby Pool</a></li>
                                <li><a class="dropdown-item" href="{{ route('assignments.bulkAssign') }}">Bulk Assign</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('assignments.reassign') }}">Reassign (cross-team)</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('members.index') }}"
                               class="nav-link {{ $is('members.*') ? 'active' : '' }}">Members</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('reports.homecells.index') }}"
                               class="nav-link {{ $is('reports.homecells.*') ? 'active' : '' }}">Homecells</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $is('services*','attendance*') ? 'active' : '' }}"
                               href="#" id="serviceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Services
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="serviceDropdown">
                                <li><a class="dropdown-item" href="{{ route('services.index') }}">Manage Services</a></li>
                                <li><a class="dropdown-item" href="{{ route('attendance.index') }}">Service Attendance</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $is('communications*','templates*') ? 'active' : '' }}"
                               href="#" id="commDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Communications
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="commDropdown">
                                <li><a class="dropdown-item" href="{{ route('communications.create') }}"><i class="bi bi-send me-1"></i>Compose</a></li>
                                <li><a class="dropdown-item" href="{{ route('communications.index') }}"><i class="bi bi-inbox me-1"></i>All Messages</a></li>
                                <li><a class="dropdown-item" href="{{ route('templates.index') }}"><i class="bi bi-file-text me-1"></i>Templates</a></li>
                            </ul>
                        </li>

                    {{-- ================= PASTOR ================= --}}
                    @elseif($role === 'Pastor')
                        <li class="nav-item">
                            <a href="{{ route('pastor.dashboard') }}"
                               class="nav-link {{ $is('pastor.dashboard') ? 'active' : '' }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.homecells.index') }}"
                               class="nav-link {{ $is('reports.homecells.*') ? 'active' : '' }}">Homecells</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('members.index') }}"
                               class="nav-link {{ $is('members.*') ? 'active' : '' }}">Members</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $is('services*','attendance*') ? 'active' : '' }}"
                               href="#" id="pastorServiceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Services
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="pastorServiceDropdown">
                                <li><a class="dropdown-item" href="{{ route('services.index') }}">Manage Services</a></li>
                                <li><a class="dropdown-item" href="{{ route('attendance.index') }}">Service Attendance</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $is('communications*','templates*') ? 'active' : '' }}"
                               href="#" id="pastorCommDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Communications
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="pastorCommDropdown">
                                <li><a class="dropdown-item" href="{{ route('communications.create') }}">Compose</a></li>
                                <li><a class="dropdown-item" href="{{ route('communications.index') }}">All Messages</a></li>
                                <li><a class="dropdown-item" href="{{ route('templates.index') }}">Templates</a></li>
                            </ul>
                        </li>

                    {{-- ================= STAFF ================= --}}
                    @elseif($role === 'Staff')
                        {{-- <li class="nav-item">
                            <a href="{{ route('team-member.dashboard') }}"
                               class="nav-link {{ $is('team-member.dashboard') ? 'active' : '' }}">Dashboard</a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('members.index') }}"
                               class="nav-link {{ $is('members.*') ? 'active' : '' }}">Members</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $is('services*','attendance*') ? 'active' : '' }}"
                               href="#" id="staffServiceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Services
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="staffServiceDropdown">
                                <li><a class="dropdown-item" href="{{ route('services.index') }}">Manage Services</a></li>
                                <li><a class="dropdown-item" href="{{ route('attendance.index') }}">Service Attendance</a></li>
                            </ul>
                        </li>
                        {{-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $is('communications*','templates*') ? 'active' : '' }}"
                               href="#" id="staffCommDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Communications
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="staffCommDropdown">
                                <li><a class="dropdown-item" href="{{ route('communications.create') }}">Compose</a></li>
                                <li><a class="dropdown-item" href="{{ route('communications.index') }}">All Messages</a></li>
                                <li><a class="dropdown-item" href="{{ route('templates.index') }}">Templates</a></li>
                            </ul>
                        </li> --}}

                    {{-- ================= ZONAL LEADER ================= --}}
                    @elseif($role === 'Zonal Leader')
                        <li class="nav-item">
                            <a href="{{ route('zone.dashboard') }}"
                               class="nav-link {{ $is('zone.dashboard') ? 'active' : '' }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.homecells.index') }}"
                               class="nav-link {{ $is('reports.homecells.*') ? 'active' : '' }}">Homecells</a>
                        </li
                        <li class="nav-item">
                            <a href="{{ route('reports.dashboard') }}"
                               class="nav-link {{ $is('reports.dashboard') ? 'active' : '' }}">   </a>
                        </li>
                       
                    {{-- ================= HOMECELL LEADER ================= --}}
                    @elseif($role === 'Homecell Leader')
                        <li class="nav-item">
                            <a href="{{ route('homecell.dashboard') }}"
                               class="nav-link {{ $is('homecell.dashboard') ? 'active' : '' }}">Dashboard</a>
                        </li>

                    {{-- ================= TEAM LEADER ================= --}}
                    @elseif($role === 'Team Leader')
                        <li class="nav-item">
                            <a href="{{ route('team.dashboard') }}"
                               class="nav-link {{ $is('team.dashboard') ? 'active' : '' }}">Dashboard</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $is('assignments*') ? 'active' : '' }}"
                               href="#" id="leaderAssignDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Team Assignments
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="leaderAssignDropdown">
                                <li><a class="dropdown-item" href="{{ route('assignments.index') }}">All (my team)</a></li>
                                <li><a class="dropdown-item" href="{{ route('assignments.standby') }}">Standby Pool</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('assignments.reassign') }}">Reassign (within team)</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('assignments.index') }}?mine=1"
                               class="nav-link {{ request()->routeIs('assignments.index') && request('mine') ? 'active' : '' }}">
                                My Assignments
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('followups.my') }}"
                               class="nav-link {{ $is('followups.my') ? 'active' : '' }}">My Follow-ups</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('reports.followups') }}"
                               class="nav-link {{ $is('reports.followups') ? 'active' : '' }}">Reports</a>
                        </li>

                    {{-- ================= TEAM MEMBER ================= --}}
                    @elseif($role === 'Team Member')
                        <li class="nav-item">
                            <a href="{{ route('team-member.dashboard') }}"
                               class="nav-link {{ $is('team-member.dashboard') ? 'active' : '' }}">Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('assignments.index') }}?mine=1"
                               class="nav-link {{ request()->routeIs('assignments.index') && request('mine') ? 'active' : '' }}">
                                My Assignments
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('followups.my') }}"
                               class="nav-link {{ $is('followups.my') ? 'active' : '' }}">My Follow-ups</a>
                        </li>
                    @endif
                @endauth
            </ul>

            {{-- RIGHT: auth actions (always right aligned) --}}
            <ul class="navbar-nav align-items-lg-center">
                @auth
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-link nav-link text-danger" type="submit">Logout</button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main class="container pb-5">
    {{-- Global flash + validation --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
