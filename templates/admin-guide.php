<?php
/**
 * Plantilla de Guía de Usuario - Portfolio Plugin
 * 
 * @package Sabsfe_Portfolio
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Guía de Usuario - Portfolio Plugin', 'sabsfe-portfolio-plugin'); ?></h1>
    
    <div class="sabsfe-portfolio-guide">
        <div class="guide-section">
            <h2><?php _e('🚀 Introducción', 'sabsfe-portfolio-plugin'); ?></h2>
            <p><?php _e('El Portfolio Plugin te permite crear y gestionar un portafolio profesional de proyectos con integración completa a Elementor.', 'sabsfe-portfolio-plugin'); ?></p>
        </div>

        <div class="guide-section">
            <h2><?php _e('📋 Configuración Inicial', 'sabsfe-portfolio-plugin'); ?></h2>
            <ol>
                <li><strong><?php _e('Crear Categorías:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Ve a Portfolio > Categorías y crea las categorías para tus proyectos (ej: Web Design, Mobile Apps, Branding).', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Agregar Proyectos:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Ve a Portfolio > Proyectos y añade tus proyectos con imágenes, descripciones y enlaces.', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Configurar Widget:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('En Elementor, arrastra el widget "Portfolio Grid" a tu página.', 'sabsfe-portfolio-plugin'); ?></li>
            </ol>
        </div>

        <div class="guide-section">
            <h2><?php _e('🎨 Gestión de Proyectos', 'sabsfe-portfolio-plugin'); ?></h2>
            <h3><?php _e('Agregar un Nuevo Proyecto:', 'sabsfe-portfolio-plugin'); ?></h3>
            <ol>
                <li><?php _e('Haz clic en "Agregar Nuevo Proyecto"', 'sabsfe-portfolio-plugin'); ?></li>
                <li><?php _e('Completa el título y descripción', 'sabsfe-portfolio-plugin'); ?></li>
                <li><?php _e('Selecciona una categoría', 'sabsfe-portfolio-plugin'); ?></li>
                <li><?php _e('Sube una imagen destacada', 'sabsfe-portfolio-plugin'); ?></li>
                <li><?php _e('Agrega enlaces externos si es necesario', 'sabsfe-portfolio-plugin'); ?></li>
                <li><?php _e('Guarda el proyecto', 'sabsfe-portfolio-plugin'); ?></li>
            </ol>

            <h3><?php _e('Funciones Avanzadas:', 'sabsfe-portfolio-plugin'); ?></h3>
            <ul>
                <li><strong><?php _e('Galería de Imágenes:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Agrega múltiples imágenes a cada proyecto', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Videos:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Incluye enlaces a YouTube o Vimeo', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Estadísticas:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Visualiza contadores de vistas y likes', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Proyectos Destacados:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Marca proyectos importantes como destacados', 'sabsfe-portfolio-plugin'); ?></li>
            </ul>
        </div>

        <div class="guide-section">
            <h2><?php _e('🏷️ Gestión de Categorías', 'sabsfe-portfolio-plugin'); ?></h2>
            <p><?php _e('Las categorías te ayudan a organizar tus proyectos. Cada categoría puede tener:', 'sabsfe-portfolio-plugin'); ?></p>
            <ul>
                <li><strong><?php _e('Nombre:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Nombre descriptivo de la categoría', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Slug:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('URL amigable (se genera automáticamente)', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Descripción:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Texto descriptivo de la categoría', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Color:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Color personalizado para identificar la categoría', 'sabsfe-portfolio-plugin'); ?></li>
            </ul>
        </div>

        <div class="guide-section">
            <h2><?php _e('🎯 Widget de Elementor', 'sabsfe-portfolio-plugin'); ?></h2>
            <h3><?php _e('Configuración del Widget:', 'sabsfe-portfolio-plugin'); ?></h3>
            <ul>
                <li><strong><?php _e('Categorías a Mostrar:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Selecciona qué categorías incluir', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Número de Proyectos:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Cantidad de proyectos a mostrar', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Layout:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Grid, Lista o Carrusel', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Mostrar Filtros:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Permite filtrar por categorías', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Modal de Detalles:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Activa/desactiva el modal de detalles', 'sabsfe-portfolio-plugin'); ?></li>
            </ul>
        </div>

        <div class="guide-section">
            <h2><?php _e('⚙️ Configuración Avanzada', 'sabsfe-portfolio-plugin'); ?></h2>
            <p><?php _e('En Portfolio > Configuración puedes ajustar:', 'sabsfe-portfolio-plugin'); ?></p>
            <ul>
                <li><strong><?php _e('Estilos:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Colores, fuentes y espaciado', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Comportamiento:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Animaciones y efectos', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('SEO:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Meta tags y estructura', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Estadísticas:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Configurar tracking de vistas', 'sabsfe-portfolio-plugin'); ?></li>
            </ul>
        </div>

        <div class="guide-section">
            <h2><?php _e('🔧 Personalización CSS', 'sabsfe-portfolio-plugin'); ?></h2>
            <p><?php _e('Para personalizar la apariencia, puedes agregar CSS personalizado:', 'sabsfe-portfolio-plugin'); ?></p>
            <pre><code>.sabsfe-portfolio-grid {
    /* Personaliza el grid */
}

