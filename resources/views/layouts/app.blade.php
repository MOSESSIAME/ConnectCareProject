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

    {{-- Apply saved theme ASAP (prevents flash) --}}
    <script>
      (function () {
        try {
          var saved = localStorage.getItem('theme');
          if (saved === 'dark') document.documentElement.classList.add('dark-mode');
        } catch (e) {}
      })();
    </script>

    <style>
        /* --- Base (light) look stays as-is --- */
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; letter-spacing: .3px; }
        .nav-link.active { font-weight: 600; color: #ffc107 !important; }
        .dropdown-menu a { color: #000000 !important; }

        .navbar-nav .nav-link { padding: .5rem .75rem; }
        .navbar-nav.gapped > .nav-item { margin-left: .25rem; margin-right: .25rem; }
        .navbar-dark .dropdown-menu { min-width: 16rem; }

        @media (min-width: 992px) {
            .nav-sep {
                width: 1px; height: 24px; background: hsla(238, 43%, 25%, 0.251);
                margin: 0 .5rem;
            }
        }

        /* =========================
           DARK MODE THEME
           ========================= */
        .dark-mode,
        .dark-mode body {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }

        .dark-mode .navbar {
            background-color: #0f0f11 !important;
        }

        .dark-mode .dropdown-menu {
            background-color: #1c1c1f !important;
            border-color: #2b2b2f !important;
        }
        .dark-mode .dropdown-item { color: #ddd !important; }
        .dark-mode .dropdown-item:hover { background-color: #2a2a2e !important; }

        .dark-mode .card,
        .dark-mode .table,
        .dark-mode .form-control,
        .dark-mode .form-select,
        .dark-mode .alert,
        .dark-mode .modal-content {
            background-color: #1c1c1f !important;
            color: #e0e0e0 !important;
            border-color: #2b2b2f !important;
        }

        .dark-mode .table thead,
        .dark-mode .table-dark {
            background-color: #232327 !important;
            color: #e0e0e0 !important;
        }

        .dark-mode .btn-outline-secondary {
            color: #e0e0e0 !important;
            border-color: #6c757d !important;
        }
        .dark-mode .btn-outline-secondary:hover {
            background-color: #2b2b2f !important;
        }

        .dark-mode .btn-link.nav-link { color: #ff6b6b !important; } /* logout link */
        .dark-mode a { color: #66b3ff; }

        .dark-mode .alert-success {
            background-color: #0f5132 !important;
            color: #d1e7dd !important;
            border-color: #0f5132 !important;
        }
        .dark-mode .alert-danger {
            background-color: #58151c !important;
            color: #f1aeb5 !important;
            border-color: #58151c !important;
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
            {{-- LEFT: primary navigation --}}
            <ul class="navbar-nav gapped me-auto align-items-lg-center">
                @auth
                    @php
                        $role = auth()->user()->role->name ?? '';
                        $is = fn (...$patterns) => request()->routeIs(...$patterns) || request()->is(...$patterns);

                        // Resolve a valid target for "My Follow-ups" regardless of which route set is active
                        $followupsRouteName = \Illuminate\Support\Facades\Route::has('followups.my')
                            ? 'followups.my'
                            : (\Illuminate\Support\Facades\Route::has('followups.index') ? 'followups.index' : null);
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

                    {{-- ================= ZONAL LEADER ================= --}}
                    @elseif($role === 'Zonal Leader')
                        <li class="nav-item">
                            <a href="{{ route('zone.dashboard') }}"
                               class="nav-link {{ $is('zone.dashboard') ? 'active' : '' }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.homecells.index') }}"
                               class="nav-link {{ $is('reports.homecells.*') ? 'active' : '' }}">Homecells</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.dashboard') }}"
                               class="nav-link {{ $is('reports.dashboard') ? 'active' : '' }}">Reports</a>
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

                        @if($followupsRouteName)
                            <li class="nav-item">
                                <a href="{{ route($followupsRouteName) }}"
                                   class="nav-link {{ $is($followupsRouteName) ? 'active' : '' }}">My Follow-ups</a>
                            </li>
                        @endif

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

                        @if($followupsRouteName)
                            <li class="nav-item">
                                <a href="{{ route($followupsRouteName) }}"
                                   class="nav-link {{ $is($followupsRouteName) ? 'active' : '' }}">My Follow-ups</a>
                            </li>
                        @endif
                    @endif
                @endauth
            </ul>

            {{-- RIGHT: auth actions + theme toggle --}}
            <ul class="navbar-nav align-items-lg-center">
                {{-- Theme Toggle --}}
                <li class="nav-item me-2">
                    <button id="themeToggle" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-sun" id="themeIcon"></i>
                    </button>
                </li>

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

{{-- Theme toggle logic --}}
<script>
  (function () {
    const html  = document.documentElement;
    const btn   = document.getElementById('themeToggle');
    const icon  = document.getElementById('themeIcon');

    function setIcon() {
      if (html.classList.contains('dark-mode')) {
        icon.classList.remove('bi-sun');
        icon.classList.add('bi-moon-stars');
      } else {
        icon.classList.remove('bi-moon-stars');
        icon.classList.add('bi-sun');
      }
    }

    setIcon();

    btn?.addEventListener('click', function () {
      html.classList.toggle('dark-mode');
      try {
        localStorage.setItem('theme', html.classList.contains('dark-mode') ? 'dark' : 'light');
      } catch (e) {}
      setIcon();
    });
  })();
</script>

@stack('scripts')
</body>
</html>
