# Sistema de Gestión de Spa

Sistema completo de gestión para spas y centros de bienestar. Web pública con reservas online + panel administrativo con citas, clientes, inventario, **facturación fiscal Honduras (SAR)**, bonos, tarjetas regalo, caja y reportes.

Producto **listo para vender**: cada cliente puede configurar su marca, logo, colores, datos fiscales, usuarios y autorización CAI desde el módulo **Sistema**.

---

## 🧰 Stack

- **Backend:** PHP 8.0+ puro (sin frameworks)
- **Base de datos:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** HTML + CSS3 + JavaScript vanilla
- **Librerías JS** (vía CDN): Chart.js, FullCalendar
- **Fuentes:** Playfair Display + Inter + Cormorant Garamond (Google Fonts)

Sin Composer, sin npm. Funciona en cualquier hosting con PHP y MySQL.

---

## 📦 Estructura

```
/
├── public/                 # DocumentRoot del servidor web
│   ├── index.php          # Front controller
│   ├── .htaccess          # URL rewriting
│   └── assets/            # CSS, JS, imágenes, uploads
├── app/
│   ├── config/            # Configuración + cargador .env
│   ├── core/              # Database, Router, Auth, helpers, SarHelper
│   ├── controllers/       # Un controller por módulo
│   └── views/             # Vistas (layouts, public, admin, auth, errors)
├── database/
│   ├── schema.sql         # Esquema completo (24 tablas)
│   └── seed.sql           # Datos de demostración
├── storage/               # PDFs generados, backups
├── .env.example           # Plantilla de configuración
└── README.md
```

---

## 🚀 Instalación

### 1. Clonar y configurar
```bash
git clone <repositorio>
cd PROMOL1
cp .env.example .env
```

Edita `.env` con tus datos (BD, moneda, datos SAR, etc.).

### 2. Crear base de datos
```bash
mysql -u root -p -e "CREATE DATABASE spa_serenity CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p spa_serenity < database/schema.sql
mysql -u root -p spa_serenity < database/seed.sql
```

### 3. Servidor web
Apuntar el DocumentRoot a `/public`. Apache funciona con `.htaccess` incluido.

**Desarrollo rápido con servidor PHP embebido:**
```bash
php -S localhost:8000 -t public
```

### 4. Permisos
```bash
chmod -R 775 public/assets/uploads storage
```

### 5. Acceder

- **Web pública:** http://localhost:8000
- **Panel admin:** http://localhost:8000/login

### 🔑 Usuarios demo

| Email | Contraseña | Rol |
|-------|-----------|-----|
| `admin@spa.local`  | `admin123` | Administrador |
| `maria@spa.local`  | `admin123` | Recepción |
| `laura@spa.local`  | `admin123` | Terapeuta |
| `carmen@spa.local` | `admin123` | Terapeuta |

> ⚠️ **Importante:** cambia las contraseñas antes de poner en producción.

---

## 🇭🇳 Configuración SAR Honduras

El sistema cumple con la normativa de facturación de la **SAR** (Servicio de Administración de Rentas) de Honduras.

### Datos fiscales del emisor

Accede a **Sistema → Configuración** y completa:
- Razón social y nombre comercial
- **RTN** (14 dígitos)
- Dirección fiscal completa
- Régimen tributario (General / Simplificado / Exonerado)
- Número de resolución SAR

### Autorización CAI

En **Sistema → SAR / CAI**, registra cada autorización emitida por SAR:
- **CAI** (código alfanumérico)
- Establecimiento (3 dígitos, ej: `000`)
- Punto de emisión (3 dígitos, ej: `001`)
- Tipo de documento (`01` factura, `04` nota crédito, `05` nota débito, `06` recibo)
- Rango inicial y final
- Fecha límite de emisión

El sistema asigna automáticamente el correlativo con formato `000-001-01-00000001` y avisa cuando:
- Quedan menos de 50 folios por consumir
- Faltan menos de 30 días para la caducidad

### Impuestos

