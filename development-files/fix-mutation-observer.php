<?php
/**
 * Script de Solución para Error de MutationObserver - Plugin Portfolio
 * 
 * Este script proporciona soluciones específicas para el error de MutationObserver
 * que está causando problemas con el modal del plugin.
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🔧 Solución para Error de MutationObserver - Plugin Portfolio</h2>';

echo '<h3>🔍 Problema Identificado</h3>';
echo '<p><strong>Error:</strong> TypeError: Failed to execute \'observe\' on \'MutationObserver\': parameter 1 is not of type \'Node\'</p>';
echo '<p><strong>Causa:</strong> Conflicto entre el plugin Portfolio y Elementor</p>';
echo '<p><strong>Archivo:</strong> index.ts-4e01dc6b.js (Elementor)</p>';

echo '<h3>💡 Soluciones Disponibles</h3>';

echo '<h4>1. Solución Inmediata - Código JavaScript</h4>';
echo '<p>Copia y pega este código en la consola del navegador:</p>';
echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px;">';
echo '// Solución para MutationObserver
(function() {
    // Interceptar MutationObserver para evitar errores
    const originalObserve = MutationObserver.prototype.observe;
    MutationObserver.prototype.observe = function(target, options) {
        if (target && target.nodeType === Node.ELEMENT_NODE) {
            return originalObserve.call(this, target, options);
        } else {
            console.warn("MutationObserver: Target no es un nodo válido, omitiendo observación");
            return;
        }
    };
    
    // Forzar recreación del modal del portfolio
    if (typeof createProjectModal === "function") {
        // Eliminar modal existente
        jQuery("#portfolio-modal").remove();
        
        // Crear nuevo modal
        createProjectModal();
        
        console.log("Modal del portfolio recreado correctamente");
    }
})();';
echo '</pre>';

echo '<h4>2. Solución Temporal - Desactivar Elementor</h4>';
echo '<ol>';
echo '<li>Ve a <strong>Plugins > Plugins Instalados</strong></li>';
echo '<li>Desactiva temporalmente <strong>Elementor</strong></li>';
echo '<li>Prueba el modal del portfolio</li>';
echo '<li>Si funciona, reactiva Elementor</li>';
echo '</ol>';

echo '<h4>3. Solución Definitiva - Modificar JavaScript</h4>';
echo '<p>Agrega este código al final del archivo <code>assets/js/frontend.js</code>:</p>';
echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px;">';
echo '// Protección contra errores de MutationObserver
jQuery(document).ready(function($) {
    // Interceptar MutationObserver para evitar errores
    if (window.MutationObserver) {
        const originalObserve = MutationObserver.prototype.observe;
        MutationObserver.prototype.observe = function(target, options) {
            if (target && target.nodeType === Node.ELEMENT_NODE) {
                return originalObserve.call(this, target, options);
            } else {
                console.warn("MutationObserver: Target no es un nodo válido, omitiendo observación");
                return;
            }
        };
    }
    
    // Asegurar que el modal se cree correctamente
    setTimeout(function() {
        if ($("#portfolio-modal").length === 0) {
            createProjectModal();
        }
    }, 1000);
});';
echo '</pre>';

echo '<h4>4. Solución CSS - Aislar el Modal</h4>';
echo '<p>Agrega este CSS al archivo <code>assets/css/frontend.css</code>:</p>';
echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px;">';
echo '/* Aislar modal del portfolio de Elementor */
#portfolio-modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 999999 !important;
    background: rgba(0, 0, 0, 0.8) !important;
}

#portfolio-modal * {
    box-sizing: border-box !important;
}

/* Prevenir interferencia de Elementor */
#portfolio-modal .portfolio-modal-container {
    position: relative !important;
    z-index: 1000000 !important;
}';
echo '</pre>';

echo '<h3>🧪 Prueba de Solución</h3>';
echo '<p>Después de aplicar cualquiera de las soluciones:</p>';
echo '<ol>';
echo '<li>Abre una página con el widget Portfolio</li>';
echo '<li>Abre las herramientas de desarrollador (F12)</li>';
echo '<li>Ve a la pestaña Console</li>';
echo '<li>Haz clic en "Ver Detalles" de un proyecto</li>';
echo '<li>Verifica que no aparezcan errores de MutationObserver</li>';
echo '<li>Confirma que el modal se carga con la información del proyecto</li>';
echo '</ol>';

echo '<h3>🔍 Verificación de Funcionamiento</h3>';
echo '<p>El modal debería mostrar:</p>';
echo '<ul>';
echo '<li>✅ Título del proyecto</li>';
echo '<li>✅ Categoría con color</li>';
echo '<li>✅ Descripción del proyecto</li>';
echo '<li>✅ Imagen destacada</li>';
echo '<li>✅ Galería de imágenes (si existe)</li>';
echo '<li>✅ Estadísticas (vistas/likes)</li>';
echo '<li>✅ Fecha del proyecto</li>';
echo '</ul>';

echo '<h3>⚠️ Si el Problema Persiste</h3>';
echo '<ol>';
echo '<li><strong>Verifica la consola</strong> para otros errores JavaScript</li>';
echo '<li><strong>Revisa la pestaña Network</strong> para errores AJAX</li>';
echo '<li><strong>Desactiva otros plugins</strong> temporalmente</li>';
echo '<li><strong>Cambia a un tema por defecto</strong> de WordPress</li>';
echo '<li><strong>Limpia la caché</strong> del navegador y del sitio</li>';
echo '</ol>';

echo '<h3>📞 Información Adicional</h3>';
echo '<p><strong>Archivos modificados:</strong></p>';
echo '<ul>';
echo '<li><code>assets/js/frontend.js</code> - JavaScript del frontend</li>';
echo '<li><code>assets/css/frontend.css</code> - Estilos del frontend</li>';
echo '</ul>';

echo '<p><strong>Archivos de debug disponibles:</strong></p>';
echo '<ul>';
echo '<li><code>development-files/debug-modal-frontend.php</code> - Debug completo del modal</li>';
echo '<li><code>development-files/clean-duplicates.php</code> - Limpieza de datos duplicados</li>';
echo '</ul>';

echo '<div style="background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724; margin-top: 20px;">';
echo '<h4 style="margin-top: 0;">✅ Solución Recomendada</h4>';
echo '<p>Para resolver el problema inmediatamente:</p>';
echo '<ol>';
echo '<li>Usa la <strong>Solución 1</strong> (código JavaScript en consola)</li>';
echo '<li>Si funciona, aplica la <strong>Solución 3</strong> (modificar JavaScript)</li>';
echo '<li>Agrega la <strong>Solución 4</strong> (CSS) para prevenir futuros problemas</li>';
echo '</ol>';
echo '</div>';

echo '<p style="margin-top: 30px; text-align: center; color: #666;">';
echo 'Solución generada el ' . date('Y-m-d H:i:s');
echo '</p>';
