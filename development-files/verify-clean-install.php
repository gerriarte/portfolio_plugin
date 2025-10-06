<?php
/**
 * Script de Verificación de Instalación Limpia - Plugin Portfolio
 * 
 * Este script verifica que el plugin esté correctamente instalado
 * y funcione desde cero en una nueva instalación.
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🔍 Verificación de Instalación Limpia - Plugin Portfolio</h2>';

// Verificar que el plugin esté activo
if (!class_exists('PortfolioPlugin')) {
    echo '<p style="color: red;">❌ El plugin Portfolio no está activo</p>';
    echo '<p><strong>Solución:</strong> Activa el plugin desde Plugins > Plugins Instalados</p>';
    exit;
}

echo '<p style="color: green;">✅ Plugin Portfolio está activo</p>';

// Verificar versión de WordPress
$wp_version = get_bloginfo('version');
if (version_compare($wp_version, '5.0', '<')) {
    echo '<p style="color: red;">❌ WordPress ' . $wp_version . ' - Se requiere WordPress 5.0+</p>';
} else {
    echo '<p style="color: green;">✅ WordPress ' . $wp_version . ' - Versión compatible</p>';
}

// Verificar clases del plugin
echo '<h3>📋 Verificación de Clases del Plugin</h3>';
$required_classes = [
    'PortfolioPlugin' => 'Clase principal',
    'PortfolioDatabase' => 'Gestión de base de datos',
    'PortfolioAdmin' => 'Panel de administración',
    'PortfolioFrontend' => 'Frontend',
    'PortfolioElementorWidget' => 'Widget de Elementor',
    'PortfolioAPI' => 'API REST',
    'PortfolioLogger' => 'Sistema de logging'
];

foreach ($required_classes as $class => $description) {
    if (class_exists($class)) {
        echo "<p style='color: green;'>✅ $class - $description</p>";
    } else {
        echo "<p style='color: red;'>❌ $class - $description NO encontrada</p>";
    }
}

// Verificar tablas de base de datos
echo '<h3>🗄️ Verificación de Base de Datos</h3>';
global $wpdb;

$required_tables = [
    'portfolio_categories' => 'Categorías de proyectos',
    'portfolio_projects' => 'Proyectos',
    'portfolio_project_views' => 'Vistas de proyectos',
    'portfolio_project_likes' => 'Likes de proyectos'
];

$all_tables_exist = true;
foreach ($required_tables as $table => $description) {
    $full_table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'");
    
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $full_table_name");
        echo "<p style='color: green;'>✅ $table - $description ($count registros)</p>";
    } else {
        echo "<p style='color: red;'>❌ $table - $description NO existe</p>";
        $all_tables_exist = false;
    }
}

if (!$all_tables_exist) {
    echo '<p style="color: red;">❌ Faltan tablas de la base de datos</p>';
    echo '<p><strong>Solución:</strong> Desactiva y reactiva el plugin para crear las tablas</p>';
}

// Verificar datos de ejemplo
echo '<h3>📝 Verificación de Datos de Ejemplo</h3>';

$categories_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_categories");
if ($categories_count > 0) {
    echo "<p style='color: green;'>✅ Categorías creadas ($categories_count)</p>";
    
    // Mostrar categorías
    $categories = $wpdb->get_results("SELECT name, color FROM {$wpdb->prefix}portfolio_categories ORDER BY name");
    echo '<ul>';
    foreach ($categories as $cat) {
        echo "<li style='color: {$cat->color};'>• {$cat->name}</li>";
    }
    echo '</ul>';
} else {
    echo '<p style="color: red;">❌ No hay categorías en la base de datos</p>';
}

$projects_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_projects");
if ($projects_count > 0) {
    echo "<p style='color: green;'>✅ Proyectos creados ($projects_count)</p>";
    
    // Mostrar proyectos
    $projects = $wpdb->get_results("SELECT title, status FROM {$wpdb->prefix}portfolio_projects ORDER BY created_at DESC LIMIT 5");
    echo '<ul>';
    foreach ($projects as $project) {
        $status_color = $project->status === 'published' ? 'green' : 'orange';
        echo "<li style='color: $status_color;'>• {$project->title} ({$project->status})</li>";
    }
    echo '</ul>';
} else {
    echo '<p style="color: red;">❌ No hay proyectos en la base de datos</p>';
}

// Verificar opciones del plugin
echo '<h3>⚙️ Verificación de Configuración</h3>';
$plugin_options = get_option('portfolio_plugin_options', array());
if (!empty($plugin_options)) {
    echo '<p style="color: green;">✅ Opciones del plugin configuradas</p>';
    echo '<ul>';
    foreach ($plugin_options as $key => $value) {
        $display_value = is_bool($value) ? ($value ? 'Sí' : 'No') : $value;
        echo "<li><strong>$key:</strong> $display_value</li>";
    }
    echo '</ul>';
} else {
    echo '<p style="color: red;">❌ Opciones del plugin no configuradas</p>';
}

// Verificar archivos del plugin
echo '<h3>📁 Verificación de Archivos</h3>';
$required_files = [
    'portfolio-plugin.php' => 'Archivo principal',
    'uninstall.php' => 'Script de desinstalación',
    'config.php' => 'Configuraciones',
    'admin/projects.php' => 'Gestión de proyectos',
    'admin/categories.php' => 'Gestión de categorías',
    'assets/css/admin.css' => 'Estilos del admin',
    'assets/css/frontend.css' => 'Estilos del frontend',
    'assets/js/admin.js' => 'Scripts del admin',
    'assets/js/frontend.js' => 'Scripts del frontend',
    'includes/class-database.php' => 'Clase de base de datos',
    'includes/class-admin.php' => 'Clase de administración',
    'includes/class-elementor-widget.php' => 'Widget de Elementor'
];

$plugin_path = WP_PLUGIN_DIR . '/portfolio-plugin/';
foreach ($required_files as $file => $description) {
    $file_path = $plugin_path . $file;
    if (file_exists($file_path)) {
        $size = filesize($file_path);
        echo "<p style='color: green;'>✅ $file - $description (" . round($size/1024, 1) . " KB)</p>";
    } else {
        echo "<p style='color: red;'>❌ $file - $description NO encontrado</p>";
    }
}

// Verificar Elementor
echo '<h3>🎨 Verificación de Elementor</h3>';
if (class_exists('\Elementor\Plugin')) {
    echo '<p style="color: green;">✅ Elementor está instalado y activo</p>';
    
    // Verificar si el widget está registrado
    if (class_exists('PortfolioElementorWidget')) {
        echo '<p style="color: green;">✅ Widget Portfolio Grid disponible en Elementor</p>';
    } else {
        echo '<p style="color: red;">❌ Widget Portfolio Grid no disponible</p>';
    }
} else {
    echo '<p style="color: orange;">⚠️ Elementor no está instalado (opcional)</p>';
}

// Verificar permisos
echo '<h3>👤 Verificación de Permisos</h3>';
if (current_user_can('manage_options')) {
    echo '<p style="color: green;">✅ Usuario tiene permisos de administrador</p>';
} else {
    echo '<p style="color: red;">❌ Usuario no tiene permisos de administrador</p>';
}

// Verificar configuración de WordPress
echo '<h3>🌐 Configuración de WordPress</h3>';
echo '<ul>';
echo '<li><strong>Prefijo de tablas:</strong> ' . $wpdb->prefix . '</li>';
echo '<li><strong>Charset de BD:</strong> ' . $wpdb->charset . '</li>';
echo '<li><strong>Collate:</strong> ' . $wpdb->collate . '</li>';
echo '<li><strong>WP_DEBUG:</strong> ' . (defined('WP_DEBUG') && WP_DEBUG ? 'Activado' : 'Desactivado') . '</li>';
echo '<li><strong>URL del sitio:</strong> ' . get_site_url() . '</li>';
echo '<li><strong>URL de admin:</strong> ' . admin_url() . '</li>';
echo '</ul>';

// Resumen final
echo '<h3>📊 Resumen de la Instalación</h3>';

$issues = [];
if (!class_exists('PortfolioPlugin')) $issues[] = 'Plugin no activo';
if (!$all_tables_exist) $issues[] = 'Tablas faltantes';
if ($categories_count == 0) $issues[] = 'Sin categorías';
if ($projects_count == 0) $issues[] = 'Sin proyectos';
if (empty($plugin_options)) $issues[] = 'Sin configuración';

if (empty($issues)) {
    echo '<div style="background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724;">';
    echo '<h4 style="margin-top: 0;">🎉 ¡Instalación Perfecta!</h4>';
    echo '<p>El plugin Portfolio está correctamente instalado y configurado.</p>';
    echo '<p><strong>Próximos pasos:</strong></p>';
    echo '<ul>';
    echo '<li>Ve a <strong>Portfolio > Proyectos</strong> para gestionar tu portafolio</li>';
    echo '<li>Ve a <strong>Portfolio > Categorías</strong> para organizar proyectos</li>';
    echo '<li>Usa el widget <strong>"Portfolio Grid"</strong> en Elementor</li>';
    echo '<li>Personaliza la configuración en <strong>Portfolio > Configuración</strong></li>';
    echo '</ul>';
    echo '</div>';
} else {
    echo '<div style="background: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24;">';
    echo '<h4 style="margin-top: 0;">⚠️ Problemas Detectados</h4>';
    echo '<p>Se encontraron los siguientes problemas:</p>';
    echo '<ul>';
    foreach ($issues as $issue) {
        echo "<li>$issue</li>";
    }
    echo '</ul>';
    echo '<p><strong>Soluciones:</strong></p>';
    echo '<ul>';
    echo '<li>Desactiva y reactiva el plugin</li>';
    echo '<li>Revisa los logs de error de WordPress</li>';
    echo '<li>Verifica permisos de la base de datos</li>';
    echo '<li>Contacta al administrador del sitio</li>';
    echo '</ul>';
    echo '</div>';
}

echo '<p style="margin-top: 30px; text-align: center; color: #666;">';
echo 'Verificación completada el ' . date('Y-m-d H:i:s');
echo '</p>';
