<?php
/**
 * Script de corrección rápida para el error de PortfolioDatabase
 * 
 * Este archivo corrige el error "Call to private PortfolioDatabase::__construct()"
 * 
 * INSTRUCCIONES:
 * 1. Sube este archivo a la raíz de tu sitio WordPress
 * 2. Ejecuta: tu-sitio.com/fix-portfolio-error.php
 * 3. Elimina este archivo después de ejecutarlo
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    // Cargar WordPress
    require_once('wp-config.php');
    require_once('wp-load.php');
}

// Verificar que el plugin esté activo
if (!class_exists('PortfolioPlugin')) {
    die('❌ El plugin Portfolio no está activo. Actívalo primero desde el panel de administración.');
}

echo '<h2>🔧 Corrección del Error de PortfolioDatabase</h2>';

// Verificar si el archivo principal existe
$plugin_file = WP_PLUGIN_DIR . '/portfolio-plugin-1/portfolio-plugin.php';
if (!file_exists($plugin_file)) {
    die('❌ No se encontró el archivo del plugin. Verifica la ruta del plugin.');
}

// Leer el contenido del archivo
$content = file_get_contents($plugin_file);

// Verificar si ya está corregido
if (strpos($content, 'PortfolioDatabase::get_instance()') !== false) {
    echo '<p style="color: green;">✅ El archivo ya está corregido. No es necesario hacer cambios.</p>';
} else {
    // Realizar la corrección
    $old_line = '        new PortfolioDatabase();';
    $new_line = '        PortfolioDatabase::get_instance();';
    
    if (strpos($content, $old_line) !== false) {
        $content = str_replace($old_line, $new_line, $content);
        
        // Escribir el archivo corregido
        if (file_put_contents($plugin_file, $content)) {
            echo '<p style="color: green;">✅ Archivo corregido exitosamente.</p>';
            echo '<p>El error "Call to private PortfolioDatabase::__construct()" ha sido solucionado.</p>';
        } else {
            echo '<p style="color: red;">❌ Error al escribir el archivo. Verifica los permisos.</p>';
        }
    } else {
        echo '<p style="color: orange;">⚠️ No se encontró la línea problemática. El archivo puede haber sido modificado.</p>';
    }
}

// Verificar que el plugin funcione correctamente
echo '<h3>🧪 Verificación Post-Corrección</h3>';

try {
    // Verificar que PortfolioDatabase se pueda instanciar
    if (class_exists('PortfolioDatabase')) {
        $db_instance = PortfolioDatabase::get_instance();
        echo '<p style="color: green;">✅ PortfolioDatabase se instancia correctamente.</p>';
    } else {
        echo '<p style="color: red;">❌ La clase PortfolioDatabase no existe.</p>';
    }
    
    // Verificar tablas de base de datos
    global $wpdb;
    $tables = [
        $wpdb->prefix . 'portfolio_projects',
        $wpdb->prefix . 'portfolio_categories'
    ];
    
    foreach ($tables as $table) {
        $exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'");
        if ($exists) {
            echo '<p style="color: green;">✅ Tabla ' . $table . ' existe.</p>';
        } else {
            echo '<p style="color: red;">❌ Tabla ' . $table . ' no existe.</p>';
        }
    }
    
    // Verificar opciones del plugin
    $options = get_option('portfolio_plugin_options');
    if ($options) {
        echo '<p style="color: green;">✅ Opciones del plugin configuradas.</p>';
    } else {
        echo '<p style="color: orange;">⚠️ Opciones del plugin no encontradas.</p>';
    }
    
} catch (Exception $e) {
    echo '<p style="color: red;">❌ Error durante la verificación: ' . $e->getMessage() . '</p>';
}

echo '<h3>📋 Próximos Pasos</h3>';
echo '<ol>';
echo '<li>Ve al panel de administración de WordPress</li>';
echo '<li>Verifica que el plugin Portfolio esté activo</li>';
echo '<li>Ve a <strong>Portfolio > Categorías</strong> para crear categorías</li>';
echo '<li>Ve a <strong>Portfolio > Proyectos</strong> para agregar proyectos</li>';
echo '<li>Usa el widget <strong>"Portfolio Grid"</strong> en Elementor</li>';
echo '<li><strong>Elimina este archivo</strong> después de verificar que todo funciona</li>';
echo '</ol>';

echo '<p style="background: #f0f8ff; padding: 15px; border: 1px solid #b0d4f1; border-radius: 5px;">';
echo '<strong>💡 Nota:</strong> Si sigues teniendo problemas, desactiva y reactiva el plugin desde el panel de administración.';
echo '</p>';
