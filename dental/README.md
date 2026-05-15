# DentalCore — Sistema de Gestión para Consultorio Odontológico

Backend en PHP 8 + MySQL para la gestión integral de una clínica dental.

## Características

- **Pacientes**: ficha clínica completa, historial médico, alergias, contactos de emergencia.
- **Agenda de citas**: calendario, estados, recordatorios, asignación por odontólogo y sillón.
- **Odontograma**: registro visual del estado de las 32 piezas dentales (adulto) y 20 deciduas (infantil), con histórico.
- **Historia clínica**: notas de consulta, diagnósticos, planes de tratamiento.
- **Facturación**: emisión de facturas, items, descuentos, impuestos.
- **Pagos**: registro de pagos parciales/totales, métodos múltiples, saldos.
- **Usuarios**: roles (admin, odontólogo, recepción, asistente).
- **Auditoría**: log de acciones críticas.

## Stack

- PHP 8.1+ (vanilla, sin frameworks externos)
- MySQL 8 / MariaDB 10.5+
- PDO (prepared statements)
- Apache / Nginx con `mod_rewrite`

## Estructura

```
dental/
├── app/
│   ├── config/      → configuración global
│   ├── controllers/ → controladores (HTTP / API)
│   ├── core/        → núcleo MVC (Router, DB, Auth, etc.)
│   ├── models/      → modelos de dominio
│   └── views/       → plantillas (frontend a futuro)
├── cli/             → tareas de CLI / instalación
├── database/        → schema.sql y seed.sql
├── public/          → DocumentRoot (index.php)
└── storage/         → archivos generados / uploads
```

## Instalación

1. Copia `.env.example` a `.env` y rellena credenciales de BD.
2. Importa la base de datos:
   ```bash
   mysql -u root -p < database/schema.sql
   mysql -u root -p dental_core < database/seed.sql
   ```
3. Apunta el DocumentRoot del servidor a `dental/public/`.
4. Usuario admin por defecto: `admin@dental.local` / `admin123` (cámbialo).

## Endpoints principales (API)

| Método | Ruta                          | Descripción                          |
|--------|-------------------------------|--------------------------------------|
| POST   | /api/auth/login               | Login y emisión de token de sesión   |
| GET    | /api/patients                 | Listado de pacientes (paginado)      |
| POST   | /api/patients                 | Crear paciente                       |
| GET    | /api/patients/{id}            | Detalle de paciente                  |
| GET    | /api/appointments             | Citas (con filtros de fecha)         |
| POST   | /api/appointments             | Crear cita                           |
| GET    | /api/patients/{id}/odontogram | Odontograma actual del paciente      |
| POST   | /api/patients/{id}/odontogram | Actualizar piezas del odontograma    |
| GET    | /api/invoices                 | Listado de facturas                  |
| POST   | /api/invoices                 | Crear factura                        |
| POST   | /api/payments                 | Registrar pago                       |

Ver `public/index.php` para la lista completa.
