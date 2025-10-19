<?php
/**
 * Script simple para corregir las tablas de base de datos
 * Ejecutar desde el panel de administración de WordPress
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Solo para administradores
if (!current_user_can('manage_options')) {
    wp_die('No tienes permisos para ejecutar este script');
}

// Cargar las clases necesarias
require_once(plugin_dir_path(__FILE__) . 'includes/class-database.php');

try {
    echo '<div style="padding: 20px; background: #f1f8e9; border: 1px solid #4CAF50; border-radius: 4px; margin: 20px 0;">';
    echo '<h3>🔧 Creando tablas de base de datos...</h3>';
    
    // Crear las tablas
    $result = Sabsfe_Portfolio_Database::create_tables();
    
    if ($result) {
        echo '<p style="color: #4CAF50;"><strong>✅ ¡Tablas creadas exitosamente!</strong></p>';
        
        // Verificar las tablas
        global $wpdb;
        $tables = array(
            'sabsfe_portfolio_categories',
            'sabsfe_portfolio_projects', 
            'sabsfe_portfolio_project_views',
            'sabsfe_portfolio_project_likes'
        );
        
        echo '<h4>📋 Estado de las tablas:</h4>';
        echo '<ul>';
        foreach ($tables as $table) {
            $exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'");
            if ($exists) {
                $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}{$table}");
                echo "<li>✅ {$table}: {$count} registros</li>";
            } else {
                echo "<li>❌ {$table}: NO EXISTE</li>";
            }
        }
        echo '</ul>';
        
        echo '<p style="color: #4CAF50;"><strong>🎉 ¡El plugin está listo para usar!</strong></p>';
        
    } else {
        echo '<p style="color: #f44336;"><strong>❌ Error al crear las tablas</strong></p>';
        echo '<p>Último error de MySQL: ' . ($wpdb->last_error ?: 'No hay errores') . '</p>';
    }
    
    echo '</div>';
    
} catch (Exception $e) {
    echo '<div style="padding: 20px; background: #ffebee; border: 1px solid #f44336; border-radius: 4px; margin: 20px 0;">';
    echo '<h3>❌ Error:</h3>';
    echo '<p>' . esc_html($e->getMessage()) . '</p>';
    echo '<p>Último error de MySQL: ' . ($wpdb->last_error ?: 'No hay errores') . '</p>';
    echo '</div>';
}

