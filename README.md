# 🚀 Mars Rover Control System

Sistema de control remoto para vehículos exploradores en Marte desarrollado con Laravel 11, Vue.js 3 y MySQL 8.0.

## 📋 Descripción

Este software traduce los comandos enviados desde la Tierra a instrucciones que son comprendidas por el rover. El sistema simula un planeta cuadrado de 200x200 unidades con detección de obstáculos y almacena un historial completo de todos los movimientos realizados. Incluye una interfaz web moderna desarrollada con Vue.js 3 y un sistema robusto de validaciones de seguridad.

## 🎯 Características

### 🚀 Funcionalidades Core
- ✅ Control de movimiento del rover (adelante, izquierda, derecha)
- ✅ Sistema de coordenadas 200x200 con bucle del mundo
- ✅ Detección de obstáculos antes de cada movimiento
- ✅ API REST para ejecutar comandos
- ✅ Base de datos MySQL con historial de movimientos
- ✅ Respuestas JSON estructuradas

### 🌐 Interfaz Web Moderna
- ✅ Interfaz desarrollada con Vue.js 3 (Composition API)
- ✅ Validaciones en tiempo real en el frontend
- ✅ Historial con scroll automático
- ✅ Diseño responsive con Bootstrap 5
- ✅ Feedback visual inmediato
- ✅ Tema visual inspirado en Marte

### 🔒 Seguridad y Validaciones
- ✅ Validaciones robustas en frontend y backend
- ✅ Prevención de inyecciones SQL y JavaScript
- ✅ Sanitización automática de inputs
- ✅ Tokens CSRF para protección
- ✅ Validación de rangos y formatos
- ✅ Mensajes de error personalizados

### 🐳 Entorno de Desarrollo
- ✅ Docker completo con phpMyAdmin
- ✅ Hot reload para desarrollo
- ✅ Vite para build de assets
- ✅ Tailwind CSS para estilos

## 🛠️ Instalación

### Opción 1: Con Docker (Recomendado)

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd rover_mars_teoarandapaez
```

2. **Levantar los contenedores**
```bash
docker compose up -d
```

3. **Instalar dependencias de Node.js**
```bash
docker compose exec app npm install
```

4. **Compilar assets**
```bash
docker compose exec app npm run build
```

5. **Ejecutar migraciones**
```bash
docker compose exec app php artisan migrate
```

6. **Acceder a la aplicación**
- **Levantar proyecto**:
  ```bash
  npm run build
  ```
- **API y Web**: http://localhost:8000
- **Interfaz de pruebas**: http://localhost:8000/rover-ui
- **phpMyAdmin**: http://localhost:8080 (usuario: `root`, contraseña: `secret`)

### Opción 2: Instalación local

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd rover_mars_teoarandapaez
```

2. **Instalar dependencias PHP**
```bash
composer install
```

3. **Instalar dependencias Node.js**
```bash
npm install
```

4. **Configurar variables de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurar base de datos**
```bash
# Editar .env con tus credenciales de MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

6. **Compilar assets**
```bash
npm run build
```

7. **Ejecutar migraciones**
```bash
php artisan migrate
```

8. **Ejecutar el servidor**
```bash
php artisan serve
```

## 🚀 Uso de la API

### Endpoint: `POST /api/rover/execute`

#### Parámetros de entrada:
```json
{
  "x": 0,                    // Posición inicial X (0-199)
  "y": 0,                    // Posición inicial Y (0-199)
  "direction": "N",          // Dirección inicial (N, E, S, W)
  "commands": "FFRFFFRL",    // Secuencia de comandos (máx. 100 caracteres)
  "obstacles": [[0,2],[2,3]] // Lista de obstáculos (opcional)
}
```

#### Comandos disponibles:
- `f` o `F`: Mover hacia adelante
- `l` o `L`: Girar a la izquierda
- `r` o `R`: Girar a la derecha

#### Respuestas:

**Sin obstáculo:**
```json
{
  "status": "ok",
  "x": 3,
  "y": 2
}
```

**Con obstáculo:**
```json
{
  "status": "obstacle",
  "x": 0,
  "y": 1
}
```

### Endpoint: `GET /api/rover/history`

Retorna los últimos 20 movimientos registrados:
```json
[
  {
    "id": 1,
    "x": 0,
    "y": 0,
    "direction": "N",
    "commands": "FFRFFFRL",
    "obstacles": [],
    "result_status": "ok",
    "result_x": 3,
    "result_y": 2,
    "created_at": "2024-01-01T12:00:00.000000Z"
  }
]
```

## 🌐 Interfaz Web

### Acceso a la interfaz de pruebas
Visita `http://localhost:8000/rover-ui` para acceder a una interfaz web moderna desarrollada con Vue.js que te permite:

#### 🎮 Funcionalidades de Control
- Configurar posición inicial del rover (X, Y: 0-199)
- Seleccionar dirección inicial (N, E, S, W)
- Ingresar secuencias de comandos (F, L, R)
- Definir obstáculos opcionales (formato: x1,y1;x2,y2)
- Ver resultados en tiempo real

#### 📊 Historial Inteligente
- Historial con scroll automático (máx. 400px altura)
- Título dinámico que muestra "Últimos X registros"
- Header fijo al hacer scroll
- Contador de registros en tiempo real
- Auto-carga después de cada comando exitoso

#### 🔒 Validaciones en Tiempo Real
- Validación inmediata de coordenadas (0-199)
- Sanitización automática de comandos (solo F, L, R)
- Validación de formato de obstáculos
- Feedback visual con campos rojos para errores
- Botón deshabilitado cuando hay errores

## 📊 Base de Datos y Historial

### Estructura de la base de datos
El sistema almacena automáticamente cada comando ejecutado en la tabla `movement_histories`:

```sql
- id: Identificador único
- x, y: Posición inicial (0-199)
- direction: Dirección inicial (N, E, S, W)
- commands: Secuencia de comandos ejecutada (máx. 100 caracteres)
- obstacles: Array JSON de obstáculos
- result_status: Estado final (ok/obstacle)
- result_x, result_y: Posición final
- created_at, updated_at: Timestamps
```

### Acceso a phpMyAdmin
- **URL**: http://localhost:8080
- **Usuario**: `root`
- **Contraseña**: `secret`
- **Base de datos**: `laravel`

## 📝 Ejemplos de uso

### Ejemplo 1: Movimiento simple
```bash
curl -X POST http://localhost:8000/api/rover/execute \
  -H "Content-Type: application/json" \
  -d '{
    "x": 0,
    "y": 0,
    "direction": "N",
    "commands": "FFRFFFRL"
  }'
```

### Ejemplo 2: Con obstáculos
```bash
curl -X POST http://localhost:8000/api/rover/execute \
  -H "Content-Type: application/json" \
  -d '{
    "x": 0,
    "y": 0,
    "direction": "N",
    "commands": "FFRFFFRL",
    "obstacles": [[0,2],[2,3]]
  }'
```

### Ejemplo 3: Bucle del mundo
```bash
curl -X POST http://localhost:8000/api/rover/execute \
  -H "Content-Type: application/json" \
  -d '{
    "x": 199,
    "y": 199,
    "direction": "E",
    "commands": "f"
  }'
```

### Ejemplo 4: Obtener historial
```bash
curl -X GET http://localhost:8000/api/rover/history \
  -H "Accept: application/json"
```

## 🏗️ Estructura del proyecto