.sabsfe-portfolio-item {
    /* Personaliza cada item */
}

.sabsfe-portfolio-modal {
    /* Personaliza el modal */
}</code></pre>
        </div>

        <div class="guide-section">
            <h2><?php _e('📱 Responsive Design', 'sabsfe-portfolio-plugin'); ?></h2>
            <p><?php _e('El plugin está optimizado para dispositivos móviles:', 'sabsfe-portfolio-plugin'); ?></p>
            <ul>
                <li><?php _e('Grid adaptativo que se ajusta al tamaño de pantalla', 'sabsfe-portfolio-plugin'); ?></li>
                <li><?php _e('Modal táctil optimizado para móviles', 'sabsfe-portfolio-plugin'); ?></li>
                <li><?php _e('Navegación por gestos en galerías', 'sabsfe-portfolio-plugin'); ?></li>
                <li><?php _e('Imágenes optimizadas para diferentes resoluciones', 'sabsfe-portfolio-plugin'); ?></li>
            </ul>
        </div>

        <div class="guide-section">
            <h2><?php _e('🚀 Mejores Prácticas', 'sabsfe-portfolio-plugin'); ?></h2>
            <ul>
                <li><strong><?php _e('Imágenes:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Usa imágenes de alta calidad pero optimizadas (recomendado: 1200x800px)', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Descripciones:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Escribe descripciones claras y atractivas', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Categorías:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Mantén un número limitado de categorías para mejor organización', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Actualizaciones:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Mantén tu portafolio actualizado con proyectos recientes', 'sabsfe-portfolio-plugin'); ?></li>
            </ul>
        </div>

        <div class="guide-section">
            <h2><?php _e('🆘 Soporte y Ayuda', 'sabsfe-portfolio-plugin'); ?></h2>
            <p><?php _e('Si necesitas ayuda adicional:', 'sabsfe-portfolio-plugin'); ?></p>
            <ul>
                <li><strong><?php _e('Documentación:', 'sabsfe-portfolio-plugin'); ?></strong> <?php _e('Revisa el archivo readme.txt incluido', 'sabsfe-portfolio-plugin'); ?></li>
                <li><strong><?php _e('Soporte Técnico:', 'sabsfe-portfolio-plugin'); ?></strong> <a href="mailto:support@gerardoriarte.com">support@gerardoriarte.com</a></li>
                <li><strong><?php _e('Desarrollador:', 'sabsfe-portfolio-plugin'); ?></strong> <a href="https://gerardoriarte.com" target="_blank">gerardoriarte.com</a></li>
            </ul>
        </div>

        <div class="guide-section guide-footer">
            <h2><?php _e('✅ ¡Listo para Comenzar!', 'sabsfe-portfolio-plugin'); ?></h2>
            <p><?php _e('Ahora que conoces las funcionalidades del plugin, puedes comenzar a crear tu portafolio profesional.', 'sabsfe-portfolio-plugin'); ?></p>
            <p>
                <a href="<?php echo admin_url('admin.php?page=portfolio-categories'); ?>" class="button button-primary">
                    <?php _e('Crear Primera Categoría', 'sabsfe-portfolio-plugin'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=portfolio-admin'); ?>" class="button">
                    <?php _e('Agregar Primer Proyecto', 'sabsfe-portfolio-plugin'); ?>
                </a>
            </p>
        </div>
    </div>
</div>

<style>
.sabsfe-portfolio-guide {
    max-width: 800px;
    margin: 20px 0;
}

.guide-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
    margin: 20px 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.guide-section h2 {
    color: #23282d;
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.guide-section h3 {
    color: #0073aa;
    margin-top: 20px;
    margin-bottom: 10px;
}

.guide-section ul, .guide-section ol {
    margin: 15px 0;
    padding-left: 20px;
}

.guide-section li {
    margin: 8px 0;
    line-height: 1.5;
}

.guide-section pre {
    background: #f8f9fa;
    border: 1px solid #e1e4e8;
    border-radius: 3px;
    padding: 15px;
    overflow-x: auto;
    font-family: 'Courier New', monospace;
    font-size: 14px;
}

.guide-footer {
    background: linear-gradient(135deg, #0073aa 0%, #005a87 100%);
    color: white;
    border: none;
}

.guide-footer h2 {
    color: white;
    border-bottom-color: rgba(255,255,255,0.3);
}

.guide-footer .button {
    margin: 5px 10px 5px 0;
    background: white;
    color: #0073aa;
    border: 2px solid white;
}

.guide-footer .button:hover {
    background: #f8f9fa;
    color: #005a87;
}

.guide-footer .button-primary {
    background: #fff;
    color: #0073aa;
}

.guide-footer .button-primary:hover {
    background: #f8f9fa;
    color: #005a87;
}
</style>

