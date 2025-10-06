# Instrucciones de Instalación - Plugin Portfolio

## ⚠️ Solución de Errores Fatales

Si el plugin genera un error fatal al instalar, sigue estos pasos:

### Error: "Call to private PortfolioDatabase::__construct()"
**Solución Rápida**:
1. Descarga el archivo `fix-portfolio-error.php` del repositorio
2. Súbelo a la raíz de tu sitio WordPress
3. Ejecuta: `tu-sitio.com/fix-portfolio-error.php`
4. Elimina el archivo después de ejecutarlo

**Solución Manual**:
1. Edita el archivo `/wp-content/plugins/portfolio-plugin-1/portfolio-plugin.php`
2. Busca la línea: `new PortfolioDatabase();`
3. Cámbiala por: `PortfolioDatabase::get_instance();`
4. Guarda el archivo

### 1. Verificar Requisitos del Sistema
- **WordPress**: 5.0 o superior
- **PHP**: 7.4 o superior
- **MySQL**: 5.6 o superior

### 2. Instalación Paso a Paso

#### Opción A: Instalación Manual (Recomendada)
1. **Descargar el plugin completo**
2. **Subir la carpeta `portfolio-plugin` a `/wp-content/plugins/`**
3. **Verificar permisos de archivos** (644 para archivos, 755 para directorios)
4. **Activar desde el panel de administración**

#### Opción B: Instalación via ZIP
1. **Comprimir la carpeta del plugin en un archivo ZIP**
2. **Ir a Plugins > Añadir nuevo**
3. **Subir el archivo ZIP**
4. **Activar el plugin**

### 3. Verificación Post-Instalación

Después de activar el plugin, ejecuta el archivo de prueba:

1. **Accede a**: `tu-sitio.com/wp-content/plugins/portfolio-plugin/test-installation.php`
2. **Verifica que aparezca el mensaje de éxito**
3. **Elimina el archivo `test-installation.php` después de la verificación**

### 4. Configuración Inicial

1. **Ve a Portfolio > Categorías** y crea algunas categorías
2. **Ve a Portfolio > Proyectos** y agrega algunos proyectos de prueba
3. **Edita una página con Elementor** y busca el widget "Portfolio Grid"

## 🔧 Solución de Problemas Comunes

### Error: "Fatal error: Class 'PortfolioDatabase' not found"
**Solución**: Verifica que todos los archivos estén en su lugar y con permisos correctos.

### Error: "Fatal error: Call to undefined function wp_die()"
**Solución**: Asegúrate de que WordPress esté completamente cargado.

### Error: "Table 'wp_portfolio_projects' doesn't exist"
**Solución**: Desactiva y reactiva el plugin para recrear las tablas.

### Error: "Permission denied"
**Solución**: Verifica los permisos de archivos y directorios:
```bash
chmod 644 *.php
chmod 755 includes/ admin/ assets/
```

## 📋 Checklist de Instalación

- [ ] WordPress 5.0+ instalado
- [ ] PHP 7.4+ configurado
- [ ] Archivos del plugin subidos correctamente
- [ ] Permisos de archivos configurados
- [ ] Plugin activado sin errores
- [ ] Tablas de base de datos creadas
- [ ] Archivo de prueba ejecutado exitosamente
- [ ] Categorías creadas
- [ ] Proyectos agregados
- [ ] Widget de Elementor funcionando

## 🆘 Soporte Adicional

Si sigues teniendo problemas:

1. **Revisa los logs de error de WordPress** en `/wp-content/debug.log`
2. **Activa el modo debug** en `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
3. **Verifica la compatibilidad** con otros plugins activos
4. **Contacta al soporte** con los detalles del error

## 📞 Información de Debug

Para obtener información de debug, agrega esto a tu `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Esto creará un archivo de log en `/wp-content/debug.log` con información detallada sobre cualquier error.
