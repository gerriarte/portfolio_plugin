<?php
/**
 * Página de administración de proyectos
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap portfolio-admin">
    <h1 class="wp-heading-inline"><?php _e('Proyectos del Portafolio', 'sabsfe-portfolio-plugin'); ?></h1>
    <button type="button" class="page-title-action" id="add-new-project">
        <?php _e('Agregar Nuevo', 'sabsfe-portfolio-plugin'); ?>
    </button>
    <hr class="wp-header-end">

    <div class="portfolio-projects-grid">
        <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $project): ?>
                <div class="portfolio-project-card" data-project-id="<?php echo $project->id; ?>">
                    <div class="project-image">
                        <?php if ($project->featured_image): ?>
                            <img src="<?php echo esc_url($project->featured_image); ?>" alt="<?php echo esc_attr($project->title); ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <span class="dashicons dashicons-format-image"></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="project-actions">
                            <button class="edit-project" data-project-id="<?php echo $project->id; ?>">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button class="delete-project" data-project-id="<?php echo $project->id; ?>">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="project-content">
                        <h3 class="project-title"><?php echo esc_html($project->title); ?></h3>
                        
                        <?php if ($project->category_name): ?>
                            <div class="project-category">
                                <span class="category-badge" style="background-color: <?php echo esc_attr($project->category_color); ?>">
                                    <?php echo esc_html($project->category_name); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="project-meta">
                            <span class="project-status status-<?php echo esc_attr($project->status); ?>">
                                <?php echo ucfirst($project->status); ?>
                            </span>
                            
                            <?php if ($project->featured): ?>
                                <span class="featured-badge">
                                    <span class="dashicons dashicons-star-filled"></span>
                                    <?php _e('Destacado', 'sabsfe-portfolio-plugin'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="project-stats">
                            <span class="views">
                                <span class="dashicons dashicons-visibility"></span>
                                <?php echo number_format($project->views); ?>
                            </span>
                            <span class="likes">
                                <span class="dashicons dashicons-heart"></span>
                                <?php echo number_format($project->likes); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-projects">
                <div class="no-projects-content">
                    <span class="dashicons dashicons-portfolio"></span>
                    <h3><?php _e('No hay proyectos aún', 'sabsfe-portfolio-plugin'); ?></h3>
                    <p><?php _e('Comienza agregando tu primer proyecto al portafolio.', 'sabsfe-portfolio-plugin'); ?></p>
                    <button type="button" class="button button-primary" id="add-first-project">
                        <?php _e('Agregar Primer Proyecto', 'sabsfe-portfolio-plugin'); ?>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para agregar/editar proyecto -->
<div id="project-modal" class="portfolio-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title"><?php _e('Agregar Nuevo Proyecto', 'sabsfe-portfolio-plugin'); ?></h2>
            <button type="button" class="modal-close">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        
        <form id="project-form" class="modal-body">
            <input type="hidden" id="project-id" name="project_id" value="">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="project-title"><?php _e('Título del Proyecto', 'sabsfe-portfolio-plugin'); ?> *</label>
                    <input type="text" id="project-title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="project-category"><?php _e('Categoría', 'sabsfe-portfolio-plugin'); ?></label>
                    <select id="project-category" name="category_id">
                        <option value=""><?php _e('Seleccionar categoría', 'sabsfe-portfolio-plugin'); ?></option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>">
                                <?php echo esc_html($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="project-description"><?php _e('Descripción', 'sabsfe-portfolio-plugin'); ?></label>
                <textarea id="project-description" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="project-content"><?php _e('Contenido Detallado', 'sabsfe-portfolio-plugin'); ?></label>
                <?php
                wp_editor('', 'project-content', array(
                    'textarea_name' => 'content',
                    'media_buttons' => true,
                    'textarea_rows' => 10,
                    'teeny' => false,
                    'tinymce' => true
                ));
                ?>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="project-image"><?php _e('Imagen Destacada', 'sabsfe-portfolio-plugin'); ?></label>
                    <div class="image-upload">
                        <input type="hidden" id="project-image" name="featured_image" value="">
                        <div class="image-preview" style="display: none;">
                            <img src="" alt="">
                            <button type="button" class="remove-image">
                                <span class="dashicons dashicons-no-alt"></span>
                            </button>
                        </div>
                        <button type="button" class="button upload-image-btn">
                            <?php _e('Seleccionar Imagen', 'sabsfe-portfolio-plugin'); ?>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="project-date"><?php _e('Fecha del Proyecto', 'sabsfe-portfolio-plugin'); ?></label>
                    <input type="date" id="project-date" name="project_date" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="project-url"><?php _e('URL Externa', 'sabsfe-portfolio-plugin'); ?></label>
                <input type="url" id="project-url" name="external_url" placeholder="https://ejemplo.com">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="project-youtube"><?php _e('Video de YouTube', 'sabsfe-portfolio-plugin'); ?></label>
                    <input type="url" id="project-youtube" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                    <small><?php _e('Ingresa la URL completa del video de YouTube', 'sabsfe-portfolio-plugin'); ?></small>
                </div>
                
                <div class="form-group">
                    <label for="project-vimeo"><?php _e('Video de Vimeo', 'sabsfe-portfolio-plugin'); ?></label>
                    <input type="url" id="project-vimeo" name="vimeo_url" placeholder="https://vimeo.com/...">
                    <small><?php _e('Ingresa la URL completa del video de Vimeo', 'sabsfe-portfolio-plugin'); ?></small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="project-gallery"><?php _e('Galería de Imágenes', 'sabsfe-portfolio-plugin'); ?></label>
                <input type="hidden" id="project-gallery" name="gallery" value="">
                <div class="gallery-upload">
                    <div class="gallery-preview"></div>
                    <button type="button" class="button upload-gallery-btn">
                        <?php _e('Agregar Imágenes', 'sabsfe-portfolio-plugin'); ?>
                    </button>
                    <small><?php _e('Arrastra las imágenes para reordenarlas', 'sabsfe-portfolio-plugin'); ?></small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="project-status"><?php _e('Estado', 'sabsfe-portfolio-plugin'); ?></label>
                    <select id="project-status" name="status">
                        <option value="published"><?php _e('Publicado', 'sabsfe-portfolio-plugin'); ?></option>
                        <option value="draft"><?php _e('Borrador', 'sabsfe-portfolio-plugin'); ?></option>
                        <option value="private"><?php _e('Privado', 'sabsfe-portfolio-plugin'); ?></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="project-featured" name="featured" value="1">
                        <?php _e('Proyecto Destacado', 'sabsfe-portfolio-plugin'); ?>
                    </label>
                </div>
            </div>
        </form>
        
        <div class="modal-footer">
            <button type="button" class="button modal-cancel">
                <?php _e('Cancelar', 'sabsfe-portfolio-plugin'); ?>
            </button>
            <button type="button" class="button button-primary modal-save">
                <?php _e('Guardar Proyecto', 'sabsfe-portfolio-plugin'); ?>
            </button>
        </div>
    </div>
</div>
