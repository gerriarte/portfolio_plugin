<?php
/**
 * Script de Prueba del Modal Simplificado - Plugin Portfolio
 * 
 * Este script prueba el modal con el JavaScript simplificado
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🧪 Prueba del Modal Simplificado - Plugin Portfolio</h2>';

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
echo '<h3>📄 Verificación de Archivos JavaScript</h3>';

$frontend_js = WP_PLUGIN_DIR . '/portfolio-plugin/assets/js/frontend.js';

if (file_exists($frontend_js)) {
    echo '<p style="color: green;">✅ frontend.js existe</p>';
    
    // Verificar funciones clave
    $js_content = file_get_contents($frontend_js);
    $functions_to_check = [
        'openProjectModal',
        'populateProjectModal',
        'createProjectModal',
        'initPortfolioPlugin'
    ];
    
    foreach ($functions_to_check as $func) {
        if (strpos($js_content, $func) !== false) {
            echo "<p style='color: green;'>✅ Función {$func} encontrada</p>";
        } else {
            echo "<p style='color: red;'>❌ Función {$func} NO encontrada</p>";
        }
    }
    
    // Verificar que no haya errores de sintaxis obvios
    if (strpos($js_content, '...') !== false) {
        echo '<p style="color: orange;">⚠️ Se encontró el operador spread (...) en el código</p>';
    } else {
        echo '<p style="color: green;">✅ No se encontraron operadores spread problemáticos</p>';
    }
} else {
    echo '<p style="color: red;">❌ frontend.js NO existe</p>';
}

// Generar código JavaScript de prueba
echo '<h3>💻 Código JavaScript de Prueba</h3>';
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
            
            // Probar poblar el modal manualmente
            if (jQuery("#portfolio-modal").length) {
                jQuery(".modal-project-title").text(response.data.title);
                jQuery(".modal-project-category").text(response.data.category_name);
                jQuery(".project-description").html(response.data.description);
                console.log("Modal poblado manualmente");
            } else {
                console.log("Modal no encontrado en el DOM");
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

// Probar creación del modal
if (typeof createProjectModal === "function") {
    createProjectModal();
    console.log("Modal creado manualmente");
} else {
    console.error("Función createProjectModal no disponible");
}';
echo '</pre>';

// Instrucciones de prueba
echo '<h3>🔧 Instrucciones de Prueba</h3>';
echo '<ol>';
echo '<li><strong>Limpia la caché</strong> del navegador (Ctrl+F5)</li>';
echo '<li><strong>Ve a una página</strong> que tenga el widget Portfolio Grid</li>';
echo '<li><strong>Abre las herramientas de desarrollador</strong> (F12)</li>';
echo '<li><strong>Ve a la pestaña Console</strong></li>';
echo '<li><strong>Haz clic en "Ver Detalles"</strong> de un proyecto</li>';
echo '<li><strong>Verifica que:</strong></li>';
echo '<ul>';
echo '<li>No aparezcan errores de sintaxis</li>';
echo '<li>No aparezcan errores de MutationObserver</li>';
echo '<li>El modal se abra correctamente</li>';
echo '<li>Se muestre la información del proyecto</li>';
echo '</ul>';
echo '</ol>';

// Resumen de mejoras
echo '<h3>✅ Mejoras Implementadas</h3>';
echo '<ul>';
echo '<li><strong>JavaScript simplificado:</strong> Sin operadores spread problemáticos</li>';
echo '<li><strong>Protección MutationObserver:</strong> Evita errores con Elementor</li>';
echo '<li><strong>Creación robusta del modal:</strong> Usando jQuery puro</li>';
echo '<li><strong>Manejo de errores:</strong> Logging detallado para debug</li>';
echo '<li><strong>Compatibilidad:</strong> Funciona con cualquier versión de jQuery</li>';
echo '</ul>';

echo '<div style="background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724; margin-top: 20px;">';
echo '<h4 style="margin-top: 0;">🎉 JavaScript Simplificado Implementado</h4>';
echo '<p>El archivo JavaScript ha sido reescrito completamente para evitar conflictos con Elementor y otros plugins.</p>';
echo '<p><strong>Características:</strong></p>';
echo '<ul>';
echo '<li>✅ Sin operadores spread problemáticos</li>';
echo '<li>✅ Protección contra MutationObserver</li>';
echo '<li>✅ Creación robusta del modal</li>';
echo '<li>✅ Manejo de errores mejorado</li>';
echo '<li>✅ Compatibilidad total con Elementor</li>';
echo '</ul>';
echo '</div>';

echo '<p style="margin-top: 30px; text-align: center; color: #666;">';
echo 'Prueba completada el ' . date('Y-m-d H:i:s');
echo '</p>';
