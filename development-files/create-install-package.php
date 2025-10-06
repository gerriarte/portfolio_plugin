<?php
/**
 * Script para crear el ZIP de instalación del plugin Portfolio
 * 
 * Este script crea automáticamente el ZIP con solo los archivos necesarios
 * para la instalación en WordPress, excluyendo archivos de desarrollo.
 */

echo "🚀 Creando ZIP de instalación del Plugin Portfolio...\n\n";

// Archivos y carpetas que SÍ se deben incluir
$include_files = [
    'portfolio-plugin.php',
    'uninstall.php', 
    'config.php',
    'README.md',
    'admin/',
    'assets/',
    'includes/',
    'templates/'
];

// Archivos y carpetas que NO se deben incluir
$exclude_files = [
    'development-files/',
    'ARCHIVOS-INSTALACION.md',
    'create-package.php',
    'debug-*.php',
    'test-*.php',
    'fix-*.php',
    '*.md',
    '*.zip',
    '.git/',
    '.DS_Store',
    'Thumbs.db'
];

// Verificar que estamos en el directorio correcto
if (!file_exists('portfolio-plugin.php')) {
    echo "❌ Error: No se encontró portfolio-plugin.php\n";
    echo "   Ejecuta este script desde la raíz del proyecto\n";
    exit(1);
}

// Crear directorio temporal
$temp_dir = 'temp-package';
if (is_dir($temp_dir)) {
    echo "🧹 Limpiando directorio temporal...\n";
    removeDirectory($temp_dir);
}

echo "📁 Creando directorio temporal...\n";
mkdir($temp_dir);

// Copiar archivos necesarios
echo "📋 Copiando archivos necesarios...\n";
foreach ($include_files as $file) {
    if (file_exists($file) || is_dir($file)) {
        if (is_dir($file)) {
            copyDirectory($file, $temp_dir . '/' . $file);
            echo "   ✅ Carpeta: $file\n";
        } else {
            copy($file, $temp_dir . '/' . basename($file));
            echo "   ✅ Archivo: $file\n";
        }
    } else {
        echo "   ⚠️  No encontrado: $file\n";
    }
}

// Verificar estructura
echo "\n🔍 Verificando estructura del paquete...\n";
$required_files = [
    'portfolio-plugin.php',
    'uninstall.php',
    'config.php',
    'admin/categories.php',
    'admin/projects.php', 
    'admin/settings.php',
    'assets/css/admin.css',
    'assets/css/frontend.css',
    'assets/js/admin.js',
    'assets/js/frontend.js',
    'includes/class-admin.php',
    'includes/class-api.php',
    'includes/class-database.php',
    'includes/class-elementor-widget.php',
    'includes/class-frontend.php',
    'includes/class-logger.php'
];

$missing_files = [];
foreach ($required_files as $file) {
    if (!file_exists($temp_dir . '/' . $file)) {
        $missing_files[] = $file;
    }
}

if (!empty($missing_files)) {
    echo "❌ Archivos faltantes:\n";
    foreach ($missing_files as $file) {
        echo "   - $file\n";
    }
    echo "\n⚠️  El paquete puede estar incompleto\n";
} else {
    echo "✅ Todos los archivos necesarios están presentes\n";
}

// Crear ZIP
$zip_filename = 'portfolio-plugin-install.zip';
if (file_exists($zip_filename)) {
    unlink($zip_filename);
}

echo "\n📦 Creando archivo ZIP...\n";

// Usar ZipArchive si está disponible
if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    if ($zip->open($zip_filename, ZipArchive::CREATE) === TRUE) {
        addDirectoryToZip($zip, $temp_dir, '');
        $zip->close();
        echo "✅ ZIP creado exitosamente: $zip_filename\n";
    } else {
        echo "❌ Error al crear el ZIP\n";
        exit(1);
    }
} else {
    echo "⚠️  ZipArchive no disponible, usando método alternativo...\n";
    // Método alternativo usando exec
    $command = "cd $temp_dir && zip -r ../$zip_filename .";
    exec($command, $output, $return_code);
    
    if ($return_code === 0) {
        echo "✅ ZIP creado exitosamente: $zip_filename\n";
    } else {
        echo "❌ Error al crear el ZIP\n";
        exit(1);
    }
}

// Limpiar directorio temporal
echo "\n🧹 Limpiando archivos temporales...\n";
removeDirectory($temp_dir);

// Mostrar información del ZIP
if (file_exists($zip_filename)) {
    $size = filesize($zip_filename);
    $size_mb = round($size / 1024 / 1024, 2);
    echo "📊 Tamaño del ZIP: {$size_mb} MB ({$size} bytes)\n";
    
    echo "\n🎉 ¡Paquete de instalación creado exitosamente!\n";
    echo "📁 Archivo: $zip_filename\n";
    echo "🚀 Listo para instalar en WordPress\n\n";
    
    echo "📋 Instrucciones de instalación:\n";
    echo "1. Ve a WordPress Admin > Plugins > Añadir nuevo\n";
    echo "2. Haz clic en 'Subir plugin'\n";
    echo "3. Selecciona el archivo: $zip_filename\n";
    echo "4. Haz clic en 'Instalar ahora'\n";
    echo "5. Activa el plugin\n";
    echo "6. Ve a Portfolio en el menú de administración\n\n";
    
} else {
    echo "❌ Error: No se pudo crear el archivo ZIP\n";
    exit(1);
}

// Funciones auxiliares
function copyDirectory($src, $dst) {
    if (!is_dir($dst)) {
        mkdir($dst, 0755, true);
    }
    
    $dir = opendir($src);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $srcFile = $src . '/' . $file;
            $dstFile = $dst . '/' . $file;
            
            if (is_dir($srcFile)) {
                copyDirectory($srcFile, $dstFile);
            } else {
                copy($srcFile, $dstFile);
            }
        }
    }
    closedir($dir);
}

function removeDirectory($dir) {
    if (!is_dir($dir)) return;
    
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

function addDirectoryToZip($zip, $dir, $zipPath) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $filePath = $dir . '/' . $file;
            $zipFilePath = $zipPath . $file;
            
            if (is_dir($filePath)) {
                $zip->addEmptyDir($zipFilePath);
                addDirectoryToZip($zip, $filePath, $zipFilePath . '/');
            } else {
                $zip->addFile($filePath, $zipFilePath);
            }
        }
    }
}
