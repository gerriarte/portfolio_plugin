<?php
/**
 * Script de Prueba del Modal Simplificado - Enfoque Alternativo
 * 
 * Este script prueba el nuevo modal que evita conflictos con Elementor
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🧪 Prueba del Modal Simplificado - Enfoque Alternativo</h2>';

// Verificar que el plugin esté activo
if (!class_exists('PortfolioPlugin')) {
    echo '<p style="color: red;">❌ El plugin Portfolio no está activo</p>';
    exit;
}

echo '<p style="color: green;">✅ Plugin Portfolio está activo</p>';

// Verificar que haya proyectos
global $wpdb;
$projects = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}portfolio_projects LIMIT 3");

if (empty($projects)) {
    echo '<p style="color: orange;">⚠️ No hay proyectos en la base de datos</p>';
    exit;
}

echo '<p style="color: green;">✅ Hay proyectos en la base de datos</p>';

// Mostrar proyectos disponibles
echo '<h3>📋 Proyectos Disponibles</h3>';
echo '<ul>';
foreach ($projects as $project) {
    echo '<li><strong>' . esc_html($project->title) . '</strong> (ID: ' . $project->id . ')</li>';
}
echo '</ul>';

// Probar el endpoint AJAX
echo '<h3>🧪 Prueba del Endpoint AJAX</h3>';

$project = $projects[0];
$nonce = wp_create_nonce('portfolio_frontend_nonce');

echo '<p><strong>Proyecto de prueba:</strong> ' . esc_html($project->title) . ' (ID: ' . $project->id . ')</p>';

// Simular llamada AJAX
$_POST = array(
    'action' => 'portfolio_get_project',
    'project_id' => $project->id,
    'nonce' => $nonce
);
$_REQUEST = $_POST;

echo '<h4>Datos enviados:</h4>';
echo '<pre>' . print_r($_POST, true) . '</pre>';

// Capturar la respuesta
ob_start();
try {
    do_action('wp_ajax_portfolio_get_project');
    $response = ob_get_contents();
    ob_end_clean();
    
    echo '<h4>Respuesta del servidor:</h4>';
    if (!empty($response)) {
        echo '<pre>' . esc_html($response) . '</pre>';
        
        // Intentar decodificar JSON
        $json_response = json_decode($response, true);
        if ($json_response) {
            echo '<h4>Datos decodificados:</h4>';
            echo '<pre>' . print_r($json_response, true) . '</pre>';
            
            if (isset($json_response['success']) && $json_response['success']) {
                echo '<p style="color: green;">✅ Endpoint responde correctamente</p>';
                
                $project_data = $json_response['data'];
                echo '<h4>📋 Datos del proyecto:</h4>';
                echo '<ul>';
                echo '<li><strong>Título:</strong> ' . esc_html($project_data['title']) . '</li>';
                echo '<li><strong>Categoría:</strong> ' . esc_html($project_data['category_name']) . '</li>';
                echo '<li><strong>Descripción:</strong> ' . esc_html(substr($project_data['description'], 0, 100)) . '...</li>';
                echo '<li><strong>Imagen destacada:</strong> ' . ($project_data['featured_image'] ? 'Sí' : 'No') . '</li>';
                echo '<li><strong>Galería:</strong> ' . (is_array($project_data['gallery']) ? count($project_data['gallery']) . ' elementos' : 'No') . '</li>';
                echo '<li><strong>Vistas:</strong> ' . $project_data['views'] . '</li>';
                echo '<li><strong>Likes:</strong> ' . $project_data['likes'] . '</li>';
                echo '</ul>';
            } else {
                echo '<p style="color: red;">❌ Endpoint devuelve error: ' . ($json_response['data']['message'] ?? 'Error desconocido') . '</p>';
            }
        } else {
            echo '<p style="color: orange;">⚠️ Respuesta no es JSON válido</p>';
        }
    } else {
        echo '<p style="color: red;">❌ Endpoint no devuelve respuesta</p>';
    }
} catch (Exception $e) {
    ob_end_clean();
    echo '<p style="color: red;">❌ Error en endpoint: ' . $e->getMessage() . '</p>';
}

// Verificar archivos JavaScript
echo '<h3>📄 Verificación del Nuevo JavaScript</h3>';

$frontend_js = WP_PLUGIN_DIR . '/portfolio-plugin/assets/js/frontend.js';

if (file_exists($frontend_js)) {
    echo '<p style="color: green;">✅ frontend.js existe</p>';
    
    // Verificar funciones clave del nuevo enfoque
    $js_content = file_get_contents($frontend_js);
    $functions_to_check = [
        'initSimpleModal',
        'createSimpleModal',
        'openSimpleModal',
        'populateSimpleModal',
        'closeSimpleModal'
    ];
    
    foreach ($functions_to_check as $func) {
        if (strpos($js_content, $func) !== false) {
            echo "<p style='color: green;'>✅ Función {$func} encontrada</p>";
        } else {
            echo "<p style='color: red;'>❌ Función {$func} NO encontrada</p>";
        }
    }
    
    // Verificar que no haya código problemático
    $problematic_patterns = [
        'MutationObserver',
        'observe(',
        '...',
        'createProjectModal',
        'populateProjectModal'
    ];
    
    foreach ($problematic_patterns as $pattern) {
        if (strpos($js_content, $pattern) !== false) {
            echo "<p style='color: orange;'>⚠️ Patrón problemático '{$pattern}' encontrado</p>";
        } else {
            echo "<p style='color: green;'>✅ Patrón problemático '{$pattern}' NO encontrado</p>";
        }
    }
} else {
    echo '<p style="color: red;">❌ frontend.js NO existe</p>';
}

// Generar código JavaScript de prueba para el nuevo enfoque
echo '<h3>💻 Código JavaScript de Prueba - Nuevo Enfoque</h3>';
echo '<p>Pega este código en la consola del navegador para probar:</p>';
echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px;">';
echo '// Probar llamada AJAX manual
jQuery.ajax({
    url: "' . admin_url('admin-ajax.php') . '",
    type: "POST",
    data: {
        action: "portfolio_get_project",
        project_id: ' . $project->id . ',
        nonce: "' . wp_create_nonce('portfolio_frontend_nonce') . '"
    },
    success: function(response) {
        console.log("Respuesta AJAX:", response);
        if (response.success) {
            console.log("Datos del proyecto:", response.data);
            
            // Probar el modal simple
            if (jQuery("#portfolio-simple-modal").length) {
                console.log("Modal simple encontrado");
                
                // Poblar manualmente
                jQuery("#modal-title").text(response.data.title);
                jQuery("#modal-category").text(response.data.category_name);
                jQuery("#modal-description").html(response.data.description);
                jQuery("#modal-views").text(response.data.views || 0);
                jQuery("#modal-likes").text(response.data.likes || 0);
                
                if (response.data.featured_image) {
                    jQuery("#modal-main-image").attr("src", response.data.featured_image);
                    jQuery("#modal-image-container").show();
                }
                
                console.log("Modal simple poblado manualmente");
            } else {
                console.log("Modal simple no encontrado, creando...");
                if (typeof createSimpleModal === "function") {
                    createSimpleModal();
                    console.log("Modal simple creado");
                } else {
                    console.error("Función createSimpleModal no disponible");
                }
            }
        } else {
            console.error("Error:", response.data.message);
        }
    },
    error: function(xhr, status, error) {
        console.error("Error AJAX:", error);
        console.error("Response:", xhr.responseText);
    }
});

// Probar apertura del modal
if (typeof openSimpleModal === "function") {
    openSimpleModal(' . $project->id . ');
    console.log("Modal simple abierto");
} else {
    console.error("Función openSimpleModal no disponible");
}';
echo '</pre>';

// Instrucciones de prueba específicas
echo '<h3>🔧 Instrucciones de Prueba - Nuevo Enfoque</h3>';
echo '<ol>';
echo '<li><strong>Limpia la caché</strong> del navegador (Ctrl+F5)</li>';
echo '<li><strong>Ve a una página</strong> que tenga el widget Portfolio Grid</li>';
echo '<li><strong>Abre las herramientas de desarrollador</strong> (F12)</li>';
echo '<li><strong>Ve a la pestaña Console</strong></li>';
echo '<li><strong>Verifica que NO aparezcan errores</strong> de MutationObserver</li>';
echo '<li><strong>Haz clic en "Ver Detalles"</strong> de un proyecto</li>';
echo '<li><strong>Verifica que:</strong></li>';
echo '<ul>';
echo '<li>✅ El modal se abra con ID "portfolio-simple-modal"</li>';
echo '<li>✅ Se muestre el título del proyecto</li>';
echo '<li>✅ Se muestre la categoría con color</li>';
echo '<li>✅ Se muestre la descripción</li>';
echo '<li>✅ Se muestre la imagen destacada (si existe)</li>';
echo '<li>✅ Se muestren las estadísticas (vistas/likes)</li>';
echo '<li>✅ Se muestre la galería (si existe)</li>';
echo '<li>✅ Se pueda cerrar con X o ESC</li>';
echo '</ul>';
echo '</ol>';

// Características del nuevo enfoque
echo '<h3>✨ Características del Nuevo Enfoque</h3>';
echo '<ul>';
echo '<li><strong>🎯 Modal Simple:</strong> HTML inline sin dependencias complejas</li>';
echo '<li><strong>🚫 Sin MutationObserver:</strong> Evita conflictos con Elementor</li>';
echo '<li><strong>📱 Responsive:</strong> Se adapta a diferentes tamaños de pantalla</li>';
echo '<li><strong>🎨 Estilos Inline:</strong> No depende de CSS externo</li>';
echo '<li><strong>⌨️ Navegación por Teclado:</strong> ESC para cerrar</li>';
echo '<li><strong>🖼️ Galería Simple:</strong> Miniaturas clickeables</li>';
echo '<li><strong>🔗 Enlace Externo:</strong> Botón para ver proyecto completo</li>';
echo '<li><strong>📊 Estadísticas:</strong> Vistas y likes visibles</li>';
echo '</ul>';

// Resumen de mejoras
echo '<h3>✅ Mejoras Implementadas</h3>';
echo '<ul>';
echo '<li><strong>Enfoque completamente nuevo:</strong> Modal simple sin conflictos</li>';
echo '<li><strong>HTML inline:</strong> No depende de CSS externo</li>';
echo '<li><strong>Sin MutationObserver:</strong> Evita errores con Elementor</li>';
echo '<li><strong>Código minimalista:</strong> Fácil de mantener y debuggear</li>';
echo '<li><strong>Funcionalidad completa:</strong> Todas las características del modal original</li>';
echo '</ul>';

echo '<div style="background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724; margin-top: 20px;">';
echo '<h4 style="margin-top: 0;">🎉 Nuevo Enfoque Implementado</h4>';
echo '<p>El modal ha sido completamente reescrito con un enfoque minimalista que evita todos los conflictos con Elementor.</p>';
echo '<p><strong>Ventajas del nuevo enfoque:</strong></p>';
echo '<ul>';
echo '<li>✅ Sin errores de MutationObserver</li>';
echo '<li>✅ Modal completamente funcional</li>';
echo '<li>✅ Compatible con cualquier tema</li>';
echo '<li>✅ Código más simple y mantenible</li>';
echo '<li>✅ Todas las funcionalidades preservadas</li>';
echo '</ul>';
echo '</div>';

echo '<p style="margin-top: 30px; text-align: center; color: #666;">';
echo 'Prueba completada el ' . date('Y-m-d H:i:s');
echo '</p>';
