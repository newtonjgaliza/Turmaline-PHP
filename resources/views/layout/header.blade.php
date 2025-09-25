<!-- Header que será o mesmo em todas as páginas -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<header class="bg-white border-bottom p-2" style="position: relative; z-index: 1000; box-shadow: 0 2px 50px rgba(0,0,0,0.1);">
    <div class="container-fluid"> <!-- troquei container por container-fluid -->
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center" style="width: 45%;">
                <img src="{{ asset('images/pbsoft.jpeg') }}" alt="PBSoft Logo" style="height: 40px;">
            </div>
            <nav class="d-flex align-items-center" style="width: 55%;">
                <i class="bi bi-map"></i><a href="/" class="text-dark mx-2 text-decoration-none">Mapa</a>
                <i class="bi bi-file-earmark-bar-graph"></i><a href="/sobre" class="text-dark mx-2 text-decoration-none">Avaliação</a>
                <i class="bi bi-file-check"></i><a href="/contato" class="text-dark mx-2 text-decoration-none">Ranking</a>
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
