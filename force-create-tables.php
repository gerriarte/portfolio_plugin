<?php
/**
 * Script robusto para crear tablas usando SQL directo
 * 
 * INSTRUCCIONES:
 * 1. Sube este archivo a la carpeta raíz de tu plugin
 * 2. Accede a: http://tudominio.com/wp-content/plugins/portfolio-plugin/force-create-tables.php
 * 3. Una vez que veas el mensaje de éxito, elimina este archivo
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
    <title>Forzar Creación de Tablas - Portfolio Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: #4CAF50; background: #f1f8e9; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: #f44336; background: #ffebee; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #2196F3; background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #FF9800; background: #fff3e0; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .table-status { display: inline-block; margin: 5px 10px; padding: 5px 10px; border-radius: 3px; }
        .exists { background: #e8f5e8; color: #2e7d32; }
        .missing { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <h1>🔧 Forzar Creación de Tablas - Portfolio Plugin</h1>
    
    <?php
    try {
        echo '<div class="info">🔄 Iniciando creación forzada de tablas...</div>';
        
        global $wpdb;
        
        // Información de la base de datos
        echo '<div class="info">📊 Información de Base de Datos:</div>';
        echo '<pre>';
        echo "Prefijo de tablas: {$wpdb->prefix}\n";
        echo "Charset: " . $wpdb->get_charset_collate() . "\n";
        echo "Versión MySQL: " . $wpdb->get_var("SELECT VERSION()") . "\n";
        echo '</pre>';
        
        // Definir las tablas a crear
        $tables = array();
        
        // Tabla de categorías
        $tables['sabsfe_portfolio_categories'] = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}sabsfe_portfolio_categories` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `description` text,
                `color` varchar(7) DEFAULT '#2196F3',
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        // Tabla de proyectos
        $tables['sabsfe_portfolio_projects'] = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}sabsfe_portfolio_projects` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `description` text,
                `featured_image` varchar(500),
                `gallery` text,
                `category_id` int(11),
                `status` varchar(20) DEFAULT 'published',
                `featured` tinyint(1) DEFAULT 0,
                `views` int(11) DEFAULT 0,
                `likes` int(11) DEFAULT 0,
                `external_url` varchar(500),
                `youtube_url` varchar(500),
                `vimeo_url` varchar(500),
                `project_year` varchar(4),
                `project_date` date,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `category_id` (`category_id`),
                KEY `status` (`status`),
                KEY `featured` (`featured`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        // Tabla de vistas
        $tables['sabsfe_portfolio_project_views'] = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}sabsfe_portfolio_project_views` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `project_id` int(11) NOT NULL,
                `ip_address` varchar(45),
                `user_agent` text,
                `viewed_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `viewed_at` (`viewed_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        // Tabla de likes
        $tables['sabsfe_portfolio_project_likes'] = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}sabsfe_portfolio_project_likes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `project_id` int(11) NOT NULL,
                `ip_address` varchar(45),
                `liked_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `project_ip` (`project_id`, `ip_address`),
                KEY `project_id` (`project_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        echo '<div class="info">📋 Creando tablas...</div>';
        
        $results = array();
        foreach ($tables as $table_name => $sql) {
            echo "<div class='info'>🔨 Creando tabla: {$table_name}</div>";
            
            // Ejecutar SQL directo
            $result = $wpdb->query($sql);
            
            if ($result !== false) {
                $results[$table_name] = 'success';
                echo "<div class='success'>✅ Tabla {$table_name} creada exitosamente</div>";
            } else {
                $results[$table_name] = 'error';
                echo "<div class='error'>❌ Error al crear tabla {$table_name}: " . $wpdb->last_error . "</div>";
            }
        }
        
        echo '<h3>📋 Verificación Final de Tablas:</h3>';
        echo '<div>';
        
        foreach ($tables as $table_name => $sql) {
            $full_table_name = $wpdb->prefix . $table_name;
            $exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table_name}'");
            
            if ($exists) {
                $count = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}{$table_name}`");
                echo "<span class='table-status exists'>✅ {$table_name}: {$count} registros</span>";
            } else {
                echo "<span class='table-status missing'>❌ {$table_name}: NO EXISTE</span>";
            }
        }
        
        echo '</div>';
        
        // Verificar si todas las tablas se crearon
        $all_created = true;
        foreach ($tables as $table_name => $sql) {
            $exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table_name}'");
            if (!$exists) {
                $all_created = false;
                break;
            }
        }
        
        if ($all_created) {
            echo '<div class="success">🎉 ¡Todas las tablas se crearon exitosamente!</div>';
            
            // Insertar datos de ejemplo si las tablas están vacías
            echo '<div class="info">📝 Insertando datos de ejemplo...</div>';
            
            // Insertar categorías por defecto
            $categories_table = $wpdb->prefix . 'sabsfe_portfolio_categories';
            $existing_categories = $wpdb->get_var("SELECT COUNT(*) FROM `{$categories_table}`");
            
            if ($existing_categories == 0) {
                $default_categories = array(
                    array('name' => 'Web Design', 'slug' => 'web-design', 'description' => 'Proyectos de diseño web', 'color' => '#2196F3'),
                    array('name' => 'Mobile App', 'slug' => 'mobile-app', 'description' => 'Aplicaciones móviles', 'color' => '#4CAF50'),
                    array('name' => 'UI/UX', 'slug' => 'ui-ux', 'description' => 'Diseño de interfaz y experiencia de usuario', 'color' => '#FF9800'),
                    array('name' => 'Branding', 'slug' => 'branding', 'description' => 'Identidad corporativa y branding', 'color' => '#9C27B0')
                );
                
                foreach ($default_categories as $category) {
                    $wpdb->insert($categories_table, $category);
                }
                echo '<div class="success">✅ Categorías de ejemplo insertadas</div>';
            } else {
                echo '<div class="info">ℹ️ Las categorías ya existen, omitiendo inserción</div>';
            }
            
            // Insertar proyecto de ejemplo
            $projects_table = $wpdb->prefix . 'sabsfe_portfolio_projects';
            $existing_projects = $wpdb->get_var("SELECT COUNT(*) FROM `{$projects_table}`");
            
            if ($existing_projects == 0) {
                $sample_project = array(
                    'title' => 'Proyecto de Ejemplo',
                    'slug' => 'proyecto-ejemplo',
                    'description' => 'Este es un proyecto de ejemplo que demuestra las capacidades del plugin Portfolio.',
                    'category_id' => 1,
                    'status' => 'published',
                    'featured' => 1,
                    'external_url' => 'https://ejemplo.com',
                    'project_year' => date('Y'),
                    'project_date' => date('Y-m-d')
                );
                
                $wpdb->insert($projects_table, $sample_project);
                echo '<div class="success">✅ Proyecto de ejemplo insertado</div>';
            } else {
                echo '<div class="info">ℹ️ Los proyectos ya existen, omitiendo inserción</div>';
            }
            
            echo '<div class="success">🚀 ¡El plugin está completamente listo para usar!</div>';
            
        } else {
            echo '<div class="error">❌ Algunas tablas no se pudieron crear. Revisa los errores anteriores.</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="error">❌ Error: ' . esc_html($e->getMessage()) . '</div>';
        
        // Mostrar información de debug
        global $wpdb;
        echo '<h3>🔍 Información de Debug:</h3>';
        echo '<pre>';
        echo "Última consulta: " . $wpdb->last_query . "\n";
        echo "Último error: " . ($wpdb->last_error ?: 'Ninguno') . "\n";
        echo "Charset: " . $wpdb->get_charset_collate() . "\n";
        echo '</pre>';
    }
    ?>
    
    <hr>
    <div class="warning">
        <strong>⚠️ IMPORTANTE:</strong> 
        <ul>
            <li>Elimina este archivo después de ejecutarlo por seguridad.</li>
            <li>Si persisten los errores, contacta a tu proveedor de hosting.</li>
            <li>Verifica que el usuario de la base de datos tenga permisos CREATE TABLE.</li>
        </ul>
    </div>
    <p><a href="<?php echo admin_url('plugins.php'); ?>">← Volver a Plugins</a></p>
</body>
</html>