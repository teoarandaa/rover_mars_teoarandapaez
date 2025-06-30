# 🚀 Mars Rover Control System

Sistema de control remoto para vehículos exploradores en Marte desarrollado con Laravel 11.

## 📋 Descripción

Este software traduce los comandos enviados desde la Tierra a instrucciones que son comprendidas por el rover. El sistema simula un planeta cuadrado de 200x200 unidades con detección de obstáculos y almacena un historial completo de todos los movimientos realizados.

## 🎯 Características

- ✅ Control de movimiento del rover (adelante, izquierda, derecha)
- ✅ Sistema de coordenadas 200x200 con bucle del mundo
- ✅ Detección de obstáculos antes de cada movimiento
- ✅ API REST para ejecutar comandos
- ✅ Interfaz web para pruebas visuales
- ✅ Base de datos MySQL con historial de movimientos
- ✅ Validación de datos de entrada
- ✅ Respuestas JSON estructuradas
- ✅ Entorno Docker completo con phpMyAdmin

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

3. **Ejecutar migraciones**
```bash
docker compose exec app php artisan migrate
```

4. **Acceder a la aplicación**
- **API y Web**: http://localhost:8000
- **Interfaz de pruebas**: http://localhost:8000/rover-ui
- **phpMyAdmin**: http://localhost:8080 (usuario: `root`, contraseña: `secret`)

### Opción 2: Instalación local

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd rover_mars_teoarandapaez
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar variables de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos**
```bash
# Editar .env con tus credenciales de MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

5. **Ejecutar migraciones**
```bash
php artisan migrate
```

6. **Ejecutar el servidor**
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
  "commands": "FFRFFFRL",    // Secuencia de comandos
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

## 🌐 Interfaz Web

### Acceso a la interfaz de pruebas
Visita `http://localhost:8000/rover-ui` para acceder a una interfaz web intuitiva que te permite:

- Configurar posición inicial del rover
- Seleccionar dirección inicial
- Ingresar secuencias de comandos
- Definir obstáculos opcionales
- Ver resultados en tiempo real
- Historial visual de movimientos

## 📊 Base de Datos y Historial

### Estructura de la base de datos
El sistema almacena automáticamente cada comando ejecutado en la tabla `movement_histories`:

```sql
- id: Identificador único
- x, y: Posición inicial
- direction: Dirección inicial
- commands: Secuencia de comandos ejecutada
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

## 🏗️ Estructura del proyecto

```
app/
├── Models/
│   ├── Rover.php                    # Modelo principal del rover
│   └── MovementHistory.php          # Modelo para historial de movimientos
└── Http/Controllers/
    └── RoverController.php          # Controlador de la API

routes/
├── api.php                          # Rutas de la API
└── web.php                          # Rutas web (incluye interfaz)

resources/views/
└── rover.blade.php                  # Interfaz web de pruebas

database/migrations/
└── 2024_01_01_000003_create_movement_histories_table.php

docker-compose.yml                   # Configuración Docker
dockerfile                          # Imagen de la aplicación
```

## 🔧 Funcionalidades técnicas

### Modelo Rover
- **Movimiento**: Calcula la siguiente posición basada en la dirección actual
- **Rotación**: Gira 90° a la izquierda o derecha
- **Bucle del mundo**: Coordenadas circulares en un planeta 200x200
- **Detección de obstáculos**: Verifica colisiones antes de moverse

### Controlador
- **Validación**: Verifica formato y rangos de datos de entrada
- **Procesamiento**: Ejecuta comandos y retorna resultados
- **Persistencia**: Guarda automáticamente cada movimiento en la base de datos
- **Respuestas JSON**: Formato consistente para todas las respuestas

### Base de Datos
- **MySQL 8.0**: Base de datos principal
- **Historial automático**: Cada comando se registra con timestamp
- **phpMyAdmin**: Interfaz de administración incluida

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

## 📄 Licencia

Este proyecto está bajo la Licencia MIT.

## 👥 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

---

**Desarrollado con ❤️ para la exploración de Marte**
