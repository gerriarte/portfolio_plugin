# 🔍 Guía de Diagnóstico - Modal No Muestra Información

## 📋 Pasos para Identificar el Problema

### **Paso 1: Verificar la Consola del Navegador**

1. **Abre tu sitio WordPress** donde está el widget Portfolio Grid
2. **Abre las herramientas de desarrollador** (F12)
3. **Ve a la pestaña "Console"**
4. **Busca estos mensajes al cargar la página**:
   ```
   🚀 Portfolio Plugin: Iniciando versión robusta...
   📋 DOM listo, inicializando plugin...
   🔧 Inicializando plugin portfolio...
   🔨 Creando modal robusto...
   ✅ Modal robusto creado
   🔗 Bindando eventos robustos...
   ✅ Eventos robustos bindeados
   🔍 Verificando botones existentes...
   📊 Botones encontrados: X
   ✅ Plugin portfolio inicializado correctamente
   🎉 Portfolio Plugin: Versión robusta cargada correctamente
   ```

5. **Si NO ves estos mensajes**, el problema es que:
   - ❌ El JavaScript no se está cargando
   - ❌ Hay un error de sintaxis
   - ❌ Hay un conflicto con otro plugin

### **Paso 2: Hacer Click en "Ver Detalles"**

1. **Haz clic en el botón "Ver Detalles"** de un proyecto
2. **Observa la consola**, deberías ver:
   ```
   🖱️ Click en botón de proyecto detectado
   📋 Project ID: X
   🚪 Abriendo modal robusto para proyecto: X
   📤 Enviando petición AJAX...
   🔗 URL: https://tudominio.com/wp-admin/admin-ajax.php
   🔑 Nonce: xxxxx
   ✅ Respuesta AJAX recibida: {success: true, data: {...}}
   📋 Datos del proyecto: {id: X, title: "...", ...}
   📝 Poblando modal robusto con datos: {...}
   🖼️ Imagen destacada cargada: ...
   ✅ Modal robusto poblado correctamente
   ```

3. **Si NO ves estos mensajes**, anota qué es lo que sí ves

### **Paso 3: Verificar Elementos en el DOM**

1. **En las herramientas de desarrollador**, ve a la pestaña **"Elements"** o **"Inspector"**
2. **Presiona Ctrl+F** para buscar
3. **Busca**: `portfolio-robust-modal`
4. **¿Lo encontraste?**
   - ✅ **SÍ**: El modal existe en el DOM
   - ❌ **NO**: El modal no se está creando

### **Paso 4: Verificar el Objeto portfolio_frontend**

1. **En la consola del navegador**, escribe:
   ```javascript
   console.log(portfolio_frontend);
   ```

2. **Deberías ver**:
   ```javascript
   {
     ajax_url: "https://tudominio.com/wp-admin/admin-ajax.php",
     nonce: "xxxxxxxxxx",
     strings: {...}
   }
   ```

3. **Si ves "undefined"**, el problema es que:
   - ❌ El script no se está encolando correctamente
   - ❌ Hay un conflicto con el orden de carga

### **Paso 5: Probar AJAX Manualmente**

1. **En la consola del navegador**, copia y pega:
   ```javascript
   jQuery.ajax({
       url: portfolio_frontend.ajax_url,
       type: 'POST',
       data: {
           action: 'portfolio_get_project',
           project_id: 1, // Cambia esto por un ID real
           nonce: portfolio_frontend.nonce
       },
       success: function(response) {
           console.log('Respuesta:', response);
       },
       error: function(xhr, status, error) {
           console.log('Error:', error, xhr.responseText);
       }
   });
   ```

2. **¿Qué respuesta obtuviste?**
   - ✅ **`{success: true, data: {...}}`**: El AJAX funciona
   - ❌ **Error o success: false**: Hay un problema con el endpoint

### **Paso 6: Verificar Botones**

1. **En la consola del navegador**, escribe:
   ```javascript
   console.log('Botones:', jQuery('.portfolio-view-btn').length);
   jQuery('.portfolio-view-btn').each(function() {
       console.log('ID:', jQuery(this).data('project-id'));
   });
   ```

2. **¿Cuántos botones se encontraron?**
   - ✅ **> 0**: Los botones existen
   - ❌ **0**: Los botones no tienen la clase correcta

