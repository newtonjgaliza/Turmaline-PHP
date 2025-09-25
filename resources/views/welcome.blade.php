<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa da Paraíba</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

        body {
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        #map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .leaflet-top {
            top: 76px;
        }

        /* Estilo da barra de busca */
        .search-container {
            position: absolute;
            top: 90px;
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
            padding: 8px 8px;
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
    @include('layout.header')
    
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
            attributionControl: false,
            dragging: true,  // Habilita o arrasto por padrão
        });
        
        // Inicializa o controle de arrasto
        map.dragging.enable();
        
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

        /**
         * Seleciona um município no mapa (usado tanto para clique quanto busca)
         * @param {string} nome - Nome do município a ser selecionado
         * @returns {boolean} Verdadeiro se o município foi encontrado e selecionado
         */
        function selecionarMunicipio(nome) {
            if (!geoJsonLayer) return false;

            let encontrado = false;
            
            // Encontra o município pelo nome
            geoJsonLayer.eachLayer(function(layer) {
                const nomeMunicipio = layer.feature?.properties?.name;
                if (!nomeMunicipio) return;
                
                // Compara ignorando acentos e case
                const normalizar = (str) => {
                    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                };
                
                if (normalizar(nomeMunicipio) === normalizar(nome)) {
                    encontrado = true;
                    
                    // Se clicar no mesmo município que já está selecionado, desmarca
                    if (layer === selectedLayer) {
                        deselectLayer();
                        return;
                    }
                    
                    // Remove qualquer popup de hover ativo
                    if (hoverPopup) {
                        map.removeLayer(hoverPopup);
                        hoverPopup = null;
                    }
                    
                    // Se houver um município previamente selecionado, desmarca ele
                    if (selectedLayer) {
                        geoJsonLayer.resetStyle(selectedLayer);
                        if (selectedLayer._popup) {
                            map.removeLayer(selectedLayer._popup);
                            selectedLayer._popup = null;
                        }
                    }
                    
                    // Define o novo município como selecionado
                    selectedLayer = layer;
                    
                    // Aplica o estilo visual de seleção
                    layer.setStyle({
                        weight: 3,
                        color: '#f5a142',
                        fillOpacity: 0.7
                    });
                    layer.bringToFront();
                    
                    // Cria e exibe o popup de seleção
                    if (layer._municipioName) {
                        createSelectionPopup(layer, layer._municipioName);
                    }
                    
                    // Centraliza o mapa no município
                    map.fitBounds(layer.getBounds(), {
                        padding: [50, 50],
                        maxZoom: 12
                    });
                    
                    // Desativa o arrasto do mapa
                    if (map.dragging.enabled()) {
                        map.dragging.disable();
                    }
                }
            });
            
            return encontrado;
        }
        
        // Função de compatibilidade para manter o código existente
        function destacarMunicipio(nome) {
            return selecionarMunicipio(nome);
        }

        // Variáveis de controle do estado dos municípios
        let selectedLayer = null;  // Armazena a camada do município atualmente selecionado (por clique ou busca)
        let hoverPopup = null;     // Referência ao popup de hover ativo no momento
        
        /**
         * Desmarca o município atualmente selecionado (se houver)
         * Remove o destaque visual e fecha o popup de seleção
         */
        function deselectLayer() {
            if (selectedLayer) {
                // Remove o destaque visual do município
                geoJsonLayer.resetStyle(selectedLayer);
                
                // Fecha o popup de seleção se existir
                if (selectedLayer._popup) {
                    map.removeLayer(selectedLayer._popup);
                    selectedLayer._popup = null;
                }
                
                // Reativa o arrasto do mapa
                map.dragging.enable();
                
                // Limpa a referência do município selecionado
                selectedLayer = null;
            }
        }
        
        /**
         * Cria e exibe um popup de hover sobre um município
         * @param {L.Layer} layer - A camada do município
         * @param {string} content - Conteúdo a ser exibido no popup
         */
        function createHoverPopup(layer, content) {
            // Remove qualquer popup de hover existente
            if (hoverPopup) {
                map.removeLayer(hoverPopup);
                hoverPopup = null;
            }
            
            // Cria um novo popup de hover (sem botão de fechar)
            hoverPopup = L.popup({
                closeButton: false,
                closeOnClick: false,
                className: 'municipality-hover-popup',
                offset: [0, 0]
            })
            .setLatLng(layer.getBounds().getCenter())
            .setContent(`<div class="hover-popup">${content}</div>`)
            .openOn(map);
            
            return hoverPopup;
        }
        
        /**
         * Cria e exibe um popup de seleção para um município
         * @param {L.Layer} layer - A camada do município
         * @param {string} content - Conteúdo a ser exibido no popup
         */
        function createSelectionPopup(layer, content) {
            // Remove qualquer popup de seleção existente
            if (layer._popup) {
                map.removeLayer(layer._popup);
            }
            
            // Cria um novo popup de seleção (com botão de fechar)
            const popup = L.popup({
                closeButton: true,
                className: 'municipality-selected-popup',
                autoClose: false,
                closeOnClick: false
            })
            .setLatLng(layer.getBounds().getCenter())
            .setContent(`<strong>${content}</strong>`)
            .openOn(map);
            
            // Armazena referência do popup na camada
            layer._popup = popup;
            
            // Quando o popup for fechado, desmarca o município
            popup.on('remove', function() {
                if (layer === selectedLayer) {
                    deselectLayer();
                }
            });
            
            return popup;
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
                            layer.bindPopup(`<strong>${feature.properties.name}</strong>`, {
                                closeButton: true,
                                autoClose: false,
                                closeOnClick: false,
                                className: 'municipality-popup'
                            });

                            // Armazena o nome do município na camada para referência
                            layer._municipioName = feature.properties.name;
                        }
                        
                        // Adiciona eventos de mouse para interação com os municípios
                        layer.on({
                            // Evento quando o mouse entra em um município
                            mouseover: function(e) {
                                const layer = e.target;
                                
                                // Apenas mostra o popup de hover se nenhum município estiver selecionado
                                if (!selectedLayer) {
                                    // Aplica estilo visual de hover
                                    layer.setStyle({
                                        weight: 3,
                                        color: '#f5a142',
                                        fillOpacity: 0.7
                                    });
                                    layer.bringToFront();
                                    
                                    // Exibe o popup de hover com o nome do município
                                    if (layer._municipioName) {
                                        createHoverPopup(layer, layer._municipioName);
                                    }
                                }
                            },
                            
                            // Evento quando o mouse sai de um município
                            mouseout: function(e) {
                                const layer = e.target;
                                
                                // Apenas remove o hover se não for o município selecionado
                                if (layer !== selectedLayer) {
                                    // Remove o estilo de hover
                                    geoJsonLayer.resetStyle(layer);
                                    
                                    // Fecha o popup de hover se existir
                                    if (hoverPopup) {
                                        map.removeLayer(hoverPopup);
                                        hoverPopup = null;
                                    }
                                }
                            },
                            
                            // Evento de clique em um município
                            click: function(e) {
                                const layer = e.target;
                                
                                // Remove qualquer popup de hover ativo
                                if (hoverPopup) {
                                    map.removeLayer(hoverPopup);
                                    hoverPopup = null;
                                }
                                
                                // Usa a função unificada para seleção
                                selecionarMunicipio(layer._municipioName);
                                
                                // Previne comportamento padrão
                                e.originalEvent.preventDefault();
                                e.originalEvent.stopPropagation();
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