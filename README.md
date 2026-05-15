# Reservas de Salas

Sistema de reservas para salas de reuniones construido con Laravel, Inertia.js, Vue 3, Tailwind CSS y Vite. La aplicación incluye panel administrativo oscuro, catálogo público, calendario semanal, imágenes de salas, cancelación por usuario, valoraciones posteriores al uso y métricas para administración.

## Funcionalidades

- Catálogo público de salas con imagen, capacidad, ubicación y precio.
- Reserva pública con validación de disponibilidad, capacidad y antelación mínima.
- Confirmación automática de reservas disponibles.
- Cancelación por el usuario dueño de la reserva.
- Panel admin oscuro con calendario semanal, listado de reservas y dashboard de métricas.
- Salas destacadas con imagen y ranking de uso.
- Valoración de una reserva después de finalizar su uso.

## Requisitos

- PHP 8.2 o superior.
- Composer.
- Node.js 18 o superior.
- Base de datos SQLite, MySQL o la que prefieras.

## Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

Si trabajas en local con Vite:

```bash
npm run dev
```

## Variables de entorno

El archivo [.env.example](.env.example) incluye los valores base del proyecto. Las variables más importantes son:

- `APP_NAME` - nombre visible de la aplicación.
- `APP_URL` - URL local o de despliegue.
- `APP_LOCALE` - idioma principal.
- `RESERVATION_SLOT_MINUTES` - duración mínima del bloque de reserva.
- `RESERVATION_MIN_LEAD_MINUTES` - antelación mínima para reservar.

Ejemplo recomendado:

```env
APP_NAME="Reservas Salas"
APP_URL=http://reservas-salas.test
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_CO
RESERVATION_SLOT_MINUTES=60
RESERVATION_MIN_LEAD_MINUTES=240
```

## Datos de prueba

El seeder crea varias salas de ejemplo, incluyendo portadas SVG locales en [public/images/spaces](public/images/spaces). Eso te deja material listo para probar el flujo completo sin subir fotos reales todavía.

Para volver a cargar los datos:

```bash
php artisan db:seed --class=DatabaseSeeder
```

## Cancelación de reservas

Las reservas quedan asociadas al usuario autenticado cuando la sesión está iniciada. Ese usuario puede revisar sus reservas en la vista **Mis reservas** y cancelar las que sigan confirmadas.

## Rutas principales

- `/` - catálogo público de salas.
- `/spaces/{space}` - detalle de una sala.
- `/reservations/new` - formulario de reserva.
- `/reservations/{reservation}/confirmation` - confirmación.
- `/my-reservations` - reservas del usuario autenticado.
- `/admin/dashboard` - panel administrativo.
- `/admin/calendar` - calendario semanal.
- `/admin/reservations` - listado administrativo.

## Cuenta admin de prueba

El seeder deja un usuario administrador listo para entrar al panel:

- Correo: `admin@reservas.com`
- Contraseña: `password`

## Imágenes de salas

Las salas ya pueden mostrar una imagen con la clave `image`. En este momento el proyecto usa SVG locales de ejemplo, pero puedes reemplazarlos por fotos reales o subirlas al almacenamiento público cuando lo necesites.

## GitHub

Antes de subir el repositorio:

- No subas `.env`.
- Sí sube `.env.example`.
- Mantén ignorados `vendor`, `node_modules`, `public/build` y bases locales SQLite.
- Ejecuta `php artisan migrate --seed` y `npm run build` antes de publicar.

## Nota visual

La interfaz usa una paleta oscura con acentos violetas/rojos y tarjetas de alto contraste para mantener consistencia entre el panel admin, el catálogo y el flujo de reserva.
