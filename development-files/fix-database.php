<?php
/**
 * Script de reparación de base de datos para el Plugin Portfolio
 * 
 * Este script crea las tablas de la base de datos si no existen
 * y corrige problemas de activación del plugin.
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🔧 Reparación de Base de Datos - Plugin Portfolio</h2>';

// Verificar que el plugin esté activo
if (!class_exists('PortfolioPlugin')) {
    echo '<p style="color: red;">❌ El plugin Portfolio no está activo</p>';
    echo '<p>Activa el plugin primero desde Plugins > Plugins Instalados</p>';
    exit;
}

echo '<p style="color: green;">✅ Plugin Portfolio está activo</p>';

// Verificar que la clase PortfolioDatabase exista
if (!class_exists('PortfolioDatabase')) {
    echo '<p style="color: red;">❌ Clase PortfolioDatabase no encontrada</p>';
    echo '<p>El plugin puede estar corrupto. Reinstala el plugin.</p>';
    exit;
}

echo '<p style="color: green;">✅ Clase PortfolioDatabase encontrada</p>';

// Verificar tablas existentes
global $wpdb;
$tables_to_check = [
    'portfolio_projects',
    'portfolio_categories', 
    'portfolio_project_views',
    'portfolio_project_likes'
];

echo '<h3>🔍 Verificando Tablas Existentes</h3>';
$missing_tables = [];

foreach ($tables_to_check as $table) {
    $full_table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'");
    
    if ($exists) {
        echo "<p style='color: green;'>✅ Tabla $table existe</p>";
    } else {
        echo "<p style='color: red;'>❌ Tabla $table NO existe</p>";
        $missing_tables[] = $table;
    }
}

// Crear tablas faltantes
if (!empty($missing_tables)) {
    echo '<h3>🔨 Creando Tablas Faltantes</h3>';
    
    try {
        // Llamar al método de creación de tablas
        PortfolioDatabase::create_tables();
        echo '<p style="color: green;">✅ Tablas creadas exitosamente</p>';
        
        // Verificar nuevamente
        echo '<h3>🔍 Verificación Post-Creación</h3>';
        foreach ($tables_to_check as $table) {
            $full_table_name = $wpdb->prefix . $table;
            $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'");
            
            if ($exists) {
                echo "<p style='color: green;'>✅ Tabla $table creada correctamente</p>";
            } else {
                echo "<p style='color: red;'>❌ Error: Tabla $table no se pudo crear</p>";
            }
        }
        
    } catch (Exception $e) {
        echo '<p style="color: red;">❌ Error al crear tablas: ' . $e->getMessage() . '</p>';
    }
} else {
    echo '<p style="color: green;">✅ Todas las tablas existen</p>';
}

// Verificar estructura de tablas
echo '<h3>📋 Verificando Estructura de Tablas</h3>';

$expected_structure = [
    'portfolio_projects' => [
        'id', 'title', 'slug', 'description', 'content', 'featured_image', 
        'gallery', 'category_id', 'status', 'featured', 'external_url', 
        'project_date', 'views', 'likes', 'created_at', 'updated_at'
    ],
    'portfolio_categories' => [
        'id', 'name', 'slug', 'description', 'color', 'created_at', 'updated_at'
    ]
];

foreach ($expected_structure as $table => $columns) {
    $full_table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'");
    
    if ($exists) {
        echo "<h4>Tabla: $table</h4>";
        $actual_columns = $wpdb->get_col("DESCRIBE $full_table_name");
        
        foreach ($columns as $column) {
            if (in_array($column, $actual_columns)) {
                echo "<p style='color: green;'>✅ Columna $column existe</p>";
            } else {
                echo "<p style='color: red;'>❌ Columna $column NO existe</p>";
            }
        }
    }
}

// Crear datos de ejemplo si las tablas están vacías
echo '<h3>📝 Creando Datos de Ejemplo</h3>';

// Verificar si hay categorías
$categories_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_categories");
if ($categories_count == 0) {
    echo '<p>Creando categorías de ejemplo...</p>';
    
    $sample_categories = [
        ['name' => 'Desarrollo Web', 'slug' => 'desarrollo-web', 'color' => '#2196F3'],
        ['name' => 'Diseño', 'slug' => 'diseno', 'color' => '#FF9800'],
        ['name' => 'Marketing', 'slug' => 'marketing', 'color' => '#4CAF50']
    ];
    
    foreach ($sample_categories as $cat) {
        $result = PortfolioDatabase::create_category($cat);
        if ($result) {
            echo "<p style='color: green;'>✅ Categoría creada: {$cat['name']}</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al crear categoría: {$cat['name']}</p>";
        }
    }
} else {
    echo "<p style='color: green;'>✅ Ya hay $categories_count categorías en la base de datos</p>";
}

// Verificar si hay proyectos
$projects_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_projects");
if ($projects_count == 0) {
    echo '<p>Creando proyecto de ejemplo...</p>';
    
    $sample_project = [
        'title' => 'Proyecto de Ejemplo',
        'slug' => 'proyecto-ejemplo',
        'description' => 'Este es un proyecto de ejemplo para demostrar el funcionamiento del plugin.',
        'content' => '<p>Contenido detallado del proyecto de ejemplo.</p>',
        'category_id' => 1,
        'status' => 'published',
        'featured' => 1,
        'project_date' => date('Y-m-d')
    ];
    
    $result = PortfolioDatabase::create_project($sample_project);
    if ($result) {
        echo "<p style='color: green;'>✅ Proyecto de ejemplo creado</p>";
    } else {
        echo "<p style='color: red;'>❌ Error al crear proyecto de ejemplo</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Ya hay $projects_count proyectos en la base de datos</p>";
}

// Verificar permisos de usuario
echo '<h3>👤 Verificando Permisos</h3>';
if (current_user_can('manage_options')) {
    echo '<p style="color: green;">✅ Usuario tiene permisos de administrador</p>';
} else {
    echo '<p style="color: red;">❌ Usuario no tiene permisos de administrador</p>';
}

// Verificar configuración de WordPress
echo '<h3>⚙️ Configuración de WordPress</h3>';
echo '<ul>';
echo '<li><strong>Prefijo de tablas:</strong> ' . $wpdb->prefix . '</li>';
echo '<li><strong>Charset de BD:</strong> ' . $wpdb->charset . '</li>';
echo '<li><strong>Collate:</strong> ' . $wpdb->collate . '</li>';
echo '<li><strong>WP_DEBUG:</strong> ' . (defined('WP_DEBUG') && WP_DEBUG ? 'Activado' : 'Desactivado') . '</li>';
echo '</ul>';

// Instrucciones finales
echo '<h3>🎯 Próximos Pasos</h3>';
echo '<ol>';
echo '<li><strong>Si las tablas se crearon correctamente:</strong> Ve a Portfolio > Proyectos para gestionar tu portafolio</li>';
echo '<li><strong>Si hay errores:</strong> Revisa los logs de error de WordPress</li>';
echo '<li><strong>Para crear más proyectos:</strong> Ve a Portfolio > Proyectos > Añadir Nuevo</li>';
echo '<li><strong>Para usar en Elementor:</strong> Busca "Portfolio Grid" en la categoría General</li>';
echo '</ol>';

echo '<p style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724;">';
echo '<strong>✅ Reparación completada</strong><br>';
echo 'El plugin Portfolio debería funcionar correctamente ahora.';
echo '</p>';
