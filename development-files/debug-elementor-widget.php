<?php
/**
 * Script de debug para verificar el widget de Elementor
 * 
 * Este archivo ayuda a diagnosticar por qué el widget no aparece en Elementor
 */

// Verificar que WordPress esté cargado
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo '<h2>🔍 Debug del Widget de Elementor - Portfolio</h2>';

// Verificar que el plugin esté activo
if (!class_exists('PortfolioPlugin')) {
    echo '<p style="color: red;">❌ El plugin Portfolio no está activo</p>';
    exit;
}

echo '<p style="color: green;">✅ Plugin Portfolio está activo</p>';

// Verificar que Elementor esté instalado
if (!class_exists('\Elementor\Plugin')) {
    echo '<p style="color: red;">❌ Elementor no está instalado o activo</p>';
    exit;
}

echo '<p style="color: green;">✅ Elementor está instalado</p>';

// Verificar que la clase del widget exista
if (!class_exists('PortfolioElementorWidget')) {
    echo '<p style="color: red;">❌ La clase PortfolioElementorWidget no existe</p>';
    exit;
}

echo '<p style="color: green;">✅ Clase PortfolioElementorWidget existe</p>';

// Verificar hooks de Elementor
echo '<h3>🔗 Verificación de Hooks de Elementor</h3>';

$hooks_to_check = [
    'elementor/loaded' => 'Elementor cargado',
    'elementor/widgets/register' => 'Registro de widgets (nuevo)',
    'elementor/widgets/widgets_registered' => 'Registro de widgets (antiguo)',
    'elementor/elements/categories_registered' => 'Categorías registradas'
];

foreach ($hooks_to_check as $hook => $description) {
    if (did_action($hook)) {
        echo "<p style='color: green;'>✅ {$description} - Hook ejecutado</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ {$description} - Hook no ejecutado</p>";
    }
}

// Verificar widgets registrados
echo '<h3>📋 Widgets Registrados en Elementor</h3>';

if (method_exists('\Elementor\Plugin', 'instance')) {
    $widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
    
    if (method_exists($widgets_manager, 'get_widget_types')) {
        $widgets = $widgets_manager->get_widget_types();
        
        $portfolio_widgets = [];
        foreach ($widgets as $widget_name => $widget) {
            if (strpos($widget_name, 'portfolio') !== false || 
                strpos(get_class($widget), 'Portfolio') !== false) {
                $portfolio_widgets[] = $widget_name;
            }
        }
        
        if (!empty($portfolio_widgets)) {
            echo '<p style="color: green;">✅ Widgets de Portfolio encontrados:</p>';
            echo '<ul>';
            foreach ($portfolio_widgets as $widget) {
                echo "<li>{$widget}</li>";
            }
            echo '</ul>';
        } else {
            echo '<p style="color: red;">❌ No se encontraron widgets de Portfolio registrados</p>';
        }
        
        // Mostrar todos los widgets para debug
        echo '<details>';
        echo '<summary>Ver todos los widgets registrados</summary>';
        echo '<ul>';
        foreach ($widgets as $widget_name => $widget) {
            echo "<li>{$widget_name} - " . get_class($widget) . "</li>";
        }
        echo '</ul>';
        echo '</details>';
    }
}

// Verificar categorías de Elementor
echo '<h3>📂 Categorías de Elementor</h3>';

if (method_exists('\Elementor\Plugin', 'instance')) {
    $elements_manager = \Elementor\Plugin::instance()->elements_manager;
    
    if (method_exists($elements_manager, 'get_categories')) {
        $categories = $elements_manager->get_categories();
        
        echo '<ul>';
        foreach ($categories as $category) {
            echo "<li>{$category['title']} ({$category['name']})</li>";
        }
        echo '</ul>';
    }
}

// Intentar registrar el widget manualmente
echo '<h3>🔧 Registro Manual del Widget</h3>';

try {
    $widget_instance = new PortfolioElementorWidget();
    echo '<p style="color: green;">✅ Widget se puede instanciar correctamente</p>';
    
    echo '<h4>Información del Widget:</h4>';
    echo '<ul>';
    echo '<li><strong>Nombre:</strong> ' . $widget_instance->get_name() . '</li>';
    echo '<li><strong>Título:</strong> ' . $widget_instance->get_title() . '</li>';
    echo '<li><strong>Icono:</strong> ' . $widget_instance->get_icon() . '</li>';
    echo '<li><strong>Categorías:</strong> ' . implode(', ', $widget_instance->get_categories()) . '</li>';
    echo '<li><strong>Palabras clave:</strong> ' . implode(', ', $widget_instance->get_keywords()) . '</li>';
    echo '</ul>';
    
    // Intentar registrar manualmente
    if (method_exists('\Elementor\Plugin', 'instance')) {
        $widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
        $widgets_manager->register($widget_instance);
        echo '<p style="color: green;">✅ Widget registrado manualmente</p>';
    }
    
} catch (Exception $e) {
    echo '<p style="color: red;">❌ Error al instanciar el widget: ' . $e->getMessage() . '</p>';
}

// Verificar archivos del plugin
echo '<h3>📁 Archivos del Plugin</h3>';

$plugin_files = [
    'portfolio-plugin.php',
    'includes/class-elementor-widget.php'
];

foreach ($plugin_files as $file) {
    $file_path = WP_PLUGIN_DIR . '/portfolio-plugin/' . $file;
    if (file_exists($file_path)) {
        echo "<p style='color: green;'>✅ {$file} existe</p>";
    } else {
        echo "<p style='color: red;'>❌ {$file} no existe</p>";
    }
}

echo '<h3>💡 Soluciones Sugeridas</h3>';
echo '<ol>';
echo '<li><strong>Desactiva y reactiva el plugin</strong> desde el panel de administración</li>';
echo '<li><strong>Limpia la caché</strong> de Elementor (Elementor > Tools > Regenerate CSS)</li>';
echo '<li><strong>Verifica la versión de Elementor</strong> (debe ser 3.0+)</li>';
echo '<li><strong>Revisa los logs de error</strong> de WordPress</li>';
echo '<li><strong>Desactiva otros plugins</strong> temporalmente para verificar conflictos</li>';
echo '</ol>';

echo '<p style="background: #f0f8ff; padding: 15px; border: 1px solid #b0d4f1; border-radius: 5px;">';
echo '<strong>💡 Nota:</strong> Si el widget aparece en la lista de widgets registrados pero no en la interfaz de Elementor, puede ser un problema de caché o de versión de Elementor.';
echo '</p>';
