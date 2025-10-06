<?php
/**
 * Script de Limpieza de Datos Duplicados - Plugin Portfolio
 * 
 * Este script limpia datos duplicados y corrige problemas de activación múltiple
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🧹 Limpieza de Datos Duplicados - Plugin Portfolio</h2>';

// Verificar que el plugin esté activo
if (!class_exists('PortfolioPlugin')) {
    echo '<p style="color: red;">❌ El plugin Portfolio no está activo</p>';
    exit;
}

echo '<p style="color: green;">✅ Plugin Portfolio está activo</p>';

global $wpdb;

// Limpiar categorías duplicadas
echo '<h3>🗂️ Limpiando Categorías Duplicadas</h3>';

$table_categories = $wpdb->prefix . 'portfolio_categories';

// Encontrar categorías duplicadas por slug
$duplicates = $wpdb->get_results("
    SELECT slug, COUNT(*) as count 
    FROM $table_categories 
    GROUP BY slug 
    HAVING COUNT(*) > 1
");

if (!empty($duplicates)) {
    echo '<p>Encontradas categorías duplicadas:</p>';
    foreach ($duplicates as $dup) {
        echo "<p>• Slug '{$dup->slug}' aparece {$dup->count} veces</p>";
        
        // Mantener solo la primera categoría, eliminar las demás
        $categories_to_delete = $wpdb->get_results($wpdb->prepare(
            "SELECT id FROM $table_categories WHERE slug = %s ORDER BY id ASC",
            $dup->slug
        ));
        
        // Eliminar todas excepto la primera
        for ($i = 1; $i < count($categories_to_delete); $i++) {
            $wpdb->delete($table_categories, array('id' => $categories_to_delete[$i]->id));
            echo "<p style='color: orange;'>  Eliminada categoría duplicada ID: {$categories_to_delete[$i]->id}</p>";
        }
    }
} else {
    echo '<p style="color: green;">✅ No hay categorías duplicadas</p>';
}

// Limpiar proyectos duplicados
echo '<h3>📁 Limpiando Proyectos Duplicados</h3>';

$table_projects = $wpdb->prefix . 'portfolio_projects';

// Encontrar proyectos duplicados por slug
$duplicate_projects = $wpdb->get_results("
    SELECT slug, COUNT(*) as count 
    FROM $table_projects 
    GROUP BY slug 
    HAVING COUNT(*) > 1
");

if (!empty($duplicate_projects)) {
    echo '<p>Encontrados proyectos duplicados:</p>';
    foreach ($duplicate_projects as $dup) {
        echo "<p>• Slug '{$dup->slug}' aparece {$dup->count} veces</p>";
        
        // Mantener solo el primer proyecto, eliminar los demás
        $projects_to_delete = $wpdb->get_results($wpdb->prepare(
            "SELECT id FROM $table_projects WHERE slug = %s ORDER BY id ASC",
            $dup->slug
        ));
        
        // Eliminar todos excepto el primero
        for ($i = 1; $i < count($projects_to_delete); $i++) {
            $wpdb->delete($table_projects, array('id' => $projects_to_delete[$i]->id));
            echo "<p style='color: orange;'>  Eliminado proyecto duplicado ID: {$projects_to_delete[$i]->id}</p>";
        }
    }
} else {
    echo '<p style="color: green;">✅ No hay proyectos duplicados</p>';
}

// Verificar integridad de datos
echo '<h3>🔍 Verificando Integridad de Datos</h3>';

// Verificar que todos los proyectos tengan categorías válidas
$orphan_projects = $wpdb->get_results("
    SELECT p.id, p.title, p.category_id 
    FROM {$wpdb->prefix}portfolio_projects p 
    LEFT JOIN $table_categories c ON p.category_id = c.id 
    WHERE c.id IS NULL AND p.category_id IS NOT NULL
");

if (!empty($orphan_projects)) {
    echo '<p style="color: red;">❌ Proyectos con categorías inválidas:</p>';
    foreach ($orphan_projects as $project) {
        echo "<p>• Proyecto '{$project->title}' (ID: {$project->id}) tiene categoría inválida: {$project->category_id}</p>";
        
        // Asignar a la primera categoría disponible
        $first_category = $wpdb->get_var("SELECT id FROM $table_categories ORDER BY id ASC LIMIT 1");
        if ($first_category) {
            $wpdb->update(
                $table_projects,
                array('category_id' => $first_category),
                array('id' => $project->id)
            );
            echo "<p style='color: green;'>  Asignado a categoría ID: $first_category</p>";
        }
    }
} else {
    echo '<p style="color: green;">✅ Todos los proyectos tienen categorías válidas</p>';
}

// Limpiar vistas y likes huérfanos
echo '<h3>📊 Limpiando Estadísticas Huérfanas</h3>';

// Limpiar vistas de proyectos que no existen
$orphan_views = $wpdb->query("
    DELETE v FROM {$wpdb->prefix}portfolio_project_views v 
    LEFT JOIN $table_projects p ON v.project_id = p.id 
    WHERE p.id IS NULL
");

if ($orphan_views > 0) {
    echo "<p style='color: orange;'>⚠️ Eliminadas $orphan_views vistas huérfanas</p>";
} else {
    echo '<p style="color: green;">✅ No hay vistas huérfanas</p>';
}

// Limpiar likes de proyectos que no existen
$orphan_likes = $wpdb->query("
    DELETE l FROM {$wpdb->prefix}portfolio_project_likes l 
    LEFT JOIN $table_projects p ON l.project_id = p.id 
    WHERE p.id IS NULL
");

if ($orphan_likes > 0) {
    echo "<p style='color: orange;'>⚠️ Eliminados $orphan_likes likes huérfanos</p>";
} else {
    echo '<p style="color: green;">✅ No hay likes huérfanos</p>';
}

// Actualizar contadores de vistas y likes
echo '<h3>🔄 Actualizando Contadores</h3>';

$projects = $wpdb->get_results("SELECT id FROM $table_projects");
foreach ($projects as $project) {
    // Actualizar contador de vistas
    $views_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_project_views WHERE project_id = %d",
        $project->id
    ));
    
    // Actualizar contador de likes
    $likes_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_project_likes WHERE project_id = %d",
        $project->id
    ));
    
    // Actualizar el proyecto
    $wpdb->update(
        $table_projects,
        array(
            'views' => $views_count,
            'likes' => $likes_count
        ),
        array('id' => $project->id)
    );
}

echo '<p style="color: green;">✅ Contadores actualizados</p>';

// Resetear flag de primera activación si es necesario
echo '<h3>⚙️ Configurando Opciones</h3>';

$first_activation = get_option('portfolio_first_activation', true);
if ($first_activation) {
    update_option('portfolio_first_activation', false);
    echo '<p style="color: green;">✅ Flag de primera activación actualizado</p>';
} else {
    echo '<p style="color: green;">✅ Flag de primera activación ya está configurado</p>';
}

// Resumen final
echo '<h3>📊 Resumen de la Limpieza</h3>';

$final_categories = $wpdb->get_var("SELECT COUNT(*) FROM $table_categories");
$final_projects = $wpdb->get_var("SELECT COUNT(*) FROM $table_projects");
$final_views = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_project_views");
$final_likes = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_project_likes");

echo '<ul>';
echo "<li><strong>Categorías:</strong> $final_categories</li>";
echo "<li><strong>Proyectos:</strong> $final_projects</li>";
echo "<li><strong>Vistas:</strong> $final_views</li>";
echo "<li><strong>Likes:</strong> $final_likes</li>";
echo '</ul>';

echo '<div style="background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724; margin-top: 20px;">';
echo '<h4 style="margin-top: 0;">✅ Limpieza Completada</h4>';
echo '<p>Los datos duplicados han sido eliminados y la integridad de la base de datos ha sido restaurada.</p>';
echo '<p><strong>Próximos pasos:</strong></p>';
echo '<ul>';
echo '<li>El plugin debería funcionar correctamente ahora</li>';
echo '<li>Puedes activar/desactivar el plugin sin problemas</li>';
echo '<li>Los datos están limpios y organizados</li>';
echo '</ul>';
echo '</div>';

echo '<p style="margin-top: 30px; text-align: center; color: #666;">';
echo 'Limpieza completada el ' . date('Y-m-d H:i:s');
echo '</p>';
