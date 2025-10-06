# Lista de Archivos para el Paquete ZIP del Plugin Portfolio

## ✅ Archivos OBLIGATORIOS (Deben incluirse en el ZIP)

### Archivos Principales
- `portfolio-plugin.php` - Archivo principal del plugin
- `uninstall.php` - Script de desinstalación
- `config.php` - Configuraciones del plugin
- `README.md` - Documentación del plugin

### Clases PHP (carpeta includes/)
- `includes/class-database.php` - Manejo de base de datos
- `includes/class-logger.php` - Sistema de logging
- `includes/class-admin.php` - Panel de administración
- `includes/class-elementor-widget.php` - Widget de Elementor
- `includes/class-frontend.php` - Frontend y modal
- `includes/class-api.php` - API REST

### Páginas de Administración (carpeta admin/)
- `admin/projects.php` - Gestión de proyectos
- `admin/categories.php` - Gestión de categorías
- `admin/settings.php` - Configuración del plugin

### Recursos Estáticos (carpeta assets/)
- `assets/css/admin.css` - Estilos del panel admin
- `assets/css/frontend.css` - Estilos del frontend
- `assets/js/admin.js` - JavaScript del panel admin
- `assets/js/frontend.js` - JavaScript del frontend

### Carpetas Vacías (deben crearse)
- `assets/images/` - Para imágenes del plugin
- `templates/` - Para plantillas futuras
- `languages/` - Para archivos de traducción

## 🚫 Archivos que NO deben incluirse

### Archivos de Desarrollo/Prueba
- `modal detail portfolio.jpg` - Imagen de referencia
- `portfolio list.jpg` - Imagen de referencia
- `fix-portfolio-error.php` - Script de corrección manual
- `test-installation.php` - Script de prueba
- `INSTALACION.md` - Instrucciones de desarrollo
- `create-package.php` - Generador de paquetes
- `portfolio-plugin.zip` - El propio archivo ZIP

## 📦 Estructura Final del ZIP

```
portfolio-plugin.zip
└── portfolio-plugin/
    ├── portfolio-plugin.php
    ├── uninstall.php
    ├── config.php
    ├── README.md
    ├── includes/
    │   ├── class-database.php
    │   ├── class-logger.php
    │   ├── class-admin.php
    │   ├── class-elementor-widget.php
    │   ├── class-frontend.php
    │   └── class-api.php
    ├── admin/
    │   ├── projects.php
    │   ├── categories.php
    │   └── settings.php
    ├── assets/
    │   ├── css/
    │   │   ├── admin.css
    │   │   └── frontend.css
    │   ├── js/
    │   │   ├── admin.js
    │   │   └── frontend.js
    │   └── images/
    ├── templates/
    └── languages/
```

## 🚀 Instrucciones para Crear el ZIP

### Método 1: Manual
1. Crea una carpeta llamada `portfolio-plugin`
2. Copia todos los archivos obligatorios a esta carpeta
3. Crea las carpetas vacías necesarias
4. Comprime la carpeta en un archivo ZIP

### Método 2: Automático
1. Ejecuta el script `create-package.php`
2. El script creará automáticamente el ZIP con los archivos correctos

## ✅ Verificación del Paquete

Antes de distribuir, verifica que el ZIP contenga:
- [ ] Archivo principal `portfolio-plugin.php`
- [ ] Todas las clases en `includes/`
- [ ] Todas las páginas admin en `admin/`
- [ ] Todos los archivos CSS y JS en `assets/`
- [ ] Carpetas vacías necesarias
- [ ] NO contiene archivos de desarrollo/prueba

## 📋 Checklist de Distribución

- [ ] ZIP creado con estructura correcta
- [ ] Todos los archivos obligatorios incluidos
- [ ] Archivos de desarrollo excluidos
- [ ] Plugin probado en instalación limpia
- [ ] Documentación actualizada
- [ ] Versión numerada correctamente
