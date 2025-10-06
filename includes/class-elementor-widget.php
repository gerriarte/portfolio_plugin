<?php
/**
 * Widget de Elementor para Portfolio
 */

if (!defined('ABSPATH')) {
    exit;
}

class PortfolioElementorWidget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'portfolio-grid';
    }
    
    public function get_title() {
        return __('Portfolio Grid', 'portfolio-plugin');
    }
    
    public function get_icon() {
        return 'eicon-posts-grid';
    }
    
    public function get_categories() {
        return ['general'];
    }
    
    public function get_keywords() {
        return ['portfolio', 'projects', 'grid', 'gallery'];
    }
    
    protected function register_controls() {
        
        // Sección de Contenido
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Contenido', 'portfolio-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'show_title',
            [
                'label' => __('Mostrar Título', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Mostrar', 'portfolio-plugin'),
                'label_off' => __('Ocultar', 'portfolio-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'title_text',
            [
                'label' => __('Título', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Portfolio', 'portfolio-plugin'),
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'items_per_page',
            [
                'label' => __('Proyectos por página', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'default' => 12,
            ]
        );
        
        $this->add_control(
            'category_filter',
            [
                'label' => __('Filtrar por categoría', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_categories_options(),
                'multiple' => true,
                'label_block' => true,
            ]
        );
        
        $this->add_control(
            'show_featured_only',
            [
                'label' => __('Solo proyectos destacados', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'portfolio-plugin'),
                'label_off' => __('No', 'portfolio-plugin'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'order_by',
            [
                'label' => __('Ordenar por', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'created_at' => __('Fecha de creación', 'portfolio-plugin'),
                    'project_date' => __('Fecha del proyecto', 'portfolio-plugin'),
                    'title' => __('Título', 'portfolio-plugin'),
                    'views' => __('Vistas', 'portfolio-plugin'),
                    'likes' => __('Likes', 'portfolio-plugin'),
                ],
                'default' => 'created_at',
            ]
        );
        
        $this->add_control(
            'order',
            [
                'label' => __('Orden', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'DESC' => __('Descendente', 'portfolio-plugin'),
                    'ASC' => __('Ascendente', 'portfolio-plugin'),
                ],
                'default' => 'DESC',
            ]
        );
        
        $this->end_controls_section();
        
        // Sección de Diseño
        $this->start_controls_section(
            'design_section',
            [
                'label' => __('Diseño', 'portfolio-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'columns',
            [
                'label' => __('Columnas', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'default' => '3',
            ]
        );
        
        $this->add_control(
            'show_category',
            [
                'label' => __('Mostrar categoría', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Mostrar', 'portfolio-plugin'),
                'label_off' => __('Ocultar', 'portfolio-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_stats',
            [
                'label' => __('Mostrar estadísticas', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Mostrar', 'portfolio-plugin'),
                'label_off' => __('Ocultar', 'portfolio-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'enable_modal',
            [
                'label' => __('Habilitar modal', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'portfolio-plugin'),
                'label_off' => __('No', 'portfolio-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->end_controls_section();
        
        // Sección de Estilo
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Estilo', 'portfolio-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'card_background',
            [
                'label' => __('Fondo de tarjeta', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#2C2C2C',
                'selectors' => [
                    '{{WRAPPER}} .portfolio-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Radio de borde', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .portfolio-card' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Tipografía del título', 'portfolio-plugin'),
                'selector' => '{{WRAPPER}} .portfolio-card-title',
            ]
        );
        
        $this->add_control(
            'title_color',
            [
                'label' => __('Color del título', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .portfolio-card-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Sección de Estilo del Modal
        $this->start_controls_section(
            'modal_style_section',
            [
                'label' => __('Estilo del Modal', 'portfolio-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'modal_background',
            [
                'label' => __('Fondo del modal', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'modal_title_typography',
                'label' => __('Tipografía del título', 'portfolio-plugin'),
                'selector' => '#pf-modal #pf-title',
            ]
        );
        
        $this->add_control(
            'modal_title_color',
            [
                'label' => __('Color del título', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'modal_text_typography',
                'label' => __('Tipografía del texto', 'portfolio-plugin'),
                'selector' => '#pf-modal #pf-desc',
            ]
        );
        
        $this->add_control(
            'modal_text_color',
            [
                'label' => __('Color del texto', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#555555',
            ]
        );
        
        $this->add_control(
            'modal_border_radius',
            [
                'label' => __('Radio de borde del modal', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
            ]
        );
        
        // Separador
        $this->add_control(
            'category_badge_heading',
            [
                'label' => __('Badge de Categoría', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        
        $this->add_control(
            'modal_category_bg',
            [
                'label' => __('Color de fondo del badge', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#2196F3',
            ]
        );
        
        $this->add_control(
            'modal_category_text_color',
            [
                'label' => __('Color de texto del badge', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
            ]
        );
        
        $this->add_control(
            'modal_category_border_radius',
            [
                'label' => __('Radio de borde del badge', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
            ]
        );
        
        // Separador
        $this->add_control(
            'external_button_heading',
            [
                'label' => __('Botón Ver Proyecto', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'modal_button_typography',
                'label' => __('Tipografía del botón', 'portfolio-plugin'),
                'selector' => '#pf-external-link',
            ]
        );
        
        $this->add_control(
            'modal_button_bg',
            [
                'label' => __('Color de fondo del botón', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#2196F3',
            ]
        );
        
        $this->add_control(
            'modal_button_text_color',
            [
                'label' => __('Color de texto del botón', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
            ]
        );
        
        $this->add_control(
            'modal_button_hover_bg',
            [
                'label' => __('Color de fondo al hover', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1976D2',
            ]
        );
        
        $this->add_control(
            'modal_button_border_radius',
            [
                'label' => __('Radio de borde del botón', 'portfolio-plugin'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 6,
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Preparar argumentos para la consulta
        $args = array(
            'status' => 'published',
            'limit' => intval($settings['items_per_page']),
            'orderby' => $settings['order_by'],
            'order' => $settings['order']
        );
        
        if ($settings['show_featured_only'] === 'yes') {
            $args['featured'] = 1;
        }
        
        if (!empty($settings['category_filter'])) {
            $args['category_id'] = $settings['category_filter'][0]; // Por simplicidad, solo primera categoría
        }
        
        $projects = PortfolioDatabase::get_projects($args);
        
        // Renderizar la grilla
        $this->render_portfolio_grid($projects, $settings);
    }
    
    protected function render_portfolio_grid($projects, $settings) {
        $columns = intval($settings['columns']);
        $enable_modal = $settings['enable_modal'] === 'yes';
        
        // Estilos del modal como data attributes
        $modal_bg = !empty($settings['modal_background']) ? $settings['modal_background'] : '#FFFFFF';
        $modal_title_color = !empty($settings['modal_title_color']) ? $settings['modal_title_color'] : '#333333';
        $modal_text_color = !empty($settings['modal_text_color']) ? $settings['modal_text_color'] : '#555555';
        $modal_border_radius = !empty($settings['modal_border_radius']['size']) ? $settings['modal_border_radius']['size'] . 'px' : '12px';
        
        // Estilos del badge de categoría
        $category_bg = !empty($settings['modal_category_bg']) ? $settings['modal_category_bg'] : '#2196F3';
        $category_text_color = !empty($settings['modal_category_text_color']) ? $settings['modal_category_text_color'] : '#FFFFFF';
        $category_border_radius = !empty($settings['modal_category_border_radius']['size']) ? $settings['modal_category_border_radius']['size'] . 'px' : '20px';
        
        // Estilos del botón externo
        $button_bg = !empty($settings['modal_button_bg']) ? $settings['modal_button_bg'] : '#2196F3';
        $button_text_color = !empty($settings['modal_button_text_color']) ? $settings['modal_button_text_color'] : '#FFFFFF';
        $button_hover_bg = !empty($settings['modal_button_hover_bg']) ? $settings['modal_button_hover_bg'] : '#1976D2';
        $button_border_radius = !empty($settings['modal_button_border_radius']['size']) ? $settings['modal_button_border_radius']['size'] . 'px' : '6px';
        
        ?>
        <div class="portfolio-elementor-widget" 
             data-columns="<?php echo $columns; ?>" 
             data-modal="<?php echo $enable_modal ? 'true' : 'false'; ?>"
             data-modal-bg="<?php echo esc_attr($modal_bg); ?>"
             data-modal-title-color="<?php echo esc_attr($modal_title_color); ?>"
             data-modal-text-color="<?php echo esc_attr($modal_text_color); ?>"
             data-modal-border-radius="<?php echo esc_attr($modal_border_radius); ?>"
             data-category-bg="<?php echo esc_attr($category_bg); ?>"
             data-category-text-color="<?php echo esc_attr($category_text_color); ?>"
             data-category-border-radius="<?php echo esc_attr($category_border_radius); ?>"
             data-button-bg="<?php echo esc_attr($button_bg); ?>"
             data-button-text-color="<?php echo esc_attr($button_text_color); ?>"
             data-button-hover-bg="<?php echo esc_attr($button_hover_bg); ?>"
             data-button-border-radius="<?php echo esc_attr($button_border_radius); ?>">
            
            <?php if ($settings['show_title'] === 'yes' && !empty($settings['title_text'])): ?>
                <h2 class="portfolio-widget-title"><?php echo esc_html($settings['title_text']); ?></h2>
            <?php endif; ?>
            
            <div class="portfolio-grid portfolio-grid-<?php echo $columns; ?>-columns">
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="portfolio-card" data-project-id="<?php echo $project->id; ?>">
                            
                            <div class="portfolio-card-image">
                                <?php if ($project->featured_image): ?>
                                    <img src="<?php echo esc_url($project->featured_image); ?>" 
                                         alt="<?php echo esc_attr($project->title); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="portfolio-no-image">
                                        <span class="dashicons dashicons-format-image"></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="portfolio-card-overlay">
                                    <div class="portfolio-card-actions">
                                        <?php if ($enable_modal): ?>
                                            <button class="portfolio-view-btn" data-project-id="<?php echo $project->id; ?>">
                                                <span class="dashicons dashicons-visibility"></span>
                                                <?php _e('Ver Detalles', 'portfolio-plugin'); ?>
                                            </button>
                                        <?php else: ?>
                                            <a href="<?php echo esc_url($project->external_url ?: '#'); ?>" 
                                               class="portfolio-view-btn" 
                                               target="_blank">
                                                <span class="dashicons dashicons-external"></span>
                                                <?php _e('Ver Proyecto', 'portfolio-plugin'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="portfolio-card-content">
                                <h3 class="portfolio-card-title"><?php echo esc_html($project->title); ?></h3>
                                
                                <?php if ($settings['show_category'] === 'yes' && $project->category_name): ?>
                                    <div class="portfolio-card-category">
                                        <span class="category-badge" 
                                              style="background-color: <?php echo esc_attr($project->category_color); ?>">
                                            <?php echo esc_html($project->category_name); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($project->description): ?>
                                    <p class="portfolio-card-description">
                                        <?php echo wp_trim_words($project->description, 15); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($settings['show_stats'] === 'yes'): ?>
                                    <div class="portfolio-card-stats">
                                        <span class="portfolio-stat">
                                            <span class="dashicons dashicons-visibility"></span>
                                            <?php echo number_format($project->views); ?>
                                        </span>
                                        <span class="portfolio-stat">
                                            <span class="dashicons dashicons-heart"></span>
                                            <?php echo number_format($project->likes); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="portfolio-no-projects">
                        <p><?php _e('No hay proyectos para mostrar.', 'portfolio-plugin'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    private function get_categories_options() {
        $categories = PortfolioDatabase::get_categories();
        $options = array();
        
        foreach ($categories as $category) {
            $options[$category->id] = $category->name;
        }
        
        return $options;
    }
}
