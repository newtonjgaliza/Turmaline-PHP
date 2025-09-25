<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa da Paraíba</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet Search CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-search@3.0.2/dist/leaflet-search.min.css" />
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        #map {
            height: 100vh;
            width: 100vw;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Estilo da barra de busca */
        .search-container {
            position: absolute;
            top: 15px;
            left: 50px;
            z-index: 1000;
            width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .search-input-container {
            position: relative;
            display: flex;
            align-items: center;
            padding: 8px 12px;
        }

        .search-input {
            flex: 1;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color:rgb(71, 77, 85);
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 20px;
            color: #777;
        }

        .clear-icon {
            position: absolute;
            right: 20px;
            color: #777;
            cursor: pointer;
            display: none;
        }

        .clear-icon:hover {
            color: #333;
        }

        .search-results {
            display: none;
            max-height: 300px;
            overflow-y: auto;
            border-top: 1px solid #eee;
            margin: 0;
            padding: 0;
            list-style: none;
            background: white;
            border-radius: 0 0 8px 8px;
        }

        .search-results.visible {
            display: block;
        }

        .search-result-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-result-item:hover {
            background-color: #f5f5f5;
        }

        .search-result-item:not(:last-child) {
            border-bottom: 1px solid #f0f0f0;
        }
    </style>
</head>
<body>
    <div id="map"></div>
    
    <!-- Barra de busca -->
    <div class="search-container">
        <div class="search-input-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="search-input" class="search-input" placeholder="Buscar município..." autocomplete="off">
            <i class="fas fa-times clear-icon" id="clear-search"></i>
        </div>
        <ul class="search-results" id="search-results"></ul>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- jQuery para facilitar as requisições AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Inicializa o mapa
        const map = L.map('map', {
            zoomControl: true,
            attributionControl: false
        });
        
        // Variáveis globais
        let geoJsonLayer;
        let searchTimeout;

        // Adiciona a camada do OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(map);

        // Função para buscar municípios
        function buscarMunicipios(termo) {
            if (!termo || termo.length < 2) {
                $('#search-results').removeClass('visible').empty();
                return;
            }

            // Cancela a busca anterior se ainda estiver em andamento
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Adiciona um pequeno atraso para evitar múltiplas requisições rápidas
            searchTimeout = setTimeout(() => {
                $.get('{{ route("municipios.buscar") }}', { q: termo })
                    .done(function(data) {
                        const results = $('#search-results');
                        results.empty();
                        
                        if (data.length === 0) {
                            results.append('<li class="search-result-item">Nenhum município encontrado</li>');
                        } else {
                            data.forEach(function(municipio) {
                                results.append(`<li class="search-result-item" data-nome="${municipio.nome}">${municipio.nome}</li>`);
                            });
                        }
                        
                        results.addClass('visible');
                    })
                    .fail(function() {
                        console.error('Erro ao buscar municípios');
                    });
            }, 300);
        }

        // Função para destacar o município no mapa
        function destacarMunicipio(nome) {
            if (!geoJsonLayer) return;

            // Encontra e destaca o município selecionado
            geoJsonLayer.eachLayer(function(layer) {
                const nomeMunicipio = layer.feature.properties.name; // Note que usamos 'name' em vez de 'nome'
                
                // Compara ignorando acentos e case
                const normalizar = (str) => {
                    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                };
                
                if (normalizar(nomeMunicipio) === normalizar(nome)) {
                    // Centraliza o mapa no município
                    map.fitBounds(layer.getBounds(), {
                        padding: [50, 50],
                        maxZoom: 12
                    });
                    
                    // Destaca o município
                    layer.setStyle({
                        weight: 3,
                        color: '#f5a142',
                        fillOpacity: 0.7
                    });
                    layer.bringToFront();
                    
                    // Abre o popup
                    if (layer.getPopup()) {
                        layer.openPopup();
                    } else {
                        layer.bindPopup(`<strong>${nomeMunicipio}</strong>`).openPopup();
                    }
                } else {
                    // Reseta o estilo dos outros municípios
                    geoJsonLayer.resetStyle(layer);
                }
            });
        }

        // Carrega o GeoJSON e adiciona ao mapa
        fetch('/paraiba.geojson')
            .then(response => response.json())
            .then(data => {
                // Adiciona os municípios ao mapa com estilo básico
                geoJsonLayer = L.geoJSON(data, {
                    style: {
                        color: '#333',
                        weight: 1,
                        fillColor: '#808080',
                        fillOpacity: 0.5
                    },
                    onEachFeature: function(feature, layer) {
                        // Adiciona popup com o nome do município
                        if (feature.properties && feature.properties.name) {
                            layer.bindPopup(`<strong>${feature.properties.name}</strong>`);
                        }
                        
                        // Adiciona eventos de mouse
                        layer.on({
                            mouseover: function(e) {
                                const layer = e.target;
                                layer.setStyle({
                                    weight: 3,
                                    color: '#f5a142',
                                    fillOpacity: 0.7
                                });
                                layer.bringToFront();
                            },
                            mouseout: function(e) {
                                geoJsonLayer.resetStyle(e.target);
                            },
                            click: function(e) {
                                map.fitBounds(e.target.getBounds(), {
                                    padding: [50, 50],
                                    maxZoom: 12
                                });
                            }
                        });
                    }
                }).addTo(map);

                // Ajusta o zoom para mostrar toda a Paraíba
                map.fitBounds(geoJsonLayer.getBounds(), {
                    padding: [20, 20]
                });
            })
            .catch(error => console.error('Erro ao carregar GeoJSON:', error));

        // Eventos da barra de busca
        $(document).ready(function() {
            const searchInput = $('#search-input');
            const clearSearch = $('#clear-search');
            const searchResults = $('#search-results');

            // Mostra/oculta o botão de limpar ao digitar e busca municípios
            searchInput.on('input', function() {
                const termo = $(this).val().trim();
                clearSearch.toggle(termo.length > 0);
                buscarMunicipios(termo);
            });

            // Limpa o campo de busca e volta o mapa ao estado original
            clearSearch.on('click', function() {
                searchInput.val('').focus();
                $(this).hide();
                searchResults.removeClass('visible').empty();
                
                // Remove o destaque do município selecionado
                if (geoJsonLayer) {
                    // Reseta o estilo de todas as camadas
                    geoJsonLayer.eachLayer(function(layer) {
                        geoJsonLayer.resetStyle(layer);
                    });
                    
                    // Volta o zoom para mostrar toda a Paraíba
                    map.fitBounds(geoJsonLayer.getBounds(), {
                        padding: [20, 20]
                    });
                }
            });
            
            // Seleciona um município da lista de resultados
            searchResults.on('click', '.search-result-item', function() {
                const nome = $(this).data('nome');
                searchInput.val(nome);
                searchResults.removeClass('visible');
                destacarMunicipio(nome);
            });

            // Fecha os resultados ao clicar fora
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-container').length) {
                    searchResults.removeClass('visible');
                }
            });
            // Fechar com a tecla ESC
            $(document).on('keyup', function(e) {
                if (e.key === 'Escape') {
                    searchResults.removeClass('visible');
                }
            });
        });
    </script>
</body>
</html>