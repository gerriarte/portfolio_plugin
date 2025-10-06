<?php
/**
 * Script de prueba simple para el modal
 * 
 * Este script simula una llamada AJAX y muestra exactamente qué datos se están devolviendo
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🧪 Prueba Simple del Modal</h2>';

// Obtener un proyecto de prueba
global $wpdb;
$project = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}portfolio_projects LIMIT 1");

if (!$project) {
    echo '<p style="color: red;">❌ No hay proyectos en la base de datos</p>';
    exit;
}

echo '<h3>📋 Proyecto desde Base de Datos</h3>';
echo '<ul>';
echo '<li><strong>ID:</strong> ' . $project->id . '</li>';
echo '<li><strong>Título:</strong> ' . esc_html($project->title) . '</li>';
echo '<li><strong>Descripción:</strong> ' . esc_html(substr($project->description, 0, 100)) . '...</li>';
echo '<li><strong>Imagen destacada:</strong> ' . ($project->featured_image ? esc_html($project->featured_image) : 'No') . '</li>';
echo '<li><strong>Galería:</strong> ' . ($project->gallery ? 'Sí (' . strlen($project->gallery) . ' chars)' : 'No') . '</li>';
echo '<li><strong>Categoría ID:</strong> ' . $project->category_id . '</li>';
echo '<li><strong>Estado:</strong> ' . $project->status . '</li>';
echo '</ul>';

// Probar la función get_project
echo '<h3>🔍 Prueba de PortfolioDatabase::get_project()</h3>';

if (class_exists('PortfolioDatabase')) {
    $project_data = PortfolioDatabase::get_project($project->id);
    
    if ($project_data) {
        echo '<p style="color: green;">✅ Función get_project() funciona</p>';
        echo '<ul>';
        echo '<li><strong>Título:</strong> ' . esc_html($project_data->title) . '</li>';
        echo '<li><strong>Categoría:</strong> ' . esc_html($project_data->category_name ?? 'Sin categoría') . '</li>';
        echo '<li><strong>Color categoría:</strong> ' . esc_html($project_data->category_color ?? 'Sin color') . '</li>';
        echo '<li><strong>Descripción:</strong> ' . esc_html(substr($project_data->description, 0, 100)) . '...</li>';
        echo '<li><strong>Imagen:</strong> ' . ($project_data->featured_image ? 'Sí' : 'No') . '</li>';
        echo '<li><strong>Galería:</strong> ' . ($project_data->gallery ? 'Sí' : 'No') . '</li>';
        echo '</ul>';
        
        // Probar unserialize de galería
        if ($project_data->gallery) {
            $gallery = unserialize($project_data->gallery);
            echo '<p><strong>Galería deserializada:</strong></p>';
            if (is_array($gallery)) {
                echo '<ul>';
                foreach ($gallery as $i => $item) {
                    echo '<li>Item ' . ($i + 1) . ': ' . esc_html($item) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p style="color: red;">❌ Galería no es un array válido</p>';
            }
        }
    } else {
        echo '<p style="color: red;">❌ Función get_project() devuelve null</p>';
    }
} else {
    echo '<p style="color: red;">❌ Clase PortfolioDatabase no existe</p>';
}

// Simular el endpoint AJAX completo
echo '<h3>🌐 Simulación del Endpoint AJAX</h3>';

if ($project_data) {
    // Preparar datos como lo hace el endpoint
    $ajax_data = array(
        'id' => $project_data->id,
        'title' => $project_data->title,
        'description' => $project_data->description,
        'content' => $project_data->content,
        'featured_image' => $project_data->featured_image,
        'gallery' => $project_data->gallery ? unserialize($project_data->gallery) : array(),
        'category_name' => $project_data->category_name,
        'category_color' => $project_data->category_color,
        'external_url' => $project_data->external_url,
        'project_date' => $project_data->project_date,
        'views' => $project_data->views + 1,
        'likes' => $project_data->likes,
        'created_at' => $project_data->created_at
    );
    
    echo '<p style="color: green;">✅ Datos preparados para AJAX</p>';
    echo '<h4>Datos que se enviarían al JavaScript:</h4>';
    echo '<pre>' . print_r($ajax_data, true) . '</pre>';
    
    // Verificar campos críticos
    echo '<h4>🔍 Verificación de Campos Críticos:</h4>';
    echo '<ul>';
    echo '<li><strong>Título:</strong> ' . (!empty($ajax_data['title']) ? '✅ OK' : '❌ Vacío') . '</li>';
    echo '<li><strong>Descripción:</strong> ' . (!empty($ajax_data['description']) ? '✅ OK' : '❌ Vacío') . '</li>';
    echo '<li><strong>Categoría:</strong> ' . (!empty($ajax_data['category_name']) ? '✅ OK' : '❌ Vacía') . '</li>';
    echo '<li><strong>Imagen destacada:</strong> ' . (!empty($ajax_data['featured_image']) ? '✅ OK' : '❌ Vacía') . '</li>';
    echo '<li><strong>Galería:</strong> ' . (is_array($ajax_data['gallery']) && !empty($ajax_data['gallery']) ? '✅ OK (' . count($ajax_data['gallery']) . ' items)' : '❌ Vacía') . '</li>';
    echo '</ul>';
}

// Generar código JavaScript de prueba
echo '<h3>💻 Código JavaScript de Prueba</h3>';
echo '<p>Pega este código en la consola del navegador para probar:</p>';
echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px;">';
echo '// Probar llamada AJAX manual
jQuery.ajax({
    url: "' . admin_url('admin-ajax.php') . '",
    type: "POST",
    data: {
        action: "portfolio_get_project",
        project_id: ' . $project->id . ',
        nonce: "' . wp_create_nonce('portfolio_frontend_nonce') . '"
    },
    success: function(response) {
        console.log("Respuesta AJAX:", response);
        if (response.success) {
            console.log("Datos del proyecto:", response.data);
            console.log("Título:", response.data.title);
            console.log("Descripción:", response.data.description);
            console.log("Categoría:", response.data.category_name);
            console.log("Galería:", response.data.gallery);
        } else {
            console.error("Error:", response.data.message);
        }
    },
    error: function(xhr, status, error) {
        console.error("Error AJAX:", error);
    }
});';
echo '</pre>';

echo '<p style="background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;">';
echo '<strong>💡 Instrucciones:</strong><br>';
echo '1. Copia el código JavaScript de arriba<br>';
echo '2. Ve a una página con el widget Portfolio<br>';
echo '3. Abre la consola del navegador (F12)<br>';
echo '4. Pega y ejecuta el código<br>';
echo '5. Revisa la respuesta en la consola';
echo '</p>';
