<?php
/**
 * Script para crear tablas de base de datos del plugin Portfolio
 * 
 * INSTRUCCIONES:
 * 1. Sube este archivo a la carpeta raíz de tu plugin
 * 2. Accede a: http://tudominio.com/wp-content/plugins/portfolio-plugin/create-tables.php
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

// Cargar las clases del plugin
$plugin_path = plugin_dir_path(__FILE__);
require_once($plugin_path . 'includes/class-database.php');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Crear Tablas - Portfolio Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: #4CAF50; background: #f1f8e9; padding: 15px; border-radius: 4px; }
        .error { color: #f44336; background: #ffebee; padding: 15px; border-radius: 4px; }
        .info { color: #2196F3; background: #e3f2fd; padding: 15px; border-radius: 4px; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 Crear Tablas de Base de Datos - Portfolio Plugin</h1>
    
    <?php
    try {
        echo '<div class="info">🔄 Iniciando creación de tablas...</div>';
        
        // Verificar que la clase existe
        if (!class_exists('Sabsfe_Portfolio_Database')) {
            throw new Exception('Error: La clase Sabsfe_Portfolio_Database no está disponible');
        }
        
        // Crear las tablas
        $result = Sabsfe_Portfolio_Database::create_tables();
        
        if ($result) {
            echo '<div class="success">✅ ¡Tablas creadas exitosamente!</div>';
            
            // Verificar que las tablas existen
            global $wpdb;
            $tables = array(
                'sabsfe_portfolio_categories',
                'sabsfe_portfolio_projects',
                'sabsfe_portfolio_project_views',
                'sabsfe_portfolio_project_likes'
            );
            
            echo '<h3>📋 Verificación de Tablas:</h3>';
            echo '<pre>';
            foreach ($tables as $table) {
                $full_table_name = $wpdb->prefix . $table;
                $exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'");
                
                if ($exists) {
                    $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}{$table}");
                    echo "✅ {$full_table_name} - {$count} registros\n";
                } else {
                    echo "❌ {$full_table_name} - NO EXISTE\n";
                }
            }
            echo '</pre>';
            
            // Mostrar información de la base de datos
            echo '<h3>🔍 Información de Base de Datos:</h3>';
            echo '<pre>';
            echo "Prefijo de tablas: {$wpdb->prefix}\n";
            echo "Charset: " . $wpdb->get_charset_collate() . "\n";
            echo "Última consulta: " . $wpdb->last_query . "\n";
            echo "Último error: " . ($wpdb->last_error ?: 'Ninguno') . "\n";
            echo '</pre>';
            
            echo '<div class="success">🎉 ¡El plugin está listo para usar!</div>';
            
        } else {
            throw new Exception('Error: No se pudieron crear las tablas');
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
    <p><strong>⚠️ IMPORTANTE:</strong> Elimina este archivo después de ejecutarlo por seguridad.</p>
    <p><a href="<?php echo admin_url('plugins.php'); ?>">← Volver a Plugins</a></p>
</body>
</html>