## 🎯 Escenarios Comunes y Soluciones

### **Escenario 1: No se ve ningún mensaje en la consola**
**Problema**: El JavaScript no se está cargando
**Solución**:
1. Verifica que el archivo existe: `wp-content/plugins/portfolio-plugin/assets/js/frontend.js`
2. Limpia la caché del navegador (Ctrl+F5)
3. Limpia la caché de WordPress si usas un plugin de caché
4. Verifica que no haya errores de sintaxis en el archivo

### **Escenario 2: Se ve "portfolio_frontend is not defined"**
**Problema**: El script no se está localizando correctamente
**Solución**:
1. Verifica que el plugin está activo
2. Desactiva otros plugins temporalmente para descartar conflictos
3. Verifica que el hook `wp_enqueue_scripts` se está ejecutando

### **Escenario 3: El modal se abre pero está vacío**
**Problema**: El AJAX funciona pero no se están poblando los datos
**Solución**:
1. Verifica que el modal tiene los IDs correctos
2. Verifica que la función `populateRobustModal` se está ejecutando
3. Revisa si hay errores de JavaScript al poblar

### **Escenario 4: El AJAX devuelve error**
**Problema**: El endpoint no funciona o el nonce es inválido
**Solución**:
1. Verifica que el nonce es válido
2. Verifica que el proyecto existe en la base de datos
3. Revisa los logs de WordPress para errores PHP

### **Escenario 5: El botón no responde al click**
**Problema**: Los eventos no están bindeados correctamente
**Solución**:
1. Verifica que jQuery está cargado
2. Verifica que el evento está delegado en document
3. Verifica que no hay conflictos con otros scripts

## 📧 Información que Necesito

Para ayudarte mejor, por favor proporciona:

1. **¿Qué mensajes ves en la consola?** (copia y pega)
2. **¿El modal se abre o no?** (incluso si está vacío)
3. **¿Qué respuesta obtienes al probar AJAX manualmente?** (Paso 5)
4. **¿Cuántos botones se encuentran?** (Paso 6)
5. **¿Hay algún error en la consola?** (cualquier mensaje en rojo)

## 🔧 Script de Diagnóstico Automático

Copia y pega este código en la consola de tu navegador:

```javascript
(function() {
    console.log('=== DIAGNÓSTICO AUTOMÁTICO PORTFOLIO MODAL ===');
    
    // Test 1: jQuery
    console.log('1. jQuery:', typeof jQuery !== 'undefined' ? '✅ Disponible (v' + jQuery.fn.jquery + ')' : '❌ NO disponible');
    
    // Test 2: portfolio_frontend
    console.log('2. portfolio_frontend:', typeof portfolio_frontend !== 'undefined' ? '✅ Disponible' : '❌ NO disponible');
    if (typeof portfolio_frontend !== 'undefined') {
        console.log('   - AJAX URL:', portfolio_frontend.ajax_url);
        console.log('   - Nonce:', portfolio_frontend.nonce);
    }
    
    // Test 3: Botones
    const buttons = jQuery('.portfolio-view-btn');
    console.log('3. Botones encontrados:', buttons.length);
    if (buttons.length > 0) {
        buttons.each(function(i) {
            console.log('   - Botón', (i+1), ':', jQuery(this).data('project-id'));
        });
    }
    
    // Test 4: Modal
    const modal = jQuery('#portfolio-robust-modal');
    console.log('4. Modal en DOM:', modal.length > 0 ? '✅ Sí' : '❌ No');
    if (modal.length > 0) {
        console.log('   - Visible:', modal.is(':visible') ? 'Sí' : 'No');
    }
    
    // Test 5: Funciones disponibles
    console.log('5. Funciones disponibles:');
    console.log('   - closeRobustModal:', typeof closeRobustModal !== 'undefined' ? '✅' : '❌');
    console.log('   - debugRobustModal:', typeof debugRobustModal !== 'undefined' ? '✅' : '❌');
    
    console.log('=== FIN DIAGNÓSTICO ===');
    console.log('Copia este resultado y compártelo para obtener ayuda');
})();
```

Este script te dará un resumen completo del estado del plugin.
