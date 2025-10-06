/**
 * Debug Script para Modal Portfolio
 * 
 * Este script identifica exactamente dónde está el problema
 * con la carga de información en el modal
 */

(function($) {
    'use strict';
    
    console.log('🔍 Debug Script Portfolio iniciado');
    
    // Función para debug completo
    function debugPortfolioModal() {
        console.log('=== INICIO DEBUG PORTFOLIO MODAL ===');
        
        // 1. Verificar jQuery
        console.log('1. jQuery disponible:', typeof $ !== 'undefined');
        console.log('   Versión jQuery:', $.fn.jquery);
        
        // 2. Verificar objeto portfolio_frontend
        console.log('2. Objeto portfolio_frontend:', typeof portfolio_frontend !== 'undefined');
        if (typeof portfolio_frontend !== 'undefined') {
            console.log('   AJAX URL:', portfolio_frontend.ajax_url);
            console.log('   Nonce:', portfolio_frontend.nonce);
        } else {
            console.error('   ❌ Objeto portfolio_frontend NO disponible');
        }
        
        // 3. Verificar botones de proyecto
        const projectButtons = $('.portfolio-view-btn');
        console.log('3. Botones de proyecto encontrados:', projectButtons.length);
        
        if (projectButtons.length > 0) {
            projectButtons.each(function(index) {
                const $btn = $(this);
                const projectId = $btn.data('project-id');
                const text = $btn.text().trim();
                console.log(`   Botón ${index + 1}: ID=${projectId}, Texto="${text}"`);
            });
        } else {
            console.error('   ❌ No se encontraron botones de proyecto');
        }
        
        // 4. Verificar modal
        const modal = $('#portfolio-simple-modal');
        console.log('4. Modal encontrado:', modal.length > 0);
        
        if (modal.length > 0) {
            console.log('   Modal visible:', modal.is(':visible'));
            console.log('   Elementos internos:');
            console.log('     - Título:', $('#modal-title').length);
            console.log('     - Categoría:', $('#modal-category').length);
            console.log('     - Descripción:', $('#modal-description').length);
            console.log('     - Imagen:', $('#modal-main-image').length);
        } else {
            console.error('   ❌ Modal NO encontrado');
        }
        
        // 5. Verificar eventos
        console.log('5. Eventos bindeados:');
        const events = $._data($(document)[0], 'events');
        if (events && events.click) {
            console.log('   Eventos click:', events.click.length);
        }
        
        console.log('=== FIN DEBUG PORTFOLIO MODAL ===');
    }
    
    // Función para probar AJAX manualmente
    function testAjaxManually() {
        console.log('🧪 Probando AJAX manualmente...');
        
        if (typeof portfolio_frontend === 'undefined') {
            console.error('❌ Objeto portfolio_frontend no disponible');
            return;
        }
        
        // Buscar un proyecto para probar
        const projectButtons = $('.portfolio-view-btn');
        if (projectButtons.length === 0) {
            console.error('❌ No hay botones de proyecto para probar');
            return;
        }
        
        const firstButton = projectButtons.first();
        const projectId = firstButton.data('project-id');
        
        if (!projectId) {
            console.error('❌ No se pudo obtener project_id del botón');
            return;
        }
        
        console.log('📋 Probando con Project ID:', projectId);
        
        // Realizar llamada AJAX
        $.ajax({
            url: portfolio_frontend.ajax_url,
            type: 'POST',
            data: {
                action: 'portfolio_get_project',
                project_id: projectId,
                nonce: portfolio_frontend.nonce
            },
            beforeSend: function() {
                console.log('📤 Enviando petición AJAX...');
            },
            success: function(response) {
                console.log('✅ Respuesta AJAX recibida:', response);
                
                if (response.success) {
                    console.log('📋 Datos del proyecto:', response.data);
                    
                    // Probar poblar el modal
                    populateModalManually(response.data);
                } else {
                    console.error('❌ Error en respuesta:', response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error AJAX:', error);
                console.error('📋 Response Text:', xhr.responseText);
                console.error('📋 Status:', status);
            }
        });
    }
    
    // Función para poblar el modal manualmente
    function populateModalManually(projectData) {
        console.log('🔧 Poblando modal manualmente...');
        
        // Crear modal si no existe
        if ($('#portfolio-simple-modal').length === 0) {
            console.log('🔨 Creando modal...');
            createModalManually();
        }
        
        // Poblar datos
        $('#modal-title').text(projectData.title || 'Sin título');
        $('#modal-category').text(projectData.category_name || 'Sin categoría');
        $('#modal-description').html(projectData.description || 'Sin descripción');
        $('#modal-views').text(projectData.views || 0);
        $('#modal-likes').text(projectData.likes || 0);
        
        if (projectData.featured_image) {
            $('#modal-main-image').attr('src', projectData.featured_image).attr('alt', projectData.title);
            $('#modal-image-container').show();
        }
        
        if (projectData.category_color) {
            $('#modal-category').css('background-color', projectData.category_color);
        }
        
        // Mostrar modal
        $('#portfolio-simple-modal').show();
        console.log('✅ Modal poblado y mostrado');
    }
    
    // Función para crear modal manualmente
    function createModalManually() {
        const modalHtml = `
            <div id="portfolio-simple-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 999999; overflow-y: auto;">
                <div style="position: relative; max-width: 900px; margin: 50px auto; background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                    <div style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h2 id="modal-title" style="margin: 0; color: #333; font-size: 24px;">Título del Proyecto</h2>
                            <div style="margin-top: 5px;">
                                <span id="modal-category" style="background: #2196F3; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Categoría</span>
                                <span id="modal-date" style="margin-left: 10px; color: #666; font-size: 14px;">Fecha</span>
                            </div>
                        </div>
                        <button id="modal-close" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
                    </div>
                    <div style="padding: 20px;">
                        <div id="modal-image-container" style="margin-bottom: 20px; text-align: center;">
                            <img id="modal-main-image" src="" alt="" style="max-width: 100%; max-height: 400px; border-radius: 4px;">
                        </div>
                        <div id="modal-description" style="margin-bottom: 20px; line-height: 1.6; color: #555;">
                            Descripción del proyecto...
                        </div>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                            <h4 style="margin-top: 0; color: #333;">Detalles del Proyecto</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div><strong>Vistas:</strong> <span id="modal-views">0</span></div>
                                <div><strong>Likes:</strong> <span id="modal-likes">0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        
        // Bindear eventos
        $('#modal-close').on('click', function() {
            $('#portfolio-simple-modal').hide();
        });
        
        $('#portfolio-simple-modal').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });
        
        console.log('✅ Modal creado manualmente');
    }
    
    // Función para verificar eventos de botones
    function checkButtonEvents() {
        console.log('🔍 Verificando eventos de botones...');
        
        const projectButtons = $('.portfolio-view-btn');
        console.log('Botones encontrados:', projectButtons.length);
        
        projectButtons.each(function(index) {
            const $btn = $(this);
            const events = $._data($btn[0], 'events');
            console.log(`Botón ${index + 1}:`, events ? Object.keys(events) : 'Sin eventos');
        });
        
        // Probar click manual
        if (projectButtons.length > 0) {
            console.log('🖱️ Probando click manual en primer botón...');
            projectButtons.first().trigger('click');
        }
    }
    
    // Ejecutar cuando el DOM esté listo
    $(document).ready(function() {
        console.log('🚀 DOM listo, ejecutando debug...');
        
        // Esperar un poco para que se cargue todo
        setTimeout(function() {
            debugPortfolioModal();
            
            console.log('💡 Funciones disponibles:');
            console.log('  - debugPortfolioModal()');
            console.log('  - testAjaxManually()');
            console.log('  - populateModalManually(data)');
            console.log('  - createModalManually()');
            console.log('  - checkButtonEvents()');
        }, 1000);
    });
    
    // Exponer funciones globalmente
    window.debugPortfolioModal = debugPortfolioModal;
    window.testAjaxManually = testAjaxManually;
    window.populateModalManually = populateModalManually;
    window.createModalManually = createModalManually;
    window.checkButtonEvents = checkButtonEvents;
    
})(jQuery);
