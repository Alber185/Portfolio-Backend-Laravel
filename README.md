# Portfolio Backend — Laravel API REST

Backend API REST desarrollado con **Laravel 13** y **PostgreSQL** para gestionar un portfolio personal con proyectos y tecnologías.

## Características

- 🔐 **Autenticación** con Laravel Sanctum (token API)
- 📂 **CRUD completo** de proyectos (título, descripción, imagen, URLs, estado)
- 🏷️ **Tecnologías** relacionadas mediante tabla pivote (many-to-many)
- 🖼️ **Subida de imágenes** almacenadas en `storage/app/public/projects`
- 🌐 **CORS** configurado para frontend separado
- 🛡️ **Rutas protegidas** con middleware Sanctum

## Requisitos

- PHP ^8.3
- Composer
- PostgreSQL

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/Alber185/Portfolio-Backend-Laravel.git
cd Portfolio-Backend-Laravel

# 2. Instalar dependencias PHP
composer install

# 3. Copiar y configurar el archivo de entorno
cp .env.example .env

# 4. Generar la clave de la aplicación
php artisan key:generate

# 5. Configurar la base de datos en .env
#    DB_CONNECTION=pgsql
#    DB_HOST=127.0.0.1
#    DB_PORT=5432
#    DB_DATABASE=portfolio
#    DB_USERNAME=postgres
#    DB_PASSWORD=tu_contraseña

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Crear el enlace simbólico para el almacenamiento de imágenes
php artisan storage:link

# 8. Iniciar el servidor de desarrollo
php artisan serve
```

## Variables de Entorno

| Variable | Descripción | Ejemplo |
|---|---|---|
| `DB_HOST` | Host de PostgreSQL | `127.0.0.1` |
| `DB_PORT` | Puerto de PostgreSQL | `5432` |
| `DB_DATABASE` | Nombre de la base de datos | `portfolio` |
| `DB_USERNAME` | Usuario de PostgreSQL | `postgres` |
| `DB_PASSWORD` | Contraseña de PostgreSQL | `secret` |
| `ADMIN_NAME` | Nombre del usuario administrador | `Portfolio Owner` |
| `ADMIN_EMAIL` | Email del usuario administrador | `admin@portfolio.test` |
| `ADMIN_PASSWORD` | Contraseña inicial del administrador | `change_me` |
| `CORS_ALLOWED_ORIGINS` | Orígenes permitidos para CORS | `http://localhost:3000` |

## API Endpoints

### Públicos

| Método | Endpoint | Descripción |
|---|---|---|
| `GET` | `/api/projects` | Listar proyectos publicados |
| `GET` | `/api/projects/{id}` | Detalle de un proyecto |

### Privados (requieren `Authorization: ******

| Método | Endpoint | Descripción |
|---|---|---|
| `POST` | `/api/auth/login` | Iniciar sesión |
| `POST` | `/api/auth/logout` | Cerrar sesión |
| `GET` | `/api/auth/user` | Datos del usuario autenticado |
| `GET` | `/api/projects-admin` | Listar todos los proyectos (incluye borradores) |
| `POST` | `/api/projects` | Crear un proyecto |
| `PUT` | `/api/projects/{id}` | Actualizar un proyecto |
| `DELETE` | `/api/projects/{id}` | Eliminar un proyecto |

### Ejemplo: Login

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@portfolio.test",
  "password": "change_me"
}
```

Respuesta:
```json
{
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

### Ejemplo: Crear Proyecto

```http
POST /api/projects
Authorization: ******
Content-Type: multipart/form-data

title=Mi Proyecto
description=Descripción del proyecto
image=<archivo>
project_url=https://miproyecto.com
github_url=https://github.com/usuario/repo
status=published
technologies[]=1
technologies[]=3
```

### Respuesta de Proyecto

```json
{
  "data": {
    "id": 1,
    "title": "Mi Proyecto",
    "description": "Descripción del proyecto",
    "image_url": "http://localhost:8000/storage/projects/imagen.jpg",
    "project_url": "https://miproyecto.com",
    "github_url": "https://github.com/usuario/repo",
    "status": "published",
    "technologies": [
      { "id": 1, "name": "PHP", "icon": "php" },
      { "id": 3, "name": "JavaScript", "icon": "javascript" }
    ],
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

## Estructura del Proyecto

```
app/
  Http/
    Controllers/
      AuthController.php       # Login, logout, user
      ProjectController.php    # CRUD proyectos
    Requests/
      LoginRequest.php
      StoreProjectRequest.php
      UpdateProjectRequest.php
    Resources/
      ProjectResource.php      # Transformación JSON
  Models/
    User.php
    Project.php
    Technology.php
database/
  migrations/
    *_create_users_table.php
    *_create_technologies_table.php
    *_create_projects_table.php
    *_create_project_technology_table.php
    *_create_personal_access_tokens_table.php
  seeders/
    DatabaseSeeder.php
    UserSeeder.php
    TechnologySeeder.php
routes/
  api.php
config/
  sanctum.php
  cors.php
```

## Licencia

MIT
