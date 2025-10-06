<?php
/**
 * Panel de administración del plugin Portfolio
 */

if (!defined('ABSPATH')) {
    exit;
}

class PortfolioAdmin {
    
    private $plugin_name = 'Portfolio Projects Manager';
    private $version = PORTFOLIO_PLUGIN_VERSION;
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_portfolio_save_project', array($this, 'ajax_save_project'));
        add_action('wp_ajax_portfolio_delete_project', array($this, 'ajax_delete_project'));
        add_action('wp_ajax_portfolio_save_category', array($this, 'ajax_save_category'));
        add_action('wp_ajax_portfolio_delete_category', array($this, 'ajax_delete_category'));
        add_action('wp_ajax_portfolio_upload_image', array($this, 'ajax_upload_image'));
        add_action('wp_ajax_portfolio_get_project_for_edit', array($this, 'ajax_get_project_for_edit'));
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
            PORTFOLIO_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            $this->version
        );
        
        wp_enqueue_script(
            'portfolio-admin-js',
            PORTFOLIO_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable'),
            $this->version,
            true
        );
        
        wp_localize_script('portfolio-admin-js', 'portfolio_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('portfolio_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('¿Estás seguro de que quieres eliminar este elemento?', 'portfolio-plugin'),
                'saving' => __('Guardando...', 'portfolio-plugin'),
                'saved' => __('Guardado exitosamente', 'portfolio-plugin'),
                'error' => __('Error al guardar', 'portfolio-plugin')
            )
        ));
    }
    
    /**
     * Página de administración de proyectos
     */
    public function admin_page_projects() {
        $projects = PortfolioDatabase::get_projects(array('limit' => -1));
        $categories = PortfolioDatabase::get_categories();
        
        include PORTFOLIO_PLUGIN_PATH . 'admin/projects.php';
    }
    
    /**
     * Página de administración de categorías
     */
    public function admin_page_categories() {
        $categories = PortfolioDatabase::get_categories();
        
        include PORTFOLIO_PLUGIN_PATH . 'admin/categories.php';
    }
    
    /**
     * Página de configuración
     */
    public function admin_page_settings() {
        $options = get_option('portfolio_plugin_options', array());
        
        if (isset($_POST['submit'])) {
            $this->save_settings();
            $options = get_option('portfolio_plugin_options', array());
        }
        
        include PORTFOLIO_PLUGIN_PATH . 'admin/settings.php';
    }
    
    /**
     * Guardar configuración
     */
    private function save_settings() {
        if (!wp_verify_nonce($_POST['portfolio_settings_nonce'], 'portfolio_settings')) {
            wp_die(__('Error de seguridad', 'portfolio-plugin'));
        }
        
        $options = array(
            'portfolio_items_per_page' => intval($_POST['portfolio_items_per_page']),
            'portfolio_enable_modal' => isset($_POST['portfolio_enable_modal']),
            'portfolio_theme' => sanitize_text_field($_POST['portfolio_theme'])
        );
        
        update_option('portfolio_plugin_options', $options);
        
        if (class_exists('PortfolioLogger')) {
            PortfolioLogger::info('admin', 'save_settings', 'Configuración guardada exitosamente');
        }
        
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Configuración guardada exitosamente', 'portfolio-plugin') . '</p></div>';
        });
    }
    
    /**
     * AJAX: Guardar proyecto
     */
    public function ajax_save_project() {
        check_ajax_referer('portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'portfolio-plugin'));
        }
        
        $project_data = array(
            'title' => sanitize_text_field($_POST['title']),
            'description' => sanitize_textarea_field($_POST['description']),
            'content' => wp_kses_post($_POST['content']),
            'featured_image' => esc_url_raw($_POST['featured_image']),
            'category_id' => intval($_POST['category_id']),
            'status' => sanitize_text_field($_POST['status']),
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'external_url' => esc_url_raw($_POST['external_url']),
            'project_date' => sanitize_text_field($_POST['project_date'])
        );
        
        // Procesar galería
        if (isset($_POST['gallery']) && is_array($_POST['gallery'])) {
            $project_data['gallery'] = array_map('esc_url_raw', $_POST['gallery']);
        }
        
        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        
        if ($project_id > 0) {
            $result = PortfolioDatabase::update_project($project_id, $project_data);
        } else {
            $result = PortfolioDatabase::create_project($project_data);
        }
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Proyecto guardado exitosamente', 'portfolio-plugin'),
                'project_id' => $project_id > 0 ? $project_id : $result
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error al guardar el proyecto', 'portfolio-plugin')
            ));
        }
    }
    
    /**
     * AJAX: Eliminar proyecto
     */
    public function ajax_delete_project() {
        check_ajax_referer('portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'portfolio-plugin'));
        }
        
        $project_id = intval($_POST['project_id']);
        
        $result = PortfolioDatabase::delete_project($project_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Proyecto eliminado exitosamente', 'portfolio-plugin')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error al eliminar el proyecto', 'portfolio-plugin')
            ));
        }
    }
    
    /**
     * AJAX: Guardar categoría
     */
    public function ajax_save_category() {
        check_ajax_referer('portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'portfolio-plugin'));
        }
        
        $category_data = array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
            'color' => sanitize_hex_color($_POST['color'])
        );
        
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        
        if ($category_id > 0) {
            $result = PortfolioDatabase::update_category($category_id, $category_data);
        } else {
            $result = PortfolioDatabase::create_category($category_data);
        }
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Categoría guardada exitosamente', 'portfolio-plugin'),
                'category_id' => $category_id > 0 ? $category_id : $result
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error al guardar la categoría', 'portfolio-plugin')
            ));
        }
    }
    
    /**
     * AJAX: Eliminar categoría
     */
    public function ajax_delete_category() {
        check_ajax_referer('portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'portfolio-plugin'));
        }
        
        $category_id = intval($_POST['category_id']);
        
        $result = PortfolioDatabase::delete_category($category_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Categoría eliminada exitosamente', 'portfolio-plugin')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error al eliminar la categoría. Verifica que no tenga proyectos asociados.', 'portfolio-plugin')
            ));
        }
    }
    
    /**
     * AJAX: Subir imagen
     */
    public function ajax_upload_image() {
        check_ajax_referer('portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'portfolio-plugin'));
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
        check_ajax_referer('portfolio_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción', 'portfolio-plugin'));
        }
        
        $project_id = intval($_POST['project_id']);
        $project = PortfolioDatabase::get_project($project_id);
        
        if (!$project) {
            wp_send_json_error(array(
                'message' => __('Proyecto no encontrado', 'portfolio-plugin')
            ));
        }
        
        // Preparar datos del proyecto para edición
        $project_data = array(
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'content' => $project->content,
            'featured_image' => $project->featured_image,
            'gallery' => $project->gallery ? unserialize($project->gallery) : array(),
            'category_id' => $project->category_id,
            'status' => $project->status,
            'featured' => $project->featured,
            'external_url' => $project->external_url,
            'project_date' => $project->project_date
        );
        
        wp_send_json_success($project_data);
    }
}
