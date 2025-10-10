# Portfolio Projects Manager

Un plugin completo para WordPress que permite gestionar portafolios de proyectos con integración a Elementor. Diseñado con Material Design y tema oscuro, replicando exactamente el diseño de las imágenes de referencia.

## Características

### ✨ Funcionalidades Principales
- **Gestión completa de proyectos**: Crear, editar, eliminar proyectos
- **Sistema de categorías**: Organizar proyectos por categorías con colores personalizables
- **Widget de Elementor**: Integración completa con Elementor para mostrar la grilla de proyectos
- **Modal de detalle**: Vista detallada de proyectos con galería de imágenes
- **Tema oscuro**: Diseño Material Design con tema oscuro elegante
- **Responsive**: Completamente adaptable a dispositivos móviles
- **API REST**: Endpoints para integración con aplicaciones externas

### 🎨 Diseño Visual
- **Grilla de 3 columnas**: Replica exactamente el diseño de `portfolio list.jpg`
- **Modal de detalle**: Replica exactamente el diseño de `modal detail portfolio.jpg`
- **Material Design**: Componentes siguiendo las guías de Material Design
- **Tema oscuro**: Paleta de colores oscura y moderna
- **Animaciones suaves**: Transiciones y efectos hover elegantes

### 🔧 Panel de Administración
- **Gestión de proyectos**: Interfaz intuitiva para CRUD de proyectos
- **Gestión de categorías**: Sistema completo de categorías con colores
- **Configuración**: Opciones de personalización del plugin
- **Logs del sistema**: Monitoreo de actividad y errores
- **Estadísticas**: Métricas de uso y rendimiento

## Instalación

### Método 1: Instalación Manual
1. Descarga el plugin desde el repositorio
2. Sube la carpeta `portfolio-plugin` a `/wp-content/plugins/`
3. Activa el plugin desde el panel de administración de WordPress
4. Ve a **Portfolio** en el menú de administración para comenzar

### Método 2: Instalación via ZIP
1. Comprime la carpeta del plugin en un archivo ZIP
2. Ve a **Plugins > Añadir nuevo** en WordPress
3. Sube el archivo ZIP
4. Activa el plugin

## Configuración Inicial

### 1. Crear Categorías
1. Ve a **Portfolio > Categorías**
2. Haz clic en **Agregar Nueva**
3. Completa el nombre, descripción y selecciona un color
4. Guarda la categoría

### 2. Agregar Proyectos
1. Ve a **Portfolio > Proyectos**
2. Haz clic en **Agregar Nuevo**
3. Completa la información del proyecto:
   - Título del proyecto
   - Descripción
   - Contenido detallado
   - Imagen destacada
   - Categoría
   - URL externa (opcional)
   - Galería de imágenes (opcional)

### 3. Configurar Elementor
1. Edita una página con Elementor
2. Busca el widget **Portfolio Grid**
3. Arrastra el widget a tu página
4. Configura las opciones:
   - Número de proyectos por página
   - Filtros por categoría
   - Ordenamiento
   - Diseño de columnas

## Uso del Widget de Elementor

### Opciones de Contenido
- **Mostrar Título**: Activar/desactivar título del widget
- **Proyectos por página**: Número de proyectos a mostrar
- **Filtrar por categoría**: Mostrar solo proyectos de categorías específicas
- **Solo proyectos destacados**: Mostrar únicamente proyectos marcados como destacados
- **Ordenar por**: Fecha, título, vistas, likes
- **Orden**: Ascendente o descendente

### Opciones de Diseño
- **Columnas**: 1, 2, 3 o 4 columnas
- **Mostrar categoría**: Activar/desactivar badge de categoría
- **Mostrar estadísticas**: Activar/desactivar contadores de vistas y likes
- **Habilitar modal**: Activar/desactivar modal de detalle

### Opciones de Estilo
- **Fondo de tarjeta**: Color de fondo de las tarjetas
- **Radio de borde**: Redondez de las esquinas
- **Tipografía del título**: Fuente y tamaño del título
- **Color del título**: Color del texto del título

## Estructura de Archivos

```
portfolio-plugin/
├── portfolio-plugin.php          # Archivo principal del plugin
├── includes/                      # Clases PHP del plugin
│   ├── class-database.php         # Manejo de base de datos
│   ├── class-logger.php           # Sistema de logging
│   ├── class-admin.php            # Panel de administración
│   ├── class-elementor-widget.php # Widget de Elementor
│   ├── class-frontend.php         # Frontend y modal
│   └── class-api.php              # API REST
├── admin/                         # Páginas de administración
│   ├── projects.php               # Gestión de proyectos
│   ├── categories.php              # Gestión de categorías
│   └── settings.php               # Configuración
├── assets/                        # Recursos estáticos
│   ├── css/
│   │   ├── admin.css              # Estilos del admin
│   │   └── frontend.css            # Estilos del frontend
│   ├── js/
│   │   ├── admin.js                # JavaScript del admin
│   │   └── frontend.js             # JavaScript del frontend
│   └── images/                     # Imágenes del plugin
└── templates/                      # Plantillas (futuro uso)
```

