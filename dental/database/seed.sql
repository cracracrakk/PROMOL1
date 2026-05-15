-- ============================================================
-- DentalCore - Datos iniciales
-- Ejecutar despues de schema.sql
-- ============================================================
USE dental_core;

-- Usuario admin por defecto (password: admin123)
INSERT INTO users (name, email, password_hash, role, specialty) VALUES
('Administrador', 'admin@dental.local',
 '$2y$10$bsciVzZ/Y38SFDOz3lQBIuWk0eTiWOTitQX1Eb.EI5tQhknHsRQDC',
 'admin', NULL),
('Dr. Juan Perez', 'juan.perez@dental.local',
 '$2y$10$bsciVzZ/Y38SFDOz3lQBIuWk0eTiWOTitQX1Eb.EI5tQhknHsRQDC',
 'dentist', 'Odontologia General'),
('Dra. Maria Lopez', 'maria.lopez@dental.local',
 '$2y$10$bsciVzZ/Y38SFDOz3lQBIuWk0eTiWOTitQX1Eb.EI5tQhknHsRQDC',
 'dentist', 'Ortodoncia'),
('Recepcion', 'recepcion@dental.local',
 '$2y$10$bsciVzZ/Y38SFDOz3lQBIuWk0eTiWOTitQX1Eb.EI5tQhknHsRQDC',
 'reception', NULL);

-- Sillones / boxes
INSERT INTO rooms (name, color) VALUES
('Box 1', '#3b82f6'),
('Box 2', '#10b981'),
('Box 3', '#f59e0b');

-- Catalogo de tratamientos basicos
INSERT INTO treatments (code, name, category, default_price, duration_min, requires_tooth) VALUES
('CONS-001', 'Consulta y diagnostico',          'Diagnostico',    500.00, 30, 0),
('PROF-001', 'Profilaxis dental',                'Preventiva',     800.00, 45, 0),
('PROF-002', 'Aplicacion de fluor',              'Preventiva',     400.00, 20, 0),
('PROF-003', 'Sellantes (por pieza)',            'Preventiva',     300.00, 20, 1),
('REST-001', 'Resina (1 superficie)',            'Restauradora',   600.00, 40, 1),
('REST-002', 'Resina (2 superficies)',           'Restauradora',   900.00, 60, 1),
('REST-003', 'Resina (3 superficies)',           'Restauradora',  1200.00, 75, 1),
('REST-004', 'Amalgama',                         'Restauradora',   500.00, 40, 1),
('ENDO-001', 'Endodoncia unirradicular',         'Endodoncia',    3500.00, 90, 1),
('ENDO-002', 'Endodoncia multirradicular',       'Endodoncia',    5000.00,120, 1),
('CIRU-001', 'Extraccion simple',                'Cirugia',       1000.00, 45, 1),
('CIRU-002', 'Extraccion de tercer molar',       'Cirugia',       3000.00, 90, 1),
('PROT-001', 'Corona porcelana',                 'Protesis',      7000.00,120, 1),
('PROT-002', 'Puente fijo (por unidad)',         'Protesis',      6000.00,120, 1),
('PROT-003', 'Protesis total acrilico',          'Protesis',     12000.00,180, 0),
('ORTO-001', 'Brackets metalicos (instalacion)', 'Ortodoncia',   15000.00,120, 0),
('ORTO-002', 'Control mensual de ortodoncia',    'Ortodoncia',     800.00, 30, 0),
('ESTE-001', 'Blanqueamiento dental',            'Estetica',      4500.00, 90, 0),
('ESTE-002', 'Carillas (por pieza)',             'Estetica',      6500.00,120, 1),
('IMPL-001', 'Implante dental',                  'Cirugia',      25000.00,150, 1);

-- Configuracion del negocio
INSERT INTO settings (`key`, `value`) VALUES
('clinic_name',        'DentalCore Clinic'),
('clinic_tagline',     'Tu sonrisa, nuestra prioridad'),
('clinic_address',     'Av. Principal 123, Ciudad'),
('clinic_phone',       '+504 0000-0000'),
('clinic_email',       'info@dental.local'),
('clinic_rtn',         '00000000000000'),
('currency_symbol',    'L'),
('currency_code',      'HNL'),
('tax_rate',           '15'),
('appointment_step',   '30'),
('working_hours_start','08:00'),
('working_hours_end',  '18:00'),
('working_days',       '1,2,3,4,5,6');

-- Pacientes de ejemplo
INSERT INTO patients (code, first_name, last_name, document_type, document_number,
                      birth_date, gender, blood_type, email, phone, mobile,
                      address, city, occupation, emergency_name, emergency_phone, emergency_rel,
                      created_by) VALUES
('P-000001', 'Carlos',  'Mendoza',   'dni', '0801-1985-12345', '1985-04-12', 'male',   'O+',
  'carlos.m@example.com', '2222-3333', '9999-1111',
  'Col. Palmira, Casa 5', 'Tegucigalpa', 'Contador',
  'Lucia Mendoza', '9999-1112', 'Esposa', 1),
('P-000002', 'Sofia',   'Ramirez',   'dni', '0801-1992-67890', '1992-09-23', 'female', 'A+',
  'sofia.r@example.com',  '2222-4444', '9999-2222',
  'Res. Las Hadas', 'Tegucigalpa', 'Disenadora',
  'Pedro Ramirez', '9999-2223', 'Padre', 1),
('P-000003', 'Luis',    'Cardenas',  'dni', '0801-1978-33333', '1978-01-05', 'male',   'B+',
  'luis.c@example.com',   '2222-5555', '9999-3333',
  'Col. Kennedy', 'Tegucigalpa', 'Ingeniero',
  'Ana Cardenas', '9999-3334', 'Hermana', 1);

INSERT INTO patient_medical_history (patient_id, allergies, chronic_conditions, smokes, hypertension) VALUES
(1, 'Penicilina', NULL, 0, 1),
(2, NULL, NULL, 0, 0),
(3, NULL, 'Diabetes tipo 2', 1, 0);

-- Citas de ejemplo (proximos dias)
INSERT INTO appointments (patient_id, dentist_id, room_id, treatment_id,
                          starts_at, ends_at, status, reason, created_by) VALUES
(1, 2, 1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 9  HOUR,
            DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 9  HOUR + INTERVAL 30 MINUTE,
            'confirmed', 'Consulta general', 4),
(2, 3, 2, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 10 HOUR,
            DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 10 HOUR + INTERVAL 45 MINUTE,
            'scheduled', 'Limpieza dental', 4),
(3, 2, 1, 11, DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 11 HOUR,
            DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 11 HOUR + INTERVAL 45 MINUTE,
            'scheduled', 'Extraccion molar', 4);

-- Odontograma inicial: todas las piezas sanas para los pacientes 1 y 2
-- (Se crea automaticamente al consultar; aqui inyectamos algunos casos)
INSERT INTO odontogram (patient_id, tooth_code, status, surface_o, updated_by) VALUES
(1, '16', 'filled',   'amalgam',  2),
(1, '26', 'caries',   NULL,       2),
(1, '36', 'root_canal', NULL,     2),
(2, '11', 'healthy',  NULL,       3),
(3, '18', 'extracted',NULL,       2),
(3, '48', 'to_extract',NULL,      2);
