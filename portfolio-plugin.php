<?php
/**
 * Plugin Name: Portfolio Projects Manager
 * Plugin URI: https://gerardoriarte.com
 * Description: Plugin para gestionar portafolios de proyectos con integración a Elementor
 * Version: 1.1.0
 * Author: Gerardo Riarte + Cursor
 * Author URI: https://gerardoriarte.com
 * License: GPL v2 or later
 * Text Domain: portfolio-plugin
 * Domain Path: /languages
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('PORTFOLIO_PLUGIN_VERSION', '1.1.0');
define('PORTFOLIO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PORTFOLIO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PORTFOLIO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Clase principal del plugin
 */
class PortfolioPlugin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Cargar clases necesarias
        $this->load_dependencies();
        
        // Inicializar componentes
        $this->init_components();
    }
    
    private function load_dependencies() {
        $required_files = array(
            'includes/class-database.php',
            'includes/class-logger.php',
            'includes/class-admin.php',
            'includes/class-elementor-widget.php',
            'includes/class-frontend.php',
            'includes/class-api.php'
        );
        
        foreach ($required_files as $file) {
            $file_path = PORTFOLIO_PLUGIN_PATH . $file;
            if (!file_exists($file_path)) {
                throw new Exception("Archivo requerido no encontrado: {$file}");
            }
            require_once $file_path;
        }
    }
    
    private function init_components() {
        PortfolioDatabase::get_instance();
        new PortfolioAdmin();
        new PortfolioFrontend();
        new PortfolioAPI();
        
        // Registrar widget de Elementor con múltiples métodos para compatibilidad
        add_action('elementor/widgets/register', array($this, 'register_elementor_widgets'));
        add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widgets_legacy'));
    }
    
    /**
     * Registrar widgets de Elementor (método moderno)
     */
    public function register_elementor_widgets($widgets_manager) {
        if (class_exists('PortfolioElementorWidget')) {
            $widgets_manager->register(new PortfolioElementorWidget());
        }
    }
    
    /**
     * Registrar widgets de Elementor (método legacy)
     */
    public function register_elementor_widgets_legacy() {
        if (class_exists('PortfolioElementorWidget') && class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new PortfolioElementorWidget());
        }
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('portfolio-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function activate() {
        try {
            // Verificar permisos de usuario
            if (!current_user_can('activate_plugins')) {
                throw new Exception('No tienes permisos para activar plugins');
            }
            
            // Cargar dependencias antes de activar
            $this->load_dependencies();
            
            // Verificar que WordPress esté en la versión mínima
            if (version_compare(get_bloginfo('version'), '5.0', '<')) {
                throw new Exception('Este plugin requiere WordPress 5.0 o superior');
            }
            
            // Crear tablas de base de datos
            if (class_exists('PortfolioDatabase')) {
                $result = PortfolioDatabase::create_tables();
                if (!$result) {
                    throw new Exception('Error al crear las tablas de la base de datos');
                }
            } else {
                throw new Exception('No se pudo cargar la clase PortfolioDatabase');
            }
            
            // Crear opciones por defecto
            $this->set_default_options();
            
            // Flush rewrite rules
            flush_rewrite_rules();
            
            // Log de activación exitosa
            if (class_exists('PortfolioLogger')) {
                PortfolioLogger::log('info', 'plugin', 'activate', 'Plugin Portfolio activado correctamente');
            }
            
        } catch (Exception $e) {
            // Desactivar el plugin si hay errores
            deactivate_plugins(plugin_basename(__FILE__));
            
            // Log del error
            error_log('Error al activar Portfolio Plugin: ' . $e->getMessage());
            
            wp_die(
                '<h1>Error de Activación del Plugin Portfolio</h1>' .
                '<p><strong>Error:</strong> ' . esc_html($e->getMessage()) . '</p>' .
                '<p>Por favor, revisa los logs de error de WordPress o contacta al administrador del sitio.</p>' .
                '<p><a href="' . admin_url('plugins.php') . '">← Volver a Plugins</a></p>',
                'Error de Activación',
                array('back_link' => false)
            );
        }
    }
    
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    private function set_default_options() {
        $default_options = array(
            'portfolio_items_per_page' => 12,
            'portfolio_enable_modal' => true,
            'portfolio_theme' => 'dark',
            'portfolio_enable_views' => true,
            'portfolio_enable_likes' => true,
            'portfolio_enable_sharing' => true,
            'portfolio_carousel_autoplay' => false,
            'portfolio_carousel_speed' => 3000,
            'portfolio_show_categories' => true,
            'portfolio_show_dates' => true,
            'portfolio_show_stats' => true,
            'portfolio_version' => PORTFOLIO_PLUGIN_VERSION,
            'portfolio_installed_date' => current_time('mysql'),
            'portfolio_first_activation' => true
        );
        
        // Solo agregar opciones si no existen
        if (!get_option('portfolio_plugin_options')) {
            add_option('portfolio_plugin_options', $default_options);
        } else {
            // Actualizar opciones existentes con valores por defecto faltantes
            $existing_options = get_option('portfolio_plugin_options', array());
            $updated_options = array_merge($default_options, $existing_options);
            update_option('portfolio_plugin_options', $updated_options);
        }
        
        // Crear opción de versión para futuras actualizaciones
        update_option('portfolio_plugin_version', PORTFOLIO_PLUGIN_VERSION);
    }
}

// Inicializar el plugin
function portfolio_plugin_init() {
    return PortfolioPlugin::get_instance();
}

// Iniciar el plugin
portfolio_plugin_init();