## Base de Datos

El plugin crea dos tablas principales:

### `wp_portfolio_projects`
- `id`: ID único del proyecto
- `title`: Título del proyecto
- `slug`: URL slug
- `description`: Descripción corta
- `content`: Contenido detallado
- `featured_image`: URL de imagen destacada
- `gallery`: Galería de imágenes (serializada)
- `category_id`: ID de la categoría
- `status`: Estado (published, draft, private)
- `featured`: Proyecto destacado (0/1)
- `views`: Contador de vistas
- `likes`: Contador de likes
- `external_url`: URL externa
- `project_date`: Fecha del proyecto
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

### `wp_portfolio_categories`
- `id`: ID único de la categoría
- `name`: Nombre de la categoría
- `slug`: URL slug
- `description`: Descripción
- `color`: Color hexadecimal
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

## API REST

El plugin incluye endpoints REST API:

- `GET /wp-json/portfolio/v1/projects` - Obtener proyectos
- `GET /wp-json/portfolio/v1/projects/{id}` - Obtener proyecto específico
- `GET /wp-json/portfolio/v1/categories` - Obtener categorías
- `POST /wp-json/portfolio/v1/projects/{id}/views` - Incrementar vistas
- `POST /wp-json/portfolio/v1/projects/{id}/likes` - Incrementar likes

## Galería de Imágenes

### Configuración de la Galería en el Modal

El plugin incluye una galería con scroll vertical que permite visualizar múltiples imágenes y videos de cada proyecto.

#### Controles Disponibles en Elementor:

1. **Ancho de las imágenes**: Controla el ancho de cada imagen en la galería (por defecto 100%)
2. **Altura de las imágenes**: Controla la altura de cada imagen
   - **Auto** (recomendado): Mantiene las proporciones originales de las imágenes
   - **Píxeles fijos**: Establece una altura fija (se recortarán para ajustarse)
3. **Espacio entre imágenes**: Controla la separación vertical entre imágenes (por defecto 15px)

#### Características:

- ✅ Scroll vertical suave con scrollbar personalizada
- ✅ Visualización de imágenes y videos
- ✅ Efectos hover en cada imagen
- ✅ Mantiene proporciones originales (modo 'auto')
- ✅ Adaptable a diferentes tamaños de pantalla

#### Galería de Elementor:

Además de las imágenes del proyecto, puedes usar una galería de Elementor:

1. Activa "**Usar galería de Elementor**" en el widget
2. Selecciona las imágenes en "**Galería de Elementor**"
3. Esta galería se mostrará en el modal de todos los proyectos

## Personalización

### Hooks y Filtros

```php
// Filtrar proyectos antes de mostrar
add_filter('portfolio_get_projects', function($projects) {
    // Modificar $projects
    return $projects;
});

// Modificar datos del proyecto
add_filter('portfolio_project_data', function($data, $project) {
    // Modificar $data
    return $data;
}, 10, 2);
```

### CSS Personalizado

```css
/* Personalizar colores del tema */
:root {
    --portfolio-primary: #your-color;
    --portfolio-card-bg: #your-bg-color;
}

/* Personalizar tarjetas */
.portfolio-card {
    /* Tus estilos personalizados */
}
```

## Requisitos del Sistema

- **WordPress**: 5.0 o superior
- **PHP**: 7.4 o superior
- **Elementor**: 3.0 o superior (opcional pero recomendado)
- **MySQL**: 5.6 o superior

## Soporte y Contribución

### Reportar Problemas
Si encuentras algún problema, por favor:
1. Verifica que cumples los requisitos del sistema
2. Revisa los logs del plugin en **Portfolio > Configuración**
3. Crea un issue en el repositorio con detalles del problema

### Contribuir
Las contribuciones son bienvenidas. Por favor:
1. Fork el repositorio
2. Crea una rama para tu feature
3. Haz commit de tus cambios
4. Crea un Pull Request

## Changelog

### Versión 1.1.0
- **Nuevo:** Soporte para videos de YouTube y Vimeo en proyectos
- **Nuevo:** Campos de video en el formulario de administración
- **Nuevo:** Videos embebidos responsive en el modal (aspect ratio 16:9)
- **Mejora:** Detección automática de IDs de YouTube y Vimeo
- **Mejora:** Sección de videos solo visible cuando hay contenido
- **Mejora:** Estilos Material Design con efectos hover y transiciones

### Versión 1.0.0
- Lanzamiento inicial
- Gestión completa de proyectos y categorías
- Widget de Elementor
- Modal de detalle
- API REST
- Sistema de logging
- Tema oscuro Material Design

## Licencia

Este plugin está licenciado bajo GPL v2 o posterior.

## Autor

**Gerardo Riarte + Cursor**
- Website: [gerardoriarte.com](https://gerardoriarte.com)
- Desarrollado con asistencia de Cursor AI

## Créditos

Desarrollado siguiendo las mejores prácticas de WordPress y Material Design Guidelines.
