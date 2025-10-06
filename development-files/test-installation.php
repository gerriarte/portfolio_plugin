<?php
/**
 * Archivo de prueba para verificar la instalación del plugin
 * Este archivo se puede eliminar después de verificar que todo funciona
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    die('Acceso directo no permitido');
}

// Verificar que el plugin esté activo
if (!class_exists('PortfolioPlugin')) {
    die('El plugin Portfolio no está activo');
}

// Verificar que las clases principales existan
$required_classes = [
    'PortfolioDatabase',
    'PortfolioLogger', 
    'PortfolioAdmin',
    'PortfolioFrontend',
    'PortfolioAPI'
];

foreach ($required_classes as $class) {
    if (!class_exists($class)) {
        die("Error: La clase {$class} no existe");
    }
}

// Verificar que PortfolioDatabase se pueda instanciar correctamente
try {
    $db_instance = PortfolioDatabase::get_instance();
    if (!$db_instance) {
        die("Error: No se pudo obtener la instancia de PortfolioDatabase");
    }
} catch (Exception $e) {
    die("Error al instanciar PortfolioDatabase: " . $e->getMessage());
}

// Verificar que las tablas de base de datos existan
global $wpdb;
$tables = [
    $wpdb->prefix . 'portfolio_projects',
    $wpdb->prefix . 'portfolio_categories'
];

foreach ($tables as $table) {
    $exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'");
    if (!$exists) {
        die("Error: La tabla {$table} no existe");
    }
}

// Verificar que las opciones del plugin existan
$options = get_option('portfolio_plugin_options');
if (!$options) {
    die('Error: Las opciones del plugin no están configuradas');
}

echo '<div style="background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px;">';
echo '<h3>✅ Plugin Portfolio instalado correctamente</h3>';
echo '<p>Todas las verificaciones han pasado exitosamente:</p>';
echo '<ul>';
echo '<li>✅ Clases principales cargadas</li>';
echo '<li>✅ Tablas de base de datos creadas</li>';
echo '<li>✅ Opciones del plugin configuradas</li>';
echo '</ul>';
echo '<p><strong>El plugin está listo para usar.</strong></p>';
echo '</div>';

// Mostrar información adicional
echo '<div style="background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; margin: 20px;">';
echo '<h4>Información del Plugin:</h4>';
echo '<ul>';
echo '<li><strong>Versión:</strong> ' . PORTFOLIO_PLUGIN_VERSION . '</li>';
echo '<li><strong>Ruta del plugin:</strong> ' . PORTFOLIO_PLUGIN_PATH . '</li>';
echo '<li><strong>URL del plugin:</strong> ' . PORTFOLIO_PLUGIN_URL . '</li>';
echo '<li><strong>Proyectos en la base de datos:</strong> ' . $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_projects") . '</li>';
echo '<li><strong>Categorías en la base de datos:</strong> ' . $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_categories") . '</li>';
echo '</ul>';
echo '</div>';

echo '<div style="background: #fff3cd; color: #856404; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px;">';
echo '<h4>📝 Próximos pasos:</h4>';
echo '<ol>';
echo '<li>Ve a <strong>Portfolio > Categorías</strong> para crear categorías</li>';
echo '<li>Ve a <strong>Portfolio > Proyectos</strong> para agregar proyectos</li>';
echo '<li>Edita una página con Elementor y busca el widget <strong>"Portfolio Grid"</strong></li>';
echo '<li>Puedes eliminar este archivo de prueba después de verificar que todo funciona</li>';
echo '</ol>';
echo '</div>';
