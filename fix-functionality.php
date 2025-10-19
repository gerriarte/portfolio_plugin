<?php
/**
 * Script para corregir problemas de funcionalidad del plugin
 * 
 * INSTRUCCIONES:
 * 1. Sube este archivo a la carpeta raíz de tu plugin
 * 2. Acceder a: http://tudominio.com/wp-content/plugins/portfolio-plugin/fix-functionality.php
 * 3. Seguir las instrucciones mostradas
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
    <title>Corrección de Funcionalidad - Portfolio Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: #4CAF50; background: #f1f8e9; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: #f44336; background: #ffebee; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #2196F3; background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #FF9800; background: #fff3e0; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .fix-section { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .fix-result { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .fix-success { background: #e8f5e8; color: #2e7d32; }
        .fix-error { background: #ffebee; color: #c62828; }
        .fix-warning { background: #fff3e0; color: #ef6c00; }
        .code-block { background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
        .button { display: inline-block; padding: 10px 20px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>🔧 Corrección de Funcionalidad - Portfolio Plugin</h1>
    
    <?php
    echo '<div class="info">🔄 Iniciando correcciones automáticas...</div>';
    
    $fixes_applied = array();
    $fixes_failed = array();
    
    // Fix 1: Corregir opciones de configuración
    echo '<div class="fix-section">';
    echo '<h3>1️⃣ Corrigiendo Configuración del Plugin</h3>';
    
    try {
        $options = get_option('sabsfe_portfolio_plugin_options', array());
        $default_options = array(
            'portfolio_items_per_page' => 12,
            'portfolio_enable_modal' => true,
            'portfolio_theme' => 'dark',
            'portfolio_columns' => 3,
            'portfolio_enable_views' => true,
            'portfolio_enable_likes' => true,
            'portfolio_enable_sharing' => true,
            'portfolio_carousel_autoplay' => false,
            'portfolio_carousel_speed' => 3000,
            'portfolio_show_categories' => true,
            'portfolio_show_dates' => true,
            'portfolio_show_stats' => true
        );
        
        $updated_options = array_merge($default_options, $options);
        update_option('sabsfe_portfolio_plugin_options', $updated_options);
        
        echo '<div class="fix-result fix-success">✅ Configuración del plugin corregida</div>';
        $fixes_applied[] = 'Configuración del plugin';
        
    } catch (Exception $e) {
        echo '<div class="fix-result fix-error">❌ Error al corregir configuración: ' . $e->getMessage() . '</div>';
        $fixes_failed[] = 'Configuración del plugin';
    }
    echo '</div>';
    
    // Fix 2: Verificar y corregir permisos de upload
    echo '<div class="fix-section">';
    echo '<h3>2️⃣ Corrigiendo Permisos de Upload</h3>';
    
    try {
        $upload_dir = wp_upload_dir();
        $plugin_upload_dir = $upload_dir['basedir'] . '/portfolio-plugin/';
        
        if (!is_dir($plugin_upload_dir)) {
            wp_mkdir_p($plugin_upload_dir);
            echo '<div class="fix-result fix-success">✅ Directorio de uploads creado: ' . $plugin_upload_dir . '</div>';
        }
        
        // Crear archivo .htaccess para proteger el directorio
        $htaccess_file = $plugin_upload_dir . '.htaccess';
        if (!file_exists($htaccess_file)) {
            $htaccess_content = "Options -Indexes\nDeny from all\n<Files ~ \"\\.(jpg|jpeg|png|gif|webp)$\">\nAllow from all\n</Files>";
            file_put_contents($htaccess_file, $htaccess_content);
            echo '<div class="fix-result fix-success">✅ Archivo .htaccess creado para proteger uploads</div>';
        }
        
        // Crear archivo index.php para proteger el directorio
        $index_file = $plugin_upload_dir . 'index.php';
        if (!file_exists($index_file)) {
            file_put_contents($index_file, '<?php // Silence is golden');
            echo '<div class="fix-result fix-success">✅ Archivo index.php creado para proteger directorio</div>';
        }
        
        echo '<div class="fix-result fix-success">✅ Permisos de upload corregidos</div>';
        $fixes_applied[] = 'Permisos de upload';
        
    } catch (Exception $e) {
        echo '<div class="fix-result fix-error">❌ Error al corregir permisos: ' . $e->getMessage() . '</div>';
        $fixes_failed[] = 'Permisos de upload';
    }
    echo '</div>';
    
    // Fix 3: Verificar hooks AJAX
    echo '<div class="fix-section">';
    echo '<h3>3️⃣ Verificando Hooks AJAX</h3>';
    
    try {
        // Cargar las clases necesarias
        $plugin_path = plugin_dir_path(__FILE__);
        require_once($plugin_path . 'includes/class-admin.php');
        
        // Verificar que los hooks están registrados
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
            echo '<div class="fix-result fix-success">✅ Todos los hooks AJAX están registrados correctamente</div>';
            $fixes_applied[] = 'Hooks AJAX';
        } else {
            echo '<div class="fix-result fix-warning">⚠️ Algunos hooks AJAX no están registrados. Esto se corregirá al recargar el plugin.</div>';
            $fixes_failed[] = 'Hooks AJAX';
        }
        
    } catch (Exception $e) {
        echo '<div class="fix-result fix-error">❌ Error al verificar hooks AJAX: ' . $e->getMessage() . '</div>';
        $fixes_failed[] = 'Hooks AJAX';
    }
    echo '</div>';
    
    // Fix 4: Limpiar cache si es necesario
    echo '<div class="fix-section">';
    echo '<h3>4️⃣ Limpiando Cache</h3>';
    
    try {
        // Limpiar cache de WordPress
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
            echo '<div class="fix-result fix-success">✅ Cache de WordPress limpiado</div>';
        }
        
        // Limpiar cache de opciones
        wp_cache_delete('sabsfe_portfolio_plugin_options', 'options');
        echo '<div class="fix-result fix-success">✅ Cache de opciones limpiado</div>';
        
        $fixes_applied[] = 'Cache';
        
    } catch (Exception $e) {
        echo '<div class="fix-result fix-error">❌ Error al limpiar cache: ' . $e->getMessage() . '</div>';
        $fixes_failed[] = 'Cache';
    }
    echo '</div>';
    
    // Fix 5: Verificar base de datos
    echo '<div class="fix-section">';
    echo '<h3>5️⃣ Verificando Base de Datos</h3>';
    
    try {
        global $wpdb;
        $tables = array(
            'sabsfe_portfolio_categories',
            'sabsfe_portfolio_projects',
            'sabsfe_portfolio_project_views',
            'sabsfe_portfolio_project_likes'
        );
        
        $missing_tables = array();
        foreach ($tables as $table) {
            $exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'");
            if (!$exists) {
                $missing_tables[] = $table;
            }
        }
        
        if (empty($missing_tables)) {
            echo '<div class="fix-result fix-success">✅ Todas las tablas de la base de datos existen</div>';
            $fixes_applied[] = 'Base de datos';
        } else {
            echo '<div class="fix-result fix-error">❌ Faltan tablas: ' . implode(', ', $missing_tables) . '</div>';
            echo '<div class="fix-result fix-warning">⚠️ Usa el script force-create-tables.php para crear las tablas faltantes</div>';
            $fixes_failed[] = 'Base de datos';
        }
        
    } catch (Exception $e) {
        echo '<div class="fix-result fix-error">❌ Error al verificar base de datos: ' . $e->getMessage() . '</div>';
        $fixes_failed[] = 'Base de datos';
    }
    echo '</div>';
    
    // Resumen
    echo '<div class="fix-section">';
    echo '<h3>📋 Resumen de Correcciones</h3>';
    
    echo '<div class="info">📊 Correcciones aplicadas: ' . count($fixes_applied) . '</div>';
    if (!empty($fixes_applied)) {
        echo '<ul>';
        foreach ($fixes_applied as $fix) {
            echo '<li>✅ ' . $fix . '</li>';
        }
        echo '</ul>';
    }
    
    if (!empty($fixes_failed)) {
        echo '<div class="warning">⚠️ Correcciones fallidas: ' . count($fixes_failed) . '</div>';
        echo '<ul>';
        foreach ($fixes_failed as $fix) {
            echo '<li>❌ ' . $fix . '</li>';
        }
        echo '</ul>';
    }
    
    if (count($fixes_failed) === 0) {
        echo '<div class="success">🎉 ¡Todas las correcciones se aplicaron exitosamente!</div>';
        echo '<h4>Próximos pasos:</h4>';
        echo '<ol>';
        echo '<li>Ve a <strong>Portfolio > Configuración</strong> y guarda la configuración</li>';
        echo '<li>Prueba crear un nuevo proyecto</li>';
        echo '<li>Verifica que la carga de imágenes funcione</li>';
        echo '<li>Configura el número de columnas deseado</li>';
        echo '</ol>';
    } else {
        echo '<div class="warning">⚠️ Algunas correcciones fallaron. Revisa los errores anteriores.</div>';
    }
    echo '</div>';
    
    // Información adicional
    echo '<div class="fix-section">';
    echo '<h3>🔍 Información de Debug</h3>';
    
    global $wpdb;
    echo '<div class="code-block">';
    echo "Versión de WordPress: " . get_bloginfo('version') . "\n";
    echo "Versión de PHP: " . PHP_VERSION . "\n";
    echo "Prefijo de tablas: {$wpdb->prefix}\n";
    echo "Directorio de uploads: " . wp_upload_dir()['basedir'] . "\n";
    echo "Plugin activo: " . (is_plugin_active('portfolio-plugin/portfolio-plugin.php') ? 'Sí' : 'No') . "\n";
    echo '</div>';
    echo '</div>';
    ?>
    
    <hr>
    <div class="warning">
        <strong>⚠️ IMPORTANTE:</strong> 
        <ul>
            <li>Elimina este archivo después de usar por seguridad.</li>
            <li>Si los problemas persisten, desactiva y reactiva el plugin.</li>
            <li>Revisa la consola del navegador para errores JavaScript.</li>
        </ul>
    </div>
    <p>
        <a href="<?php echo admin_url('admin.php?page=portfolio-settings'); ?>" class="button">Ir a Configuración</a>
        <a href="<?php echo admin_url('admin.php?page=portfolio-admin'); ?>" class="button">Ir a Proyectos</a>
        <a href="<?php echo admin_url('plugins.php'); ?>" class="button">Volver a Plugins</a>
    </p>
</body>
</html>

