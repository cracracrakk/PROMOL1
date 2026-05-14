-- Spa Serenity - Esquema de Base de Datos (versión PRO)
-- Compatible con MySQL 5.7+ / MariaDB 10.3+

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- USUARIOS DEL SISTEMA
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','recepcion','terapeuta') NOT NULL DEFAULT 'recepcion',
    phone VARCHAR(40) NULL,
    photo VARCHAR(255) NULL,
    bio TEXT NULL,
    color VARCHAR(7) DEFAULT '#6b8a7a',
    active TINYINT(1) NOT NULL DEFAULT 1,
    two_factor_secret VARCHAR(255) NULL,
    last_login_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Horarios de trabajo del personal (días de la semana)
CREATE TABLE IF NOT EXISTS user_schedules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    weekday TINYINT NOT NULL COMMENT '0=domingo, 1=lunes...6=sabado',
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ausencias / vacaciones del personal
CREATE TABLE IF NOT EXISTS user_absences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    reason VARCHAR(160) NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CLIENTES (con ficha clínica)
-- ============================================================
CREATE TABLE IF NOT EXISTS customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(120) NULL,
    email VARCHAR(160) NULL,
    phone VARCHAR(40) NULL,
    birthdate DATE NULL,
    gender ENUM('M','F','otro','prefiero_no_decirlo') NULL,
    tax_id VARCHAR(30) NULL COMMENT 'RTN (Honduras) / NIF (España) para facturas',
    is_company TINYINT(1) DEFAULT 0,
    address VARCHAR(255) NULL,
    city VARCHAR(80) NULL,
    postal_code VARCHAR(20) NULL,
    -- Ficha clínica
    allergies TEXT NULL,
    medical_conditions TEXT NULL,
    medications TEXT NULL,
    pregnant TINYINT(1) DEFAULT 0,
    pregnancy_weeks INT NULL,
    preferences TEXT NULL,
    -- Marketing / Loyalty
    vip TINYINT(1) DEFAULT 0,
    loyalty_points INT NOT NULL DEFAULT 0,
    referral_code VARCHAR(20) NULL UNIQUE,
    referred_by INT UNSIGNED NULL,
    accepts_marketing TINYINT(1) DEFAULT 1,
    gdpr_consent_at DATETIME NULL,
    notes TEXT NULL,
    no_show_count INT NOT NULL DEFAULT 0,
    portal_password VARCHAR(255) NULL COMMENT 'Hash bcrypt para portal cliente',
    portal_token VARCHAR(64) NULL COMMENT 'Token de login mágico',
    portal_token_expires DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_customer_phone (phone),
    INDEX idx_customer_email (email),
    INDEX idx_customer_birthdate (birthdate),
    FOREIGN KEY (referred_by) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SERVICIOS
-- ============================================================
CREATE TABLE IF NOT EXISTS service_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    description TEXT NULL,
    icon VARCHAR(60) NULL,
    sort_order INT DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NULL,
    name VARCHAR(160) NOT NULL,
    description TEXT NULL,
    duration_minutes INT NOT NULL DEFAULT 60,
    buffer_minutes INT NOT NULL DEFAULT 15 COMMENT 'Tiempo de limpieza entre citas',
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    cost DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Coste del servicio para margen',
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 21,
    image VARCHAR(255) NULL,
    requires_room_type VARCHAR(60) NULL COMMENT 'Tipo de cabina requerida',
    bookable_online TINYINT(1) DEFAULT 1,
    active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CABINAS / SALAS
-- ============================================================
CREATE TABLE IF NOT EXISTS rooms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    room_type VARCHAR(60) NULL COMMENT 'masaje, facial, humeda, manicura...',
    capacity INT DEFAULT 1,
    description TEXT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CITAS
