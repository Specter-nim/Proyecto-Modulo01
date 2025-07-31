# Proyecto Django con Autenticación JWT

Este proyecto implementa un sistema de autenticación completo usando Django REST Framework y JWT (JSON Web Tokens).

## Características

- ✅ Modelo de usuario personalizado
- ✅ Autenticación con JWT
- ✅ API REST para registro, login y logout
- ✅ Gestión de perfiles de usuario
- ✅ Configuración completa de DRF
- ✅ Django Guardian para permisos a nivel de objeto
- ✅ Sistema de envío de emails con templates HTML

## Instalación

1. Clonar el repositorio
2. Crear un entorno virtual:
   ```bash
   python -m venv venv
   ```

3. Activar el entorno virtual:
   ```bash
   # Windows
   .\venv\Scripts\Activate.ps1
   
   # Linux/Mac
   source venv/bin/activate
   ```

4. Instalar dependencias:
   ```bash
   pip install -r requirements.txt
   ```

5. Ejecutar migraciones:
   ```bash
   python manage.py makemigrations
   python manage.py migrate
   ```

6. Crear superusuario:
   ```bash
   python manage.py createsuperuser
   ```

7. Configurar email (opcional):
   - Copia `email_config.py` y renómbralo como `email_settings.py`
   - Actualiza las credenciales de email
   - Para desarrollo, puedes usar la configuración de consola

8. Ejecutar el servidor:
   ```bash
   python manage.py runserver
   ```

## Endpoints de la API

### Autenticación

- `POST /api/auth/register/` - Registro de usuarios
- `POST /api/auth/login/` - Login de usuarios
- `POST /api/auth/logout/` - Logout de usuarios
- `GET /api/auth/profile/` - Obtener perfil del usuario
- `PUT /api/auth/profile/` - Actualizar perfil del usuario
- `POST /api/auth/token/refresh/` - Renovar token JWT
- `GET /api/auth/permissions/` - Ver permisos del usuario (Guardian)
- `POST /api/auth/password-reset/` - Solicitar restablecimiento de contraseña

### Admin

- `GET /admin/` - Panel de administración

## Ejemplo de uso

### Registro de usuario
```bash
curl -X POST http://localhost:8000/api/auth/register/ \
  -H "Content-Type: application/json" \
  -d '{
    "email": "usuario@ejemplo.com",
    "username": "usuario",
    "password": "password123",
    "password_confirm": "password123",
    "first_name": "Juan",
    "last_name": "Pérez"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login/ \
  -H "Content-Type: application/json" \
  -d '{
    "email": "usuario@ejemplo.com",
    "password": "password123"
  }'
```

### Acceder a endpoint protegido
```bash
curl -X GET http://localhost:8000/api/auth/profile/ \
  -H "Authorization: Bearer <tu_token_jwt>"
```

## Estructura del proyecto

```
DjangoEntorno/
├── authentication/          # Aplicación de autenticación
│   ├── models.py           # Modelo de usuario personalizado
│   ├── serializers.py      # Serializers para la API
│   ├── views.py            # Vistas de la API
│   ├── urls.py             # URLs de la aplicación
│   └── admin.py            # Configuración del admin
├── config/                 # Configuración del proyecto
│   ├── settings.py         # Configuración principal
│   └── urls.py             # URLs principales
├── requirements.txt         # Dependencias del proyecto
└── README.md              # Documentación
```

## Configuración JWT

El proyecto está configurado con las siguientes opciones de JWT:

- **Access Token Lifetime**: 60 minutos
- **Refresh Token Lifetime**: 1 día
- **Algorithm**: HS256
- **Header Type**: Bearer

## Tecnologías utilizadas

- Django 5.2.4
- Django REST Framework 3.16.0
- djangorestframework-simplejwt 5.5.1
- django-guardian 2.4.0
- Python 3.x 