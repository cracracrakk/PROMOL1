-- ============================================================
-- DentalCore v2 - Migración: nuevas funcionalidades
-- Ejecutar después de schema.sql + seed.sql
-- ============================================================
USE dental_core;

-- ------------------------------------------------------------
-- Portal del paciente (acceso con magic-link)
-- ------------------------------------------------------------
ALTER TABLE patients
    ADD COLUMN portal_token         VARCHAR(120) NULL AFTER notes,
    ADD COLUMN portal_token_expires DATETIME     NULL,
    ADD COLUMN portal_last_login    DATETIME     NULL,
    ADD INDEX  idx_patients_portal_token (portal_token);

-- ------------------------------------------------------------
-- Tokens públicos para confirmar / cancelar / encuesta de citas
-- ------------------------------------------------------------
CREATE TABLE appointment_tokens (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id  INT UNSIGNED NOT NULL,
    token           VARCHAR(80) NOT NULL UNIQUE,
    action          ENUM('confirm','cancel','survey') NOT NULL,
    used_at         DATETIME NULL,
    expires_at      DATETIME NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    INDEX idx_tokens_action (appointment_id, action)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Log de comunicaciones (email / sms / whatsapp)
-- ------------------------------------------------------------
CREATE TABLE communications (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NULL,
    appointment_id  INT UNSIGNED NULL,
    channel         ENUM('email','sms','whatsapp','call') NOT NULL,
    direction       ENUM('outbound','inbound') NOT NULL DEFAULT 'outbound',
    subject         VARCHAR(180) NULL,
    body            TEXT NULL,
    `to`            VARCHAR(180) NULL,
    status          ENUM('queued','sent','failed','received') NOT NULL DEFAULT 'queued',
    sent_at         DATETIME NULL,
    error           VARCHAR(255) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id)     REFERENCES patients(id)     ON DELETE SET NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    INDEX idx_comms_patient (patient_id, created_at),
    INDEX idx_comms_status (status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Periodontograma (sondaje periodontal)
-- 6 mediciones por pieza: vestibular mesial/central/distal,
-- lingual mesial/central/distal
-- ------------------------------------------------------------
CREATE TABLE periodontograms (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    exam_date       DATE NOT NULL,
    examiner_id     INT UNSIGNED NULL,
    notes           TEXT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id)  REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (examiner_id) REFERENCES users(id)    ON DELETE SET NULL,
    INDEX idx_perio_patient (patient_id, exam_date)
) ENGINE=InnoDB;

CREATE TABLE periodontogram_teeth (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    periodontogram_id INT UNSIGNED NOT NULL,
    tooth_code      VARCHAR(3) NOT NULL,
    pd_vm           TINYINT NULL, pd_vc TINYINT NULL, pd_vd TINYINT NULL,  -- profundidad de bolsa vestibular
    pd_lm           TINYINT NULL, pd_lc TINYINT NULL, pd_ld TINYINT NULL,  -- lingual / palatina
    rec_vm          TINYINT NULL, rec_vc TINYINT NULL, rec_vd TINYINT NULL,
    rec_lm          TINYINT NULL, rec_lc TINYINT NULL, rec_ld TINYINT NULL,
    bleeding        TINYINT(1) NOT NULL DEFAULT 0,
    plaque          TINYINT(1) NOT NULL DEFAULT 0,
    suppuration     TINYINT(1) NOT NULL DEFAULT 0,
    mobility        TINYINT NULL,        -- 0..3
    furcation       TINYINT NULL,        -- 0..3
    FOREIGN KEY (periodontogram_id) REFERENCES periodontograms(id) ON DELETE CASCADE,
    UNIQUE KEY uq_perio_tooth (periodontogram_id, tooth_code)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Documentos del paciente (rayos X, fotos, consentimientos)
-- ------------------------------------------------------------
CREATE TABLE patient_documents (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    type            ENUM('xray','photo','consent','document','other') NOT NULL DEFAULT 'document',
    title           VARCHAR(180) NOT NULL,
    description     VARCHAR(255) NULL,
    filename        VARCHAR(255) NOT NULL,
    original_name   VARCHAR(255) NULL,
    mime            VARCHAR(80) NULL,
    size_bytes      INT UNSIGNED NULL,
    tooth_code      VARCHAR(3) NULL,
    taken_at        DATE NULL,
    uploaded_by     INT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id)  REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)    ON DELETE SET NULL,
    INDEX idx_docs_patient (patient_id, type)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Recetas / prescripciones digitales
-- ------------------------------------------------------------
CREATE TABLE prescriptions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(20) NOT NULL UNIQUE,
    patient_id      INT UNSIGNED NOT NULL,
    dentist_id      INT UNSIGNED NOT NULL,
    issue_date      DATE NOT NULL,
    diagnosis       VARCHAR(255) NULL,
    notes           TEXT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (dentist_id) REFERENCES users(id)    ON DELETE RESTRICT,
    INDEX idx_rx_patient (patient_id, issue_date)
) ENGINE=InnoDB;

