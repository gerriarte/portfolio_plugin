<?php
/**
 * Clase mejorada para manejo de base de datos del plugin Portfolio
 * Incluye métodos alternativos para crear tablas
 * 
 * @package Sabsfe_Portfolio
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sabsfe_Portfolio_Database_Enhanced {
    
    /**
     * Crear tablas usando SQL directo (más robusto)
     * 
     * @return bool True si se crearon exitosamente, false en caso contrario
     */
    public static function create_tables_direct() {
        global $wpdb;
        
        try {
            echo '<div style="padding: 10px; background: #e3f2fd; margin: 10px 0;">🔄 Creando tablas con SQL directo...</div>';
            
            // Definir las tablas
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
            
            $success_count = 0;
            $total_tables = count($tables);
            
            foreach ($tables as $table_name => $sql) {
                echo "<div style='padding: 5px; margin: 5px 0;'>🔨 Creando: {$table_name}</div>";
                
                // Ejecutar SQL directo
                $result = $wpdb->query($sql);
                
                if ($result !== false) {
                    $success_count++;
                    echo "<div style='color: #4CAF50; padding: 5px;'>✅ {$table_name} creada</div>";
                } else {
                    echo "<div style='color: #f44336; padding: 5px;'>❌ Error en {$table_name}: " . $wpdb->last_error . "</div>";
                }
            }
            
            if ($success_count === $total_tables) {
                echo '<div style="color: #4CAF50; background: #e8f5e8; padding: 15px; margin: 10px 0;">🎉 ¡Todas las tablas creadas exitosamente!</div>';
                
                // Insertar datos de ejemplo
                self::insert_sample_data();
                
                return true;
            } else {
                echo '<div style="color: #f44336; background: #ffebee; padding: 15px; margin: 10px 0;">❌ Solo se crearon ' . $success_count . ' de ' . $total_tables . ' tablas</div>';
                return false;
            }
            
        } catch (Exception $e) {
            echo '<div style="color: #f44336; background: #ffebee; padding: 15px; margin: 10px 0;">❌ Error: ' . $e->getMessage() . '</div>';
            return false;
        }
    }
    
    /**
     * Insertar datos de ejemplo
     */
    private static function insert_sample_data() {
        global $wpdb;
        
        echo '<div style="padding: 10px; background: #fff3e0; margin: 10px 0;">📝 Insertando datos de ejemplo...</div>';
        
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
            echo '<div style="color: #4CAF50;">✅ Categorías de ejemplo insertadas</div>';
        } else {
            echo '<div style="color: #2196F3;">ℹ️ Las categorías ya existen</div>';
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
            echo '<div style="color: #4CAF50;">✅ Proyecto de ejemplo insertado</div>';
        } else {
            echo '<div style="color: #2196F3;">ℹ️ Los proyectos ya existen</div>';
        }
    }
    
    /**
     * Verificar estado de las tablas
     */
    public static function check_tables_status() {
        global $wpdb;
        
        $tables = array(
            'sabsfe_portfolio_categories',
            'sabsfe_portfolio_projects',
            'sabsfe_portfolio_project_views',
            'sabsfe_portfolio_project_likes'
        );
        
        $status = array();
        
        foreach ($tables as $table) {
            $exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'");
            
            if ($exists) {
                $count = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}{$table}`");
                $status[$table] = array(
                    'exists' => true,
                    'count' => $count,
                    'full_name' => $wpdb->prefix . $table
                );
            } else {
                $status[$table] = array(
                    'exists' => false,
                    'count' => 0,
                    'full_name' => $wpdb->prefix . $table
                );
            }
        }
        
        return $status;
    }
    
    /**
     * Mostrar información de debug de la base de datos
     */
    public static function show_debug_info() {
        global $wpdb;
        
        echo '<div style="background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 4px;">';
        echo '<h4>🔍 Información de Debug:</h4>';
        echo '<pre>';
        echo "Prefijo de tablas: {$wpdb->prefix}\n";
        echo "Charset: " . $wpdb->get_charset_collate() . "\n";
        echo "Versión MySQL: " . $wpdb->get_var("SELECT VERSION()") . "\n";
        echo "Última consulta: " . $wpdb->last_query . "\n";
        echo "Último error: " . ($wpdb->last_error ?: 'Ninguno') . "\n";
        echo '</pre>';
        echo '</div>';
    }
}

