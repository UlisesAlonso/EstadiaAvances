# 📚 Inventario de Librerías del Sistema Cardiovascular

## 📋 Resumen Ejecutivo

Este documento detalla todas las librerías y dependencias utilizadas en el sistema, su propósito y ubicación de uso.

---

## 🔵 DEPENDENCIAS DE PRODUCCIÓN (PHP - Composer)

### 1. **PHP ^8.0.2**
- **Propósito**: Versión del lenguaje de programación PHP requerida
- **Uso**: Base del sistema, todas las funcionalidades del backend
- **Ubicación**: Todo el sistema

### 2. **guzzlehttp/guzzle ^7.2**
- **Propósito**: Cliente HTTP para realizar peticiones HTTP a APIs externas
- **Uso**: 
  - Utilizado por Laravel internamente para peticiones HTTP
  - Configurado en `config/broadcasting.php` para opciones de cliente HTTP
  - Laravel lo usa automáticamente cuando se utiliza `Http::get()`, `Http::post()`, etc.
- **Estado**: ✅ Instalada pero no se usa directamente en el código del proyecto
- **Ubicación**: 
  - `composer.json` (línea 9)
  - `config/broadcasting.php` (comentario sobre opciones de Guzzle)

### 3. **laravel/framework ^9.19**
- **Propósito**: Framework PHP principal del sistema
- **Uso**: 
  - Base de toda la aplicación
  - Routing, Middleware, Eloquent ORM, Blade Templates, etc.
- **Ubicación**: Todo el sistema
- **Componentes utilizados**:
  - `Illuminate\Http\Request` - En todos los controladores
  - `Illuminate\Support\Facades\Auth` - Autenticación
  - `Illuminate\Support\Facades\DB` - Consultas a base de datos
  - `Illuminate\Support\Facades\Hash` - Encriptación de contraseñas
  - `Illuminate\Support\Facades\Mail` - Envío de correos
  - `Illuminate\Support\Facades\Storage` - Manejo de archivos
  - `Illuminate\Database\Eloquent\Model` - Todos los modelos

### 4. **laravel/sanctum ^3.0**
- **Propósito**: Sistema de autenticación ligero para SPAs (Single Page Applications) y APIs simples
- **Uso**: 
  - Configurado para autenticación de API
  - Trait `HasApiTokens` en el modelo `User`
  - Ruta API protegida con middleware `auth:sanctum`
- **Estado**: ✅ Instalada y configurada, pero actualmente no se usa activamente (el sistema usa autenticación web tradicional)
- **Ubicación**:
  - `composer.json` (línea 11)
  - `app/Models/User.php` (línea 8, 14) - Trait `HasApiTokens`
  - `config/sanctum.php` - Configuración completa
  - `routes/api.php` (línea 17) - Ruta de ejemplo con middleware `auth:sanctum`
  - `app/Http/Kernel.php` (línea 42) - Middleware comentado

### 5. **laravel/tinker ^2.7**
- **Propósito**: REPL (Read-Eval-Print Loop) interactivo para Laravel
- **Uso**: Herramienta de desarrollo para interactuar con la aplicación desde la línea de comandos
- **Estado**: ✅ Instalada, disponible para uso en desarrollo
- **Ubicación**: 
  - `composer.json` (línea 12)
  - Uso: `php artisan tinker`

---

## 🟡 DEPENDENCIAS DE DESARROLLO (PHP - Composer)

### 6. **fakerphp/faker ^1.9.1**
- **Propósito**: Generador de datos falsos para testing y seeders
- **Uso**: Crear datos de prueba para la base de datos
- **Estado**: ✅ Instalada, disponible para uso en seeders y tests
- **Ubicación**: `composer.json` (línea 15)

### 7. **laravel/pint ^1.0**
- **Propósito**: Linter y formateador de código PHP
- **Uso**: Mantener consistencia en el estilo de código
- **Estado**: ✅ Instalada, disponible para formatear código
- **Ubicación**: `composer.json` (línea 16)

### 8. **laravel/sail ^1.0.1**
- **Propósito**: Entorno de desarrollo Docker para Laravel
- **Uso**: Crear contenedores Docker para desarrollo
- **Estado**: ✅ Instalada, disponible para uso con Docker
- **Ubicación**: `composer.json` (línea 17)