CREATE TABLE prescription_items (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT UNSIGNED NOT NULL,
    drug            VARCHAR(180) NOT NULL,
    dosage          VARCHAR(120) NULL,        -- ej: 500 mg
    frequency       VARCHAR(120) NULL,        -- ej: cada 8 horas
    duration        VARCHAR(120) NULL,        -- ej: 7 días
    instructions    VARCHAR(255) NULL,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Caja diaria
-- ------------------------------------------------------------
CREATE TABLE cash_sessions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opened_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at       DATETIME NULL,
    opening_amount  DECIMAL(10,2) NOT NULL DEFAULT 0,
    closing_amount  DECIMAL(10,2) NULL,
    expected_cash   DECIMAL(10,2) NULL,
    difference      DECIMAL(10,2) NULL,
    notes           TEXT NULL,
    opened_by       INT UNSIGNED NOT NULL,
    closed_by       INT UNSIGNED NULL,
    FOREIGN KEY (opened_by) REFERENCES users(id),
    FOREIGN KEY (closed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_cash_opened (opened_at)
) ENGINE=InnoDB;

CREATE TABLE expenses (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expense_date    DATE NOT NULL,
    category        VARCHAR(80) NULL,
    description     VARCHAR(255) NOT NULL,
    amount          DECIMAL(10,2) NOT NULL,
    method          ENUM('cash','card','transfer','check','other') NOT NULL DEFAULT 'cash',
    cash_session_id INT UNSIGNED NULL,
    receipt_doc     VARCHAR(255) NULL,
    created_by      INT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cash_session_id) REFERENCES cash_sessions(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by)      REFERENCES users(id)         ON DELETE SET NULL,
    INDEX idx_expenses_date (expense_date)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Aseguradoras / convenios
-- ------------------------------------------------------------
CREATE TABLE insurance_providers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(160) NOT NULL,
    contact_name    VARCHAR(120) NULL,
    phone           VARCHAR(30) NULL,
    email           VARCHAR(180) NULL,
    notes           TEXT NULL,
    is_active       TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE patient_insurance (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    insurance_id    INT UNSIGNED NOT NULL,
    policy_number   VARCHAR(80) NULL,
    coverage_pct    DECIMAL(5,2) NOT NULL DEFAULT 0,
    expires_at      DATE NULL,
    is_primary      TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (patient_id)   REFERENCES patients(id)            ON DELETE CASCADE,
    FOREIGN KEY (insurance_id) REFERENCES insurance_providers(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Inventario de insumos / materiales
-- ------------------------------------------------------------
CREATE TABLE products (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku             VARCHAR(40) UNIQUE,
    name            VARCHAR(160) NOT NULL,
    category        VARCHAR(80) NULL,
    unit            VARCHAR(30) NOT NULL DEFAULT 'unidad',
    stock           DECIMAL(10,2) NOT NULL DEFAULT 0,
    min_stock       DECIMAL(10,2) NOT NULL DEFAULT 0,
    cost            DECIMAL(10,2) NOT NULL DEFAULT 0,
    supplier        VARCHAR(160) NULL,
    notes           TEXT NULL,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_products_category (category),
    INDEX idx_products_active (is_active)
) ENGINE=InnoDB;

CREATE TABLE product_movements (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id      INT UNSIGNED NOT NULL,
    movement_date   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    type            ENUM('in','out','adjust') NOT NULL,
    quantity        DECIMAL(10,2) NOT NULL,
    reason          VARCHAR(255) NULL,
    user_id         INT UNSIGNED NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)   ON DELETE SET NULL,
    INDEX idx_mov_product (product_id, movement_date)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Órdenes de laboratorio dental
-- ------------------------------------------------------------
CREATE TABLE lab_orders (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(20) NOT NULL UNIQUE,
    patient_id      INT UNSIGNED NOT NULL,
    dentist_id      INT UNSIGNED NOT NULL,
    lab_name        VARCHAR(160) NOT NULL,
    work_description TEXT NOT NULL,
    tooth_codes     VARCHAR(120) NULL,
    sent_date       DATE NULL,
    due_date        DATE NULL,
    received_date   DATE NULL,
    cost            DECIMAL(10,2) NOT NULL DEFAULT 0,
    status          ENUM('draft','sent','in_progress','received','installed','cancelled')
                        NOT NULL DEFAULT 'draft',
    notes           TEXT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT,
    FOREIGN KEY (dentist_id) REFERENCES users(id),
    INDEX idx_lab_status (status),
    INDEX idx_lab_due (due_date)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Plantillas de consentimiento + firmas digitales
-- ------------------------------------------------------------
CREATE TABLE consent_templates (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(160) NOT NULL,
    body            MEDIUMTEXT NOT NULL,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE patient_consents (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    template_id     INT UNSIGNED NULL,
    title           VARCHAR(180) NOT NULL,
    body            MEDIUMTEXT NOT NULL,
    treatment       VARCHAR(180) NULL,
    signed_at       DATETIME NULL,
    signature_data  MEDIUMTEXT NULL,
    created_by      INT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id)  REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES consent_templates(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by)  REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tareas internas (to-do)
-- ------------------------------------------------------------
CREATE TABLE tasks (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(180) NOT NULL,
    description     TEXT NULL,
    assigned_to     INT UNSIGNED NULL,
    related_patient_id INT UNSIGNED NULL,
    due_date        DATE NULL,
    priority        ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    status          ENUM('open','in_progress','done','cancelled') NOT NULL DEFAULT 'open',
    created_by      INT UNSIGNED NULL,
    completed_at    DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to)        REFERENCES users(id)    ON DELETE SET NULL,
    FOREIGN KEY (related_patient_id) REFERENCES patients(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by)         REFERENCES users(id)    ON DELETE SET NULL,
    INDEX idx_tasks_assigned (assigned_to, status),
    INDEX idx_tasks_due (due_date, status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Encuestas / NPS post-visita
-- ------------------------------------------------------------
CREATE TABLE surveys (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id  INT UNSIGNED NOT NULL,
    score           TINYINT NULL,        -- 0..10 NPS
    comment         TEXT NULL,
    submitted_at    DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Datos iniciales
-- ------------------------------------------------------------
INSERT INTO insurance_providers (name) VALUES
('Particular'), ('Seguros del País'), ('MAPFRE Dental'), ('Pan-American Life');

INSERT INTO consent_templates (name, body) VALUES
('Consentimiento general',
'Yo, [PACIENTE], autorizo al Dr./Dra. [ODONTOLOGO] a realizar el tratamiento dental que ha sido explicado y comprendido, así como las posibles complicaciones, beneficios y alternativas. Me comprometo a seguir las indicaciones post-operatorias.'),
('Consentimiento de extracción',
'Yo, [PACIENTE], autorizo la extracción de la(s) pieza(s) [PIEZAS]. Entiendo los riesgos: sangrado, infección, alveolitis, fractura mandibular o lesión nerviosa.'),
('Consentimiento de endodoncia',
'Yo, [PACIENTE], autorizo el tratamiento de conductos en la pieza [PIEZA]. Comprendo que el éxito no está garantizado al 100% y puede requerir retratamiento o cirugía apical.');

INSERT INTO products (sku, name, category, unit, stock, min_stock, cost) VALUES
('GUA-001', 'Guantes desechables (caja 100)', 'Bioseguridad', 'caja', 12, 5, 250.00),
('CUB-001', 'Cubrebocas tricapa (caja 50)',   'Bioseguridad', 'caja',  8, 4, 150.00),
('ANE-001', 'Anestesia Lidocaína 2%',          'Anestesia',    'cartucho', 50, 20, 35.00),
('RES-001', 'Resina compuesta A2 (4g)',        'Restauración', 'jeringa', 6, 3, 850.00),
('SUT-001', 'Sutura Seda 3-0',                 'Cirugía',      'sobre', 25, 10, 45.00),
('AGU-001', 'Aguja dental corta 27G (100u)',   'Anestesia',    'caja',  4, 2, 220.00);

-- Settings adicionales para branding y notificaciones
INSERT INTO settings (`key`, `value`) VALUES
('clinic_logo',           ''),
('clinic_color_primary',  '#0ea5e9'),
('clinic_color_accent',   '#06b6d4'),
('reminder_hours_before', '24'),
('reminder_enabled',      '1'),
('reminder_channel',      'email'),
('recall_months',         '6'),
('survey_enabled',        '1'),
('setup_completed',       '1'),
('whatsapp_api_url',      ''),
('whatsapp_api_token',    '')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