-- ============================================================
CREATE TABLE IF NOT EXISTS appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    therapist_id INT UNSIGNED NULL,
    room_id INT UNSIGNED NULL,
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    price DECIMAL(10,2) NULL COMMENT 'Precio aplicado en esa cita (puede diferir)',
    status ENUM('pendiente','confirmada','en_curso','completada','cancelada','no_show') NOT NULL DEFAULT 'pendiente',
    source ENUM('web','telefono','presencial','admin','whatsapp') NOT NULL DEFAULT 'admin',
    notes TEXT NULL,
    internal_notes TEXT NULL,
    reminder_sent_at DATETIME NULL,
    confirmation_token VARCHAR(40) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (therapist_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    INDEX idx_app_starts (starts_at),
    INDEX idx_app_status (status),
    INDEX idx_app_therapist_date (therapist_id, starts_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INVENTARIO
-- ============================================================
CREATE TABLE IF NOT EXISTS product_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS suppliers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    contact VARCHAR(160) NULL,
    phone VARCHAR(40) NULL,
    email VARCHAR(160) NULL,
    notes TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NULL,
    supplier_id INT UNSIGNED NULL,
    sku VARCHAR(60) NOT NULL UNIQUE,
    barcode VARCHAR(60) NULL,
    name VARCHAR(160) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    cost_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    sale_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 21,
    stock INT NOT NULL DEFAULT 0,
    stock_min INT NOT NULL DEFAULT 5,
    unit VARCHAR(20) DEFAULT 'ud',
    is_consumable TINYINT(1) DEFAULT 0 COMMENT 'Se consume en servicios (ej. aceite) o se vende',
    sellable TINYINT(1) DEFAULT 1,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    INDEX idx_product_sku (sku),
    INDEX idx_product_stock (stock)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stock_movements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    type ENUM('entrada','salida','ajuste','venta','consumo') NOT NULL,
    quantity INT NOT NULL,
    cost_price DECIMAL(10,2) NULL,
    reason VARCHAR(255) NULL,
    user_id INT UNSIGNED NULL,
    invoice_id INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BONOS Y TARJETAS REGALO
-- ============================================================
CREATE TABLE IF NOT EXISTS service_packs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    description TEXT NULL,
    service_id INT UNSIGNED NULL,
    sessions_total INT NOT NULL DEFAULT 10,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    valid_months INT NOT NULL DEFAULT 6,
    active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS customer_packs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    pack_id INT UNSIGNED NOT NULL,
    code VARCHAR(30) NOT NULL UNIQUE,
    sessions_total INT NOT NULL,
    sessions_used INT NOT NULL DEFAULT 0,
    purchased_at DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    price_paid DECIMAL(10,2) NOT NULL,
    status ENUM('activo','agotado','caducado','cancelado') NOT NULL DEFAULT 'activo',
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (pack_id) REFERENCES service_packs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gift_cards (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    initial_amount DECIMAL(10,2) NOT NULL,
    balance DECIMAL(10,2) NOT NULL,
    buyer_name VARCHAR(160) NULL,
    buyer_email VARCHAR(160) NULL,
    recipient_name VARCHAR(160) NULL,
    recipient_email VARCHAR(160) NULL,
    message TEXT NULL,
    purchased_at DATETIME NOT NULL,
    expires_at DATETIME NULL,
    status ENUM('activa','usada','caducada','cancelada') NOT NULL DEFAULT 'activa',
    INDEX idx_giftcard_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FACTURACIÓN (Honduras - SAR)
-- ============================================================

-- Autorizaciones CAI emitidas por SAR
CREATE TABLE IF NOT EXISTS sar_authorizations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_type ENUM('factura','nota_credito','nota_debito','recibo') NOT NULL DEFAULT 'factura',
    cai VARCHAR(80) NOT NULL,
    resolucion VARCHAR(120) NULL,
    establecimiento VARCHAR(10) NOT NULL DEFAULT '000',
    punto_emision VARCHAR(10) NOT NULL DEFAULT '001',
    tipo_documento VARCHAR(10) NOT NULL DEFAULT '01',
    rango_inicial INT UNSIGNED NOT NULL,
    rango_final INT UNSIGNED NOT NULL,
    next_number INT UNSIGNED NOT NULL,
    fecha_limite DATE NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cai_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_type ENUM('factura','nota_credito','nota_debito','recibo') NOT NULL DEFAULT 'factura',
    number VARCHAR(40) NOT NULL UNIQUE COMMENT 'Formato SAR: 000-001-01-00000001',
    sar_authorization_id INT UNSIGNED NULL,
    cai VARCHAR(80) NULL,
    fecha_limite_emision DATE NULL,
    rango_autorizado VARCHAR(50) NULL,
    related_invoice_id INT UNSIGNED NULL COMMENT 'Para notas de credito/debito',
    customer_id INT UNSIGNED NULL,
    customer_rtn VARCHAR(20) NULL,
    customer_name VARCHAR(200) NULL,
    customer_address VARCHAR(255) NULL,
    appointment_id INT UNSIGNED NULL,
    user_id INT UNSIGNED NULL,
    issue_date DATE NOT NULL,
    -- Subtotales por tipo de impuesto (Honduras)
    importe_exento DECIMAL(10,2) NOT NULL DEFAULT 0,
    importe_exonerado DECIMAL(10,2) NOT NULL DEFAULT 0,
    importe_gravado_15 DECIMAL(10,2) NOT NULL DEFAULT 0,
    importe_gravado_18 DECIMAL(10,2) NOT NULL DEFAULT 0,
    isv_15 DECIMAL(10,2) NOT NULL DEFAULT 0,
    isv_18 DECIMAL(10,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount_reason VARCHAR(160) NULL,
    tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_letras VARCHAR(500) NULL,
    tip DECIMAL(10,2) NOT NULL DEFAULT 0,
    payment_method ENUM('efectivo','tarjeta','transferencia','cheque','tarjeta_regalo','bono','mixto','otro') NOT NULL DEFAULT 'efectivo',
    payment_reference VARCHAR(80) NULL,
    gift_card_code VARCHAR(20) NULL,
    promo_code VARCHAR(30) NULL,
    status ENUM('borrador','emitida','pagada','anulada') NOT NULL DEFAULT 'pagada',
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (sar_authorization_id) REFERENCES sar_authorizations(id) ON DELETE SET NULL,
    FOREIGN KEY (related_invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    INDEX idx_invoice_date (issue_date),
    INDEX idx_invoice_status (status),
    INDEX idx_invoice_doctype (document_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoice_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT UNSIGNED NOT NULL,
    item_type ENUM('servicio','producto','bono','otro') NOT NULL,
    service_id INT UNSIGNED NULL,
    product_id INT UNSIGNED NULL,
    pack_id INT UNSIGNED NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 21,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (pack_id) REFERENCES service_packs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cierre de caja diario
CREATE TABLE IF NOT EXISTS cash_closings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    closing_date DATE NOT NULL UNIQUE,
    expected_cash DECIMAL(10,2) NOT NULL DEFAULT 0,
    counted_cash DECIMAL(10,2) NOT NULL DEFAULT 0,
    expected_card DECIMAL(10,2) NOT NULL DEFAULT 0,
    expected_other DECIMAL(10,2) NOT NULL DEFAULT 0,
    difference DECIMAL(10,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    user_id INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- WEB PÚBLICA (contenido editable)
-- ============================================================
CREATE TABLE IF NOT EXISTS site_settings (
    `key` VARCHAR(80) PRIMARY KEY,
    `value` TEXT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author VARCHAR(120) NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    comment TEXT NOT NULL,
    published TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    caption VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    published TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NULL,
    message TEXT NOT NULL,
    read_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- AUDITORÍA Y SEGURIDAD
-- ============================================================
CREATE TABLE IF NOT EXISTS audit_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    entity VARCHAR(60) NULL,
    entity_id INT UNSIGNED NULL,
    description TEXT NULL,
    ip VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_date (created_at),
    INDEX idx_audit_entity (entity, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Encuestas NPS post-servicio
CREATE TABLE IF NOT EXISTS surveys (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT UNSIGNED NULL,
    customer_id INT UNSIGNED NULL,
    nps TINYINT NOT NULL DEFAULT 0 COMMENT 'Net Promoter Score 0-10',
    comment TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Intentos de login fallidos (rate limit)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(160) NULL,
    ip VARCHAR(45) NOT NULL,
    success TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_attempts_ip_time (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Códigos promocionales / descuentos
CREATE TABLE IF NOT EXISTS promo_codes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    description VARCHAR(160) NULL,
    discount_type ENUM('percent','amount') NOT NULL DEFAULT 'percent',
    discount_value DECIMAL(10,2) NOT NULL,
    valid_from DATE NULL,
    valid_until DATE NULL,
    max_uses INT NULL,
    used_count INT NOT NULL DEFAULT 0,
    active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sucursales (multi-local)
CREATE TABLE IF NOT EXISTS branches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    address VARCHAR(255) NULL,
    phone VARCHAR(40) NULL,
    email VARCHAR(160) NULL,
    sar_cai_prefix VARCHAR(20) NULL,
    timezone VARCHAR(60) DEFAULT 'America/Tegucigalpa',
    active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wallet / saldo prepagado del cliente
CREATE TABLE IF NOT EXISTS customer_wallets (
    customer_id INT UNSIGNED PRIMARY KEY,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wallet_transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL COMMENT 'Positivo = recarga, negativo = uso',
    type ENUM('recarga','consumo','ajuste','devolucion') NOT NULL,
    invoice_id INT UNSIGNED NULL,
    reason VARCHAR(255) NULL,
    user_id INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Newsletters enviados
CREATE TABLE IF NOT EXISTS newsletters (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    segment VARCHAR(40) NULL,
    recipients INT NOT NULL DEFAULT 0,
    sent INT NOT NULL DEFAULT 0,
    user_id INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Plantillas de email editables
CREATE TABLE IF NOT EXISTS email_templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(60) NOT NULL UNIQUE,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables TEXT NULL COMMENT 'Lista de variables disponibles',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
