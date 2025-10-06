<?php
/**
 * Script para crear el paquete ZIP del plugin Portfolio
 * 
 * Este script crea un ZIP con solo los archivos necesarios para la instalación
 */

// Archivos obligatorios que deben incluirse
$required_files = [
    'portfolio-plugin.php',
    'uninstall.php',
    'config.php',
    'README.md',
    'includes/class-database.php',
    'includes/class-logger.php',
    'includes/class-admin.php',
    'includes/class-elementor-widget.php',
    'includes/class-frontend.php',
    'includes/class-api.php',
    'admin/projects.php',
    'admin/categories.php',
    'admin/settings.php',
    'assets/css/admin.css',
    'assets/css/frontend.css',
    'assets/js/admin.js',
    'assets/js/frontend.js'
];

// Carpetas que deben crearse (aunque estén vacías)
$required_dirs = [
    'assets/images',
    'templates',
    'languages'
];

// Archivos que NO deben incluirse
$excluded_files = [
    'modal detail portfolio.jpg',
    'portfolio list.jpg',
    'fix-portfolio-error.php',
    'test-installation.php',
    'INSTALACION.md',
    'portfolio-plugin.zip',
    'create-package.php'
];

echo "<h2>📦 Generador de Paquete ZIP - Plugin Portfolio</h2>\n";

// Verificar que todos los archivos requeridos existan
echo "<h3>🔍 Verificando archivos requeridos...</h3>\n";
$missing_files = [];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";
    } else {
        echo "❌ {$file} - FALTANTE\n";
        $missing_files[] = $file;
    }
}

if (!empty($missing_files)) {
    echo "\n❌ <strong>Error:</strong> Faltan archivos requeridos. No se puede crear el paquete.\n";
    echo "Archivos faltantes:\n";
    foreach ($missing_files as $file) {
        echo "- {$file}\n";
    }
    exit;
}

// Crear el ZIP
$zip_filename = 'portfolio-plugin-' . date('Y-m-d') . '.zip';
$zip = new ZipArchive();

if ($zip->open($zip_filename, ZipArchive::CREATE) !== TRUE) {
    echo "❌ No se pudo crear el archivo ZIP.\n";
    exit;
}

echo "\n<h3>📁 Agregando archivos al ZIP...</h3>\n";

// Agregar archivos requeridos
foreach ($required_files as $file) {
    if (file_exists($file)) {
        $zip->addFile($file, "portfolio-plugin/{$file}");
        echo "✅ Agregado: {$file}\n";
    }
}

// Crear carpetas vacías
foreach ($required_dirs as $dir) {
    $zip->addEmptyDir("portfolio-plugin/{$dir}");
    echo "📁 Carpeta creada: {$dir}\n";
}

// Cerrar el ZIP
$zip->close();

echo "\n<h3>✅ Paquete creado exitosamente</h3>\n";
echo "<p><strong>Archivo:</strong> {$zip_filename}</p>\n";
echo "<p><strong>Tamaño:</strong> " . formatBytes(filesize($zip_filename)) . "</p>\n";

echo "\n<h3>📋 Contenido del paquete:</h3>\n";
echo "<ul>\n";
foreach ($required_files as $file) {
    echo "<li>portfolio-plugin/{$file}</li>\n";
}
foreach ($required_dirs as $dir) {
    echo "<li>portfolio-plugin/{$dir}/ (carpeta vacía)</li>\n";
}
echo "</ul>\n";

echo "\n<h3>🚀 Instrucciones de instalación:</h3>\n";
echo "<ol>\n";
echo "<li>Sube el archivo <strong>{$zip_filename}</strong> a WordPress</li>\n";
echo "<li>Ve a <strong>Plugins > Añadir nuevo</strong></li>\n";
echo "<li>Haz clic en <strong>Subir plugin</strong></li>\n";
echo "<li>Selecciona el archivo ZIP</li>\n";
echo "<li>Haz clic en <strong>Instalar ahora</strong></li>\n";
echo "<li>Activa el plugin</li>\n";
echo "</ol>\n";

echo "\n<p style='background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>\n";
echo "✅ <strong>El paquete está listo para distribución.</strong><br>\n";
echo "El archivo ZIP contiene todos los archivos necesarios para instalar el plugin correctamente.\n";
echo "</p>\n";

function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}