### 9. **mockery/mockery ^1.4.4**
- **Propósito**: Framework de mocking para PHPUnit
- **Uso**: Crear objetos mock en tests unitarios
- **Estado**: ✅ Instalada, disponible para tests
- **Ubicación**: `composer.json` (línea 18)

### 10. **nunomaduro/collision ^6.1**
- **Propósito**: Manejo mejorado de errores y excepciones en desarrollo
- **Uso**: Mostrar errores de forma más clara en la consola
- **Estado**: ✅ Instalada, activa en desarrollo
- **Ubicación**: `composer.json` (línea 19)

### 11. **phpunit/phpunit ^9.5.10**
- **Propósito**: Framework de testing para PHP
- **Uso**: Ejecutar tests unitarios y de integración
- **Estado**: ✅ Instalada, disponible para tests
- **Ubicación**: `composer.json` (línea 20)

### 12. **spatie/laravel-ignition ^1.0**
- **Propósito**: Página de errores mejorada para Laravel
- **Uso**: Mostrar errores de forma más clara y útil en desarrollo
- **Estado**: ✅ Instalada, activa en desarrollo
- **Ubicación**: `composer.json` (línea 21)

---

## 🟢 DEPENDENCIAS DE DESARROLLO (JavaScript/Node - NPM)

### 13. **@tailwindcss/forms ^0.5.7**
- **Propósito**: Plugin de Tailwind CSS para estilizar formularios
- **Uso**: Estilizar automáticamente elementos de formulario (inputs, selects, textareas)
- **Estado**: ✅ Instalada y activa
- **Ubicación**:
  - `package.json` (línea 9)
  - `tailwind.config.js` (línea 30) - Registrado como plugin
  - Usado en todas las vistas Blade con formularios

### 14. **@tailwindcss/typography ^0.5.10**
- **Propósito**: Plugin de Tailwind CSS para estilizar contenido tipográfico
- **Uso**: Estilizar bloques de texto, artículos, contenido markdown
- **Estado**: ✅ Instalada y activa
- **Ubicación**:
  - `package.json` (línea 10)
  - `tailwind.config.js` (línea 31) - Registrado como plugin
  - Usado en vistas de reportes y contenido largo

### 15. **autoprefixer ^10.4.16**
- **Propósito**: Agregar prefijos de navegadores automáticamente a CSS
- **Uso**: Compatibilidad cross-browser para propiedades CSS modernas
- **Estado**: ✅ Instalada y activa en el proceso de build
- **Ubicación**:
  - `package.json` (línea 11)
  - Usado automáticamente por Vite durante el build

### 16. **axios ^1.6.1**
- **Propósito**: Cliente HTTP basado en promesas para JavaScript
- **Uso**: 
  - Realizar peticiones AJAX al backend
  - Enviar datos sin recargar la página
  - Configurado globalmente en `window.axios`
- **Estado**: ✅ Instalada y configurada, pero **NO se usa activamente** en el código actual
- **Ubicación**:
  - `package.json` (línea 12)
  - `resources/js/bootstrap.js` (líneas 10-13) - Importado y configurado globalmente
  - Disponible como `window.axios` en todas las vistas

### 17. **laravel-vite-plugin ^1.0.0**
- **Propósito**: Plugin de Vite para Laravel
- **Uso**: Integración entre Vite y Laravel para compilar assets
- **Estado**: ✅ Instalada y activa
- **Ubicación**:
  - `package.json` (línea 13)
  - `vite.config.js` (línea 2, 6) - Configurado en Vite
  - Usado en todas las vistas Blade con `@vite(['resources/css/app.css', 'resources/js/app.js'])`

### 18. **postcss ^8.4.32**
- **Propósito**: Herramienta para transformar CSS con plugins
- **Uso**: Procesar CSS (Tailwind, Autoprefixer) durante el build
- **Estado**: ✅ Instalada y activa en el proceso de build
- **Ubicación**:
  - `package.json` (línea 14)
  - Usado automáticamente por Vite durante el build

### 19. **tailwindcss ^3.4.0**
- **Propósito**: Framework CSS utility-first
- **Uso**: 
  - Estilizar toda la interfaz de usuario
  - Clases utilitarias para diseño responsive
  - Sistema de diseño consistente
