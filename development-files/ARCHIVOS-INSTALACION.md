# Archivos para Instalación en WordPress

## ✅ ARCHIVOS QUE SÍ SE DEBEN INSTALAR

### 📁 Estructura Completa del Plugin:
```
portfolio-plugin/
├── portfolio-plugin.php          ✅ Archivo principal del plugin
├── uninstall.php                 ✅ Script de desinstalación
├── config.php                    ✅ Configuraciones del plugin
├── README.md                     ✅ Documentación del plugin
├── admin/                        ✅ Páginas de administración
│   ├── categories.php           ✅ Gestión de categorías
│   ├── projects.php             ✅ Gestión de proyectos
│   └── settings.php             ✅ Configuraciones
├── assets/                       ✅ Recursos estáticos
│   ├── css/                     ✅ Estilos
│   │   ├── admin.css           ✅ Estilos del admin
│   │   └── frontend.css        ✅ Estilos del frontend
│   ├── js/                      ✅ Scripts JavaScript
│   │   ├── admin.js            ✅ Scripts del admin
│   │   └── frontend.js         ✅ Scripts del frontend
│   └── images/                  ✅ Imágenes del plugin (vacía)
├── includes/                     ✅ Clases PHP del plugin
│   ├── class-admin.php         ✅ Gestión del admin
│   ├── class-api.php           ✅ API REST
│   ├── class-database.php      ✅ Gestión de base de datos
│   ├── class-elementor-widget.php ✅ Widget de Elementor
│   ├── class-frontend.php      ✅ Gestión del frontend
│   └── class-logger.php        ✅ Sistema de logging
└── templates/                   ✅ Plantillas (vacía)
```

## ❌ ARCHIVOS QUE NO SE DEBEN INSTALAR

### 📁 Carpeta `development-files/` (COMPLETA):
```
development-files/
├── create-package.php           ❌ Script para crear ZIP
├── debug-elementor-widget.php   ❌ Debug del widget
├── debug-modal-data.php         ❌ Debug del modal
├── debug-modal.php              ❌ Debug general
├── ELEMENTOR-WIDGET-SOLUTION.md ❌ Solución widget
├── fix-portfolio-error.php      ❌ Corrección de errores
├── FIXES-SUMMARY.md             ❌ Resumen de correcciones
├── INSTALACION.md               ❌ Instrucciones de instalación
├── modal detail portfolio.jpg   ❌ Imagen de referencia
├── MODAL-FIX-GUIDE.md           ❌ Guía de solución modal
├── PACKAGE-FILES.md             ❌ Lista de archivos
├── portfolio list.jpg           ❌ Imagen de referencia
├── portfolio-plugin.zip         ❌ ZIP anterior
├── PROJECT-STATUS.md            ❌ Estado del proyecto
├── test-installation.php        ❌ Prueba de instalación
├── test-modal-data.php          ❌ Prueba de datos
└── UX-IMPROVEMENTS.md           ❌ Mejoras de UX
```

## 🚀 Instrucciones de Instalación

### Para Crear el ZIP de Instalación:
1. **Copia SOLO los archivos marcados con ✅**
2. **Excluye COMPLETAMENTE la carpeta `development-files/`**
3. **Crea un ZIP con la estructura del plugin**

### Comando para Crear ZIP (desde la raíz del proyecto):
```bash
# Crear ZIP solo con archivos necesarios
zip -r portfolio-plugin.zip . -x "development-files/*" "*.git*" "*.DS_Store"
```

### Estructura Final del ZIP:
```
portfolio-plugin.zip
└── portfolio-plugin/
    ├── portfolio-plugin.php
    ├── uninstall.php
    ├── config.php
    ├── README.md
    ├── admin/
    ├── assets/
    ├── includes/
    └── templates/
```

## 📋 Checklist de Verificación

Antes de crear el ZIP, verifica que:

- [ ] ✅ `portfolio-plugin.php` está incluido
- [ ] ✅ `uninstall.php` está incluido
- [ ] ✅ `config.php` está incluido
- [ ] ✅ `README.md` está incluido
- [ ] ✅ Carpeta `admin/` con 3 archivos PHP
- [ ] ✅ Carpeta `assets/` con CSS y JS
- [ ] ✅ Carpeta `includes/` con 6 clases PHP
- [ ] ✅ Carpeta `templates/` (puede estar vacía)
- [ ] ❌ Carpeta `development-files/` NO está incluida
- [ ] ❌ Archivos `.md` de desarrollo NO están incluidos
- [ ] ❌ Scripts de debug NO están incluidos
- [ ] ❌ Imágenes de referencia NO están incluidas

## 🎯 Archivos Críticos

### **OBLIGATORIOS** (el plugin no funcionará sin ellos):
- `portfolio-plugin.php` - Archivo principal
- `includes/class-database.php` - Gestión de base de datos
- `includes/class-admin.php` - Panel de administración
- `includes/class-frontend.php` - Frontend
- `includes/class-elementor-widget.php` - Widget de Elementor

### **RECOMENDADOS** (mejoran la funcionalidad):
- `uninstall.php` - Limpieza al desinstalar
- `assets/css/admin.css` - Estilos del admin
- `assets/css/frontend.css` - Estilos del frontend
- `assets/js/admin.js` - Funcionalidad del admin
- `assets/js/frontend.js` - Funcionalidad del frontend

### **OPCIONALES** (pueden estar vacíos):
- `templates/` - Para plantillas personalizadas
- `assets/images/` - Para imágenes del plugin
- `README.md` - Documentación

## ⚠️ Advertencias

1. **NO incluyas archivos de desarrollo** en el ZIP de instalación
2. **NO incluyas imágenes de referencia** (son solo para desarrollo)
3. **NO incluyas scripts de debug** (solo para desarrollo)
4. **NO incluyas documentación de desarrollo** (solo para desarrolladores)

## 📦 Tamaño del Plugin

- **Archivos necesarios**: ~50KB
- **Con assets**: ~200KB
- **ZIP final**: ~100KB (comprimido)

El plugin es ligero y optimizado para producción.
