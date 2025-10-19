<?php
/**
 * Script de diagnóstico para problemas de funcionalidad del plugin
 * 
 * INSTRUCCIONES:
 * 1. Sube este archivo a la carpeta raíz de tu plugin
 * 2. Acceder a: http://tudominio.com/wp-content/plugins/portfolio-plugin/diagnose-functionality.php
 * 3. Revisar los tests y seguir las recomendaciones
 * 4. Eliminar este archivo después de usar
 * 
 * @package Sabsfe_Portfolio
 * @since 1.0.0
 */

// Cargar WordPress
require_once('../../../wp-config.php');

// Verificar que el usuario sea administrador
if (!current_user_can('manage_options')) {
    wp_die('No tienes permisos para ejecutar este script');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico de Funcionalidad - Portfolio Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: #4CAF50; background: #f1f8e9; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: #f44336; background: #ffebee; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #2196F3; background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #FF9800; background: #fff3e0; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .test-section { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .test-pass { background: #e8f5e8; color: #2e7d32; }
        .test-fail { background: #ffebee; color: #c62828; }
        .test-warning { background: #fff3e0; color: #ef6c00; }
        .code-block { background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico de Funcionalidad - Portfolio Plugin</h1>
    
    <?php
    echo '<div class="info">📊 Iniciando diagnóstico de funcionalidad...</div>';
    
    // Test 1: Verificar clases y archivos
    echo '<div class="test-section">';
    echo '<h3>1️⃣ Verificación de Archivos y Clases</h3>';
    
    $plugin_path = plugin_dir_path(__FILE__);
    $required_files = array(
        'includes/class-database.php',
        'includes/class-admin.php',
        'includes/class-frontend.php',
        'includes/class-logger.php',
        'assets/js/admin.js',
        'assets/css/admin.css',
        'templates/admin-projects.php',
        'templates/admin-settings.php'
    );
    
    $missing_files = array();
    foreach ($required_files as $file) {
        if (!file_exists($plugin_path . $file)) {
            $missing_files[] = $file;
        }
    }
    
    if (empty($missing_files)) {
        echo '<div class="test-result test-pass">✅ Todos los archivos requeridos existen</div>';
    } else {
        echo '<div class="test-result test-fail">❌ Archivos faltantes: ' . implode(', ', $missing_files) . '</div>';
    }
    
    // Verificar clases
    $required_classes = array(
        'Sabsfe_Portfolio_Database',
        'Sabsfe_Portfolio_Admin',
        'Sabsfe_Portfolio_Frontend',
        'Sabsfe_Portfolio_Logger'
    );
    
    $missing_classes = array();
    foreach ($required_classes as $class) {
        if (!class_exists($class)) {
            $missing_classes[] = $class;
        }
    }
    
    if (empty($missing_classes)) {
        echo '<div class="test-result test-pass">✅ Todas las clases están disponibles</div>';
    } else {
        echo '<div class="test-result test-fail">❌ Clases faltantes: ' . implode(', ', $missing_classes) . '</div>';
    }
    echo '</div>';
    
    // Test 2: Verificar base de datos
    echo '<div class="test-section">';
    echo '<h3>2️⃣ Verificación de Base de Datos</h3>';
    
    global $wpdb;
    $tables = array(
        'sabsfe_portfolio_categories',
        'sabsfe_portfolio_projects',
        'sabsfe_portfolio_project_views',
        'sabsfe_portfolio_project_likes'
    );
    
    $db_issues = array();
    foreach ($tables as $table) {
        $exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'");
        if (!$exists) {
            $db_issues[] = $table;
        }
    }
    
    if (empty($db_issues)) {
        echo '<div class="test-result test-pass">✅ Todas las tablas de la base de datos existen</div>';
        
        // Verificar datos de ejemplo
        $categories_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}sabsfe_portfolio_categories");
        $projects_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}sabsfe_portfolio_projects");
        
        echo '<div class="test-result test-pass">✅ Categorías: ' . $categories_count . ', Proyectos: ' . $projects_count . '</div>';
    } else {
        echo '<div class="test-result test-fail">❌ Tablas faltantes: ' . implode(', ', $db_issues) . '</div>';
    }
    echo '</div>';
    
    // Test 3: Verificar opciones del plugin
    echo '<div class="test-section">';
    echo '<h3>3️⃣ Verificación de Configuración</h3>';
    
    $options = get_option('sabsfe_portfolio_plugin_options', array());
    $default_options = array(
        'portfolio_items_per_page' => 12,
        'portfolio_enable_modal' => true,
        'portfolio_theme' => 'dark',
        'portfolio_columns' => 3
    );
    
    $missing_options = array();
    foreach ($default_options as $key => $default_value) {
        if (!isset($options[$key])) {
            $missing_options[] = $key;
        }
    }
    
    if (empty($missing_options)) {
        echo '<div class="test-result test-pass">✅ Todas las opciones de configuración están presentes</div>';
        echo '<pre>';
        foreach ($options as $key => $value) {
            echo "{$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
        }
        echo '</pre>';
    } else {
        echo '<div class="test-result test-fail">❌ Opciones faltantes: ' . implode(', ', $missing_options) . '</div>';
    }
    echo '</div>';
    
    // Test 4: Verificar AJAX hooks
    echo '<div class="test-section">';
    echo '<h3>4️⃣ Verificación de Hooks AJAX</h3>';
    
    $ajax_hooks = array(
        'wp_ajax_sabsfe_portfolio_save_project',
        'wp_ajax_sabsfe_portfolio_delete_project',
        'wp_ajax_sabsfe_portfolio_save_category',
        'wp_ajax_sabsfe_portfolio_delete_category',
        'wp_ajax_sabsfe_portfolio_upload_image',
        'wp_ajax_sabsfe_portfolio_get_project_for_edit',
        'wp_ajax_sabsfe_portfolio_create_tables'
    );
    
    $registered_hooks = array();
    foreach ($ajax_hooks as $hook) {
        if (has_action($hook)) {
            $registered_hooks[] = $hook;
        }
    }
    
    if (count($registered_hooks) === count($ajax_hooks)) {
        echo '<div class="test-result test-pass">✅ Todos los hooks AJAX están registrados</div>';
    } else {
        echo '<div class="test-result test-warning">⚠️ Solo ' . count($registered_hooks) . ' de ' . count($ajax_hooks) . ' hooks están registrados</div>';
        $missing_hooks = array_diff($ajax_hooks, $registered_hooks);
        echo '<div class="code-block">Hooks faltantes: ' . implode(', ', $missing_hooks) . '</div>';
    }
    echo '</div>';
    
    // Test 5: Verificar scripts y estilos
    echo '<div class="test-section">';
    echo '<h3>5️⃣ Verificación de Scripts y Estilos</h3>';
    
    $assets_path = $plugin_path . 'assets/';
    
    if (is_dir($assets_path . 'js') && is_dir($assets_path . 'css')) {
        echo '<div class="test-result test-pass">✅ Directorios de assets existen</div>';
        
        $js_files = array('admin.js', 'frontend.js');
        $css_files = array('admin.css', 'frontend.css', 'modal-styles.css');
        
        $missing_assets = array();
        foreach ($js_files as $file) {
            if (!file_exists($assets_path . 'js/' . $file)) {
                $missing_assets[] = 'js/' . $file;
            }
        }
        foreach ($css_files as $file) {
            if (!file_exists($assets_path . 'css/' . $file)) {
                $missing_assets[] = 'css/' . $file;
            }
        }
        
        if (empty($missing_assets)) {
            echo '<div class="test-result test-pass">✅ Todos los archivos de assets existen</div>';
        } else {
            echo '<div class="test-result test-fail">❌ Assets faltantes: ' . implode(', ', $missing_assets) . '</div>';
        }
    } else {
        echo '<div class="test-result test-fail">❌ Directorios de assets no existen</div>';
    }
    echo '</div>';
    
    // Test 6: Verificar permisos
    echo '<div class="test-section">';
    echo '<h3>6️⃣ Verificación de Permisos</h3>';
    
    $upload_dir = wp_upload_dir();
    $plugin_upload_dir = $upload_dir['basedir'] . '/portfolio-plugin/';
    
    if (!is_dir($plugin_upload_dir)) {
        wp_mkdir_p($plugin_upload_dir);
    }
    
    if (is_writable($plugin_upload_dir)) {
        echo '<div class="test-result test-pass">✅ Directorio de uploads es escribible</div>';
    } else {
        echo '<div class="test-result test-fail">❌ Directorio de uploads no es escribible: ' . $plugin_upload_dir . '</div>';
    }
    
    if (current_user_can('upload_files')) {
        echo '<div class="test-result test-pass">✅ Usuario actual puede subir archivos</div>';
    } else {
        echo '<div class="test-result test-fail">❌ Usuario actual no puede subir archivos</div>';
    }
    echo '</div>';
    
    // Test 7: Verificar JavaScript
    echo '<div class="test-section">';
    echo '<h3>7️⃣ Verificación de JavaScript</h3>';
    
    $admin_js_path = $plugin_path . 'assets/js/admin.js';
    if (file_exists($admin_js_path)) {
        $js_content = file_get_contents($admin_js_path);
        
        $js_functions = array(
            'saveProject',
            'openProjectModal',
            'initImageUpload',
            'initGalleryUpload'
        );
        
        $missing_functions = array();
        foreach ($js_functions as $function) {
            if (strpos($js_content, 'function ' . $function) === false) {
                $missing_functions[] = $function;
            }
        }
        
        if (empty($missing_functions)) {
            echo '<div class="test-result test-pass">✅ Todas las funciones JavaScript requeridas existen</div>';
        } else {
            echo '<div class="test-result test-fail">❌ Funciones JavaScript faltantes: ' . implode(', ', $missing_functions) . '</div>';
        }
    } else {
        echo '<div class="test-result test-fail">❌ Archivo admin.js no existe</div>';
    }
    echo '</div>';
    
    // Resumen y recomendaciones
    echo '<div class="test-section">';
    echo '<h3>📋 Resumen y Recomendaciones</h3>';
    
    $total_tests = 7;
    $passed_tests = 0;
    
    if (empty($missing_files) && empty($missing_classes)) $passed_tests++;
    if (empty($db_issues)) $passed_tests++;
    if (empty($missing_options)) $passed_tests++;
    if (count($registered_hooks) === count($ajax_hooks)) $passed_tests++;
    if (empty($missing_assets)) $passed_tests++;
    if (is_writable($plugin_upload_dir) && current_user_can('upload_files')) $passed_tests++;
    if (empty($missing_functions)) $passed_tests++;
    
    echo '<div class="info">📊 Resultado: ' . $passed_tests . ' de ' . $total_tests . ' tests pasaron</div>';
    
    if ($passed_tests === $total_tests) {
        echo '<div class="success">🎉 ¡Todos los tests pasaron! El plugin debería funcionar correctamente.</div>';
        echo '<h4>Si aún tienes problemas:</h4>';
        echo '<ul>';
        echo '<li>Verifica la consola del navegador para errores JavaScript</li>';
        echo '<li>Revisa los logs de error de WordPress</li>';
        echo '<li>Prueba en un navegador diferente</li>';
        echo '<li>Desactiva otros plugins temporalmente</li>';
        echo '</ul>';
    } else {
        echo '<div class="warning">⚠️ Algunos tests fallaron. Recomendaciones:</div>';
        echo '<ul>';
        
        if (!empty($missing_files) || !empty($missing_classes)) {
            echo '<li><strong>Archivos/Clases faltantes:</strong> Verifica que todos los archivos estén subidos correctamente</li>';
        }
        
        if (!empty($db_issues)) {
            echo '<li><strong>Base de datos:</strong> Usa el script force-create-tables.php para crear las tablas</li>';
        }
        
        if (!empty($missing_options)) {
            echo '<li><strong>Configuración:</strong> Ve a Portfolio > Configuración y guarda la configuración</li>';
        }
        
        if (count($registered_hooks) < count($ajax_hooks)) {
            echo '<li><strong>Hooks AJAX:</strong> Desactiva y reactiva el plugin</li>';
        }
        
        if (!empty($missing_assets)) {
            echo '<li><strong>Assets faltantes:</strong> Verifica que los archivos CSS/JS estén presentes</li>';
        }
        
        if (!is_writable($plugin_upload_dir) || !current_user_can('upload_files')) {
            echo '<li><strong>Permisos:</strong> Contacta al administrador del sitio para verificar permisos</li>';
        }
        
        if (!empty($missing_functions)) {
            echo '<li><strong>JavaScript:</strong> Verifica que el archivo admin.js esté completo</li>';
        }
        
        echo '</ul>';
    }
    echo '</div>';
    ?>
    
    <hr>
    <div class="warning">
        <strong>⚠️ IMPORTANTE:</strong> Elimina este archivo después de usar por seguridad.
    </div>
    <p><a href="<?php echo admin_url('plugins.php'); ?>">← Volver a Plugins</a></p>
</body>
</html>