```
app/
├── Models/
│   ├── Rover.php                    # Modelo principal del rover
│   ├── MovementHistory.php          # Modelo para historial de movimientos
│   └── User.php                     # Modelo de usuario (Laravel default)
└── Http/Controllers/
    ├── RoverController.php          # Controlador de la API con validaciones
    └── Controller.php               # Controlador base

routes/
├── api.php                          # Rutas de la API
└── web.php                          # Rutas web (incluye interfaz)

resources/
├── views/
│   └── rover.blade.php              # Interfaz web con Vue.js
├── js/
│   ├── app.js                       # Inicialización de Vue.js
│   └── components/
│       └── RoverControl.vue         # Componente principal de Vue
└── css/
    └── app.css                      # Estilos CSS

database/
├── migrations/
│   └── 2024_01_01_000003_create_movement_histories_table.php
└── seeders/
    └── DatabaseSeeder.php

tests/
├── Feature/
│   └── RoverTest.php                # Pruebas de funcionalidad
└── Unit/
    └── ExampleTest.php              # Pruebas unitarias

docker-compose.yml                   # Configuración Docker
dockerfile                          # Imagen de la aplicación
vite.config.js                      # Configuración de Vite + Vue
package.json                        # Dependencias Node.js
```

## 🔧 Funcionalidades técnicas

### 🚀 Modelo Rover
- **Movimiento**: Calcula la siguiente posición basada en la dirección actual
- **Rotación**: Gira 90° a la izquierda o derecha
- **Bucle del mundo**: Coordenadas circulares en un planeta 200x200
- **Detección de obstáculos**: Verifica colisiones antes de moverse

### 🎮 Controlador con Validaciones
- **Validación robusta**: Verifica formato, rangos y tipos de datos
- **Prevención de inyecciones**: Regex estricto para comandos
- **Mensajes personalizados**: Errores claros en español
- **Manejo de excepciones**: Try-catch para errores internos
- **Sanitización**: Conversión automática a mayúsculas

### 🌐 Frontend con Vue.js 3
- **Composition API**: Código más limpio y mantenible
- **Validaciones reactivas**: Errores en tiempo real
- **Estado reactivo**: Loading, errores, resultados
- **Componentes modulares**: Separación de responsabilidades
- **Event handling**: Submit, click, input events

### 🔒 Sistema de Seguridad
- **Validación frontend**: Prevención de datos maliciosos
- **Validación backend**: Doble verificación de seguridad
- **CSRF protection**: Tokens para prevenir ataques
- **Sanitización**: Limpieza automática de inputs
- **Rangos estrictos**: Límites en todos los campos

### 📊 Base de Datos
- **MySQL 8.0**: Base de datos principal
- **Historial automático**: Cada comando se registra con timestamp
- **phpMyAdmin**: Interfaz de administración incluida
- **Migraciones**: Estructura versionada de la BD

## 🐳 Docker

### Servicios incluidos:
- **app**: Aplicación Laravel (puerto 8000)
- **db**: MySQL 8.0 (puerto 3306)
- **phpmyadmin**: Interfaz de administración (puerto 8080)

### Comandos útiles:
```bash
# Levantar servicios
docker compose up -d

# Ver logs
docker compose logs -f

# Ejecutar comandos en el contenedor
docker compose exec app php artisan migrate
docker compose exec app composer install
docker compose exec app npm install
docker compose exec app npm run build

# Parar servicios
docker compose down
```

## 🧪 Testing

Para ejecutar las pruebas:
```bash
# Con Docker
docker compose exec app php artisan test

# Local
php artisan test
```

### Cobertura de pruebas:
- ✅ Movimiento básico (adelante, izquierda, derecha)
- ✅ Detección de obstáculos
- ✅ Bucle del mundo (coordenadas circulares)
- ✅ Validación de datos de entrada
- ✅ Secuencias complejas de comandos
- ✅ Comandos case-insensitive
- ✅ Validaciones de seguridad
- ✅ API endpoints

## 🚀 Desarrollo

### Comandos de desarrollo:
```bash
# Instalar dependencias
composer install
npm install

# Compilar assets en desarrollo
npm run dev

# Compilar assets para producción
npm run build

# Ejecutar migraciones
php artisan migrate

# Ejecutar pruebas
php artisan test
```

### Tecnologías utilizadas:
- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Vue.js 3, Bootstrap 5
- **Build Tool**: Vite
- **Base de datos**: MySQL 8.0
- **Docker**: Docker Compose
- **Testing**: PHPUnit
