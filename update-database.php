<?php
/**
 * Script de actualización de base de datos
 * Agregar columna project_year a la tabla portfolio_projects
 */

// Cargar WordPress
require_once('../../wp-load.php');

// Verificar permisos
if (!current_user_can('manage_options')) {
    die('No tienes permisos para ejecutar este script');
}

global $wpdb;

echo "<h2>Actualización de Base de Datos - Portfolio Plugin</h2>";
echo "<hr>";

// Nombre de la tabla
$table_name = $wpdb->prefix . 'portfolio_projects';

// Verificar si la tabla existe
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

if (!$table_exists) {
    echo "<p style='color:red;'><strong>❌ ERROR:</strong> La tabla $table_name no existe.</p>";
    die();
}

echo "<p>✅ Tabla encontrada: <strong>$table_name</strong></p>";

// Verificar si la columna project_year existe
$column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'project_year'");

if (empty($column_exists)) {
    echo "<p>⚠️ La columna <strong>project_year</strong> NO existe. Agregando...</p>";
    
    // Agregar la columna después de external_url
    $sql = "ALTER TABLE `$table_name` ADD COLUMN `project_year` VARCHAR(4) NULL AFTER `external_url`";
    
    $result = $wpdb->query($sql);
    
    if ($result !== false) {
        echo "<p style='color:green;'><strong>✅ ÉXITO:</strong> Columna project_year agregada correctamente.</p>";
    } else {
        echo "<p style='color:red;'><strong>❌ ERROR:</strong> No se pudo agregar la columna.</p>";
        echo "<p>Error: " . $wpdb->last_error . "</p>";
    }
} else {
    echo "<p style='color:green;'>✅ La columna <strong>project_year</strong> ya existe.</p>";
}

// Verificar si la columna youtube_url existe
$youtube_column = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'youtube_url'");

if (empty($youtube_column)) {
    echo "<p>⚠️ La columna <strong>youtube_url</strong> NO existe. Agregando...</p>";
    
    // Agregar la columna después de external_url
    $sql = "ALTER TABLE `$table_name` ADD COLUMN `youtube_url` VARCHAR(500) NULL AFTER `external_url`";
    
    $result = $wpdb->query($sql);
    
    if ($result !== false) {
        echo "<p style='color:green;'><strong>✅ ÉXITO:</strong> Columna youtube_url agregada correctamente.</p>";
    } else {
        echo "<p style='color:red;'><strong>❌ ERROR:</strong> No se pudo agregar la columna.</p>";
        echo "<p>Error: " . $wpdb->last_error . "</p>";
    }
} else {
    echo "<p style='color:green;'>✅ La columna <strong>youtube_url</strong> ya existe.</p>";
}

// Verificar si la columna vimeo_url existe
$vimeo_column = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'vimeo_url'");

if (empty($vimeo_column)) {
    echo "<p>⚠️ La columna <strong>vimeo_url</strong> NO existe. Agregando...</p>";
    
    // Agregar la columna después de youtube_url
    $sql = "ALTER TABLE `$table_name` ADD COLUMN `vimeo_url` VARCHAR(500) NULL AFTER `youtube_url`";
    
    $result = $wpdb->query($sql);
    
    if ($result !== false) {
        echo "<p style='color:green;'><strong>✅ ÉXITO:</strong> Columna vimeo_url agregada correctamente.</p>";
    } else {
        echo "<p style='color:red;'><strong>❌ ERROR:</strong> No se pudo agregar la columna.</p>";
        echo "<p>Error: " . $wpdb->last_error . "</p>";
    }
} else {
    echo "<p style='color:green;'>✅ La columna <strong>vimeo_url</strong> ya existe.</p>";
}

// Verificar si la columna content existe (debe eliminarse)
$content_column = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'content'");

if (!empty($content_column)) {
    echo "<p>⚠️ La columna <strong>content</strong> aún existe. Eliminando...</p>";
    
    $sql = "ALTER TABLE `$table_name` DROP COLUMN `content`";
    
    $result = $wpdb->query($sql);
    
    if ($result !== false) {
        echo "<p style='color:green;'><strong>✅ ÉXITO:</strong> Columna content eliminada correctamente.</p>";
    } else {
        echo "<p style='color:red;'><strong>❌ ERROR:</strong> No se pudo eliminar la columna content.</p>";
        echo "<p>Error: " . $wpdb->last_error . "</p>";
    }
} else {
    echo "<p style='color:green;'>✅ La columna <strong>content</strong> ya fue eliminada.</p>";
}

echo "<hr>";

// Mostrar estructura actual de la tabla
echo "<h3>Estructura actual de la tabla:</h3>";
$columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");

echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($columns as $column) {
    echo "<tr>";
    echo "<td><strong>" . $column->Field . "</strong></td>";
    echo "<td>" . $column->Type . "</td>";
    echo "<td>" . $column->Null . "</td>";
    echo "<td>" . $column->Key . "</td>";
    echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<p><strong>✅ Actualización completada.</strong></p>";
echo "<p><a href='wp-admin/admin.php?page=portfolio-projects'>&larr; Volver al Panel de Proyectos</a></p>";


