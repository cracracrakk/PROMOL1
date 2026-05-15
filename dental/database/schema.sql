-- ============================================================
-- DentalCore - Esquema de base de datos
-- Motor: MySQL 8 / MariaDB 10.5+
-- ============================================================

DROP DATABASE IF EXISTS dental_core;
CREATE DATABASE dental_core
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE dental_core;

-- ------------------------------------------------------------
-- Usuarios del sistema (odontólogos, recepción, admin)
-- ------------------------------------------------------------
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120) NOT NULL,
    email           VARCHAR(180) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('admin','dentist','reception','assistant') NOT NULL DEFAULT 'reception',
    license_number  VARCHAR(60)  NULL,                -- colegiatura del odontólogo
    specialty       VARCHAR(120) NULL,
    phone           VARCHAR(30)  NULL,
    avatar          VARCHAR(255) NULL,
    is_active       TINYINT(1)   NOT NULL DEFAULT 1,
    last_login_at   DATETIME     NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role),
    INDEX idx_users_active (is_active)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Pacientes
-- ------------------------------------------------------------
CREATE TABLE patients (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(20)  NOT NULL UNIQUE,     -- ficha: P-000001
    first_name      VARCHAR(80)  NOT NULL,
    last_name       VARCHAR(80)  NOT NULL,
    document_type   ENUM('dni','passport','other') NOT NULL DEFAULT 'dni',
    document_number VARCHAR(40)  NULL,
    birth_date      DATE         NULL,
    gender          ENUM('male','female','other') NULL,
    blood_type      VARCHAR(5)   NULL,
    email           VARCHAR(180) NULL,
    phone           VARCHAR(30)  NULL,
    mobile          VARCHAR(30)  NULL,
    address         VARCHAR(255) NULL,
    city            VARCHAR(80)  NULL,
    state           VARCHAR(80)  NULL,
    occupation      VARCHAR(120) NULL,
    emergency_name  VARCHAR(120) NULL,
    emergency_phone VARCHAR(30)  NULL,
    emergency_rel   VARCHAR(60)  NULL,
    referred_by     VARCHAR(120) NULL,
    notes           TEXT         NULL,
    is_active       TINYINT(1)   NOT NULL DEFAULT 1,
    created_by      INT UNSIGNED NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_patients_name (last_name, first_name),
    INDEX idx_patients_doc  (document_number),
    INDEX idx_patients_phone (phone, mobile)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Historia médica general (alergias, condiciones, medicamentos)
-- Un registro por paciente (1:1)
-- ------------------------------------------------------------
CREATE TABLE patient_medical_history (
    patient_id          INT UNSIGNED PRIMARY KEY,
    allergies           TEXT NULL,
    chronic_conditions  TEXT NULL,
    current_medications TEXT NULL,
    past_surgeries      TEXT NULL,
    smokes              TINYINT(1) NOT NULL DEFAULT 0,
    drinks_alcohol      TINYINT(1) NOT NULL DEFAULT 0,
    pregnant            TINYINT(1) NOT NULL DEFAULT 0,
    diabetes            TINYINT(1) NOT NULL DEFAULT 0,
    hypertension        TINYINT(1) NOT NULL DEFAULT 0,
    heart_disease       TINYINT(1) NOT NULL DEFAULT 0,
    bleeding_disorders  TINYINT(1) NOT NULL DEFAULT 0,
    notes               TEXT NULL,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Catálogo de tratamientos / procedimientos
-- ------------------------------------------------------------
CREATE TABLE treatments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(30) NOT NULL UNIQUE,      -- ej: PROF-001
    name            VARCHAR(160) NOT NULL,
    category        VARCHAR(80)  NULL,                -- preventiva, restauradora, endodoncia...
    description     TEXT NULL,
    default_price   DECIMAL(10,2) NOT NULL DEFAULT 0,
    duration_min    INT UNSIGNED  NOT NULL DEFAULT 30,
    requires_tooth  TINYINT(1)    NOT NULL DEFAULT 0, -- ¿aplica a una pieza específica?
    is_active       TINYINT(1)    NOT NULL DEFAULT 1,
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_treatments_cat (category),
    INDEX idx_treatments_active (is_active)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Sillones / boxes de atención
-- ------------------------------------------------------------
CREATE TABLE rooms (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(80) NOT NULL,
    color       VARCHAR(9) NOT NULL DEFAULT '#3b82f6',
    is_active   TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Citas
-- ------------------------------------------------------------
CREATE TABLE appointments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    dentist_id      INT UNSIGNED NOT NULL,
    room_id         INT UNSIGNED NULL,
    treatment_id    INT UNSIGNED NULL,                -- motivo principal opcional
    starts_at       DATETIME NOT NULL,
    ends_at         DATETIME NOT NULL,
    status          ENUM('scheduled','confirmed','in_progress','completed','cancelled','no_show')
                        NOT NULL DEFAULT 'scheduled',
    reason          VARCHAR(255) NULL,
    notes           TEXT NULL,
    reminder_sent   TINYINT(1) NOT NULL DEFAULT 0,
    created_by      INT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id)   REFERENCES patients(id)   ON DELETE CASCADE,
    FOREIGN KEY (dentist_id)   REFERENCES users(id)      ON DELETE RESTRICT,
    FOREIGN KEY (room_id)      REFERENCES rooms(id)      ON DELETE SET NULL,
    FOREIGN KEY (treatment_id) REFERENCES treatments(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by)   REFERENCES users(id)      ON DELETE SET NULL,
    INDEX idx_appointments_starts (starts_at),
    INDEX idx_appointments_dentist (dentist_id, starts_at),
    INDEX idx_appointments_patient (patient_id, starts_at),
    INDEX idx_appointments_status (status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Notas de consulta / historia clínica por visita
-- ------------------------------------------------------------
CREATE TABLE clinical_notes (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    dentist_id      INT UNSIGNED NOT NULL,
    appointment_id  INT UNSIGNED NULL,
    visit_date      DATE NOT NULL,
    chief_complaint TEXT NULL,                        -- motivo de consulta
    diagnosis       TEXT NULL,
    treatment_done  TEXT NULL,
    prescription    TEXT NULL,
    next_visit      TEXT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id)     REFERENCES patients(id)     ON DELETE CASCADE,
    FOREIGN KEY (dentist_id)     REFERENCES users(id)        ON DELETE RESTRICT,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    INDEX idx_notes_patient (patient_id, visit_date)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Odontograma: estado por pieza dental
-- Numeración FDI: 11-18, 21-28, 31-38, 41-48 (adulto)
-- y 51-55, 61-65, 71-75, 81-85 (decidua)
-- Una fila por (paciente, pieza). Updates upsert.
-- ------------------------------------------------------------
CREATE TABLE odontogram (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    tooth_code      VARCHAR(3) NOT NULL,              -- ej: '11', '36', '54'
    dentition       ENUM('permanent','deciduous') NOT NULL DEFAULT 'permanent',
    -- Estado por superficie (vestibular, lingual, mesial, distal, oclusal/incisal)
    surface_v       VARCHAR(20) NULL,
    surface_l       VARCHAR(20) NULL,
    surface_m       VARCHAR(20) NULL,
    surface_d       VARCHAR(20) NULL,
    surface_o       VARCHAR(20) NULL,
    -- Estado general de la pieza
    status          ENUM('healthy','caries','filled','crown','root_canal','extracted',
                         'missing','implant','bridge','sealant','fractured','to_extract')
                        NOT NULL DEFAULT 'healthy',
    notes           VARCHAR(255) NULL,
    updated_by      INT UNSIGNED NULL,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_odontogram_tooth (patient_id, tooth_code),
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id)    ON DELETE SET NULL,
    INDEX idx_odontogram_status (status)
) ENGINE=InnoDB;

-- Histórico de cambios del odontograma (auditoría clínica)
CREATE TABLE odontogram_history (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    tooth_code      VARCHAR(3) NOT NULL,
    previous_status VARCHAR(40) NULL,
    new_status      VARCHAR(40) NOT NULL,
    surface         VARCHAR(20) NULL,
    note            VARCHAR(255) NULL,
    changed_by      INT UNSIGNED NULL,
    changed_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id)    ON DELETE SET NULL,
    INDEX idx_odontogram_hist_patient (patient_id, changed_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Planes de tratamiento (presupuesto + ejecución)
-- ------------------------------------------------------------
CREATE TABLE treatment_plans (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    dentist_id      INT UNSIGNED NOT NULL,
    code            VARCHAR(20) NOT NULL UNIQUE,      -- PLAN-000001
    title           VARCHAR(160) NOT NULL,
    diagnosis       TEXT NULL,
    status          ENUM('draft','approved','in_progress','completed','cancelled')
                        NOT NULL DEFAULT 'draft',
    total_amount    DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount        DECIMAL(10,2) NOT NULL DEFAULT 0,
    notes           TEXT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (dentist_id) REFERENCES users(id)    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE treatment_plan_items (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    treatment_plan_id   INT UNSIGNED NOT NULL,
    treatment_id        INT UNSIGNED NOT NULL,
    tooth_code          VARCHAR(3) NULL,
    surface             VARCHAR(20) NULL,
    quantity            INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price          DECIMAL(10,2) NOT NULL,
    line_total          DECIMAL(10,2) NOT NULL,
    status              ENUM('pending','done','cancelled') NOT NULL DEFAULT 'pending',
    done_at             DATETIME NULL,
    notes               VARCHAR(255) NULL,
    FOREIGN KEY (treatment_plan_id) REFERENCES treatment_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (treatment_id)      REFERENCES treatments(id)      ON DELETE RESTRICT,
    INDEX idx_plan_items_status (status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Facturación
-- ------------------------------------------------------------
CREATE TABLE invoices (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(20) NOT NULL UNIQUE,      -- F-000001
    patient_id      INT UNSIGNED NOT NULL,
    treatment_plan_id INT UNSIGNED NULL,
    issue_date      DATE NOT NULL,
    due_date        DATE NULL,
    subtotal        DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount        DECIMAL(10,2) NOT NULL DEFAULT 0,
    tax_rate        DECIMAL(5,2)  NOT NULL DEFAULT 0,
    tax_amount      DECIMAL(10,2) NOT NULL DEFAULT 0,
    total           DECIMAL(10,2) NOT NULL DEFAULT 0,
    paid            DECIMAL(10,2) NOT NULL DEFAULT 0,
    balance         DECIMAL(10,2) GENERATED ALWAYS AS (total - paid) STORED,
    status          ENUM('draft','issued','partial','paid','cancelled')
                        NOT NULL DEFAULT 'draft',
    notes           TEXT NULL,
    created_by      INT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id)        REFERENCES patients(id)        ON DELETE RESTRICT,
    FOREIGN KEY (treatment_plan_id) REFERENCES treatment_plans(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by)        REFERENCES users(id)           ON DELETE SET NULL,
    INDEX idx_invoices_patient (patient_id),
    INDEX idx_invoices_status (status),
    INDEX idx_invoices_date (issue_date)
) ENGINE=InnoDB;

CREATE TABLE invoice_items (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id      INT UNSIGNED NOT NULL,
    treatment_id    INT UNSIGNED NULL,
    description     VARCHAR(255) NOT NULL,
    tooth_code      VARCHAR(3) NULL,
    quantity        INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price      DECIMAL(10,2) NOT NULL,
    line_total      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (invoice_id)   REFERENCES invoices(id)   ON DELETE CASCADE,
    FOREIGN KEY (treatment_id) REFERENCES treatments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Pagos
-- ------------------------------------------------------------
CREATE TABLE payments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id      INT UNSIGNED NOT NULL,
    paid_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    amount          DECIMAL(10,2) NOT NULL,
    method          ENUM('cash','card','transfer','check','wallet','other') NOT NULL DEFAULT 'cash',
    reference       VARCHAR(120) NULL,
    received_by     INT UNSIGNED NULL,
    notes           VARCHAR(255) NULL,
    FOREIGN KEY (invoice_id)  REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (received_by) REFERENCES users(id)    ON DELETE SET NULL,
    INDEX idx_payments_invoice (invoice_id),
    INDEX idx_payments_date    (paid_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Auditoría
-- ------------------------------------------------------------
CREATE TABLE audit_log (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NULL,
    action      VARCHAR(60) NOT NULL,
    entity      VARCHAR(60) NOT NULL,
    entity_id   VARCHAR(40) NULL,
    payload     JSON NULL,
    ip          VARCHAR(45) NULL,
    user_agent  VARCHAR(255) NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_entity (entity, entity_id),
    INDEX idx_audit_user (user_id, created_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Configuración general (key/value)
-- ------------------------------------------------------------
CREATE TABLE settings (
    `key`       VARCHAR(80) PRIMARY KEY,
    `value`     TEXT NULL,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
