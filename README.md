# 🏥 Sistema de Laudos ZuMed

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Firebase-FFCA28?style=for-the-badge&logo=firebase&logoColor=black" alt="Firebase">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
</p>

<p align="center">
  <strong>Sistema integral de gestión de laudos médicos para clínicas y centros de salud</strong>
</p>

## 📋 Descripción

El **Sistema de Laudos ZuMed** es una plataforma web desarrollada para optimizar la gestión, almacenamiento y consulta de laudos médicos. Diseñado específicamente para clínicas y centros de salud, ofrece una interfaz intuitiva y segura para el manejo de documentos médicos.

## ✨ Características Principales

- 🔐 **Autenticación Segura**: Sistema de login con Firebase Authentication
- 📄 **Gestión de Laudos**: Creación, edición y consulta de laudos médicos
- 🏥 **Multi-clínica**: Soporte para múltiples clínicas en una sola plataforma
- 📱 **Diseño Responsivo**: Interfaz adaptable para dispositivos móviles y desktop
- 🔍 **Búsqueda Avanzada**: Filtros y búsqueda rápida de laudos
- 📊 **Dashboard Intuitivo**: Panel de control con métricas y accesos rápidos
- 🔒 **Seguridad**: Cumplimiento de estándares de seguridad médica
- 📁 **Almacenamiento en la Nube**: Integración con Firebase Storage

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11.x
- **Frontend**: Blade Templates + Tailwind CSS
- **Base de Datos**: SQLite (configurable para MySQL/PostgreSQL)
- **Autenticación**: Firebase Authentication
- **Almacenamiento**: Firebase Storage
- **Servidor Web**: Apache/Nginx
- **Gestión de Dependencias**: Composer, NPM

## 📦 Instalación

### Prerrequisitos

- PHP 8.2 o superior
- Composer
- Node.js y NPM
- Cuenta de Firebase

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/sistema-laudos-zumed.git
   cd sistema-laudos-zumed
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Node.js**
   ```bash
   npm install
   ```

4. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   ```

5. **Generar clave de aplicación**
   ```bash
   php artisan key:generate
   ```

6. **Configurar Firebase**
   
   Edita el archivo `.env` con tus credenciales de Firebase:
   ```env
   FIREBASE_API_KEY=tu_api_key
   FIREBASE_PROJECT_ID=tu_project_id
   FIREBASE_STORAGE_BUCKET=tu_storage_bucket
   ```

7. **Ejecutar migraciones**
   ```bash
   php artisan migrate
   ```

8. **Compilar assets**
   ```bash
   npm run build
   ```

9. **Iniciar el servidor de desarrollo**
   ```bash
   php artisan serve
   ```

## ⚙️ Configuración

### Base de Datos

Por defecto, el sistema utiliza SQLite. Para cambiar a MySQL o PostgreSQL:

1. Modifica las variables de entorno en `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sistema_laudos
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_contraseña
   ```

2. Ejecuta las migraciones:
   ```bash
   php artisan migrate:fresh
   ```

### Firebase

1. Crea un proyecto en [Firebase Console](https://console.firebase.google.com/)
2. Habilita Authentication y Storage
3. Obtén las credenciales del proyecto
4. Configura las variables en `.env`

## 🚀 Uso

### Acceso al Sistema

1. Navega a `http://localhost:8000`
2. Inicia sesión con tus credenciales
3. Accede al dashboard principal

### Funcionalidades Principales

- **Dashboard**: Vista general de laudos y estadísticas
- **Gestión de Laudos**: Crear, editar y consultar laudos
- **Búsqueda**: Filtrar laudos por fecha, paciente, tipo, etc.
- **Configuración**: Administrar usuarios y configuraciones de clínica

## 📁 Estructura del Proyecto

```
sistema-laudos-zumed/
├── app/
│   ├── Http/Controllers/     # Controladores
│   ├── Models/              # Modelos Eloquent
│   └── Services/            # Servicios (Firebase, etc.)
├── resources/
│   ├── views/               # Vistas Blade
│   ├── css/                 # Estilos CSS
│   └── js/                  # JavaScript
├── routes/
│   └── web.php              # Rutas web
├── database/
│   └── migrations/          # Migraciones de BD
└── public/                  # Archivos públicos
```

## 🔒 Seguridad

- Autenticación mediante Firebase
- Validación de datos en servidor
- Protección CSRF
- Sanitización de entradas
- Encriptación de datos sensibles

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 📞 Soporte

Para soporte técnico o consultas:

- 📧 Email: soporte@zumed.com
- 🌐 Web: [www.zumed.com](https://www.zumed.com)
- 📱 WhatsApp: +55 (11) 9999-9999

## 🏆 Créditos

Desarrollado por el equipo de ZuMed para optimizar la gestión de laudos médicos en clínicas y centros de salud.

---

<p align="center">
  <strong>Sistema de Laudos ZuMed</strong> - Gestión médica eficiente y segura
</p>