Cada servicio/producto soporta:
- **ISV 15%** — tasa estándar
- **ISV 18%** — turismo, alcohol, tabaco
- **Exento** — algunos servicios médicos

La factura desglosa automáticamente los importes gravados por tasa y los ISV recaudados.

### Funcionalidades fiscales

✅ Factura · Recibo · Nota de Crédito · Nota de Débito
✅ Conversión automática del total a letras en español
✅ Pie legal "La factura es beneficio de todos, exíjala"
✅ RTN del cliente para personas jurídicas
✅ Vista imprimible / PDF con layout SAR
✅ **Libro de ventas** exportable a CSV/Excel para presentación a SAR

---

## 📋 Módulos del sistema

| Módulo | Funcionalidad |
|--------|---------------|
| **Web pública** | Landing, servicios, sobre nosotros, contacto, reserva online, tarjetas regalo, WhatsApp flotante |
| **Dashboard** | KPIs del día/mes, gráfica de ingresos 14 días, top servicios, alertas stock + SAR |
| **Agenda** | FullCalendar con vista semanal/diaria/mensual, click para crear cita |
| **Citas** | CRUD, estados (pendiente→confirmada→completada/cancelada/no_show), origen (web/teléfono/admin) |
| **Clientes** | Datos personales, fiscales (RTN), **ficha clínica** (alergias, condiciones, embarazo), historial, VIP, fidelidad |
| **Servicios** | Categorías, duración, margen limpieza, precio, coste, ISV, reservable online |
| **Cabinas** | Salas físicas asociadas a tipos de servicio |
| **Inventario** | Productos, SKU, código de barras, proveedores, stock, movimientos (entrada/salida/consumo/ajuste/venta) |
| **Facturación** | Documentos SAR con CAI, ISV 15%/18%/exento, total en letras, descuentos, propinas, multi-método |
| **Bonos** | Service packs (10 sesiones por X) + tarjetas regalo con código y saldo |
| **Caja diaria** | Cuadre por método de pago, diferencia esperado vs contado |
| **Reportes** | Libro de ventas SAR, top servicios, comisiones por terapeuta, exportación CSV |
| **Sistema** | Identidad, colores, contacto, hero, fiscal SAR, autorizaciones CAI, usuarios y roles, auditoría |

---

## 👥 Roles

| Rol | Permisos |
|-----|----------|
| `admin` | Acceso total, incluido módulo Sistema |
| `gerente` | Acceso operativo, sin Sistema |
| `recepcion` | Citas, clientes, facturación, caja |
| `terapeuta` | Su agenda, sus citas, sus clientes |
| `contable` | Reportes y libro de ventas |

---

## 🎨 Personalización (sin código)

Desde **Sistema → Configuración** el cliente puede cambiar:
- Nombre del spa, eslogan, descripción
- **Logo** (subir imagen)
- Colores primario y de acento
- Dirección, teléfono, email, WhatsApp, Instagram
- Horario de atención
- Título y subtítulo del hero
- Texto "Sobre nosotros"
- Datos fiscales SAR
- Moneda (símbolo)

Todo se aplica inmediatamente a la web pública y al panel.

---

## 🔒 Seguridad

- Contraseñas con **bcrypt**
- **CSRF tokens** en todos los formularios
- **Sesiones HttpOnly + SameSite=Lax**
- **Validación de roles** en cada controlador
- **Audit log** (quién, qué, cuándo, IP, user-agent)
- **PDO con prepared statements** (anti SQL injection)
- **Escape automático** de salida HTML (función `e()`)

---

## 🌐 Producción

**Antes de poner en producción:**

1. `APP_DEBUG=false` en `.env`
2. Cambiar contraseñas de los usuarios demo
3. Configurar HTTPS (Let's Encrypt)
4. Configurar backups automáticos de la base de datos
5. Configurar SMTP para emails de confirmación
6. Subir logo del cliente, ajustar colores
7. Registrar autorización CAI real desde SAR
8. Borrar datos demo (clientes, citas, productos de ejemplo)

---

## 📝 Licencia

Producto comercial. Cada licencia se vende por instalación / cliente.
