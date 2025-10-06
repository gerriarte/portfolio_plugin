# Script PowerShell para crear ZIP de instalación del Plugin Portfolio
# Ejecutar desde la raíz del proyecto

Write-Host "🚀 Creando ZIP de instalación del Plugin Portfolio..." -ForegroundColor Green
Write-Host ""

# Verificar que estamos en el directorio correcto
if (-not (Test-Path "portfolio-plugin.php")) {
    Write-Host "❌ Error: No se encontró portfolio-plugin.php" -ForegroundColor Red
    Write-Host "   Ejecuta este script desde la raíz del proyecto" -ForegroundColor Yellow
    exit 1
}

# Archivos y carpetas que SÍ se deben incluir
$includeItems = @(
    "portfolio-plugin.php",
    "uninstall.php", 
    "config.php",
    "README.md",
    "admin",
    "assets",
    "includes",
    "templates"
)

# Crear directorio temporal
$tempDir = "temp-package"
if (Test-Path $tempDir) {
    Write-Host "🧹 Limpiando directorio temporal..." -ForegroundColor Yellow
    Remove-Item -Recurse -Force $tempDir
}

Write-Host "📁 Creando directorio temporal..." -ForegroundColor Cyan
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Copiar archivos necesarios
Write-Host "📋 Copiando archivos necesarios..." -ForegroundColor Cyan
foreach ($item in $includeItems) {
    if (Test-Path $item) {
        if (Test-Path $item -PathType Container) {
            Copy-Item -Recurse -Path $item -Destination "$tempDir/$item"
            Write-Host "   ✅ Carpeta: $item" -ForegroundColor Green
        } else {
            Copy-Item -Path $item -Destination "$tempDir/$item"
            Write-Host "   ✅ Archivo: $item" -ForegroundColor Green
        }
    } else {
        Write-Host "   ⚠️  No encontrado: $item" -ForegroundColor Yellow
    }
}

# Verificar estructura
Write-Host "`n🔍 Verificando estructura del paquete..." -ForegroundColor Cyan
$requiredFiles = @(
    "portfolio-plugin.php",
    "uninstall.php",
    "config.php",
    "admin/categories.php",
    "admin/projects.php", 
    "admin/settings.php",
    "assets/css/admin.css",
    "assets/css/frontend.css",
    "assets/js/admin.js",
    "assets/js/frontend.js",
    "includes/class-admin.php",
    "includes/class-api.php",
    "includes/class-database.php",
    "includes/class-elementor-widget.php",
    "includes/class-frontend.php",
    "includes/class-logger.php"
)

$missingFiles = @()
foreach ($file in $requiredFiles) {
    if (-not (Test-Path "$tempDir/$file")) {
        $missingFiles += $file
    }
}

if ($missingFiles.Count -gt 0) {
    Write-Host "❌ Archivos faltantes:" -ForegroundColor Red
    foreach ($file in $missingFiles) {
        Write-Host "   - $file" -ForegroundColor Red
    }
    Write-Host "`n⚠️  El paquete puede estar incompleto" -ForegroundColor Yellow
} else {
    Write-Host "✅ Todos los archivos necesarios están presentes" -ForegroundColor Green
}

# Crear ZIP
$zipFilename = "portfolio-plugin-install.zip"
if (Test-Path $zipFilename) {
    Remove-Item $zipFilename
}

Write-Host "`n📦 Creando archivo ZIP..." -ForegroundColor Cyan

try {
    # Usar Compress-Archive (PowerShell 5.0+)
    Compress-Archive -Path "$tempDir/*" -DestinationPath $zipFilename -Force
    Write-Host "✅ ZIP creado exitosamente: $zipFilename" -ForegroundColor Green
} catch {
    Write-Host "❌ Error al crear el ZIP: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "   Intenta usar el Método A (navegador) o Método C (XAMPP)" -ForegroundColor Yellow
    exit 1
}

# Limpiar directorio temporal
Write-Host "`n🧹 Limpiando archivos temporales..." -ForegroundColor Yellow
Remove-Item -Recurse -Force $tempDir

# Mostrar información del ZIP
if (Test-Path $zipFilename) {
    $size = (Get-Item $zipFilename).Length
    $sizeMB = [math]::Round($size / 1MB, 2)
    Write-Host "📊 Tamaño del ZIP: $sizeMB MB ($size bytes)" -ForegroundColor Cyan
    
    Write-Host "`n🎉 ¡Paquete de instalación creado exitosamente!" -ForegroundColor Green
    Write-Host "📁 Archivo: $zipFilename" -ForegroundColor Cyan
    Write-Host "🚀 Listo para instalar en WordPress" -ForegroundColor Green
    Write-Host ""
    
    Write-Host "📋 Instrucciones de instalación:" -ForegroundColor Cyan
    Write-Host "1. Ve a WordPress Admin > Plugins > Añadir nuevo" -ForegroundColor White
    Write-Host "2. Haz clic en 'Subir plugin'" -ForegroundColor White
    Write-Host "3. Selecciona el archivo: $zipFilename" -ForegroundColor White
    Write-Host "4. Haz clic en 'Instalar ahora'" -ForegroundColor White
    Write-Host "5. Activa el plugin" -ForegroundColor White
    Write-Host "6. Ve a Portfolio en el menú de administración" -ForegroundColor White
    Write-Host ""
    
} else {
    Write-Host "❌ Error: No se pudo crear el archivo ZIP" -ForegroundColor Red
    exit 1
}

Write-Host "Presiona cualquier tecla para continuar..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
