<?php
/**
 * Clase API para manejo de endpoints del plugin Portfolio
 */

if (!defined('ABSPATH')) {
    exit;
}

class PortfolioAPI {
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    /**
     * Registrar rutas de la API REST
     */
    public function register_routes() {
        // Endpoint para obtener proyectos
        register_rest_route('portfolio/v1', '/projects', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_projects'),
            'permission_callback' => '__return_true',
            'args' => array(
                'per_page' => array(
                    'default' => 12,
                    'sanitize_callback' => 'absint',
                ),
                'page' => array(
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                ),
                'category' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'featured' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'orderby' => array(
                    'default' => 'created_at',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'order' => array(
                    'default' => 'DESC',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));
        
        // Endpoint para obtener un proyecto específico
        register_rest_route('portfolio/v1', '/projects/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_project'),
            'permission_callback' => '__return_true',
            'args' => array(
                'id' => array(
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ));
        
        // Endpoint para obtener categorías
        register_rest_route('portfolio/v1', '/categories', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_categories'),
            'permission_callback' => '__return_true',
        ));
        
        // Endpoint para incrementar vistas
        register_rest_route('portfolio/v1', '/projects/(?P<id>\d+)/views', array(
            'methods' => 'POST',
            'callback' => array($this, 'increment_views'),
            'permission_callback' => '__return_true',
            'args' => array(
                'id' => array(
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ));
        
        // Endpoint para incrementar likes
        register_rest_route('portfolio/v1', '/projects/(?P<id>\d+)/likes', array(
            'methods' => 'POST',
            'callback' => array($this, 'increment_likes'),
            'permission_callback' => '__return_true',
            'args' => array(
                'id' => array(
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ));
    }
    
    /**
     * Obtener proyectos
     */
    public function get_projects($request) {
        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        $category = $request->get_param('category');
        $featured = $request->get_param('featured');
        $orderby = $request->get_param('orderby');
        $order = $request->get_param('order');
        
        $args = array(
            'status' => 'published',
            'limit' => $per_page,
            'offset' => ($page - 1) * $per_page,
            'orderby' => $orderby,
            'order' => $order
        );
        
        if (!empty($category)) {
            $args['category_id'] = intval($category);
        }
        
        if ($featured === 'true') {
            $args['featured'] = 1;
        }
        
        $projects = PortfolioDatabase::get_projects($args);
        
        // Preparar respuesta
        $formatted_projects = array();
        foreach ($projects as $project) {
            $formatted_projects[] = $this->format_project($project);
        }
        
        // Obtener total para paginación
        $total_args = $args;
        unset($total_args['limit'], $total_args['offset']);
        $total_projects = count(PortfolioDatabase::get_projects($total_args));
        
        $response = array(
            'projects' => $formatted_projects,
            'pagination' => array(
                'current_page' => $page,
                'per_page' => $per_page,
                'total' => $total_projects,
                'total_pages' => ceil($total_projects / $per_page)
            )
        );
        
        if (class_exists('PortfolioLogger')) {
            PortfolioLogger::info('api', 'get_projects', 'Proyectos obtenidos via API', array(
                'count' => count($formatted_projects),
                'page' => $page
            ));
        }
        
        return new WP_REST_Response($response, 200);
    }
    
    /**
     * Obtener un proyecto específico
     */
    public function get_project($request) {
        $project_id = $request->get_param('id');
        $project = PortfolioDatabase::get_project($project_id);
        
        if (!$project) {
            return new WP_Error('project_not_found', __('Proyecto no encontrado', 'portfolio-plugin'), array('status' => 404));
        }
        
        // Incrementar vistas
        PortfolioDatabase::increment_views($project_id);
        
        $formatted_project = $this->format_project($project, true);
        
        if (class_exists('PortfolioLogger')) {
            PortfolioLogger::info('api', 'get_project', 'Proyecto obtenido via API', array(
                'project_id' => $project_id
            ));
        }
        
        return new WP_REST_Response($formatted_project, 200);
    }
    
    /**
     * Obtener categorías
     */
    public function get_categories($request) {
        $categories = PortfolioDatabase::get_categories();
        
        $formatted_categories = array();
        foreach ($categories as $category) {
            $formatted_categories[] = array(
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'color' => $category->color,
                'created_at' => $category->created_at
            );
        }
        
        if (class_exists('PortfolioLogger')) {
            PortfolioLogger::info('api', 'get_categories', 'Categorías obtenidas via API', array(
                'count' => count($formatted_categories)
            ));
        }
        
        return new WP_REST_Response($formatted_categories, 200);
    }
    
    /**
     * Incrementar vistas
     */
    public function increment_views($request) {
        $project_id = $request->get_param('id');
        $result = PortfolioDatabase::increment_views($project_id);
        
        if ($result) {
            if (class_exists('PortfolioLogger')) {
                PortfolioLogger::info('api', 'increment_views', 'Vistas incrementadas via API', array(
                    'project_id' => $project_id
                ));
            }
            
            return new WP_REST_Response(array('success' => true), 200);
        } else {
            return new WP_Error('increment_failed', __('Error al incrementar vistas', 'portfolio-plugin'), array('status' => 500));
        }
    }
    
    /**
     * Incrementar likes
     */
    public function increment_likes($request) {
        $project_id = $request->get_param('id');
        $result = PortfolioDatabase::increment_likes($project_id);
        
        if ($result) {
            if (class_exists('PortfolioLogger')) {
                PortfolioLogger::info('api', 'increment_likes', 'Likes incrementados via API', array(
                    'project_id' => $project_id
                ));
            }
            
            return new WP_REST_Response(array('success' => true), 200);
        } else {
            return new WP_Error('increment_failed', __('Error al incrementar likes', 'portfolio-plugin'), array('status' => 500));
        }
    }
    
    /**
     * Formatear proyecto para respuesta
     */
    private function format_project($project, $include_content = false) {
        $formatted = array(
            'id' => $project->id,
            'title' => $project->title,
            'slug' => $project->slug,
            'description' => $project->description,
            'featured_image' => $project->featured_image,
            'category' => array(
                'id' => $project->category_id,
                'name' => $project->category_name,
                'color' => $project->category_color
            ),
            'status' => $project->status,
            'featured' => (bool) $project->featured,
            'views' => intval($project->views),
            'likes' => intval($project->likes),
            'external_url' => $project->external_url,
            'project_date' => $project->project_date,
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at
        );
        
        if ($include_content) {
            $formatted['content'] = $project->content;
            $formatted['gallery'] = $project->gallery ? unserialize($project->gallery) : array();
        }
        
        return $formatted;
    }
}
