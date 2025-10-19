<?php
/**
 * Clase para manejo del frontend del plugin Portfolio
 * 
 * @package Sabsfe_Portfolio
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sabsfe_Portfolio_Frontend {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('wp_ajax_sabsfe_portfolio_get_project', array($this, 'ajax_get_project'));
        add_action('wp_ajax_nopriv_portfolio_get_project', array($this, 'ajax_get_project'));
        add_action('wp_ajax_sabsfe_portfolio_increment_views', array($this, 'ajax_increment_views'));
        add_action('wp_ajax_nopriv_portfolio_increment_views', array($this, 'ajax_increment_views'));
        add_action('wp_ajax_sabsfe_portfolio_increment_likes', array($this, 'ajax_increment_likes'));
        add_action('wp_ajax_nopriv_portfolio_increment_likes', array($this, 'ajax_increment_likes'));
    }
    
    /**
     * Cargar scripts y estilos del frontend
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            'portfolio-frontend-css',
            SABSFE_PORTFOLIO_URL . 'assets/css/frontend.css',
            array(),
            SABSFE_PORTFOLIO_VERSION
        );
        
        wp_enqueue_style(
            'portfolio-modal-css',
            SABSFE_PORTFOLIO_URL . 'assets/css/modal-styles.css',
            array(),
            SABSFE_PORTFOLIO_VERSION . '-' . time() // Forzar recarga sin caché
        );
        
        wp_enqueue_script(
            'portfolio-frontend-js',
            SABSFE_PORTFOLIO_URL . 'assets/js/frontend.js',
            array('jquery'),
            SABSFE_PORTFOLIO_VERSION . '-' . time(), // Forzar recarga sin caché
            true
        );
        
        wp_localize_script('portfolio-frontend-js', 'portfolio_frontend', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sabsfe_portfolio_frontend_nonce'),
            'strings' => [
                'loading' => __('Cargando...', 'sabsfe-portfolio-plugin'),
                'error' => __('Error al cargar el proyecto', 'sabsfe-portfolio-plugin'),
                'close' => __('Cerrar', 'sabsfe-portfolio-plugin'),
                'share' => __('Compartir', 'sabsfe-portfolio-plugin'),
                'like' => __('Me gusta', 'sabsfe-portfolio-plugin'),
                'liked' => __('Te gusta', 'sabsfe-portfolio-plugin')
            ]
        ));
    }
    
    /**
     * AJAX: Obtener proyecto para modal
     */
    public function ajax_get_project() {
        check_ajax_referer('sabsfe_portfolio_frontend_nonce', 'nonce');
        
        $project_id = intval($_POST['project_id']);
        $project = Sabsfe_Portfolio_Database::get_project($project_id);
        
        if (!$project) {
            wp_send_json_error(array(
                'message' => __('Proyecto no encontrado', 'sabsfe-portfolio-plugin')
            ));
        }
        
        // Incrementar vistas
        Sabsfe_Portfolio_Database::increment_views($project_id);
        
        // Preparar datos del proyecto
        $project_data = array(
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'featured_image' => $project->featured_image,
            'gallery' => $project->gallery ? unserialize($project->gallery) : array(),
            'category_name' => $project->category_name,
            'category_color' => $project->category_color,
            'external_url' => $project->external_url,
            'youtube_url' => $project->youtube_url ?? '',
            'vimeo_url' => $project->vimeo_url ?? '',
            'project_year' => $project->project_year,
            'project_date' => $project->project_date,
            'views' => $project->views + 1,
            'likes' => $project->likes,
            'created_at' => $project->created_at
        );
        
        wp_send_json_success($project_data);
    }
    
    /**
     * AJAX: Incrementar vistas
     */
    public function ajax_increment_views() {
        check_ajax_referer('sabsfe_portfolio_frontend_nonce', 'nonce');
        
        $project_id = intval($_POST['project_id']);
        $result = Sabsfe_Portfolio_Database::increment_views($project_id);
        
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }
    
    /**
     * AJAX: Incrementar likes
     */
    public function ajax_increment_likes() {
        check_ajax_referer('sabsfe_portfolio_frontend_nonce', 'nonce');
        
        $project_id = intval($_POST['project_id']);
        $result = Sabsfe_Portfolio_Database::increment_likes($project_id);
        
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }
    
    /**
     * Renderizar modal de proyecto
     */
    public static function render_project_modal() {
        ?>
        <div id="portfolio-modal" class="portfolio-modal-overlay" style="display: none;">
            <div class="portfolio-modal-container">
                <div class="portfolio-modal-content">
                    
                    <!-- Header del modal -->
                    <div class="portfolio-modal-header">
                        <div class="modal-header-left">
                            <h2 class="modal-project-title"></h2>
                            <div class="modal-project-meta">
                                <span class="modal-project-category"></span>
                                <span class="modal-project-date"></span>
                            </div>
                        </div>
                        
                        <div class="modal-header-right">
                            <div class="modal-project-stats">
                                <span class="modal-project-views">
                                    <span class="dashicons dashicons-visibility"></span>
                                    <span class="views-count">0</span>
                                </span>
                                <span class="modal-project-likes">
                                    <span class="dashicons dashicons-heart"></span>
                                    <span class="likes-count">0</span>
                                </span>
                            </div>
                            
                            <div class="modal-actions">
                                <button class="modal-like-btn" data-liked="false">
                                    <span class="dashicons dashicons-heart"></span>
                                </button>
                                <button class="modal-share-btn">
                                    <span class="dashicons dashicons-share"></span>
                                </button>
                                <button class="modal-close-btn">
                                    <span class="dashicons dashicons-no-alt"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contenido del modal -->
                    <div class="portfolio-modal-body">
                        <div class="modal-content-left">
                            
                            <!-- Imagen principal -->
                            <div class="modal-project-image">
                                <img src="" alt="" class="main-image">
                                <div class="image-overlay">
                                    <button class="image-zoom-btn">
                                        <span class="dashicons dashicons-search"></span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Videos -->
                            <div class="modal-project-videos" style="display: none;">
                                <h4><?php _e('Videos', 'sabsfe-portfolio-plugin'); ?></h4>
                                <div class="videos-container"></div>
                            </div>
                            
                            <!-- Galería -->
                            <div class="modal-project-gallery" style="display: none;">
                                <h4><?php _e('Galería', 'sabsfe-portfolio-plugin'); ?></h4>
                                <div class="gallery-grid"></div>
                            </div>
                            
                            <!-- Contenido detallado -->
                            <div class="modal-project-content">
                                <h3><?php _e('Sobre el Proyecto', 'sabsfe-portfolio-plugin'); ?></h3>
                                <div class="project-description"></div>
                                <div class="project-detailed-content"></div>
                            </div>
                            
                        </div>
                        
                        <div class="modal-content-right">
                            
                            <!-- Detalles del proyecto -->
                            <div class="modal-project-details">
                                <h4><?php _e('Detalles', 'sabsfe-portfolio-plugin'); ?></h4>
                                <div class="details-list">
                                    <div class="detail-item">
                                        <span class="detail-label"><?php _e('Categoría:', 'sabsfe-portfolio-plugin'); ?></span>
                                        <span class="detail-value category-value"></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label"><?php _e('Fecha:', 'sabsfe-portfolio-plugin'); ?></span>
                                        <span class="detail-value date-value"></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label"><?php _e('Vistas:', 'sabsfe-portfolio-plugin'); ?></span>
                                        <span class="detail-value views-value">0</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label"><?php _e('Likes:', 'sabsfe-portfolio-plugin'); ?></span>
                                        <span class="detail-value likes-value">0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botón de acción -->
                            <div class="modal-project-action">
                                <a href="#" class="modal-external-btn" target="_blank" style="display: none;">
                                    <span class="dashicons dashicons-external"></span>
                                    <?php _e('Ver Proyecto', 'sabsfe-portfolio-plugin'); ?>
                                </a>
                            </div>
                            
                            <!-- Compartir -->
                            <div class="modal-share-section">
                                <h4><?php _e('Compartir', 'sabsfe-portfolio-plugin'); ?></h4>
                                <div class="share-buttons">
                                    <button class="share-btn facebook" data-platform="facebook">
                                        <span class="dashicons dashicons-facebook"></span>
                                        Facebook
                                    </button>
                                    <button class="share-btn twitter" data-platform="twitter">
                                        <span class="dashicons dashicons-twitter"></span>
                                        Twitter
                                    </button>
                                    <button class="share-btn linkedin" data-platform="linkedin">
                                        <span class="dashicons dashicons-linkedin"></span>
                                        LinkedIn
                                    </button>
                                    <button class="share-btn copy-link" data-platform="copy">
                                        <span class="dashicons dashicons-admin-links"></span>
                                        <?php _e('Copiar Enlace', 'sabsfe-portfolio-plugin'); ?>
                                    </button>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <!-- Modal de imagen ampliada -->
        <div id="portfolio-image-modal" class="portfolio-image-modal" style="display: none;">
            <div class="image-modal-content">
                <button class="image-modal-close">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
                <img src="" alt="" class="modal-image">
                <div class="image-modal-nav">
                    <button class="image-prev">
                        <span class="dashicons dashicons-arrow-left-alt2"></span>
                    </button>
                    <button class="image-next">
                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
}
