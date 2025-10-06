/**
 * Script de Verificación del Modal en Frontend
 * 
 * Este script verifica que el modal se esté creando correctamente
 * y que no haya conflictos con Elementor
 */

(function($) {
    'use strict';
    
    console.log('🔍 Verificador del Modal Portfolio iniciado');
    
    // Función para verificar el estado del modal
    function checkModalStatus() {
        console.log('📋 Verificando estado del modal...');
        
        // Verificar si existe el modal
        const modal = $('#portfolio-simple-modal');
        if (modal.length > 0) {
            console.log('✅ Modal encontrado en el DOM');
            console.log('📏 Dimensiones del modal:', modal.width(), 'x', modal.height());
            console.log('👁️ Visibilidad:', modal.is(':visible') ? 'Visible' : 'Oculto');
        } else {
            console.log('❌ Modal NO encontrado en el DOM');
        }
        
        // Verificar elementos internos
        const elements = [
            '#modal-title',
            '#modal-category', 
            '#modal-date',
            '#modal-main-image',
            '#modal-description',
            '#modal-gallery',
            '#modal-views',
            '#modal-likes',
            '#modal-external',
            '#modal-close'
        ];
        
        elements.forEach(function(selector) {
            const element = $(selector);
            if (element.length > 0) {
                console.log('✅ Elemento encontrado:', selector);
            } else {
                console.log('❌ Elemento NO encontrado:', selector);
            }
        });
        
        // Verificar botones de proyecto
        const projectButtons = $('.portfolio-view-btn');
        console.log('🔘 Botones de proyecto encontrados:', projectButtons.length);
        
        if (projectButtons.length > 0) {
            projectButtons.each(function(index) {
                const projectId = $(this).data('project-id');
                console.log('🔘 Botón', index + 1, '- Project ID:', projectId);
            });
        }
    }
    
    // Función para probar el modal manualmente
    function testModalManually() {
        console.log('🧪 Probando modal manualmente...');
        
        // Crear modal si no existe
        if ($('#portfolio-simple-modal').length === 0) {
            console.log('🔨 Creando modal manualmente...');
            
            const modalHtml = `
                <div id="portfolio-simple-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 999999; overflow-y: auto;">
                    <div style="position: relative; max-width: 900px; margin: 50px auto; background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                        <div style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h2 id="modal-title" style="margin: 0; color: #333; font-size: 24px;">Proyecto de Prueba</h2>
                                <div style="margin-top: 5px;">
                                    <span id="modal-category" style="background: #2196F3; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Prueba</span>
                                    <span id="modal-date" style="margin-left: 10px; color: #666; font-size: 14px;">Hoy</span>
                                </div>
                            </div>
                            <button id="modal-close" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
                        </div>
                        <div style="padding: 20px;">
                            <div id="modal-image-container" style="margin-bottom: 20px; text-align: center;">
                                <img id="modal-main-image" src="" alt="" style="max-width: 100%; max-height: 400px; border-radius: 4px;">
                            </div>
                            <div id="modal-description" style="margin-bottom: 20px; line-height: 1.6; color: #555;">
                                Este es un proyecto de prueba para verificar que el modal funciona correctamente.
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
            console.log('✅ Modal creado manualmente');
        }
        
        // Mostrar el modal
        $('#portfolio-simple-modal').show();
        console.log('👁️ Modal mostrado');
        
        // Bindear evento de cerrar
        $('#modal-close').off('click').on('click', function() {
            $('#portfolio-simple-modal').hide();
            console.log('❌ Modal cerrado');
        });
        
        // Bindear evento de clic fuera del modal
        $('#portfolio-simple-modal').off('click').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
                console.log('❌ Modal cerrado (clic fuera)');
            }
        });
    }
    
    // Función para verificar conflictos con Elementor
    function checkElementorConflicts() {
        console.log('🔍 Verificando conflictos con Elementor...');
        
        // Verificar si Elementor está cargado
        if (typeof elementor !== 'undefined') {
            console.log('⚠️ Elementor detectado');
            
            // Verificar MutationObserver
            if (window.MutationObserver) {
                console.log('⚠️ MutationObserver disponible');
                
                // Verificar si hay observadores activos
                const observers = [];
                const originalObserve = MutationObserver.prototype.observe;
                MutationObserver.prototype.observe = function(target, options) {
                    observers.push({target: target, options: options});
                    return originalObserve.call(this, target, options);
                };
                
                console.log('📊 Observadores MutationObserver:', observers.length);
            }
        } else {
            console.log('✅ Elementor no detectado');
        }
        
        // Verificar z-index conflicts
        const modal = $('#portfolio-simple-modal');
        if (modal.length > 0) {
            const zIndex = modal.css('z-index');
            console.log('📏 Z-index del modal:', zIndex);
            
            if (parseInt(zIndex) < 999999) {
                console.log('⚠️ Z-index del modal podría ser insuficiente');
            } else {
                console.log('✅ Z-index del modal es adecuado');
            }
        }
    }
    
    // Función para probar AJAX
    function testAjax() {
        console.log('🌐 Probando conexión AJAX...');
        
        if (typeof portfolio_frontend !== 'undefined') {
            console.log('✅ Objeto portfolio_frontend disponible');
            console.log('🔗 AJAX URL:', portfolio_frontend.ajax_url);
            console.log('🔑 Nonce:', portfolio_frontend.nonce);
            
            // Probar llamada AJAX simple
            $.ajax({
                url: portfolio_frontend.ajax_url,
                type: 'POST',
                data: {
                    action: 'portfolio_get_project',
                    project_id: 1,
                    nonce: portfolio_frontend.nonce
                },
                success: function(response) {
                    console.log('✅ AJAX funcionando correctamente');
                    console.log('📋 Respuesta:', response);
                },
                error: function(xhr, status, error) {
                    console.log('❌ Error AJAX:', error);
                    console.log('📋 Response:', xhr.responseText);
                }
            });
        } else {
            console.log('❌ Objeto portfolio_frontend NO disponible');
        }
    }
    
    // Ejecutar verificaciones cuando el DOM esté listo
    $(document).ready(function() {
        console.log('🚀 DOM listo, ejecutando verificaciones...');
        
        // Esperar un poco para que se cargue todo
        setTimeout(function() {
            checkModalStatus();
            checkElementorConflicts();
            testAjax();
            
            console.log('✅ Todas las verificaciones completadas');
            console.log('💡 Para probar el modal manualmente, ejecuta: testModalManually()');
        }, 1000);
    });
    
    // Exponer funciones globalmente para pruebas manuales
    window.checkModalStatus = checkModalStatus;
    window.testModalManually = testModalManually;
    window.checkElementorConflicts = checkElementorConflicts;
    window.testAjax = testAjax;
    
    console.log('🔧 Funciones de verificación disponibles:');
    console.log('  - checkModalStatus()');
    console.log('  - testModalManually()');
    console.log('  - checkElementorConflicts()');
    console.log('  - testAjax()');
    
})(jQuery);
