/**
 * Portfolio Modal - Versión Mejorada con Carrusel
 * Compatible con navegadores antiguos, sin sintaxis moderna
 */

(function($) {
    'use strict';
    
    // Confirmación de carga
    console.log('=================================================');
    console.log('PORTFOLIO PLUGIN JAVASCRIPT CARGADO CORRECTAMENTE');
    console.log('Version: Galería con Scroll Vertical');
    console.log('=================================================');
    
    // Variables globales
    var currentProject = null;
    var modalCreated = false;
    
    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        console.log('Portfolio: Iniciando...');
        initPortfolio();
    });
    
    function initPortfolio() {
        createModal();
        bindEvents();
        console.log('Portfolio: Listo');
    }
    
    function createModal() {
        // Eliminar modal existente
        $('#pf-modal').remove();
        
        // Crear modal con HTML de 2 columnas
        var html = '';
        html += '<div id="pf-modal">';
        html += '  <div id="pf-modal-container">';
        
        // Header solo con botón cerrar
        html += '    <div class="pf-modal-header">';
        html += '      <button id="pf-close" class="pf-close-btn">&times;</button>';
        html += '    </div>';
        
        // Body con 2 columnas
        html += '    <div class="pf-modal-body">';
        
        // COLUMNA IZQUIERDA: Galería con scroll
        html += '      <div class="pf-modal-gallery-column">';
        html += '        <div id="pf-gallery-wrap" class="pf-gallery-container"></div>';
        html += '        <div id="pf-single-image-wrap" class="pf-single-image-container" style="display:none;">';
        html += '          <img id="pf-single-image" src="" alt="" class="pf-single-image">';
        html += '        </div>';
        html += '      </div>';
        
        // COLUMNA DERECHA: Información
        html += '      <div class="pf-modal-info-column">';
        
        // Título
        html += '        <div class="pf-info-section">';
        html += '          <h2 id="pf-title" class="pf-modal-title">Cargando...</h2>';
        html += '        </div>';
        
        // Meta info (categoría, año, vistas, likes)
        html += '        <div class="pf-info-section">';
        html += '          <div id="pf-meta" class="pf-modal-meta">';
        html += '            <span id="pf-cat-badge" class="pf-category-badge">Categoría</span>';
        html += '            <span id="pf-year" class="pf-stat-item" style="display:none;"><span class="pf-stat-icon">📅</span><span id="pf-year-value"></span></span>';
        html += '            <span class="pf-stat-item"><span class="pf-stat-icon">👁️</span><span id="pf-views">0</span></span>';
        html += '            <span class="pf-stat-item"><span class="pf-stat-icon">❤️</span><span id="pf-likes">0</span></span>';
        html += '          </div>';
        html += '        </div>';
        
        // Descripción
        html += '        <div class="pf-info-section">';
        html += '          <h3>Descripción</h3>';
        html += '          <div id="pf-desc" class="pf-modal-description">Cargando...</div>';
        html += '        </div>';
        
        // Botón externo
        html += '        <div id="pf-external-wrap" class="pf-external-container" style="display:none;">';
        html += '          <a id="pf-external-link" href="#" target="_blank" rel="noopener noreferrer" class="pf-external-button">';
        html += '            <span>Ver Proyecto Completo</span>';
        html += '            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>';
        html += '          </a>';
        html += '        </div>';
        
        html += '      </div>'; // fin columna derecha
        
        html += '    </div>'; // fin body
        html += '  </div>'; // fin container
        html += '</div>'; // fin modal
        
        $('body').append(html);
        modalCreated = true;
        console.log('Modal con 2 columnas creado');
    }
    
    function bindEvents() {
        // Cerrar modal
        $(document).on('click', '#pf-close, #pf-modal', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Hover en botón cerrar
        $(document).on('mouseenter', '#pf-close', function() {
            $(this).css('color', '#333');
        });
        $(document).on('mouseleave', '#pf-close', function() {
            $(this).css('color', '#999');
        });
        
        // Hover en botón externo (usa colores personalizados)
        $(document).on('mouseenter', '.pf-external-button', function() {
            var hoverBg = $(this).data('hover-bg') || '#1976D2';
            $(this).css('background', hoverBg);
        });
        $(document).on('mouseleave', '.pf-external-button', function() {
            var normalBg = $(this).data('normal-bg') || '#2196F3';
            $(this).css('background', normalBg);
        });
        
        
        // Botón ver proyecto
        $(document).on('click', '.portfolio-view-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var projectId = $(this).data('project-id');
            console.log('Click en proyecto:', projectId);
            
            if (projectId) {
                openModal(projectId);
            }
        });
        
        // ESC para cerrar
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                closeModal();
            }
        });
        
        console.log('Eventos bindeados');
    }
    
    function openModal(projectId) {
        console.log('Abriendo modal:', projectId);
        
        if (!modalCreated) {
            createModal();
        }
        
        // Mostrar modal
        $('#pf-modal').fadeIn(300);
        $('#pf-title').text('Cargando...');
        $('#pf-desc').text('Cargando información...');
        
        // Verificar datos para AJAX
        if (typeof portfolio_frontend === 'undefined') {
            console.error('portfolio_frontend no disponible');
            showError('Error de configuración');
            return;
        }
        
        console.log('AJAX URL:', portfolio_frontend.ajax_url);
        
        // Llamada AJAX
        $.ajax({
            url: portfolio_frontend.ajax_url,
            type: 'POST',
            data: {
                action: 'portfolio_get_project',
                project_id: projectId,
                nonce: portfolio_frontend.nonce
            },
            timeout: 15000,
            success: function(response) {
                console.log('AJAX exitoso:', response);
                console.log('Datos de galería recibidos:', response.data ? response.data.gallery : 'No hay datos');
                
                if (response && response.success && response.data) {
                    populateModal(response.data);
                } else {
                    console.error('Error en respuesta:', response);
                    showError('Error al cargar proyecto');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                showError('Error de conexión');
            }
        });
    }
    
    function populateModal(data) {
        console.log('Poblando modal:', data);
        
        try {
            // Título
            $('.pf-modal-title').text(data.title || 'Sin título');
            
            // Meta info
            $('.pf-category-badge').text(data.category_name || 'Sin categoría');
            // Guardar el color de la categoría para usarlo si no hay personalización
            $('.pf-category-badge').data('default-color', data.category_color || '#2196F3');
            
            // Mostrar año si existe
            if (data.project_year) {
                $('#pf-year-value').text(data.project_year);
                $('#pf-year').show();
            } else {
                $('#pf-year').hide();
            }
            
            $('#pf-views').text(data.views || 0);
            $('#pf-likes').text(data.likes || 0);
            
            // Descripción
            $('.pf-modal-description').html(data.description || 'Sin descripción');
            
            // Videos (YouTube y Vimeo)
            initVideos(data);
            
            // Galería o imagen única
            if (data.gallery && data.gallery.length > 0) {
                console.log('=== INICIANDO GALERÍA ===');
                console.log('Datos de galería:', data.gallery);
                console.log('Título:', data.title);
                console.log('Inicializando galería con', data.gallery.length, 'elementos');
                initGallery(data.gallery, data.title);
                console.log('Mostrando contenedor de galería');
                $('#pf-gallery-wrap').css('display', 'flex');
                $('#pf-single-image-wrap').css('display', 'none');
                console.log('Estado después de mostrar:', {
                    galleryVisible: $('#pf-gallery-wrap').is(':visible'),
                    singleVisible: $('#pf-single-image-wrap').is(':visible'),
                    galleryItems: $('.pf-gallery-item').length
                });
                console.log('=== FIN GALERÍA ===');
            } else if (data.featured_image) {
                $('#pf-single-image').attr('src', data.featured_image).attr('alt', data.title || '');
                $('#pf-single-image-wrap').css('display', 'block');
                $('#pf-gallery-wrap').css('display', 'none');
            } else {
                // Verificar si hay galería de Elementor configurada
                var $widget = $('.portfolio-elementor-widget').first();
                var useElementorGallery = $widget.data('use-elementor-gallery') === true;
                var elementorGallery = $widget.data('elementor-gallery');
                
                if (useElementorGallery && elementorGallery && elementorGallery.length > 0) {
                    console.log('=== USANDO GALERÍA DE ELEMENTOR ===');
                    console.log('Galería de Elementor:', elementorGallery);
                    
                    // Convertir galería de Elementor a formato de URLs
                    var elementorUrls = elementorGallery.map(function(item) {
                        return item.url || item;
                    });
                    
                    initGallery(elementorUrls, data.title);
                    $('#pf-gallery-wrap').css('display', 'flex');
                    $('#pf-single-image-wrap').css('display', 'none');
                    console.log('=== FIN GALERÍA ELEMENTOR ===');
                } else {
                    $('#pf-gallery-wrap').css('display', 'none');
                    $('#pf-single-image-wrap').css('display', 'none');
                }
            }
            
            // Botón externo
            if (data.external_url) {
                $('#pf-external-link').attr('href', data.external_url);
                $('#pf-external-wrap').css('display', 'block');
            } else {
                $('#pf-external-wrap').css('display', 'none');
            }
            
            // Aplicar estilos personalizados si existen
            applyCustomStyles();
            
            console.log('Modal poblado OK');
            
        } catch (e) {
            console.error('Error al poblar:', e);
            showError('Error al mostrar datos');
        }
    }
    
    function initGallery(gallery, title) {
        console.log('Inicializando galería con', gallery.length, 'elementos');
        
        // Limpiar galería
        $('#pf-gallery-wrap').empty();
        
        // Crear elementos de galería directamente en el contenedor
        for (var i = 0; i < gallery.length; i++) {
            var mediaUrl = gallery[i];
            var isVideo = isVideoFile(mediaUrl);
            var itemHtml = '';
            
            if (isVideo) {
                itemHtml = '<div class="pf-gallery-item">';
                itemHtml += '  <div class="pf-gallery-video">';
                itemHtml += '    <video controls class="pf-gallery-media"><source src="' + mediaUrl + '" type="video/mp4"></video>';
                itemHtml += '  </div>';
                itemHtml += '</div>';
            } else {
                itemHtml = '<div class="pf-gallery-item">';
                itemHtml += '  <div class="pf-gallery-image">';
                itemHtml += '    <img src="' + mediaUrl + '" alt="' + title + '" class="pf-gallery-media">';
                itemHtml += '  </div>';
                itemHtml += '</div>';
            }
            
            $('#pf-gallery-wrap').append(itemHtml);
        }
        
        console.log('Galería creada con', gallery.length, 'elementos');
        console.log('Elementos en DOM:', $('.pf-gallery-item').length);
    }
    
    function isVideoFile(url) {
        var videoExtensions = ['.mp4', '.webm', '.ogg', '.avi', '.mov'];
        var lowerUrl = url.toLowerCase();
        for (var i = 0; i < videoExtensions.length; i++) {
            if (lowerUrl.indexOf(videoExtensions[i]) !== -1) {
                return true;
            }
        }
        return false;
    }
    
    function getYouTubeId(url) {
        if (!url) return null;
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        var match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }
    
    function getVimeoId(url) {
        if (!url) return null;
        var regExp = /(?:vimeo)\.com.*(?:videos|video|channels|)\/([\d]+)/i;
        var match = url.match(regExp);
        return match ? match[1] : null;
    }
    
    function initVideos(data) {
        console.log('Inicializando videos...');
        
        // Buscar el contenedor de videos en el modal
        var $videosContainer = $('#pf-modal').find('.pf-videos-section');
        
        // Si no existe, crear el contenedor
        if ($videosContainer.length === 0) {
            // Insertar después de la descripción y antes de la galería
            var videosHtml = '<div class="pf-videos-section pf-info-section" style="display:none;">';
            videosHtml += '  <h3>Videos</h3>';
            videosHtml += '  <div class="pf-videos-container"></div>';
            videosHtml += '</div>';
            
            $('.pf-modal-description').closest('.pf-info-section').after(videosHtml);
            $videosContainer = $('.pf-videos-section');
        }
        
        var $container = $videosContainer.find('.pf-videos-container');
        $container.empty();
        
        var hasVideos = false;
        
        // YouTube
        if (data.youtube_url) {
            var youtubeId = getYouTubeId(data.youtube_url);
            if (youtubeId) {
                console.log('YouTube ID encontrado:', youtubeId);
                var youtubeHtml = '<div class="pf-video-wrapper">';
                youtubeHtml += '  <iframe src="https://www.youtube.com/embed/' + youtubeId + '" ';
                youtubeHtml += 'frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" ';
                youtubeHtml += 'allowfullscreen class="pf-video-iframe"></iframe>';
                youtubeHtml += '</div>';
                $container.append(youtubeHtml);
                hasVideos = true;
            }
        }
        
        // Vimeo
        if (data.vimeo_url) {
            var vimeoId = getVimeoId(data.vimeo_url);
            if (vimeoId) {
                console.log('Vimeo ID encontrado:', vimeoId);
                var vimeoHtml = '<div class="pf-video-wrapper">';
                vimeoHtml += '  <iframe src="https://player.vimeo.com/video/' + vimeoId + '" ';
                vimeoHtml += 'frameborder="0" allow="autoplay; fullscreen; picture-in-picture" ';
                vimeoHtml += 'allowfullscreen class="pf-video-iframe"></iframe>';
                vimeoHtml += '</div>';
                $container.append(vimeoHtml);
                hasVideos = true;
            }
        }
        
        // Mostrar u ocultar sección de videos
        if (hasVideos) {
            $videosContainer.css('display', 'block');
            console.log('Videos mostrados');
        } else {
            $videosContainer.css('display', 'none');
            console.log('No hay videos para mostrar');
        }
    }
    
    function applyCustomStyles() {
        // Obtener estilos personalizados del widget desde data-attributes
        var $widget = $('.portfolio-elementor-widget').first();
        
        console.log('Aplicando estilos personalizados...');
        console.log('Widget encontrado:', $widget.length > 0);
        
        if ($widget.length > 0) {
            // Estilos del modal
            var modalBg = $widget.data('modal-bg');
            var titleColor = $widget.data('modal-title-color');
            var titleSize = $widget.data('modal-title-size');
            var titleWeight = $widget.data('modal-title-weight');
            var textColor = $widget.data('modal-text-color');
            var textSize = $widget.data('modal-text-size');
            var borderRadius = $widget.data('modal-border-radius');
            
            console.log('Estilos del modal:', {
                bg: modalBg,
                titleColor: titleColor,
                titleSize: titleSize,
                titleWeight: titleWeight,
                textColor: textColor,
                textSize: textSize,
                borderRadius: borderRadius
            });
            
            // Estilos del badge de categoría
            var categoryBg = $widget.data('category-bg');
            var categoryTextColor = $widget.data('category-text-color');
            var categoryBorderRadius = $widget.data('category-border-radius');
            
            console.log('Estilos del badge:', {
                bg: categoryBg,
                textColor: categoryTextColor,
                borderRadius: categoryBorderRadius
            });
            
            // Estilos del botón
            var buttonBg = $widget.data('button-bg');
            var buttonTextColor = $widget.data('button-text-color');
            var buttonHoverBg = $widget.data('button-hover-bg');
            var buttonSize = $widget.data('button-size');
            var buttonWeight = $widget.data('button-weight');
            var buttonBorderRadius = $widget.data('button-border-radius');
            
            console.log('Estilos del botón:', {
                bg: buttonBg,
                textColor: buttonTextColor,
                hoverBg: buttonHoverBg,
                size: buttonSize,
                weight: buttonWeight,
                borderRadius: buttonBorderRadius
            });
            
            // Aplicar estilos del modal (ahora funcionará porque no hay estilos inline)
            $('#pf-modal-container').css('background', modalBg || '#FFFFFF');
            $('#pf-modal-container').css('border-radius', borderRadius || '12px');
            
            $('.pf-modal-title').css({
                'color': titleColor || '#333333',
                'font-size': titleSize || '28px',
                'font-weight': titleWeight || '600'
            });
            
            $('.pf-modal-description').css({
                'color': textColor || '#555555',
                'font-size': textSize || '15px'
            });
            
            $('.pf-modal-meta').css('color', textColor || '#666666');
            
            console.log('Estilos del modal aplicados');
            
            // Aplicar estilos del badge de categoría
            // Usar color personalizado de Elementor o el color de la categoría de la BD
            var finalCategoryBg = categoryBg || $('.pf-category-badge').data('default-color') || '#2196F3';
            $('.pf-category-badge').css('background', finalCategoryBg);
            $('.pf-category-badge').css('color', categoryTextColor || '#FFFFFF');
            $('.pf-category-badge').css('border-radius', categoryBorderRadius || '20px');
            
            console.log('Estilos del badge aplicados');
            
            // Aplicar estilos del botón
            $('.pf-external-button').css({
                'background': buttonBg || '#2196F3',
                'color': buttonTextColor || '#FFFFFF',
                'font-size': buttonSize || '14px',
                'font-weight': buttonWeight || '500',
                'border-radius': buttonBorderRadius || '6px'
            });
            
            // Guardar color hover para usar en eventos
            $('.pf-external-button').data('hover-bg', buttonHoverBg || '#1976D2');
            $('.pf-external-button').data('normal-bg', buttonBg || '#2196F3');
            
            console.log('Estilos del botón aplicados');
            
            console.log('Estilos aplicados completos');
        }
    }
    
    function closeModal() {
        console.log('Cerrando modal');
        $('#pf-modal').fadeOut(300);
        
        // Pausar todos los videos
        $('.pf-gallery-item video').each(function() {
            this.pause();
        });
        
        currentProject = null;
    }
    
    function showError(msg) {
        $('.pf-modal-title').text('Error');
        $('.pf-modal-description').html('<div style="color:#f44336;padding:20px;text-align:center;background:#ffebee;border-radius:8px;border-left:4px solid #f44336;">' + msg + '</div>');
        $('.pf-gallery-container').hide();
        $('.pf-single-image-container').hide();
        $('.pf-external-container').hide();
    }
    
    // Exponer funciones globales
    window.pfCloseModal = closeModal;
    window.pfDebug = function() {
        console.log('=== DEBUG PORTFOLIO ===');
        console.log('jQuery:', typeof $ !== 'undefined');
        console.log('portfolio_frontend:', typeof portfolio_frontend !== 'undefined');
        console.log('Botones:', $('.portfolio-view-btn').length);
        console.log('Modal creado:', modalCreated);
        console.log('Modal visible:', $('#pf-modal').is(':visible'));
        console.log('Galería items:', $('.pf-gallery-item').length);
    };
    
})(jQuery);