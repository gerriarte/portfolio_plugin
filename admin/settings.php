<?php
/**
 * Página de configuración del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

$default_options = array(
    'portfolio_items_per_page' => 12,
    'portfolio_enable_modal' => true,
    'portfolio_theme' => 'dark'
);

$options = wp_parse_args($options, $default_options);
?>

<div class="wrap portfolio-admin">
    <h1><?php _e('Configuración del Portafolio', 'portfolio-plugin'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('portfolio_settings', 'portfolio_settings_nonce'); ?>
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="portfolio_items_per_page"><?php _e('Proyectos por página', 'portfolio-plugin'); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="portfolio_items_per_page" 
                               name="portfolio_items_per_page" 
                               value="<?php echo esc_attr($options['portfolio_items_per_page']); ?>" 
                               min="1" 
                               max="50" 
                               class="small-text">
                        <p class="description">
                            <?php _e('Número de proyectos a mostrar por página en la grilla.', 'portfolio-plugin'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="portfolio_enable_modal"><?php _e('Modal de detalle', 'portfolio-plugin'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   id="portfolio_enable_modal" 
                                   name="portfolio_enable_modal" 
                                   value="1" 
                                   <?php checked($options['portfolio_enable_modal']); ?>>
                            <?php _e('Habilitar modal para mostrar detalles del proyecto', 'portfolio-plugin'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Si está deshabilitado, los proyectos se abrirán en una nueva página.', 'portfolio-plugin'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="portfolio_theme"><?php _e('Tema visual', 'portfolio-plugin'); ?></label>
                    </th>
                    <td>
                        <select id="portfolio_theme" name="portfolio_theme">
                            <option value="dark" <?php selected($options['portfolio_theme'], 'dark'); ?>>
                                <?php _e('Tema Oscuro', 'portfolio-plugin'); ?>
                            </option>
                            <option value="light" <?php selected($options['portfolio_theme'], 'light'); ?>>
                                <?php _e('Tema Claro', 'portfolio-plugin'); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php _e('Selecciona el tema visual para el frontend del portafolio.', 'portfolio-plugin'); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php submit_button(__('Guardar Configuración', 'portfolio-plugin')); ?>
    </form>
    
    <hr>
    
    <h2><?php _e('Información del Sistema', 'portfolio-plugin'); ?></h2>
    
    <div class="portfolio-system-info">
        <div class="info-grid">
            <div class="info-card">
                <h3><?php _e('Estadísticas de Logs', 'portfolio-plugin'); ?></h3>
                <?php
                $log_stats = PortfolioLogger::get_log_stats();
                ?>
                <ul>
                    <li><strong><?php _e('Total de logs:', 'portfolio-plugin'); ?></strong> <?php echo $log_stats['total_logs']; ?></li>
                    <li><strong><?php _e('Errores:', 'portfolio-plugin'); ?></strong> <?php echo $log_stats['error_count']; ?></li>
                    <li><strong><?php _e('Advertencias:', 'portfolio-plugin'); ?></strong> <?php echo $log_stats['warning_count']; ?></li>
                    <li><strong><?php _e('Información:', 'portfolio-plugin'); ?></strong> <?php echo $log_stats['info_count']; ?></li>
                    <li><strong><?php _e('Tamaño del archivo:', 'portfolio-plugin'); ?></strong> <?php echo size_format($log_stats['file_size']); ?></li>
                </ul>
            </div>
            
            <div class="info-card">
                <h3><?php _e('Base de Datos', 'portfolio-plugin'); ?></h3>
                <?php
                global $wpdb;
                $projects_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_projects");
                $categories_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}portfolio_categories");
                ?>
                <ul>
                    <li><strong><?php _e('Proyectos:', 'portfolio-plugin'); ?></strong> <?php echo $projects_count; ?></li>
                    <li><strong><?php _e('Categorías:', 'portfolio-plugin'); ?></strong> <?php echo $categories_count; ?></li>
                    <li><strong><?php _e('Versión del plugin:', 'portfolio-plugin'); ?></strong> <?php echo PORTFOLIO_PLUGIN_VERSION; ?></li>
                </ul>
            </div>
        </div>
        
        <div class="info-actions">
            <button type="button" class="button" id="clean-logs">
                <?php _e('Limpiar Logs Antiguos', 'portfolio-plugin'); ?>
            </button>
            <button type="button" class="button" id="export-data">
                <?php _e('Exportar Datos', 'portfolio-plugin'); ?>
            </button>
        </div>
    </div>
</div>
