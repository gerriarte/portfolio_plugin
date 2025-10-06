<?php
/**
 * Script de Debug del Modal - Plugin Portfolio
 * 
 * Este script ayuda a diagnosticar problemas específicos con el modal
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🔍 Debug del Modal - Plugin Portfolio</h2>';

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

// Verificar scripts del frontend
echo '<h3>📄 Verificación de Scripts</h3>';

$frontend_js = WP_PLUGIN_DIR . '/portfolio-plugin/assets/js/frontend.js';
$frontend_css = WP_PLUGIN_DIR . '/portfolio-plugin/assets/css/frontend.css';

if (file_exists($frontend_js)) {
    echo '<p style="color: green;">✅ frontend.js existe</p>';
    
    // Verificar funciones clave
    $js_content = file_get_contents($frontend_js);
    $functions_to_check = [
        'openProjectModal',
        'populateProjectModal',
        'createProjectModal',
        'initModal'
    ];
    
    foreach ($functions_to_check as $func) {
        if (strpos($js_content, $func) !== false) {
            echo "<p style='color: green;'>✅ Función {$func} encontrada</p>";
        } else {
            echo "<p style='color: red;'>❌ Función {$func} NO encontrada</p>";
        }
    }
} else {
    echo '<p style="color: red;">❌ frontend.js NO existe</p>';
}

if (file_exists($frontend_css)) {
    echo '<p style="color: green;">✅ frontend.css existe</p>';
} else {
    echo '<p style="color: red;">❌ frontend.css NO existe</p>';
}

// Verificar configuración de WordPress
echo '<h3>⚙️ Configuración</h3>';
echo '<ul>';
echo '<li><strong>AJAX URL:</strong> ' . admin_url('admin-ajax.php') . '</li>';
echo '<li><strong>Plugin URL:</strong> ' . (defined('PORTFOLIO_PLUGIN_URL') ? PORTFOLIO_PLUGIN_URL : 'No definida') . '</li>';
echo '<li><strong>Plugin Version:</strong> ' . (defined('PORTFOLIO_PLUGIN_VERSION') ? PORTFOLIO_PLUGIN_VERSION : 'No definida') . '</li>';
echo '<li><strong>jQuery Version:</strong> ' . (wp_script_is('jquery', 'registered') ? 'Disponible' : 'No disponible') . '</li>';
echo '</ul>';

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

// Instrucciones de solución
echo '<h3>💡 Soluciones Sugeridas</h3>';
echo '<ol>';
echo '<li><strong>Verifica la consola del navegador</strong> para errores JavaScript</li>';
echo '<li><strong>Revisa la pestaña Network</strong> para ver si la llamada AJAX se está realizando</li>';
echo '<li><strong>Verifica que jQuery esté cargado</strong> antes que el script del plugin</li>';
echo '<li><strong>Limpia la caché</strong> del navegador y del sitio</li>';
echo '<li><strong>Verifica que el widget esté correctamente insertado</strong> en la página</li>';
echo '<li><strong>Revisa conflictos con otros plugins</strong> o temas</li>';
echo '</ol>';

echo '<h3>🔧 Prueba Manual del Modal</h3>';
echo '<p>Para probar el modal manualmente:</p>';
echo '<ol>';
echo '<li>Ve a una página que tenga el widget Portfolio Grid</li>';
echo '<li>Abre las herramientas de desarrollador (F12)</li>';
echo '<li>Ve a la pestaña Console</li>';
echo '<li>Ejecuta: <code>jQuery(".portfolio-view-btn").first().click()</code></li>';
echo '<li>Revisa si hay errores en la consola</li>';
echo '<li>Ve a la pestaña Network y busca la llamada a admin-ajax.php</li>';
echo '</ol>';

echo '<p style="background: #f0f8ff; padding: 15px; border: 1px solid #b0d4f1; border-radius: 5px;">';
echo '<strong>💡 Nota:</strong> Si el modal muestra "Cargando proyecto..." pero no carga el contenido, revisa la consola del navegador para errores AJAX o JavaScript.';
echo '</p>';
