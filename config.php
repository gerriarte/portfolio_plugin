<?php
/**
 * Archivo de configuración del plugin Portfolio Projects Manager
 * 
 * Este archivo contiene todas las configuraciones por defecto del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// Configuraciones por defecto del plugin
return array(
    
    // Configuración general
    'version' => '1.0.0',
    'text_domain' => 'portfolio-plugin',
    'min_wp_version' => '5.0',
    'min_php_version' => '7.4',
    
    // Configuraciones de base de datos
    'database' => array(
        'tables' => array(
            'projects' => 'portfolio_projects',
            'categories' => 'portfolio_categories'
        ),
        'charset_collate' => '', // Se establece dinámicamente
    ),
    
    // Configuraciones por defecto
    'default_options' => array(
        'portfolio_items_per_page' => 12,
        'portfolio_enable_modal' => true,
        'portfolio_theme' => 'dark',
        'portfolio_enable_api' => true,
        'portfolio_log_retention_days' => 30,
        'portfolio_enable_stats' => true,
        'portfolio_enable_likes' => true,
        'portfolio_enable_views' => true,
    ),
    
    // Configuraciones de categorías por defecto
    'default_categories' => array(
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
        ),
        array(
            'name' => 'Graphic Design',
            'slug' => 'graphic-design',
            'description' => 'Diseño gráfico y visual',
            'color' => '#F44336'
        ),
        array(
            'name' => 'Development',
            'slug' => 'development',
            'description' => 'Desarrollo de software',
            'color' => '#607D8B'
        )
    ),
    
    // Configuraciones de colores del tema
    'theme_colors' => array(
        'primary' => '#2196F3',
        'primary_dark' => '#1976D2',
        'secondary' => '#FFC107',
        'success' => '#4CAF50',
        'warning' => '#FF9800',
        'error' => '#F44336',
        'dark_bg' => '#1E1E1E',
        'card_bg' => '#2C2C2C',
        'text_primary' => '#FFFFFF',
        'text_secondary' => '#B0B0B0',
        'border' => '#404040',
    ),
    
    // Configuraciones de Elementor
    'elementor' => array(
        'widget_name' => 'portfolio-grid',
        'widget_title' => 'Portfolio Grid',
        'widget_icon' => 'eicon-posts-grid',
        'widget_categories' => array('general'),
        'widget_keywords' => array('portfolio', 'projects', 'grid', 'gallery'),
    ),
    
    // Configuraciones de API REST
    'api' => array(
        'namespace' => 'portfolio/v1',
        'version' => '1.0.0',
        'endpoints' => array(
            'projects' => '/projects',
            'project' => '/projects/(?P<id>\d+)',
            'categories' => '/categories',
            'increment_views' => '/projects/(?P<id>\d+)/views',
            'increment_likes' => '/projects/(?P<id>\d+)/likes',
        ),
    ),
    
    // Configuraciones de logging
    'logging' => array(
        'enabled' => true,
        'log_file' => 'portfolio-plugin-logs.log',
        'max_file_size' => 10485760, // 10MB
        'retention_days' => 30,
        'levels' => array('info', 'warning', 'error', 'debug'),
    ),
    
    // Configuraciones de archivos
    'file_upload' => array(
        'max_file_size' => 5242880, // 5MB
        'allowed_types' => array('jpg', 'jpeg', 'png', 'gif', 'webp'),
        'upload_path' => 'portfolio-uploads',
    ),
    
    // Configuraciones de paginación
    'pagination' => array(
        'default_per_page' => 12,
        'max_per_page' => 50,
        'min_per_page' => 1,
    ),
    
    // Configuraciones de cache
    'cache' => array(
        'enabled' => true,
        'duration' => 3600, // 1 hora
        'keys' => array(
            'projects' => 'portfolio_projects_',
            'categories' => 'portfolio_categories_',
            'project' => 'portfolio_project_',
        ),
    ),
    
    // Configuraciones de seguridad
    'security' => array(
        'nonce_lifetime' => 3600, // 1 hora
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutos
        'sanitize_inputs' => true,
        'validate_capabilities' => true,
    ),
    
    // Configuraciones de performance
    'performance' => array(
        'lazy_loading' => true,
        'image_optimization' => true,
        'minify_css' => false, // Se puede habilitar en producción
        'minify_js' => false, // Se puede habilitar en producción
        'cdn_enabled' => false,
    ),
    
    // Configuraciones de internacionalización
    'i18n' => array(
        'default_language' => 'es_ES',
        'supported_languages' => array('es_ES', 'en_US'),
        'load_textdomain' => true,
    ),
    
    // Configuraciones de debugging
    'debug' => array(
        'enabled' => false,
        'log_queries' => false,
        'log_ajax' => false,
        'log_api' => false,
        'show_errors' => false,
    ),
    
    // Configuraciones de notificaciones
    'notifications' => array(
        'admin_notices' => true,
        'email_notifications' => false,
        'browser_notifications' => false,
    ),
    
    // Configuraciones de integración
    'integrations' => array(
        'elementor' => array(
            'enabled' => true,
            'min_version' => '3.0.0',
        ),
        'woocommerce' => array(
            'enabled' => false,
        ),
        'yoast_seo' => array(
            'enabled' => false,
        ),
    ),
    
    // Configuraciones de mantenimiento
    'maintenance' => array(
        'auto_cleanup' => true,
        'cleanup_interval' => 'daily',
        'cleanup_tasks' => array(
            'logs' => true,
            'temp_files' => true,
            'cache' => true,
            'revisions' => false,
        ),
    ),
    
    // Configuraciones de backup
    'backup' => array(
        'enabled' => false,
        'auto_backup' => false,
        'backup_interval' => 'weekly',
        'retention_count' => 5,
    ),
    
    // Configuraciones de analytics
    'analytics' => array(
        'enabled' => true,
        'track_views' => true,
        'track_likes' => true,
        'track_clicks' => true,
        'track_time_on_page' => false,
    ),
    
    // Configuraciones de SEO
    'seo' => array(
        'meta_title' => true,
        'meta_description' => true,
        'meta_keywords' => false,
        'og_tags' => true,
        'twitter_cards' => true,
        'structured_data' => true,
    ),
    
    // Configuraciones de accesibilidad
    'accessibility' => array(
        'aria_labels' => true,
        'keyboard_navigation' => true,
        'screen_reader_support' => true,
        'high_contrast_mode' => false,
    ),
    
    // Configuraciones de responsive
    'responsive' => array(
        'breakpoints' => array(
            'mobile' => 480,
            'tablet' => 768,
            'desktop' => 1024,
            'large' => 1200,
        ),
        'mobile_first' => true,
        'fluid_grid' => true,
    ),
    
    // Configuraciones de animaciones
    'animations' => array(
        'enabled' => true,
        'duration' => 300,
        'easing' => 'cubic-bezier(0.4, 0, 0.2, 1)',
        'reduced_motion' => true,
    ),
    
    // Configuraciones de validación
    'validation' => array(
        'client_side' => true,
        'server_side' => true,
        'sanitization' => true,
        'escaping' => true,
    ),
    
    // Configuraciones de hooks
    'hooks' => array(
        'enabled' => true,
        'priority' => 10,
        'accepted_args' => 1,
    ),
    
    // Configuraciones de cron jobs
    'cron' => array(
        'enabled' => true,
        'cleanup_logs' => 'daily',
        'cleanup_cache' => 'hourly',
        'backup_data' => 'weekly',
    ),
    
    // Configuraciones de error handling
    'error_handling' => array(
        'log_errors' => true,
        'show_errors' => false,
        'error_pages' => true,
        'fallback_content' => true,
    ),
    
    // Configuraciones de compatibilidad
    'compatibility' => array(
        'wp_versions' => array('5.0', '5.1', '5.2', '5.3', '5.4', '5.5', '5.6', '5.7', '5.8', '5.9', '6.0', '6.1', '6.2', '6.3', '6.4'),
        'php_versions' => array('7.4', '8.0', '8.1', '8.2', '8.3'),
        'mysql_versions' => array('5.6', '5.7', '8.0'),
        'themes' => array('all'),
        'plugins' => array('elementor'),
    ),
    
    // Configuraciones de desarrollo
    'development' => array(
        'debug_mode' => false,
        'dev_tools' => false,
        'hot_reload' => false,
        'source_maps' => false,
    ),
    
    // Configuraciones de producción
    'production' => array(
        'minify_assets' => true,
        'compress_images' => true,
        'optimize_database' => true,
        'enable_caching' => true,
    ),
);
