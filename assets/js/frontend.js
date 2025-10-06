/**
 * Portfolio Modal - Versión Mejorada con Carrusel
 * Compatible con navegadores antiguos, sin sintaxis moderna
 */

(function($) {
    'use strict';
    
    // Confirmación de carga
    console.log('=================================================');
    console.log('PORTFOLIO PLUGIN JAVASCRIPT CARGADO CORRECTAMENTE');
    console.log('Version: Mejorada con Carrusel');
    console.log('=================================================');
    
    // Variables globales
    var currentProject = null;
    var modalCreated = false;
    var currentSlide = 0;
    var totalSlides = 0;
    
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
        
        // Crear modal con HTML mejorado
        var html = '';
        html += '<div id="pf-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.85);z-index:999999;overflow-y:auto;">';
        html += '  <div id="pf-modal-container" style="position:relative;max-width:1000px;margin:50px auto;background:#fff;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">';
        
        // Header
        html += '    <div style="padding:30px 30px 20px;border-bottom:1px solid #eee;position:relative;">';
        html += '      <button id="pf-close" style="position:absolute;top:20px;right:20px;background:none;border:none;font-size:28px;cursor:pointer;color:#999;line-height:1;padding:5px 10px;transition:color 0.3s;">&times;</button>';
        html += '      <h2 id="pf-title" style="margin:0 40px 0 0;color:#333;font-size:28px;font-weight:600;line-height:1.3;">Cargando...</h2>';
        
        // Meta info en una línea (categoría, vistas, likes)
        html += '      <div id="pf-meta" style="display:flex;align-items:center;gap:20px;margin-top:12px;font-size:13px;color:#666;">';
        html += '        <span id="pf-cat-badge" style="background:#2196F3;color:white;padding:4px 12px;border-radius:20px;font-weight:500;font-size:12px;">Categoría</span>';
        html += '        <span style="display:flex;align-items:center;gap:5px;"><span style="font-size:16px;">👁️</span><span id="pf-views">0</span></span>';
        html += '        <span style="display:flex;align-items:center;gap:5px;"><span style="font-size:16px;">❤️</span><span id="pf-likes">0</span></span>';
        html += '      </div>';
        html += '    </div>';
        
        // Body
        html += '    <div style="padding:30px;">';
        
        // Carrusel de galería
        html += '      <div id="pf-gallery-wrap" style="display:none;margin-bottom:30px;position:relative;background:#000;border-radius:8px;overflow:hidden;">';
        html += '        <div id="pf-carousel" style="position:relative;width:100%;height:500px;overflow:hidden;"></div>';
        
        // Controles del carrusel
        html += '        <button id="pf-prev" style="position:absolute;left:15px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.9);border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:20px;color:#333;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.2);transition:all 0.3s;">‹</button>';
        html += '        <button id="pf-next" style="position:absolute;right:15px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.9);border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:20px;color:#333;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.2);transition:all 0.3s;">›</button>';
        
        // Indicadores
        html += '        <div id="pf-indicators" style="position:absolute;bottom:15px;left:50%;transform:translateX(-50%);display:flex;gap:8px;z-index:10;"></div>';
        
        // Contador
        html += '        <div id="pf-counter" style="position:absolute;top:15px;right:15px;background:rgba(0,0,0,0.6);color:white;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:500;"></div>';
        html += '      </div>';
        
        // Imagen única (sin galería)
        html += '      <div id="pf-single-image-wrap" style="display:none;margin-bottom:30px;text-align:center;background:#f8f9fa;border-radius:8px;overflow:hidden;">';
        html += '        <img id="pf-single-image" src="" alt="" style="max-width:100%;height:auto;max-height:500px;display:inline-block;">';
        html += '      </div>';
        
        // Descripción
        html += '      <div id="pf-desc" style="margin-bottom:30px;line-height:1.8;color:#555;font-size:15px;">Cargando...</div>';
        
        // Botón externo
        html += '      <div id="pf-external-wrap" style="display:none;">';
        html += '        <a id="pf-external-link" href="#" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:8px;background:#2196F3;color:white;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:500;transition:background 0.3s;">';
        html += '          <span>Ver Proyecto Completo</span>';
        html += '          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>';
        html += '        </a>';
        html += '      </div>';
        
        html += '    </div>';
        html += '  </div>';
        html += '</div>';
        
        $('body').append(html);
        modalCreated = true;
        console.log('Modal creado');
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
        $(document).on('mouseenter', '#pf-external-link', function() {
            var hoverBg = $(this).data('hover-bg') || '#1976D2';
            $(this).css('background', hoverBg);
        });
        $(document).on('mouseleave', '#pf-external-link', function() {
            var normalBg = $(this).data('normal-bg') || '#2196F3';
            $(this).css('background', normalBg);
        });
        
        // Hover en controles de carrusel
        $(document).on('mouseenter', '#pf-prev, #pf-next', function() {
            $(this).css({
                'background': 'rgba(255,255,255,1)',
                'transform': 'translateY(-50%) scale(1.1)'
            });
        });
        $(document).on('mouseleave', '#pf-prev, #pf-next', function() {
            $(this).css({
                'background': 'rgba(255,255,255,0.9)',
                'transform': 'translateY(-50%) scale(1)'
            });
        });
        
        // Navegación del carrusel
        $(document).on('click', '#pf-prev', function(e) {
            e.stopPropagation();
            prevSlide();
        });
        
        $(document).on('click', '#pf-next', function(e) {
            e.stopPropagation();
            nextSlide();
        });
        
        // Click en indicadores
        $(document).on('click', '.pf-indicator', function(e) {
            e.stopPropagation();
            var index = $(this).data('index');
            goToSlide(index);
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
            
            // Flechas para navegar carrusel
            if ($('#pf-modal').is(':visible') && totalSlides > 1) {
                if (e.key === 'ArrowLeft' || e.keyCode === 37) {
                    prevSlide();
                } else if (e.key === 'ArrowRight' || e.keyCode === 39) {
                    nextSlide();
                }
            }
        });
        
        console.log('Eventos bindeados');
    }
    
    function openModal(projectId) {
        console.log('Abriendo modal:', projectId);
        
        if (!modalCreated) {
            createModal();
        }
        
        // Reset
        currentSlide = 0;
        totalSlides = 0;
        
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
            $('#pf-title').text(data.title || 'Sin título');
            
            // Meta info
            $('#pf-cat-badge').text(data.category_name || 'Sin categoría');
            // Guardar el color de la categoría para usarlo si no hay personalización
            $('#pf-cat-badge').data('default-color', data.category_color || '#2196F3');
            $('#pf-views').text(data.views || 0);
            $('#pf-likes').text(data.likes || 0);
            
            // Descripción
            $('#pf-desc').html(data.description || 'Sin descripción');
            
            // Galería o imagen única
            if (data.gallery && data.gallery.length > 0) {
                console.log('Inicializando galería con', data.gallery.length, 'elementos');
                initGallery(data.gallery, data.title);
                $('#pf-gallery-wrap').show();
                $('#pf-single-image-wrap').hide();
            } else if (data.featured_image) {
                $('#pf-single-image').attr('src', data.featured_image).attr('alt', data.title || '');
                $('#pf-single-image-wrap').show();
                $('#pf-gallery-wrap').hide();
            } else {
                $('#pf-gallery-wrap').hide();
                $('#pf-single-image-wrap').hide();
            }
            
            // Botón externo
            if (data.external_url) {
                $('#pf-external-link').attr('href', data.external_url);
                $('#pf-external-wrap').show();
            } else {
                $('#pf-external-wrap').hide();
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
        totalSlides = gallery.length;
        currentSlide = 0;
        
        // Limpiar carrusel
        $('#pf-carousel').empty();
        $('#pf-indicators').empty();
        
        // Crear slides
        for (var i = 0; i < gallery.length; i++) {
            var mediaUrl = gallery[i];
            var isVideo = isVideoFile(mediaUrl);
            var slideHtml = '';
            
            if (isVideo) {
                slideHtml = '<div class="pf-slide" data-index="' + i + '" style="position:absolute;top:0;left:0;width:100%;height:100%;display:' + (i === 0 ? 'flex' : 'none') + ';align-items:center;justify-content:center;background:#000;">';
                slideHtml += '<video controls style="max-width:100%;max-height:100%;"><source src="' + mediaUrl + '" type="video/mp4"></video>';
                slideHtml += '</div>';
            } else {
                slideHtml = '<div class="pf-slide" data-index="' + i + '" style="position:absolute;top:0;left:0;width:100%;height:100%;display:' + (i === 0 ? 'flex' : 'none') + ';align-items:center;justify-content:center;background:#000;">';
                slideHtml += '<img src="' + mediaUrl + '" alt="' + title + '" style="max-width:100%;max-height:100%;object-fit:contain;">';
                slideHtml += '</div>';
            }
            
            $('#pf-carousel').append(slideHtml);
            
            // Crear indicador
            var indicatorHtml = '<div class="pf-indicator' + (i === 0 ? ' active' : '') + '" data-index="' + i + '" style="width:8px;height:8px;border-radius:50%;background:' + (i === 0 ? 'white' : 'rgba(255,255,255,0.5)') + ';cursor:pointer;transition:all 0.3s;"></div>';
            $('#pf-indicators').append(indicatorHtml);
        }
        
        // Actualizar contador
        updateCounter();
        
        // Mostrar/ocultar controles
        if (totalSlides <= 1) {
            $('#pf-prev, #pf-next, #pf-indicators').hide();
        } else {
            $('#pf-prev, #pf-next, #pf-indicators').show();
        }
    }
    
    function nextSlide() {
        if (totalSlides <= 1) return;
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlide();
    }
    
    function prevSlide() {
        if (totalSlides <= 1) return;
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSlide();
    }
    
    function goToSlide(index) {
        if (totalSlides <= 1) return;
        currentSlide = index;
        updateSlide();
    }
    
    function updateSlide() {
        // Ocultar todos los slides
        $('.pf-slide').hide();
        
        // Mostrar slide actual
        $('.pf-slide[data-index="' + currentSlide + '"]').fadeIn(300);
        
        // Actualizar indicadores
        $('.pf-indicator').css('background', 'rgba(255,255,255,0.5)').removeClass('active');
        $('.pf-indicator[data-index="' + currentSlide + '"]').css('background', 'white').addClass('active');
        
        // Actualizar contador
        updateCounter();
        
        // Pausar videos de otros slides
        $('.pf-slide').not('[data-index="' + currentSlide + '"]').find('video').each(function() {
            this.pause();
        });
    }
    
    function updateCounter() {
        if (totalSlides > 1) {
            $('#pf-counter').text((currentSlide + 1) + ' / ' + totalSlides).show();
        } else {
            $('#pf-counter').hide();
        }
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
    
    function applyCustomStyles() {
        // Obtener estilos personalizados del widget desde data-attributes
        var $widget = $('.portfolio-elementor-widget').first();
        
        if ($widget.length > 0) {
            // Estilos del modal
            var modalBg = $widget.data('modal-bg');
            var titleColor = $widget.data('modal-title-color');
            var textColor = $widget.data('modal-text-color');
            var borderRadius = $widget.data('modal-border-radius');
            
            // Estilos del badge de categoría
            var categoryBg = $widget.data('category-bg');
            var categoryTextColor = $widget.data('category-text-color');
            var categoryBorderRadius = $widget.data('category-border-radius');
            
            // Estilos del botón
            var buttonBg = $widget.data('button-bg');
            var buttonTextColor = $widget.data('button-text-color');
            var buttonHoverBg = $widget.data('button-hover-bg');
            var buttonBorderRadius = $widget.data('button-border-radius');
            
            // Aplicar estilos del modal
            if (modalBg) {
                $('#pf-modal-container').css('background', modalBg);
            }
            if (titleColor) {
                $('#pf-title').css('color', titleColor);
            }
            if (textColor) {
                $('#pf-desc').css('color', textColor);
                $('#pf-meta').css('color', textColor);
            }
            if (borderRadius) {
                $('#pf-modal-container').css('border-radius', borderRadius);
            }
            
            // Aplicar estilos del badge de categoría
            // Usar color personalizado de Elementor o el color de la categoría de la BD
            var finalCategoryBg = categoryBg || $('#pf-cat-badge').data('default-color') || '#2196F3';
            $('#pf-cat-badge').css('background', finalCategoryBg);
            
            if (categoryTextColor) {
                $('#pf-cat-badge').css('color', categoryTextColor);
            }
            if (categoryBorderRadius) {
                $('#pf-cat-badge').css('border-radius', categoryBorderRadius);
            }
            
            // Aplicar estilos del botón
            if (buttonBg) {
                $('#pf-external-link').css('background', buttonBg);
            }
            if (buttonTextColor) {
                $('#pf-external-link').css('color', buttonTextColor);
            }
            if (buttonBorderRadius) {
                $('#pf-external-link').css('border-radius', buttonBorderRadius);
            }
            
            // Guardar color hover para usar en eventos
            if (buttonHoverBg) {
                $('#pf-external-link').data('hover-bg', buttonHoverBg);
                $('#pf-external-link').data('normal-bg', buttonBg || '#2196F3');
            }
            
            console.log('Estilos aplicados completos');
        }
    }
    
    function closeModal() {
        console.log('Cerrando modal');
        $('#pf-modal').fadeOut(300);
        
        // Pausar todos los videos
        $('.pf-slide video').each(function() {
            this.pause();
        });
        
        currentProject = null;
        currentSlide = 0;
        totalSlides = 0;
    }
    
    function showError(msg) {
        $('#pf-title').text('Error');
        $('#pf-desc').html('<div style="color:#f44336;padding:20px;text-align:center;background:#ffebee;border-radius:8px;border-left:4px solid #f44336;">' + msg + '</div>');
        $('#pf-gallery-wrap').hide();
        $('#pf-single-image-wrap').hide();
        $('#pf-external-wrap').hide();
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
        console.log('Slides actuales:', totalSlides);
        console.log('Slide actual:', currentSlide);
    };
    
})(jQuery);