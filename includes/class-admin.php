<?php
/**
 * Panel de administración del plugin Portfolio
 * 
 * @package Sabsfe_Portfolio
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sabsfe_Portfolio_Admin {
    
    private $plugin_name = 'Portfolio Projects Manager';
    private $version = SABSFE_PORTFOLIO_VERSION;
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_sabsfe_portfolio_save_project', array($this, 'ajax_save_project'));
        add_action('wp_ajax_sabsfe_portfolio_delete_project', array($this, 'ajax_delete_project'));
        add_action('wp_ajax_sabsfe_portfolio_save_category', array($this, 'ajax_save_category'));
        add_action('wp_ajax_sabsfe_portfolio_delete_category', array($this, 'ajax_delete_category'));
        add_action('wp_ajax_sabsfe_portfolio_upload_image', array($this, 'ajax_upload_image'));
        add_action('wp_ajax_sabsfe_portfolio_get_project_for_edit', array($this, 'ajax_get_project_for_edit'));
        add_action('wp_ajax_sabsfe_portfolio_create_tables', array($this, 'ajax_create_tables'));
    }
    
    /**
     * Agregar menús de administración
     */
    public function add_admin_menu() {
        // Menú principal
        add_menu_page(
            $this->plugin_name,
            'Portfolio',
            'manage_options',
            'portfolio-admin',
            array($this, 'admin_page_projects'),
            'dashicons-portfolio',
            30
        );
        
        // Submenú de proyectos
        add_submenu_page(
            'portfolio-admin',
            'Proyectos',
            'Proyectos',
            'manage_options',
            'portfolio-admin',
            array($this, 'admin_page_projects')
        );
        
        // Submenú de categorías
        add_submenu_page(
            'portfolio-admin',
            'Categorías',
            'Categorías',
            'manage_options',
            'portfolio-categories',
            array($this, 'admin_page_categories')
        );
        
        // Submenú de configuración
        add_submenu_page(
            'portfolio-admin',
            'Configuración',
            'Configuración',
            'manage_options',
            'portfolio-settings',
            array($this, 'admin_page_settings')
        );
        
        // Submenú de guía de usuario
        add_submenu_page(
            'portfolio-admin',
            'Guía de Usuario',
            'Guía de Usuario',
            'manage_options',
            'portfolio-guide',
            array($this, 'admin_page_guide')
        );
    }
    
    /**
     * Cargar scripts y estilos del admin
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'portfolio') === false) {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
        
        wp_enqueue_style(
            'portfolio-admin-css',
            SABSFE_PORTFOLIO_URL . 'assets/css/admin.css',
            array(),
            $this->version
        );
        
        wp_enqueue_script(
            'portfolio-admin-js',
            SABSFE_PORTFOLIO_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable'),
            $this->version . '-' . time(), // Forzar recarga
            true
        );
        
        wp_localize_script('portfolio-admin-js', 'portfolio_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sabsfe_portfolio_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('¿Estás seguro de que quieres eliminar este elemento?', 'sabsfe-portfolio-plugin'),
                'saving' => __('Guardando...', 'sabsfe-portfolio-plugin'),
                'saved' => __('Guardado exitosamente', 'sabsfe-portfolio-plugin'),
                'error' => __('Error al guardar', 'sabsfe-portfolio-plugin')
            )
        ));
    }
    
    /**
     * Página de administración de proyectos
     */
    public function admin_page_projects() {
        $projects = Sabsfe_Portfolio_Database::get_projects(array('limit' => -1));
        $categories = Sabsfe_Portfolio_Database::get_categories();
        
        include SABSFE_PORTFOLIO_PATH . 'templates/admin-projects.php';
    }
    
    /**
     * Página de administración de categorías
     */
    public function admin_page_categories() {
        $categories = Sabsfe_Portfolio_Database::get_categories();
        
        include SABSFE_PORTFOLIO_PATH . 'templates/admin-categories.php';
    }
    
    /**
     * Página de configuración
     */
    public function admin_page_settings() {
        $options = get_option('sabsfe_portfolio_plugin_options', array());
        
        if (isset($_POST['submit'])) {
            $this->save_settings();
            $options = get_option('sabsfe_portfolio_plugin_options', array());
        }
        
        include SABSFE_PORTFOLIO_PATH . 'templates/admin-settings.php';
    }
    
    /**
     * Página de guía de usuario
     */
    public function admin_page_guide() {
        include SABSFE_PORTFOLIO_PATH . 'templates/admin-guide.php';
    }
    
    /**
     * Guardar configuración
     */
    private function save_settings() {
        if (!wp_verify_nonce($_POST['sabsfe_portfolio_settings_nonce'], 'sabsfe_portfolio_settings')) {
            wp_die(__('Error de seguridad', 'sabsfe-portfolio-plugin'));
        }
        
        $options = array(
            'portfolio_items_per_page' => intval($_POST['portfolio_items_per_page']),
            'portfolio_enable_modal' => isset($_POST['portfolio_enable_modal']),
            'portfolio_theme' => sanitize_text_field($_POST['portfolio_theme']),
            'portfolio_columns' => intval($_POST['portfolio_columns'])
        );
        
        update_option('sabsfe_portfolio_plugin_options', $options);
        
        if (class_exists('Sabsfe_Portfolio_Logger')) {
            Sabsfe_Portfolio_Logger::info('admin', 'save_settings', 'Configuración guardada exitosamente');
        }
        
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Configuración guardada exitosamente', 'sabsfe-portfolio-plugin') . '</p></div>';
        });
    }
    
    /**
     * AJAX: Guardar proyecto
     */
    public function ajax_save_project() {
        check_ajax_referer('sabsfe_portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array(
                'message' => __('No tienes permisos para realizar esta acción', 'sabsfe-portfolio-plugin')
            ));
            return;
        }
        
        // Validar campos requeridos
        if (empty($_POST['title'])) {
            wp_send_json_error(array(
                'message' => __('El título del proyecto es requerido', 'sabsfe-portfolio-plugin')
            ));
            return;
        }
        
        $project_data = array(
            'title' => sanitize_text_field($_POST['title']),
            'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'featured_image' => isset($_POST['featured_image']) ? esc_url_raw($_POST['featured_image']) : '',
            'category_id' => isset($_POST['category_id']) ? intval($_POST['category_id']) : 0,
            'status' => isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'published',
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'external_url' => isset($_POST['external_url']) ? esc_url_raw($_POST['external_url']) : '',
            'youtube_url' => isset($_POST['youtube_url']) ? esc_url_raw($_POST['youtube_url']) : '',
            'vimeo_url' => isset($_POST['vimeo_url']) ? esc_url_raw($_POST['vimeo_url']) : '',
            'project_year' => !empty($_POST['project_year']) ? sanitize_text_field($_POST['project_year']) : null,
            'project_date' => isset($_POST['project_date']) ? sanitize_text_field($_POST['project_date']) : date('Y-m-d')
        );
        
        // Procesar galería
        if (isset($_POST['gallery']) && is_array($_POST['gallery'])) {
            $project_data['gallery'] = array_map('esc_url_raw', $_POST['gallery']);
        }
        
        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        
        try {
            if ($project_id > 0) {
                $result = Sabsfe_Portfolio_Database::update_project($project_id, $project_data);
                $action = 'actualizado';
            } else {
                $result = Sabsfe_Portfolio_Database::create_project($project_data);
                $action = 'creado';
            }
            
            if ($result) {
                wp_send_json_success(array(
                    'message' => sprintf(__('Proyecto %s exitosamente', 'sabsfe-portfolio-plugin'), $action),
                    'project_id' => $project_id > 0 ? $project_id : $result
                ));
            } else {
                global $wpdb;
                wp_send_json_error(array(
                    'message' => __('Error al guardar el proyecto', 'sabsfe-portfolio-plugin'),
                    'error' => $wpdb->last_error
                ));
            }
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => __('Error al guardar el proyecto', 'sabsfe-portfolio-plugin'),
                'error' => $e->getMessage()
            ));
        }
    }
    
    /**
     * AJAX: Eliminar proyecto
     */
    public function ajax_delete_project() {
        check_ajax_referer('sabsfe_portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'sabsfe-portfolio-plugin'));
        }
        
        $project_id = intval($_POST['project_id']);
        
        $result = Sabsfe_Portfolio_Database::delete_project($project_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Proyecto eliminado exitosamente', 'sabsfe-portfolio-plugin')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error al eliminar el proyecto', 'sabsfe-portfolio-plugin')
            ));
        }
    }
    
    /**
     * AJAX: Guardar categoría
     */
    public function ajax_save_category() {
        check_ajax_referer('sabsfe_portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'sabsfe-portfolio-plugin'));
        }
        
        $category_data = array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
            'color' => sanitize_hex_color($_POST['color'])
        );
        
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        
        if ($category_id > 0) {
            $result = Sabsfe_Portfolio_Database::update_category($category_id, $category_data);
        } else {
            $result = Sabsfe_Portfolio_Database::create_category($category_data);
        }
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Categoría guardada exitosamente', 'sabsfe-portfolio-plugin'),
                'category_id' => $category_id > 0 ? $category_id : $result
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error al guardar la categoría', 'sabsfe-portfolio-plugin')
            ));
        }
    }
    
    /**
     * AJAX: Eliminar categoría
     */
    public function ajax_delete_category() {
        check_ajax_referer('sabsfe_portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'sabsfe-portfolio-plugin'));
        }
        
        $category_id = intval($_POST['category_id']);
        
        $result = Sabsfe_Portfolio_Database::delete_category($category_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Categoría eliminada exitosamente', 'sabsfe-portfolio-plugin')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error al eliminar la categoría. Verifica que no tenga proyectos asociados.', 'sabsfe-portfolio-plugin')
            ));
        }
    }
    
    /**
     * AJAX: Subir imagen
     */
    public function ajax_upload_image() {
        check_ajax_referer('sabsfe_portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'sabsfe-portfolio-plugin'));
        }
        
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        $uploadedfile = $_FILES['file'];
        $upload_overrides = array('test_form' => false);
        
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        
        if ($movefile && !isset($movefile['error'])) {
            wp_send_json_success(array(
                'url' => $movefile['url'],
                'file' => $movefile['file']
            ));
        } else {
            wp_send_json_error(array(
                'message' => $movefile['error']
            ));
        }
    }
    
    /**
     * AJAX: Obtener proyecto para edición
     */
    public function ajax_get_project_for_edit() {
        check_ajax_referer('sabsfe_portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'sabsfe-portfolio-plugin'));
        }
        
        $project_id = intval($_POST['project_id']);
        $project = Sabsfe_Portfolio_Database::get_project($project_id);
        
        if (!$project) {
            wp_send_json_error(array(
                'message' => __('Proyecto no encontrado', 'sabsfe-portfolio-plugin')
            ));
        }
        
        // Preparar datos del proyecto para edición
        $project_data = array(
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'featured_image' => $project->featured_image,
            'gallery' => $project->gallery ? unserialize($project->gallery) : array(),
            'category_id' => $project->category_id,
            'status' => $project->status,
            'featured' => $project->featured,
            'external_url' => $project->external_url,
            'youtube_url' => $project->youtube_url ?? '',
            'vimeo_url' => $project->vimeo_url ?? '',
            'project_year' => $project->project_year,
            'project_date' => $project->project_date
        );
        
        wp_send_json_success($project_data);
    }
    
    /**
     * AJAX: Crear tablas de base de datos
     */
    public function ajax_create_tables() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'sabsfe_portfolio_admin_nonce')) {
            wp_send_json_error('Nonce inválido');
        }
        
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No tienes permisos para realizar esta acción');
        }
        
        try {
            // Cargar la clase mejorada
            require_once(SABSFE_PORTFOLIO_PATH . 'includes/class-database-enhanced.php');
            
            // Intentar crear las tablas con método mejorado
            $result = Sabsfe_Portfolio_Database_Enhanced::create_tables_direct();
            
            if ($result) {
                // Verificar estado de las tablas
                $table_status = Sabsfe_Portfolio_Database_Enhanced::check_tables_status();
                
                wp_send_json_success(array(
                    'message' => 'Tablas creadas exitosamente con método directo',
                    'tables' => $table_status
                ));
            } else {
                // Si falla el método directo, intentar con el método original
                $result_original = Sabsfe_Portfolio_Database::create_tables();
                
                if ($result_original) {
                    $table_status = Sabsfe_Portfolio_Database_Enhanced::check_tables_status();
                    
                    wp_send_json_success(array(
                        'message' => 'Tablas creadas exitosamente con método original',
                        'tables' => $table_status
                    ));
                } else {
                    wp_send_json_error('Error al crear las tablas con ambos métodos: ' . $wpdb->last_error);
                }
            }
            
        } catch (Exception $e) {
            wp_send_json_error('Error: ' . $e->getMessage());
        }
    }
}
