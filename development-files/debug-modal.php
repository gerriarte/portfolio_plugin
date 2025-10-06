<?php
/**
 * Script de debug para el modal de proyectos
 * 
 * Este archivo ayuda a diagnosticar problemas con el modal de detalle de proyectos
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🔍 Debug del Modal de Proyectos</h2>';

// Verificar que el plugin esté activo
if (!class_exists('PortfolioPlugin')) {
    echo '<p style="color: red;">❌ El plugin Portfolio no está activo</p>';
    exit;
}

echo '<p style="color: green;">✅ Plugin Portfolio está activo</p>';

// Verificar que haya proyectos en la base de datos
global $wpdb;
$projects_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_projects");

if ($projects_count == 0) {
    echo '<p style="color: orange;">⚠️ No hay proyectos en la base de datos. Agrega algunos proyectos primero.</p>';
    echo '<p><a href="' . admin_url('admin.php?page=portfolio-admin') . '">Ir a Portfolio > Proyectos</a></p>';
    exit;
}

echo "<p style='color: green;'>✅ Hay {$projects_count} proyectos en la base de datos</p>";

// Obtener un proyecto de prueba
$project = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}portfolio_projects LIMIT 1");

if (!$project) {
    echo '<p style="color: red;">❌ No se pudo obtener un proyecto de prueba</p>';
    exit;
}

echo '<h3>📋 Proyecto de Prueba</h3>';
echo '<ul>';
echo '<li><strong>ID:</strong> ' . $project->id . '</li>';
echo '<li><strong>Título:</strong> ' . esc_html($project->title) . '</li>';
echo '<li><strong>Estado:</strong> ' . $project->status . '</li>';
echo '<li><strong>Imagen:</strong> ' . ($project->featured_image ? 'Sí' : 'No') . '</li>';
echo '</ul>';

// Verificar endpoints AJAX
echo '<h3>🔗 Verificación de Endpoints AJAX</h3>';

$ajax_actions = [
    'portfolio_get_project' => 'Obtener proyecto para modal',
    'portfolio_get_project_for_edit' => 'Obtener proyecto para edición',
    'portfolio_increment_views' => 'Incrementar vistas',
    'portfolio_increment_likes' => 'Incrementar likes'
];

foreach ($ajax_actions as $action => $description) {
    if (has_action("wp_ajax_{$action}")) {
        echo "<p style='color: green;'>✅ {$description} - Hook registrado</p>";
    } else {
        echo "<p style='color: red;'>❌ {$description} - Hook NO registrado</p>";
    }
}

// Verificar scripts y estilos
echo '<h3>📄 Verificación de Scripts y Estilos</h3>';

$required_files = [
    'assets/css/frontend.css' => 'Estilos del frontend',
    'assets/js/frontend.js' => 'JavaScript del frontend',
    'assets/css/admin.css' => 'Estilos del admin',
    'assets/js/admin.js' => 'JavaScript del admin'
];

foreach ($required_files as $file => $description) {
    $file_path = WP_PLUGIN_DIR . '/portfolio-plugin/' . $file;
    if (file_exists($file_path)) {
        echo "<p style='color: green;'>✅ {$description} - Archivo existe</p>";
    } else {
        echo "<p style='color: red;'>❌ {$description} - Archivo NO existe</p>";
    }
}

// Verificar nonces
echo '<h3>🔐 Verificación de Nonces</h3>';

$frontend_nonce = wp_create_nonce('portfolio_frontend_nonce');
$admin_nonce = wp_create_nonce('portfolio_admin_nonce');

echo "<p style='color: green;'>✅ Nonce frontend: {$frontend_nonce}</p>";
echo "<p style='color: green;'>✅ Nonce admin: {$admin_nonce}</p>";

// Probar endpoint AJAX directamente
echo '<h3>🧪 Prueba de Endpoint AJAX</h3>';

$test_data = array(
    'action' => 'portfolio_get_project',
    'project_id' => $project->id,
    'nonce' => $frontend_nonce
);

echo '<p>Probando endpoint con datos:</p>';
echo '<pre>' . print_r($test_data, true) . '</pre>';

// Simular llamada AJAX
$_POST = $test_data;
$_REQUEST = $test_data;

ob_start();
try {
    do_action('wp_ajax_portfolio_get_project');
    $output = ob_get_contents();
    ob_end_clean();
    
    if (!empty($output)) {
        echo '<p style="color: green;">✅ Endpoint responde correctamente</p>';
        echo '<details><summary>Ver respuesta</summary><pre>' . esc_html($output) . '</pre></details>';
    } else {
        echo '<p style="color: orange;">⚠️ Endpoint no devuelve respuesta</p>';
    }
} catch (Exception $e) {
    ob_end_clean();
    echo '<p style="color: red;">❌ Error en endpoint: ' . $e->getMessage() . '</p>';
}

// Verificar configuración de WordPress
echo '<h3>⚙️ Configuración de WordPress</h3>';

echo '<ul>';
echo '<li><strong>WP_DEBUG:</strong> ' . (defined('WP_DEBUG') && WP_DEBUG ? 'Activado' : 'Desactivado') . '</li>';
echo '<li><strong>WP_DEBUG_LOG:</strong> ' . (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'Activado' : 'Desactivado') . '</li>';
echo '<li><strong>WP_DEBUG_DISPLAY:</strong> ' . (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ? 'Activado' : 'Desactivado') . '</li>';
echo '<li><strong>AJAX URL:</strong> ' . admin_url('admin-ajax.php') . '</li>';
echo '</ul>';

// Instrucciones de solución
echo '<h3>💡 Soluciones Sugeridas</h3>';
echo '<ol>';
echo '<li><strong>Verifica la consola del navegador</strong> para errores JavaScript</li>';
echo '<li><strong>Revisa los logs de error</strong> de WordPress en /wp-content/debug.log</li>';
echo '<li><strong>Limpia la caché</strong> del navegador y del sitio</li>';
echo '<li><strong>Verifica que jQuery esté cargado</strong> antes que el script del plugin</li>';
echo '<li><strong>Desactiva otros plugins</strong> temporalmente para verificar conflictos</li>';
echo '</ol>';

echo '<h3>🔧 Prueba Manual del Modal</h3>';
echo '<p>Para probar el modal manualmente:</p>';
echo '<ol>';
echo '<li>Ve a una página que tenga el widget Portfolio Grid</li>';
echo '<li>Abre las herramientas de desarrollador (F12)</li>';
echo '<li>Ve a la pestaña Console</li>';
echo '<li>Haz clic en "Ver Detalles" de un proyecto</li>';
echo '<li>Revisa si hay errores en la consola</li>';
echo '</ol>';

echo '<p style="background: #f0f8ff; padding: 15px; border: 1px solid #b0d4f1; border-radius: 5px;">';
echo '<strong>💡 Nota:</strong> Si el modal muestra "Cargando proyecto..." pero no carga el contenido, revisa la consola del navegador para errores AJAX.';
echo '</p>';
