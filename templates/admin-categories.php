<?php
/**
 * Página de administración de categorías
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap portfolio-admin">
    <h1 class="wp-heading-inline"><?php _e('Categorías del Portafolio', 'sabsfe-portfolio-plugin'); ?></h1>
    <button type="button" class="page-title-action" id="add-new-category">
        <?php _e('Agregar Nueva', 'sabsfe-portfolio-plugin'); ?>
    </button>
    <hr class="wp-header-end">

    <div class="portfolio-categories-grid">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <div class="portfolio-category-card" data-category-id="<?php echo $category->id; ?>">
                    <div class="category-header">
                        <div class="category-color" style="background-color: <?php echo esc_attr($category->color); ?>"></div>
                        <div class="category-actions">
                            <button class="edit-category" data-category-id="<?php echo $category->id; ?>">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button class="delete-category" data-category-id="<?php echo $category->id; ?>">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="category-content">
                        <h3 class="category-name"><?php echo esc_html($category->name); ?></h3>
                        
                        <?php if ($category->description): ?>
                            <p class="category-description"><?php echo esc_html($category->description); ?></p>
                        <?php endif; ?>
                        
                        <div class="category-meta">
                            <span class="category-slug"><?php echo esc_html($category->slug); ?></span>
                            <span class="category-date">
                                <?php echo date_i18n(get_option('date_format'), strtotime($category->created_at)); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-categories">
                <div class="no-categories-content">
                    <span class="dashicons dashicons-category"></span>
                    <h3><?php _e('No hay categorías aún', 'sabsfe-portfolio-plugin'); ?></h3>
                    <p><?php _e('Comienza agregando tu primera categoría para organizar los proyectos.', 'sabsfe-portfolio-plugin'); ?></p>
                    <button type="button" class="button button-primary" id="add-first-category">
                        <?php _e('Agregar Primera Categoría', 'sabsfe-portfolio-plugin'); ?>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para agregar/editar categoría -->
<div id="category-modal" class="portfolio-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="category-modal-title"><?php _e('Agregar Nueva Categoría', 'sabsfe-portfolio-plugin'); ?></h2>
            <button type="button" class="modal-close">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        
        <form id="category-form" class="modal-body">
            <input type="hidden" id="category-id" name="category_id" value="">
            
            <div class="form-group">
                <label for="category-name"><?php _e('Nombre de la Categoría', 'sabsfe-portfolio-plugin'); ?> *</label>
                <input type="text" id="category-name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="category-description"><?php _e('Descripción', 'sabsfe-portfolio-plugin'); ?></label>
                <textarea id="category-description" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="category-color"><?php _e('Color de la Categoría', 'sabsfe-portfolio-plugin'); ?></label>
                <div class="color-picker-wrapper">
                    <input type="color" id="category-color" name="color" value="#2196F3">
                    <div class="color-presets">
                        <button type="button" class="color-preset" data-color="#2196F3" style="background-color: #2196F3;"></button>
                        <button type="button" class="color-preset" data-color="#4CAF50" style="background-color: #4CAF50;"></button>
                        <button type="button" class="color-preset" data-color="#FF9800" style="background-color: #FF9800;"></button>
                        <button type="button" class="color-preset" data-color="#9C27B0" style="background-color: #9C27B0;"></button>
                        <button type="button" class="color-preset" data-color="#F44336" style="background-color: #F44336;"></button>
                        <button type="button" class="color-preset" data-color="#607D8B" style="background-color: #607D8B;"></button>
                    </div>
                </div>
            </div>
        </form>
        
        <div class="modal-footer">
            <button type="button" class="button modal-cancel">
                <?php _e('Cancelar', 'sabsfe-portfolio-plugin'); ?>
            </button>
            <button type="button" class="button button-primary modal-save">
                <?php _e('Guardar Categoría', 'sabsfe-portfolio-plugin'); ?>
            </button>
        </div>
    </div>
</div>
