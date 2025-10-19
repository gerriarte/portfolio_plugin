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
define('SABSFE_PORTFOLIO_VERSION', '1.1.0');
define('SABSFE_PORTFOLIO_URL', plugin_dir_url(__FILE__));
define('SABSFE_PORTFOLIO_PATH', plugin_dir_path(__FILE__));
define('SABSFE_PORTFOLIO_BASENAME', plugin_basename(__FILE__));

/**
 * Clase principal del plugin
 * 
 * @package Sabsfe_Portfolio
 * @since 1.0.0
 */
class Sabsfe_Portfolio_Plugin {
    
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
        
        $missing_files = array();
        
        foreach ($required_files as $file) {
            $file_path = SABSFE_PORTFOLIO_PATH . $file;
            if (!file_exists($file_path)) {
                $missing_files[] = $file;
            } else {
                require_once $file_path;
            }
        }
        
        if (!empty($missing_files)) {
            throw new Exception("Archivos requeridos no encontrados: " . implode(', ', $missing_files));
        }
        
        // Verificar que las clases principales existan
        $required_classes = array('Sabsfe_Portfolio_Database', 'Sabsfe_Portfolio_Logger');
        foreach ($required_classes as $class) {
            if (!class_exists($class)) {
                throw new Exception("Clase requerida no encontrada: {$class}");
            }
        }
    }
    
    private function init_components() {
        // Inicializar base de datos de forma segura
        if (class_exists('Sabsfe_Portfolio_Database')) {
            Sabsfe_Portfolio_Database::get_instance();
        }
        
        // Inicializar componentes de forma segura
        if (class_exists('Sabsfe_Portfolio_Admin')) {
            new Sabsfe_Portfolio_Admin();
        }
        
        if (class_exists('Sabsfe_Portfolio_Frontend')) {
            new Sabsfe_Portfolio_Frontend();
        }
        
        if (class_exists('Sabsfe_Portfolio_API')) {
            new Sabsfe_Portfolio_API();
        }
        
        // Registrar widget de Elementor solo si Elementor está activo
        if (did_action('elementor/loaded')) {
            add_action('elementor/widgets/register', array($this, 'register_elementor_widgets'));
            add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widgets_legacy'));
        }
    }
    
    /**
     * Registrar widgets de Elementor (método moderno)
     */
    public function register_elementor_widgets($widgets_manager) {
        if (class_exists('Sabsfe_Portfolio_Elementor_Widget')) {
            $widgets_manager->register(new Sabsfe_Portfolio_Elementor_Widget());
        }
    }
    
    /**
     * Registrar widgets de Elementor (método legacy)
     */
    public function register_elementor_widgets_legacy() {
        if (class_exists('Sabsfe_Portfolio_Elementor_Widget') && class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Sabsfe_Portfolio_Elementor_Widget());
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
            
            // Verificar que WordPress esté en la versión mínima
            if (version_compare(get_bloginfo('version'), '5.0', '<')) {
                throw new Exception('Este plugin requiere WordPress 5.0 o superior');
            }
            
            // Verificar versión de PHP
            if (version_compare(PHP_VERSION, '7.4', '<')) {
                throw new Exception('Este plugin requiere PHP 7.4 o superior');
            }
            
            // Cargar dependencias antes de activar
            $this->load_dependencies();
            
        // Verificar que las clases se cargaron correctamente
        if (!class_exists('Sabsfe_Portfolio_Database')) {
            throw new Exception('Error: No se pudo cargar la clase Sabsfe_Portfolio_Database');
        }
        
        // Crear tablas de base de datos
        $result = Sabsfe_Portfolio_Database::create_tables();
            if (!$result) {
                throw new Exception('Error al crear las tablas de la base de datos');
            }
            
            // Crear opciones por defecto
            $this->set_default_options();
            
            // Flush rewrite rules
            flush_rewrite_rules();
            
            // Log de activación exitosa
            if (class_exists('Sabsfe_Portfolio_Logger')) {
                Sabsfe_Portfolio_Logger::log('info', 'plugin', 'activate', 'Plugin Portfolio activado correctamente');
            }
            
        } catch (Exception $e) {
            // Desactivar el plugin si hay errores
            deactivate_plugins(plugin_basename(__FILE__));
            
            // Log del error
            error_log('Error al activar Portfolio Plugin: ' . $e->getMessage());
            
            // Mostrar error más amigable
            $error_message = '<div style="max-width: 600px; margin: 20px auto; padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">';
            $error_message .= '<h1 style="color: #d63638; margin-top: 0;">Error de Activación del Plugin Portfolio</h1>';
            $error_message .= '<p><strong>Error:</strong> ' . esc_html($e->getMessage()) . '</p>';
            $error_message .= '<h3>Soluciones posibles:</h3>';
            $error_message .= '<ul>';
            $error_message .= '<li>Verifica que WordPress sea versión 5.0 o superior</li>';
            $error_message .= '<li>Verifica que PHP sea versión 7.4 o superior</li>';
            $error_message .= '<li>Verifica que todos los archivos del plugin estén presentes</li>';
            $error_message .= '<li>Verifica los permisos de archivos y directorios</li>';
            $error_message .= '</ul>';
            $error_message .= '<p><a href="' . admin_url('plugins.php') . '" class="button button-primary">← Volver a Plugins</a></p>';
            $error_message .= '</div>';
            
            wp_die($error_message, 'Error de Activación', array('back_link' => false));
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
            'portfolio_version' => SABSFE_PORTFOLIO_VERSION,
            'portfolio_installed_date' => current_time('mysql'),
            'sabsfe_portfolio_first_activation' => true
        );
        
        // Solo agregar opciones si no existen
        if (!get_option('sabsfe_portfolio_plugin_options')) {
            add_option('sabsfe_portfolio_plugin_options', $default_options);
        } else {
            // Actualizar opciones existentes con valores por defecto faltantes
            $existing_options = get_option('sabsfe_portfolio_plugin_options', array());
            $updated_options = array_merge($default_options, $existing_options);
            update_option('sabsfe_portfolio_plugin_options', $updated_options);
        }
        
        // Crear opción de versión para futuras actualizaciones
        update_option('sabsfe_portfolio_plugin_version', SABSFE_PORTFOLIO_VERSION);
    }
}

/**
 * Inicializar el plugin
 * 
 * @return Sabsfe_Portfolio_Plugin Instancia del plugin
 * @since 1.0.0
 */
function sabsfe_portfolio_plugin_init() {
    return Sabsfe_Portfolio_Plugin::get_instance();
}

// Iniciar el plugin
sabsfe_portfolio_plugin_init();
