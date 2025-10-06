<?php
/**
 * Archivo de desinstalación del plugin Portfolio Projects Manager
 * 
 * Este archivo se ejecuta cuando el plugin es eliminado completamente
 * desde el panel de administración de WordPress.
 * 
 * IMPORTANTE: Este archivo solo se ejecuta si el plugin es eliminado
 * desde el panel de administración, NO cuando se desactiva.
 */

// Prevenir acceso directo
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Verificar permisos
if (!current_user_can('delete_plugins')) {
    exit;
}

// Opción para mantener datos (configurable)
$keep_data = get_option('portfolio_keep_data_on_uninstall', false);

if (!$keep_data) {
    
    // Eliminar tablas de base de datos
    global $wpdb;
    
    $tables = array(
        $wpdb->prefix . 'portfolio_projects',
        $wpdb->prefix . 'portfolio_categories'
    );
    
    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS {$table}");
    }
    
    // Eliminar opciones del plugin
    $options_to_delete = array(
        'portfolio_plugin_options',
        'portfolio_keep_data_on_uninstall',
        'portfolio_version'
    );
    
    foreach ($options_to_delete as $option) {
        delete_option($option);
    }
    
    // Eliminar metadatos de usuarios relacionados con el plugin
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'portfolio_%'");
    
    // Eliminar archivos de log
    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/portfolio-plugin-logs.log';
    if (file_exists($log_file)) {
        unlink($log_file);
    }
    
    // Eliminar archivos de cache relacionados
    $cache_files = glob($upload_dir['basedir'] . '/portfolio-cache-*');
    foreach ($cache_files as $cache_file) {
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    }
    
    // Limpiar transients relacionados
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_portfolio_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_portfolio_%'");
    
    // Log de desinstalación (si el sistema de logging aún está disponible)
    if (function_exists('error_log')) {
        error_log('Portfolio Plugin: Datos eliminados durante la desinstalación');
    }
    
} else {
    
    // Solo eliminar configuraciones, mantener datos
    delete_option('portfolio_plugin_options');
    delete_option('portfolio_version');
    
    if (function_exists('error_log')) {
        error_log('Portfolio Plugin: Desinstalado pero datos mantenidos');
    }
}

// Limpiar rewrite rules
flush_rewrite_rules();