- **Estado**: ✅ Instalada y activa, **USO PRINCIPAL DEL SISTEMA**
- **Ubicación**:
  - `package.json` (línea 15)
  - `tailwind.config.js` - Configuración completa
  - `resources/css/app.css` (líneas 1-3) - Directivas de Tailwind
  - **Todas las vistas Blade** usan clases de Tailwind CSS

### 20. **vite ^5.0.0**
- **Propósito**: Build tool y dev server moderno
- **Uso**: 
  - Compilar y optimizar assets (CSS, JS)
  - Servidor de desarrollo con Hot Module Replacement (HMR)
  - Build de producción optimizado
- **Estado**: ✅ Instalada y activa
- **Ubicación**:
  - `package.json` (línea 16)
  - `vite.config.js` - Configuración completa
  - Comandos: `npm run dev` (desarrollo), `npm run build` (producción)

---

## 🟣 DEPENDENCIAS DE PRODUCCIÓN (JavaScript/Node - NPM)

### 21. **lodash ^4.17.21**
- **Propósito**: Biblioteca de utilidades JavaScript
- **Uso**: 
  - Funciones helper para manipulación de arrays, objetos, strings, etc.
  - Configurado globalmente como `window._`
- **Estado**: ✅ Instalada y configurada, pero **NO se usa activamente** en el código actual
- **Ubicación**:
  - `package.json` (línea 19)
  - `resources/js/bootstrap.js` (línea 1-2) - Importado y configurado globalmente
  - Disponible como `window._` en todas las vistas

---

## 📊 Resumen de Uso

### Librerías Activamente Utilizadas ✅
1. **Laravel Framework** - Base del sistema
2. **Tailwind CSS** - Estilos de toda la interfaz
3. **@tailwindcss/forms** - Estilos de formularios
4. **@tailwindcss/typography** - Estilos de contenido
5. **Vite** - Build tool
6. **laravel-vite-plugin** - Integración Vite-Laravel
7. **PostCSS** - Procesamiento de CSS
8. **Autoprefixer** - Compatibilidad CSS

### Librerías Instaladas pero No Utilizadas ⚠️
1. **Guzzle HTTP** - No se usa directamente (Laravel lo usa internamente)
2. **Laravel Sanctum** - Configurado pero no se usa (sistema usa autenticación web)
3. **Axios** - Configurado pero no se usa en el código actual
4. **Lodash** - Configurado pero no se usa en el código actual

### Librerías de Desarrollo/Testing 🛠️
- Todas las librerías de desarrollo están disponibles pero su uso depende de las necesidades del proyecto

---

## 🔍 Detalles de Uso por Componente

### Frontend (Vistas Blade)
- **Tailwind CSS**: Todas las vistas usan clases de Tailwind
  - `resources/views/**/*.blade.php`
  - Ejemplos: `layouts/app.blade.php`, `historial-clinico/*.blade.php`, `auth/login.blade.php`

### Backend (Controladores)
- **Laravel Framework**: Todos los controladores
  - `app/Http/Controllers/*.php`
  - Uso de `Request`, `Auth`, `DB`, `Storage`, etc.

### Modelos
- **Laravel Eloquent**: Todos los modelos
  - `app/Models/*.php`
  - Relaciones, scopes, mutators, accessors

### Middleware
- **Laravel Middleware**: Sistema de middleware personalizado
  - `app/Http/Middleware/*.php`
  - Autenticación, roles, timeouts, etc.

### Assets (CSS/JS)
- **Vite**: Compilación de assets
  - `resources/css/app.css` - Estilos principales con Tailwind
  - `resources/js/app.js` - JavaScript principal
  - `resources/js/bootstrap.js` - Configuración de Axios y Lodash

---

## 💡 Recomendaciones

1. **Librerías No Utilizadas**: Considerar eliminar o documentar el uso futuro de:
   - Axios (si no se planea usar AJAX)
   - Lodash (si no se necesita manipulación avanzada de datos)
   - Sanctum (si no se planea crear una API)

2. **Optimización**: Revisar si todas las dependencias de desarrollo son necesarias según el flujo de trabajo del equipo.

3. **Actualizaciones**: Revisar periódicamente las versiones de las librerías para mantener seguridad y compatibilidad.

---

**Última actualización**: Noviembre 2025
**Versión del sistema**: Laravel 9.19

