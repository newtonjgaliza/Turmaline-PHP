<!-- Header que será o mesmo em todas as páginas -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    .nav-link {
        padding: 8px 16px;
        border-radius: 4px;
        transition: all 0.3s ease;
        color: #000 !important;
        text-decoration: none;
    }
    .nav-link.active {
        background-color: #FF6B00;
        color: white !important;
    }
    .nav-link.active i {
        color: white !important;
    }
</style>

<header class="bg-white border-bottom p-2" style="position: relative; z-index: 1000; box-shadow: 0 2px 50px rgba(0,0,0,0.1);">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center" style="width: 45%;">
                <img src="{{ asset('images/pbsoft.jpeg') }}" alt="PBSoft Logo" style="height: 40px;">
            </div>
            <nav class="d-flex align-items-center" style="width: 70%;">
                <a href="/" class="nav-link mx-1 {{ request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-map"></i> Mapa
                </a>
                <a href="/avaliacao" class="nav-link mx-1 {{ request()->is('avaliacao*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Avaliação
                </a>
                <a href="/ranking" class="nav-link mx-1 {{ request()->is('ranking*') ? 'active' : '' }}">
                    <i class="bi bi-file-check"></i> Ranking
                </a>
            </nav>
            <nav class="d-flex align-items-center" style="width: 8%;">
                <a href="/admin" class="nav-link mx-1">
                    <i class="bi bi-file-person"></i> Login
                </a>
            </nav>
        </div>
    </div>
</header>




<!--
<header class="bg-white border-bottom p-2" style="position: relative; z-index: 1000; box-shadow: 0 2px 50px rgba(0,0,0,0.1);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/pbsoft.jpeg') }}" alt="PBSoft Logo" style="height: 40px;">
            </div>
            <nav class="d-flex align-items-center">
            <i class="bi bi-map"></i><a href="/" class="text-dark mx-2 text-decoration-none">Mapa</a>
            <i class="bi bi-file-earmark-bar-graph"></i><a href="/sobre" class="text-dark mx-2 text-decoration-none">Avaliação</a>
            <i class="bi bi-file-check"></i><a href="/contato" class="text-dark mx-2 text-decoration-none">Ranking</a>
            </nav>
        </div>
    </div>
</header>
