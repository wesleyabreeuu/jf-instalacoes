<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'JF Instalações')</title>

    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#f59e0b">

    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">


    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    {{-- AdminLTE CSS --}}
    @vite(['resources/adminlte/dist/css/adminlte.min.css'])

    {{-- Tema JF (inline para ficar simples agora) --}}
    <style>
        :root{
            --jf-blue:#071827;
            --jf-yellow:#F4C21A;
        }

        /* Navbar */
        .main-header.navbar{
            background-color: var(--jf-blue) !important;
            border-bottom: 1px solid rgba(255,255,255,.08) !important;
        }
        .main-header.navbar .nav-link,
        .main-header.navbar .btn.btn-link.nav-link{
            color: rgba(255,255,255,.90) !important;
        }
        .main-header.navbar .nav-link:hover,
        .main-header.navbar .btn.btn-link.nav-link:hover{
            color: var(--jf-yellow) !important;
        }

        /* Sidebar */
        .main-sidebar{
            background-color: var(--jf-blue) !important;
        }
        .brand-link{
            background-color: var(--jf-blue) !important;
            border-bottom: 1px solid rgba(255,255,255,.08) !important;
        }
        .brand-link .brand-text{
            color: #fff !important;
            font-weight: 700 !important;
            letter-spacing: .3px;
        }

        .sidebar .user-panel .info a{
            color:#fff !important;
            font-weight: 600 !important;
        }
        .sidebar .user-panel small{
            color: rgba(255,255,255,.65) !important;
        }

        /* Menu items */
        .nav-sidebar .nav-link{
            color: rgba(255,255,255,.85) !important;
        }
        .nav-sidebar .nav-link:hover{
            background: rgba(255,255,255,.06) !important;
            color: #fff !important;
        }
        .nav-sidebar .nav-link.active{
            background: rgba(244,194,26,.18) !important;
            color: #fff !important;
            border-left: 3px solid var(--jf-yellow);
        }
        .nav-sidebar .nav-icon{
            color: rgba(255,255,255,.75) !important;
        }
        .nav-sidebar .nav-link.active .nav-icon{
            color: var(--jf-yellow) !important;
        }

        .nav-header{
            color: rgba(255,255,255,.55) !important;
        }

        /* Content header */
        .content-wrapper{
            background: #f5f7fb !important;
        }
        .content-header h1{
            font-weight: 800 !important;
            color: #071827 !important;
        }

        /* Buttons */
        .btn-primary{
            background-color: var(--jf-yellow) !important;
            border-color: var(--jf-yellow) !important;
            color: var(--jf-blue) !important;
            font-weight: 800 !important;
        }
        .btn-primary:hover{
            filter: brightness(.95);
        }
    </style>
</head>

<script>
  if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
      navigator.serviceWorker.register("/sw.js");
    });
  }
</script>


<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    {{-- Navbar --}}
    <nav class="main-header navbar navbar-expand">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link" style="text-decoration:none;">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    {{-- Sidebar --}}
    <aside class="main-sidebar elevation-4">
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.home' : 'app.home') }}" class="brand-link d-flex align-items-center">
    <img src="{{ asset('img/jf-logo.jpeg') }}"
         alt="JF"
         class="brand-image img-circle elevation-2"
         style="opacity: .9; background: #071827; padding: 2px;">
    <span class="brand-text font-weight-bold">JF Instalações</span>
</a>


        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
    <div class="image">
        <div class="img-circle elevation-2 d-flex align-items-center justify-content-center"
             style="width:34px;height:34px;background:rgba(244,194,26,.18);border:1px solid rgba(244,194,26,.45);">
            <i class="fas fa-user" style="color:#F4C21A;"></i>
        </div>
    </div>

    <div class="info">
        <a href="#" class="d-block" style="line-height: 1.1;">
            {{ auth()->user()->name }}
        </a>

        @php $isAdmin = auth()->user()->isAdmin(); @endphp

        <span class="badge"
              style="
                background: {{ $isAdmin ? '#F4C21A' : 'rgba(255,255,255,.15)' }};
                color: {{ $isAdmin ? '#071827' : '#fff' }};
                font-weight: 800;
                border: 1px solid rgba(255,255,255,.15);
              ">
            {{ $isAdmin ? 'ADMINISTRADOR' : 'COLABORADOR' }}
        </span>
    </div>
</div>


            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" role="menu">

    @if(auth()->user()->isAdmin())
        <li class="nav-header">ADMIN</li>

        {{-- 1 - Painel --}}
        <li class="nav-item">
            <a href="{{ route('admin.home') }}"
               class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Painel</p>
            </a>
        </li>

        {{-- 2 - Usuários --}}
        <li class="nav-item">
            <a href="{{ route('admin.usuarios.index') }}"
               class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Usuários</p>
            </a>
        </li>

        {{-- 3 - Clientes --}}
        <li class="nav-item">
            <a href="{{ route('admin.clientes.index') }}"
               class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-tag"></i>
                <p>Clientes</p>
            </a>
        </li>

        {{-- 4 - Materiais (corrigido: sem duplicar) --}}
        <li class="nav-item">
            <a href="{{ route('admin.materiais.index') }}"
               class="nav-link {{ request()->routeIs('admin.materiais.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-boxes"></i>
                <p>Materiais</p>
            </a>
        </li>

        {{-- 5 - Serviços --}}
        <li class="nav-item">
            <a href="{{ route('admin.servicos.index') }}"
               class="nav-link {{ request()->routeIs('admin.servicos.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tools"></i>
                <p>Serviços</p>
            </a>
        </li>

        {{-- 6 - Relatórios --}}
        <li class="nav-item">
            <a href="{{ route('admin.relatorios.index') }}"
                class="nav-link {{ request()->routeIs('admin.relatorios.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>Relatórios</p>
                </a>


        </li>
    @else
        <li class="nav-header">COLABORADOR</li>

        <li class="nav-item">
            <a href="{{ route('app.servicos.index') }}"
               class="nav-link {{ request()->routeIs('app.home') || request()->routeIs('app.servicos.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tools"></i>
                <p>Serviços</p>
            </a>
        </li>
    @endif

</ul>

            </nav>
        </div>
    </aside>

    {{-- Conteúdo --}}
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">@yield('page-title', 'Página')</h1>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid pb-3">
                @yield('content')
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong>JF Instalações</strong> © {{ date('Y') }}
    </footer>

</div>

{{-- AdminLTE JS --}}
@vite([
    'resources/adminlte/plugins/jquery/jquery.min.js',
    'resources/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js',
    'resources/adminlte/dist/js/adminlte.min.js',
])
</body>
</html>
