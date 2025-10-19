1. Estándares de Código y Nomenclatura
Estándares de PHP: El código debe adherirse a los estándares de codificación de WordPress.

Prefijos Rigurosos: Absolutamente todo debe llevar un prefijo único. Esto aplica a clases, funciones, variables globales, hooks, transients y tablas de base de datos. (Ejemplo: sabsfe_ para evitar conflictos).

Nomenclatura:

Clases: Utilizar PascalCase (Ej.: Class_SABSFE_Tracker).

Funciones y Métodos: Utilizar snake_case (Ej.: sabsfe_get_active_tests()).

Archivos: Los nombres de archivo deben ser en snake_case y describir el contenido (Ej.: class-sabsfe-tracker.php).

2. Seguridad y Rendimiento (Core)
Sanitización de Entrada: Siempre se deben validar y sanear todos los datos de entrada del usuario (formularios, URL, APIs) usando funciones nativas de WordPress (sanitize_text_field(), absint(), etc.).

Escape de Salida: Siempre se debe escapar el contenido de salida (output) con funciones como esc_html(), esc_attr(), esc_url() o wp_kses() antes de mostrarlo en el frontend o backend.

Noncing (CSRF): Implementar nonces de WordPress para proteger todas las acciones (guardar datos, AJAX, etc.) y prevenir ataques de falsificación de solicitudes entre sitios (CSRF).

Capacidades y Permisos: Restringir el acceso a la lógica administrativa utilizando current_user_can() para verificar las capacidades del usuario (Ej.: 'manage_options').

Consultas Seguras: Todas las interacciones con la base de datos deben usar la clase $wpdb. Las consultas deben ser preparadas usando $wpdb->prepare() para prevenir inyecciones SQL.

Carga Condicional: Scripts y estilos solo deben cargarse cuando sean estrictamente necesarios, utilizando lógica condicional en los hooks de encolado (wp_enqueue_scripts).

3. Arquitectura y Estructura
Orientación a Objetos (OOP): El código debe ser modular y estructurado en clases. Utilizar el patrón Singleton para la clase principal.

Uso de Hooks: Toda interacción con el core debe realizarse a través de add_action y add_filter. Prohibido modificar archivos del core, temas o de otros plugins.

Arquitectura de Datos (Grandes Volúmenes): Para datos transaccionales o de alto volumen (como el seguimiento de A/B testing), utilizar Tablas Personalizadas de la base de datos en lugar de wp_options.

AJAX: Las peticiones asíncronas deben usar la API de AJAX de WordPress (wp_ajax_... y wp_ajax_nopriv_...).

Estructura de Vistas: Separar la lógica de la vista. El HTML para el frontend y las interfaces de administración debe residir en archivos de plantilla (.php) dentro de la carpeta /templates.

Internalización (i18n): Todo texto visible debe ser traducible usando las funciones __() o _e() y especificando el text domain del plugin.

4. Integración con Page Builders
Elementor: La integración debe realizarse a través de los filtros de Elementor (Ej.: elementor/frontend/the_content) para modificar el JSON de la página (_elementor_data) antes de la renderización. No se debe intentar manipular el HTML generado por Elementor mediante output buffering genérico de WordPress.

Detección de Elemento: Utilizar los IDs de Elementor (element_id) como clave de referencia para las pruebas A/B.

5. Documentación y Mantenimiento
Documentación PHPDoc: Documentar exhaustivamente clases, métodos y propiedades con PHPDoc, describiendo parámetros, valores de retorno y la lógica.

Archivos de Documentación: Incluir el readme.txt en formato WordPress estándar.

Guía de Usuario: El plugin debe incluir una Guía de Uso legible y accesible desde la interfaz de administración.