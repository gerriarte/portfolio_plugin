<?php
/**
 * Clase para manejo de base de datos del plugin Portfolio
 * 
 * @package Sabsfe_Portfolio
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sabsfe_Portfolio_Database {
    
    private static $instance = null;
    
    /**
     * Helper para logging seguro
     * 
     * @param string $level Nivel de log
     * @param string $module Módulo
     * @param string $function Función
     * @param string $message Mensaje
     * @param array $context Contexto
     * @since 1.0.0
     */
    private static function safe_log($level, $module, $function, $message, $context = array()) {
        if (class_exists('Sabsfe_Portfolio_Logger')) {
            Sabsfe_Portfolio_Logger::log($level, $module, $function, $message, $context);
        }
    }
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Constructor privado para singleton
    }
    
    /**
     * Crear tablas de base de datos
     */
    public static function create_tables() {
        global $wpdb;
        
        try {
            $charset_collate = $wpdb->get_charset_collate();
            
            // Verificar que dbDelta esté disponible
            if (!function_exists('dbDelta')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            }
            
        // Tabla de categorías de proyectos
        $table_categories = $wpdb->prefix . 'sabsfe_portfolio_categories';
            $sql_categories = "CREATE TABLE $table_categories (
                id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                slug varchar(255) NOT NULL,
                description text,
                color varchar(7) DEFAULT '#2196F3',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY slug (slug)
            ) $charset_collate;";
            
        // Tabla de proyectos
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
            $sql_projects = "CREATE TABLE $table_projects (
                id int(11) NOT NULL AUTO_INCREMENT,
                title varchar(255) NOT NULL,
                slug varchar(255) NOT NULL,
                description text,
                featured_image varchar(500),
                gallery text,
                category_id int(11),
                status varchar(20) DEFAULT 'published',
                featured tinyint(1) DEFAULT 0,
                views int(11) DEFAULT 0,
                likes int(11) DEFAULT 0,
                external_url varchar(500),
                youtube_url varchar(500),
                vimeo_url varchar(500),
                project_year varchar(4),
                project_date date,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY slug (slug),
                KEY category_id (category_id),
                KEY status (status),
                KEY featured (featured)
            ) $charset_collate;";
            
        // Tabla de vistas de proyectos
        $table_views = $wpdb->prefix . 'sabsfe_portfolio_project_views';
            $sql_views = "CREATE TABLE $table_views (
                id int(11) NOT NULL AUTO_INCREMENT,
                project_id int(11) NOT NULL,
                ip_address varchar(45),
                user_agent text,
                viewed_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY project_id (project_id),
                KEY viewed_at (viewed_at)
            ) $charset_collate;";
            
        // Tabla de likes de proyectos
        $table_likes = $wpdb->prefix . 'sabsfe_portfolio_project_likes';
            $sql_likes = "CREATE TABLE $table_likes (
                id int(11) NOT NULL AUTO_INCREMENT,
                project_id int(11) NOT NULL,
                ip_address varchar(45),
                liked_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY project_ip (project_id, ip_address),
                KEY project_id (project_id)
            ) $charset_collate;";
            
            // Crear todas las tablas usando dbDelta (maneja tablas existentes)
            $result1 = dbDelta($sql_categories);
            $result2 = dbDelta($sql_projects);
            $result3 = dbDelta($sql_views);
            $result4 = dbDelta($sql_likes);
            
            // Verificar errores de MySQL
            if (!empty($wpdb->last_error)) {
                self::safe_log('error', 'database', 'create_tables', 'Error de MySQL: ' . $wpdb->last_error);
                throw new Exception('Error de base de datos: ' . $wpdb->last_error);
            }
            
            // Verificar que las tablas se crearon correctamente
            $tables_created = array(
                'sabsfe_portfolio_categories' => $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}sabsfe_portfolio_categories'"),
                'sabsfe_portfolio_projects' => $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}sabsfe_portfolio_projects'"),
                'sabsfe_portfolio_project_views' => $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}sabsfe_portfolio_project_views'"),
                'sabsfe_portfolio_project_likes' => $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}sabsfe_portfolio_project_likes'")
            );
            
            // Verificar que todas las tablas existen
            $missing_tables = array();
            foreach ($tables_created as $table_name => $exists) {
                if (!$exists) {
                    $missing_tables[] = $table_name;
                }
            }
            
            if (!empty($missing_tables)) {
                self::safe_log('error', 'database', 'create_tables', 'Tablas faltantes: ' . implode(', ', $missing_tables));
                throw new Exception('Error: No se pudieron crear las tablas: ' . implode(', ', $missing_tables));
            }
            
            // Log de creación exitosa
            self::safe_log('info', 'database', 'create_tables', 'Tablas creadas exitosamente', $tables_created);
            
            // Insertar datos solo si es la primera activación
            $is_first_activation = get_option('sabsfe_portfolio_first_activation', true);
            
            if ($is_first_activation) {
                // Insertar categorías por defecto
                self::insert_default_categories();
                
                // Insertar proyecto de ejemplo
                self::insert_sample_project();
                
                // Marcar que ya no es la primera activación
                update_option('sabsfe_portfolio_first_activation', false);
                
                self::safe_log('info', 'database', 'create_tables', 'Primera activación - datos de ejemplo insertados');
            } else {
                self::safe_log('info', 'database', 'create_tables', 'Reactivación - omitiendo inserción de datos');
            }
            
            return true;
            
        } catch (Exception $e) {
            self::safe_log('error', 'database', 'create_tables', 'Error al crear tablas: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Insertar categorías por defecto
     */
    private static function insert_default_categories() {
        global $wpdb;
        
        $table_categories = $wpdb->prefix . 'sabsfe_portfolio_categories';
        
        // Verificar si ya hay categorías
        $existing_categories = $wpdb->get_var("SELECT COUNT(*) FROM $table_categories");
        if ($existing_categories > 0) {
            self::safe_log('info', 'database', 'insert_default_categories', 'Ya hay categorías existentes, omitiendo inserción');
            return; // No insertar si ya hay categorías
        }
        
        $default_categories = array(
            array(
                'name' => 'Web Design',
                'slug' => 'web-design',
                'description' => 'Proyectos de diseño web',
                'color' => '#2196F3'
            ),
            array(
                'name' => 'Mobile App',
                'slug' => 'mobile-app',
                'description' => 'Aplicaciones móviles',
                'color' => '#4CAF50'
            ),
            array(
                'name' => 'UI/UX',
                'slug' => 'ui-ux',
                'description' => 'Diseño de interfaz y experiencia de usuario',
                'color' => '#FF9800'
            ),
            array(
                'name' => 'Branding',
                'slug' => 'branding',
                'description' => 'Identidad corporativa y branding',
                'color' => '#9C27B0'
            )
        );
        
        foreach ($default_categories as $category) {
            // Verificar si la categoría ya existe por slug
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_categories WHERE slug = %s",
                $category['slug']
            ));
            
            if (!$existing) {
                $result = $wpdb->insert($table_categories, $category);
                if ($result) {
                    self::safe_log('info', 'database', 'insert_default_categories', 'Categoría creada: ' . $category['name']);
                } else {
                    self::safe_log('error', 'database', 'insert_default_categories', 'Error al crear categoría: ' . $category['name'] . ' - ' . $wpdb->last_error);
                }
            } else {
                self::safe_log('info', 'database', 'insert_default_categories', 'Categoría ya existe: ' . $category['name']);
            }
        }
    }
    
    /**
     * Insertar proyecto de ejemplo
     */
    private static function insert_sample_project() {
        global $wpdb;
        
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
        
        // Verificar si ya hay proyectos
        $existing_projects = $wpdb->get_var("SELECT COUNT(*) FROM $table_projects");
        if ($existing_projects > 0) {
            return; // No insertar si ya hay proyectos
        }
        
        $sample_project = array(
            'title' => 'Proyecto de Ejemplo',
            'slug' => 'proyecto-ejemplo',
            'description' => 'Este es un proyecto de ejemplo que demuestra las capacidades del plugin Portfolio. Puedes editarlo o eliminarlo desde el panel de administración.',
            'category_id' => 1, // Desarrollo Web
            'status' => 'published',
            'featured' => 1,
            'external_url' => 'https://ejemplo.com',
            'project_year' => date('Y'),
            'project_date' => date('Y-m-d'),
            'views' => 0,
            'likes' => 0
        );
        
        $result = $wpdb->insert($table_projects, $sample_project);
        
        if ($result) {
            self::safe_log('info', 'database', 'insert_sample_project', 'Proyecto de ejemplo creado');
        } else {
            self::safe_log('error', 'database', 'insert_sample_project', 'Error al crear proyecto de ejemplo: ' . $wpdb->last_error);
        }
    }
    
    /**
     * Obtener todas las categorías
     */
    public static function get_categories($args = array()) {
        global $wpdb;
        
        $table_categories = $wpdb->prefix . 'sabsfe_portfolio_categories';
        
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => -1
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $sql = "SELECT * FROM $table_categories";
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
        }
        
        $sql .= " ORDER BY {$args['orderby']} {$args['order']}";
        
        return $wpdb->get_results($sql);
    }
    
    /**
     * Obtener una categoría por ID
     */
    public static function get_category($id) {
        global $wpdb;
        
        $table_categories = $wpdb->prefix . 'sabsfe_portfolio_categories';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_categories WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Crear nueva categoría
     */
    public static function create_category($data) {
        global $wpdb;
        
        $table_categories = $wpdb->prefix . 'sabsfe_portfolio_categories';
        
        $defaults = array(
            'name' => '',
            'slug' => '',
            'description' => '',
            'color' => '#2196F3'
        );
        
        $data = wp_parse_args($data, $defaults);
        
        // Generar slug si no se proporciona
        if (empty($data['slug'])) {
            $data['slug'] = sanitize_title($data['name']);
        }
        
        $result = $wpdb->insert($table_categories, $data);
        
        if ($result) {
            self::safe_log('info', 'database', 'create_category', 'Categoría creada: ' . $data['name']);
            return $wpdb->insert_id;
        }
        
        self::safe_log('error', 'database', 'create_category', 'Error al crear categoría: ' . $wpdb->last_error);
        return false;
    }
    
    /**
     * Actualizar categoría
     */
    public static function update_category($id, $data) {
        global $wpdb;
        
        $table_categories = $wpdb->prefix . 'sabsfe_portfolio_categories';
        
        $result = $wpdb->update(
            $table_categories,
            $data,
            array('id' => $id),
            array('%s', '%s', '%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            self::safe_log('info', 'database', 'update_category', 'Categoría actualizada: ID ' . $id);
            return true;
        }
        
        self::safe_log('error', 'database', 'update_category', 'Error al actualizar categoría: ' . $wpdb->last_error);
        return false;
    }
    
    /**
     * Eliminar categoría
     */
    public static function delete_category($id) {
        global $wpdb;
        
        $table_categories = $wpdb->prefix . 'sabsfe_portfolio_categories';
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
        
        // Verificar si hay proyectos asociados
        $projects_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_projects WHERE category_id = %d",
            $id
        ));
        
        if ($projects_count > 0) {
            self::safe_log('warning', 'database', 'delete_category', 'No se puede eliminar categoría con proyectos asociados: ID ' . $id);
            return false;
        }
        
        $result = $wpdb->delete($table_categories, array('id' => $id), array('%d'));
        
        if ($result) {
            self::safe_log('info', 'database', 'delete_category', 'Categoría eliminada: ID ' . $id);
            return true;
        }
        
        self::safe_log('error', 'database', 'delete_category', 'Error al eliminar categoría: ' . $wpdb->last_error);
        return false;
    }
    
    /**
     * Obtener proyectos con filtros
     */
    public static function get_projects($args = array()) {
        global $wpdb;
        
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
        $table_categories = $wpdb->prefix . 'sabsfe_portfolio_categories';
        
        $defaults = array(
            'status' => 'published',
            'category_id' => null,
            'featured' => null,
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => -1,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $sql = "SELECT p.*, c.name as category_name, c.color as category_color 
                FROM $table_projects p 
                LEFT JOIN $table_categories c ON p.category_id = c.id";
        
        $where_conditions = array();
        $where_values = array();
        
        if ($args['status']) {
            $where_conditions[] = "p.status = %s";
            $where_values[] = $args['status'];
        }
        
        if ($args['category_id']) {
            $where_conditions[] = "p.category_id = %d";
            $where_values[] = $args['category_id'];
        }
        
        if ($args['featured'] !== null) {
            $where_conditions[] = "p.featured = %d";
            $where_values[] = $args['featured'];
        }
        
        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        $sql .= " ORDER BY p.{$args['orderby']} {$args['order']}";
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
            if ($args['offset'] > 0) {
                $sql .= " OFFSET " . intval($args['offset']);
            }
        }
        
        if (!empty($where_values)) {
            return $wpdb->get_results($wpdb->prepare($sql, $where_values));
        }
        
        return $wpdb->get_results($sql);
    }
    
    /**
     * Obtener un proyecto por ID
     */
    public static function get_project($id) {
        global $wpdb;
        
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
        $table_categories = $wpdb->prefix . 'sabsfe_portfolio_categories';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT p.*, c.name as category_name, c.color as category_color 
             FROM $table_projects p 
             LEFT JOIN $table_categories c ON p.category_id = c.id 
             WHERE p.id = %d",
            $id
        ));
    }
    
    /**
     * Crear nuevo proyecto
     */
    public static function create_project($data) {
        global $wpdb;
        
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
        
        $defaults = array(
            'title' => '',
            'slug' => '',
            'description' => '',
            'featured_image' => '',
            'gallery' => '',
            'category_id' => null,
            'status' => 'published',
            'featured' => 0,
            'external_url' => '',
            'youtube_url' => '',
            'vimeo_url' => '',
            'project_year' => null,
            'project_date' => current_time('Y-m-d')
        );
        
        $data = wp_parse_args($data, $defaults);
        
        // Generar slug si no se proporciona
        if (empty($data['slug'])) {
            $data['slug'] = sanitize_title($data['title']);
        }
        
        // Serializar gallery si es array
        if (is_array($data['gallery'])) {
            $data['gallery'] = serialize($data['gallery']);
        }
        
        $result = $wpdb->insert($table_projects, $data);
        
        if ($result) {
            self::safe_log('info', 'database', 'create_project', 'Proyecto creado: ' . $data['title']);
            return $wpdb->insert_id;
        }
        
        self::safe_log('error', 'database', 'create_project', 'Error al crear proyecto: ' . $wpdb->last_error);
        return false;
    }
    
    /**
     * Actualizar proyecto
     */
    public static function update_project($id, $data) {
        global $wpdb;
        
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
        
        // Serializar gallery si es array
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $data['gallery'] = serialize($data['gallery']);
        }
        
        $result = $wpdb->update(
            $table_projects,
            $data,
            array('id' => $id),
            null,
            array('%d')
        );
        
        if ($result !== false) {
            self::safe_log('info', 'database', 'update_project', 'Proyecto actualizado: ID ' . $id);
            return true;
        }
        
        self::safe_log('error', 'database', 'update_project', 'Error al actualizar proyecto: ' . $wpdb->last_error);
        return false;
    }
    
    /**
     * Eliminar proyecto
     */
    public static function delete_project($id) {
        global $wpdb;
        
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
        
        $result = $wpdb->delete($table_projects, array('id' => $id), array('%d'));
        
        if ($result) {
            self::safe_log('info', 'database', 'delete_project', 'Proyecto eliminado: ID ' . $id);
            return true;
        }
        
        self::safe_log('error', 'database', 'delete_project', 'Error al eliminar proyecto: ' . $wpdb->last_error);
        return false;
    }
    
    /**
     * Incrementar contador de vistas
     */
    public static function increment_views($id) {
        global $wpdb;
        
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
        
        $result = $wpdb->query($wpdb->prepare(
            "UPDATE $table_projects SET views = views + 1 WHERE id = %d",
            $id
        ));
        
        return $result !== false;
    }
    
    /**
     * Incrementar contador de likes
     */
    public static function increment_likes($id) {
        global $wpdb;
        
        $table_projects = $wpdb->prefix . 'sabsfe_portfolio_projects';
        
        $result = $wpdb->query($wpdb->prepare(
            "UPDATE $table_projects SET likes = likes + 1 WHERE id = %d",
            $id
        ));
        
        return $result !== false;
    }
}
