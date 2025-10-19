<?php
/**
 * Script de diagnóstico para problemas de base de datos
 * 
 * INSTRUCCIONES:
 * 1. Sube este archivo a la carpeta raíz de tu plugin
 * 2. Accede a: http://tudominio.com/wp-content/plugins/portfolio-plugin/diagnose-database.php
 * 3. Revisa la información mostrada
 * 4. Elimina este archivo después de usar
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

?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico de Base de Datos - Portfolio Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: #4CAF50; background: #f1f8e9; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: #f44336; background: #ffebee; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #2196F3; background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #FF9800; background: #fff3e0; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .test-section { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .table-status { display: inline-block; margin: 5px 10px; padding: 5px 10px; border-radius: 3px; }
        .exists { background: #e8f5e8; color: #2e7d32; }
        .missing { background: #ffebee; color: #c62828; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .test-pass { background: #e8f5e8; color: #2e7d32; }
        .test-fail { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico de Base de Datos - Portfolio Plugin</h1>
    
    <?php
    global $wpdb;
    
    echo '<div class="info">📊 Iniciando diagnóstico completo...</div>';
    
    // Test 1: Información básica de la base de datos
    echo '<div class="test-section">';
    echo '<h3>1️⃣ Información de Base de Datos</h3>';
    
    try {
        echo '<div class="test-result test-pass">✅ Conexión a base de datos: OK</div>';
        echo '<pre>';
        echo "Prefijo de tablas: {$wpdb->prefix}\n";
        echo "Charset: " . $wpdb->get_charset_collate() . "\n";
        echo "Versión MySQL: " . $wpdb->get_var("SELECT VERSION()") . "\n";
        echo "Base de datos actual: " . $wpdb->get_var("SELECT DATABASE()") . "\n";
        echo "Usuario de BD: " . $wpdb->get_var("SELECT USER()") . "\n";
        echo '</pre>';
    } catch (Exception $e) {
        echo '<div class="test-result test-fail">❌ Error de conexión: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';
    
    // Test 2: Verificar tablas existentes
    echo '<div class="test-section">';
    echo '<h3>2️⃣ Estado de Tablas del Plugin</h3>';
    
    $tables = array(
        'sabsfe_portfolio_categories',
        'sabsfe_portfolio_projects',
        'sabsfe_portfolio_project_views',
        'sabsfe_portfolio_project_likes'
    );
    
    $all_exist = true;
    foreach ($tables as $table) {
        $full_table_name = $wpdb->prefix . $table;
        $exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'");
        
        if ($exists) {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}{$table}`");
            echo "<span class='table-status exists'>✅ {$table}: {$count} registros</span>";
        } else {
            echo "<span class='table-status missing'>❌ {$table}: NO EXISTE</span>";
            $all_exist = false;
        }
    }
    
    if ($all_exist) {
        echo '<div class="test-result test-pass">✅ Todas las tablas existen</div>';
    } else {
        echo '<div class="test-result test-fail">❌ Faltan algunas tablas</div>';
    }
    echo '</div>';
    
    // Test 3: Permisos de usuario
    echo '<div class="test-section">';
    echo '<h3>3️⃣ Permisos de Usuario de Base de Datos</h3>';
    
    try {
        $grants = $wpdb->get_results("SHOW GRANTS FOR CURRENT_USER()");
        echo '<div class="test-result test-pass">✅ Permisos obtenidos exitosamente</div>';
        echo '<pre>';
        foreach ($grants as $grant) {
            echo $grant->{'Grants for ' . $wpdb->get_var("SELECT USER()")} . "\n";
        }
        echo '</pre>';
    } catch (Exception $e) {
        echo '<div class="test-result test-fail">❌ Error al obtener permisos: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';
    
    // Test 4: Crear tabla de prueba
    echo '<div class="test-section">';
    echo '<h3>4️⃣ Test de Creación de Tabla</h3>';
    
    $test_table = $wpdb->prefix . 'sabsfe_test_table';
    
    try {
        // Eliminar tabla de prueba si existe
        $wpdb->query("DROP TABLE IF EXISTS `{$test_table}`");
        
        // Crear tabla de prueba
        $create_sql = "
            CREATE TABLE `{$test_table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `test_field` varchar(255),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $result = $wpdb->query($create_sql);
        
        if ($result !== false) {
            echo '<div class="test-result test-pass">✅ Tabla de prueba creada exitosamente</div>';
            
            // Insertar datos de prueba
            $insert_result = $wpdb->insert($test_table, array('test_field' => 'test_value'));
            if ($insert_result) {
                echo '<div class="test-result test-pass">✅ Datos insertados en tabla de prueba</div>';
            } else {
                echo '<div class="test-result test-fail">❌ Error al insertar datos: ' . $wpdb->last_error . '</div>';
            }
            
            // Limpiar tabla de prueba
            $wpdb->query("DROP TABLE `{$test_table}`");
            echo '<div class="test-result test-pass">✅ Tabla de prueba eliminada</div>';
            
        } else {
            echo '<div class="test-result test-fail">❌ Error al crear tabla de prueba: ' . $wpdb->last_error . '</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="test-result test-fail">❌ Excepción en test de tabla: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';
    
    // Test 5: Verificar función dbDelta
    echo '<div class="test-section">';
    echo '<h3>5️⃣ Test de Función dbDelta</h3>';
    
    try {
        if (!function_exists('dbDelta')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        }
        
        if (function_exists('dbDelta')) {
            echo '<div class="test-result test-pass">✅ Función dbDelta disponible</div>';
            
            // Test con dbDelta
            $test_table_delta = $wpdb->prefix . 'sabsfe_test_delta';
            $sql_delta = "
                CREATE TABLE {$test_table_delta} (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    test_field varchar(255),
                    PRIMARY KEY (id)
                ) " . $wpdb->get_charset_collate() . ";
            ";
            
            $result_delta = dbDelta($sql_delta);
            
            if (!empty($result_delta)) {
                echo '<div class="test-result test-pass">✅ dbDelta funcionando correctamente</div>';
                echo '<pre>Resultado dbDelta: ' . print_r($result_delta, true) . '</pre>';
                
                // Limpiar
                $wpdb->query("DROP TABLE IF EXISTS `{$test_table_delta}`");
            } else {
                echo '<div class="test-result test-fail">❌ dbDelta no retornó resultados</div>';
            }
            
        } else {
            echo '<div class="test-result test-fail">❌ Función dbDelta no disponible</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="test-result test-fail">❌ Error con dbDelta: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';
    
    // Test 6: Información del plugin
    echo '<div class="test-section">';
    echo '<h3>6️⃣ Información del Plugin</h3>';
    
    try {
        $plugin_path = plugin_dir_path(__FILE__);
        echo '<pre>';
        echo "Ruta del plugin: {$plugin_path}\n";
        echo "Archivo database.php existe: " . (file_exists($plugin_path . 'includes/class-database.php') ? 'SÍ' : 'NO') . "\n";
        echo "Archivo database-enhanced.php existe: " . (file_exists($plugin_path . 'includes/class-database-enhanced.php') ? 'SÍ' : 'NO') . "\n";
        
        if (class_exists('Sabsfe_Portfolio_Database')) {
            echo "Clase Sabsfe_Portfolio_Database: DISPONIBLE\n";
        } else {
            echo "Clase Sabsfe_Portfolio_Database: NO DISPONIBLE\n";
        }
        
        if (class_exists('Sabsfe_Portfolio_Database_Enhanced')) {
            echo "Clase Sabsfe_Portfolio_Database_Enhanced: DISPONIBLE\n";
        } else {
            echo "Clase Sabsfe_Portfolio_Database_Enhanced: NO DISPONIBLE\n";
        }
        echo '</pre>';
        
    } catch (Exception $e) {
        echo '<div class="test-result test-fail">❌ Error al obtener información del plugin: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';
    
    // Resumen y recomendaciones
    echo '<div class="test-section">';
    echo '<h3>📋 Resumen y Recomendaciones</h3>';
    
    if ($all_exist) {
        echo '<div class="success">🎉 ¡Todas las tablas existen! El plugin debería funcionar correctamente.</div>';
    } else {
        echo '<div class="warning">⚠️ Faltan algunas tablas. Recomendaciones:</div>';
        echo '<ul>';
        echo '<li>Usar el script <strong>force-create-tables.php</strong> para crear las tablas</li>';
        echo '<li>Verificar que el usuario de BD tenga permisos CREATE TABLE</li>';
        echo '<li>Contactar al proveedor de hosting si persisten los problemas</li>';
        echo '<li>Probar en un entorno de desarrollo limpio</li>';
        echo '</ul>';
    }
    echo '</div>';
    ?>
    
    <hr>
    <div class="warning">
        <strong>⚠️ IMPORTANTE:</strong> Elimina este archivo después de usar por seguridad.
    </div>
    <p><a href="<?php echo admin_url('plugins.php'); ?>">← Volver a Plugins</a></p>
</body>
</html>

