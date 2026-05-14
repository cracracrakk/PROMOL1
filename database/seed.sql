-- Spa Serenity - Datos de ejemplo

-- Configuración del sitio
INSERT INTO site_settings (`key`, `value`) VALUES
('spa_name',        'Spa Serenity'),
('spa_tagline',     'Tu santuario de bienestar'),
('spa_description', 'Un oasis de tranquilidad en el corazón de la ciudad. Especialistas en masajes terapéuticos, tratamientos faciales y experiencias holísticas que renuevan cuerpo y mente.'),
('spa_address',     'Calle del Bienestar 12, 28001 Madrid'),
('spa_phone',       '+34 910 000 000'),
('spa_email',       'hola@spaserenity.es'),
('spa_whatsapp',    '34600000000'),
('spa_instagram',   '@spaserenity'),
('spa_hours',       'Lun-Vie 10:00 - 21:00\nSáb 10:00 - 20:00\nDom Cerrado'),
('hero_title',      'Renueva cuerpo y mente'),
('hero_subtitle',   'Tratamientos personalizados que despiertan tus sentidos en un entorno de pura serenidad.');

-- Usuarios (contraseña: admin123 -> hash bcrypt generado)
-- password: admin123
INSERT INTO users (name, email, password, role, phone, color) VALUES
('Administrador', 'admin@spa.local',  '$2y$12$hKTLI796CYpuEpwpfnY8nez7IwNGX/ru3HSMzQCTahOKGSObjcXdy', 'admin',      '+34 600 100 001', '#6b8a7a'),
('María Recepción', 'maria@spa.local','$2y$12$hKTLI796CYpuEpwpfnY8nez7IwNGX/ru3HSMzQCTahOKGSObjcXdy', 'recepcion',  '+34 600 100 002', '#c9a96e'),
('Laura Terapeuta', 'laura@spa.local','$2y$12$hKTLI796CYpuEpwpfnY8nez7IwNGX/ru3HSMzQCTahOKGSObjcXdy', 'terapeuta',  '+34 600 100 003', '#a87aaf'),
('Carmen Terapeuta','carmen@spa.local','$2y$12$hKTLI796CYpuEpwpfnY8nez7IwNGX/ru3HSMzQCTahOKGSObjcXdy', 'terapeuta',  '+34 600 100 004', '#6f9bbf');

-- Categorías de servicios
INSERT INTO service_categories (name, slug, description, icon, sort_order) VALUES
('Masajes',        'masajes',     'Técnicas terapéuticas y relajantes',  'spa',     1),
('Faciales',       'faciales',    'Tratamientos para el cuidado del rostro','face', 2),
('Corporales',     'corporales',  'Exfoliaciones, envolturas y rituales','body',    3),
('Manos y Pies',   'manos-pies',  'Manicura, pedicura y reflexología',   'hand',    4),
('Rituales',       'rituales',    'Experiencias completas multisensoriales','candle',5);

-- Servicios
INSERT INTO services (category_id, name, description, duration_minutes, buffer_minutes, price, cost, tax_rate, requires_room_type, bookable_online, sort_order) VALUES
(1, 'Masaje Relajante',            'Masaje suave con aceites esenciales para liberar tensiones del día a día.',     60, 15, 55.00, 8.00, 21, 'masaje',   1, 1),
(1, 'Masaje Descontracturante',    'Técnica profunda para aliviar nudos musculares y dolor.',                       60, 15, 65.00, 8.00, 21, 'masaje',   1, 2),
(1, 'Masaje con Piedras Calientes','Combina termoterapia y masaje para una relajación profunda.',                  75, 20, 80.00,12.00, 21, 'masaje',   1, 3),
(1, 'Masaje Pareja',               'Disfruta de un masaje juntos en cabina doble.',                                 60, 20,120.00,20.00, 21, 'masaje',   1, 4),
(2, 'Limpieza Facial Profunda',    'Exfoliación, extracción y mascarilla hidratante.',                              75, 15, 70.00,10.00, 21, 'facial',   1, 1),
(2, 'Facial Antiedad',             'Tratamiento con vitamina C y colágeno para piel madura.',                      90, 15, 95.00,15.00, 21, 'facial',   1, 2),
(2, 'Facial Hidratante',           'Hidratación intensa con ácido hialurónico.',                                    60, 15, 65.00, 9.00, 21, 'facial',   1, 3),
(3, 'Exfoliación Corporal',        'Renueva tu piel con sales del Himalaya y aceites.',                            45, 15, 50.00, 7.00, 21, 'humeda',   1, 1),
(3, 'Envoltura de Chocolate',      'Experiencia gourmet hidratante y antioxidante.',                               75, 20, 85.00,14.00, 21, 'humeda',   1, 2),
(4, 'Manicura Spa',                'Manicura completa con masaje de manos.',                                       45, 10, 30.00, 4.00, 21, 'manicura', 1, 1),
(4, 'Pedicura Spa',                'Pedicura completa con baño relajante.',                                        60, 10, 38.00, 5.00, 21, 'manicura', 1, 2),
(4, 'Reflexología Podal',          'Masaje terapéutico en puntos energéticos de los pies.',                        45, 10, 45.00, 5.00, 21, 'manicura', 1, 3),
(5, 'Ritual Serenity',             'Nuestra experiencia estrella: masaje + facial + envoltura. 3 horas.',         180, 30,180.00,30.00, 21, 'masaje',   1, 1),
(5, 'Ritual Romance',              'Para dos: cava, masaje en pareja y jacuzzi privado.',                         120, 30,220.00,40.00, 21, 'masaje',   1, 2);

-- Horarios laborales (lunes-viernes 10:00-20:00, sábado 10:00-18:00)
INSERT INTO user_schedules (user_id, weekday, start_time, end_time) VALUES
(3,1,'10:00','20:00'),(3,2,'10:00','20:00'),(3,3,'10:00','20:00'),(3,4,'10:00','20:00'),(3,5,'10:00','20:00'),(3,6,'10:00','18:00'),
(4,1,'10:00','20:00'),(4,2,'10:00','20:00'),(4,3,'10:00','20:00'),(4,4,'10:00','20:00'),(4,5,'10:00','20:00'),(4,6,'10:00','18:00');

-- Cabinas
INSERT INTO rooms (name, room_type, capacity, description) VALUES
('Cabina Bambú',    'masaje',   1, 'Cabina individual con decoración zen'),
('Cabina Oriental', 'masaje',   1, 'Decoración asiática con aromas suaves'),
('Cabina Doble',    'masaje',   2, 'Para masajes en pareja'),
('Sala Lotus',      'facial',   1, 'Equipada con vapor y alta frecuencia'),
('Sala Crystal',    'facial',   1, 'Tratamientos faciales premium'),
('Sala Húmeda',     'humeda',   1, 'Ducha vichy y baños'),
('Estudio Nails',   'manicura', 2, 'Manicura y pedicura');

-- Categorías de productos
INSERT INTO product_categories (name, description) VALUES
('Aceites Esenciales', 'Aceites para masaje y aromaterapia'),
('Cosmética Facial',   'Cremas, sérums y mascarillas'),
('Cuidado Corporal',   'Cremas hidratantes, exfoliantes'),
('Velas y Aromas',     'Productos para el hogar'),
('Tés e Infusiones',   'Tés relajantes y detox');

-- Proveedores
INSERT INTO suppliers (name, contact, phone, email) VALUES
('Aromas Naturales SL', 'Pedro Gómez',   '+34 911 111 111', 'pedidos@aromas.es'),
('Cosmética Profesional','Ana López',    '+34 911 222 222', 'ventas@cosmpro.es'),
('Bienestar Distribución','Luis Marín',  '+34 911 333 333', 'info@biendis.es');

-- Productos
INSERT INTO products (category_id, supplier_id, sku, name, description, cost_price, sale_price, tax_rate, stock, stock_min, is_consumable, sellable) VALUES
(1, 1, 'AC-LAV-100', 'Aceite Esencial Lavanda 100ml',  'Aceite puro de lavanda, ideal para relajación.',     8.00, 22.00, 21, 25, 5, 1, 1),
(1, 1, 'AC-EUC-100', 'Aceite Esencial Eucalipto 100ml','Refrescante y respiratorio.',                        7.50, 20.00, 21, 18, 5, 1, 1),
(1, 1, 'AC-MAS-500', 'Aceite de Masaje Neutro 500ml',  'Base para mezclas profesionales.',                   6.00, 18.00, 21, 12, 8, 1, 0),
(2, 2, 'CR-HID-50',  'Crema Hidratante Facial 50ml',   'Con ácido hialurónico y vitamina E.',               14.00, 38.00, 21, 30, 5, 0, 1),
(2, 2, 'SE-VIT-30',  'Sérum Vitamina C 30ml',          'Antioxidante e iluminador.',                        18.00, 48.00, 21, 22, 5, 0, 1),
(2, 2, 'MA-ARC-100', 'Mascarilla de Arcilla 100ml',    'Purificante para piel mixta.',                       9.00, 24.00, 21, 14, 5, 1, 1),
(3, 3, 'EX-SAL-300', 'Exfoliante Sales Himalaya 300g', 'Mineralizante y revitalizante.',                    11.00, 28.00, 21,  8, 5, 1, 1),
(3, 3, 'CR-COR-200', 'Crema Corporal Vainilla 200ml',  'Hidratación de larga duración.',                    10.00, 26.00, 21, 20, 5, 0, 1),
(4, 3, 'VE-LAV',     'Vela Aromática Lavanda',         'Vela de soja, 40 horas de duración.',                7.00, 19.00, 21, 16, 8, 0, 1),
(4, 3, 'VE-CIT',     'Vela Aromática Cítrica',         'Energizante y purificadora.',                        7.00, 19.00, 21,  4, 8, 0, 1),
(5, 3, 'TE-REL',     'Té Relajante (25 bolsitas)',     'Mezcla de manzanilla, tila y lavanda.',              5.00, 14.00, 21, 30, 10, 0, 1),
(5, 3, 'TE-DET',     'Té Detox (25 bolsitas)',         'Diente de león, jengibre y limón.',                  5.00, 14.00, 21, 12, 10, 0, 1);

-- Bonos
INSERT INTO service_packs (name, description, service_id, sessions_total, price, valid_months) VALUES
('Bono 5 Masajes Relajantes', '5 sesiones de masaje relajante de 60 min. Ahorra 25€', 1, 5, 250.00, 6),
('Bono 10 Faciales Hidratantes','10 faciales hidratantes. Ahorra 100€',                 7, 10, 550.00, 12),
('Bono 5 Reflexologías',      '5 sesiones de reflexología podal.',                     12, 5, 200.00, 6);

-- Clientes ejemplo
INSERT INTO customers (first_name, last_name, email, phone, birthdate, gender, vip, loyalty_points, accepts_marketing, notes) VALUES
('Sofía',  'Martín García',   'sofia.martin@email.es',  '+34 611 111 111', '1985-06-15', 'F', 1, 250, 1, 'Cliente VIP desde 2022. Prefiere a Laura.'),
('Carlos', 'Ruiz Fernández',  'carlos.ruiz@email.es',   '+34 622 222 222', '1978-11-03', 'M', 0,  80, 1, NULL),
('Elena',  'Sánchez López',   'elena.s@email.es',       '+34 633 333 333', '1990-03-22', 'F', 0, 120, 1, 'Alérgica a aceites de cítricos.'),
('Pablo',  'Gómez Torres',    'pablo.g@email.es',       '+34 644 444 444', '1982-09-10', 'M', 0,  45, 0, NULL),
('Andrea', 'Castro Vega',     'andrea.cv@email.es',     '+34 655 555 555', '1995-12-01', 'F', 0,  60, 1, NULL),
('Miguel', 'Hernández Ruiz',  'miguel.h@email.es',      '+34 666 666 666', '1988-07-19', 'M', 0,  30, 1, NULL),
('Lucía',  'Pérez Moreno',    'lucia.pm@email.es',      '+34 677 777 777', '1992-04-25', 'F', 1, 310, 1, 'VIP. Le encanta el ritual Serenity.'),
('Javier', 'Díaz Castaño',    'javier.dc@email.es',     '+34 688 888 888', '1980-02-14', 'M', 0,  15, 0, NULL);

UPDATE customers SET allergies = 'Cítricos' WHERE first_name = 'Elena';

-- Testimonios
INSERT INTO testimonials (author, rating, comment) VALUES
('Sofía M.',  5, 'El mejor spa de la ciudad. Cada visita es una experiencia transformadora. El masaje con piedras calientes es espectacular.'),
('Carlos R.', 5, 'Profesionalidad, ambiente impecable y trato exquisito. Salgo siempre como nuevo.'),
('Lucía P.',  5, 'El Ritual Serenity merece cada euro. Tres horas de puro paraíso. Laura es una maga con las manos.'),
('Andrea C.', 4, 'Muy buena experiencia, las cabinas son preciosas y el personal súper atento. Repetiré seguro.');

-- Galería (rutas placeholder)
INSERT INTO gallery (image, caption, sort_order) VALUES
('hero-1.jpg', 'Recepción',   1),
('hero-2.jpg', 'Cabina Bambú',2),
('hero-3.jpg', 'Sala Lotus',  3),
('hero-4.jpg', 'Zona zen',    4);

-- Códigos promocionales
INSERT INTO promo_codes (code, description, discount_type, discount_value, valid_until, max_uses, active) VALUES
('BIENVENIDA10', '10% de descuento para nuevos clientes',  'percent', 10.00, '2026-12-31', 100, 1),
('VERANO20',     '20% en tratamientos corporales',         'percent', 20.00, '2026-09-30',  50, 1);

-- Autorización CAI de ejemplo (configurable desde el módulo Sistema)
INSERT INTO sar_authorizations (document_type, cai, resolucion, establecimiento, punto_emision, tipo_documento, rango_inicial, rango_final, next_number, fecha_limite, active) VALUES
('factura',      'A1B2C3-D4E5F6-G7H8I9-J0K1L2-M3N4O5-P6Q7R8-S9T0', 'DEI-SAR-2024-001234', '000', '001', '01', 1, 10000, 1, '2026-12-31', 1),
('nota_credito', 'NC1234-567890-ABCDEF-GHIJKL-MNOPQR-STUVWX-YZAB',  'DEI-SAR-2024-001235', '000', '001', '04', 1,  1000, 1, '2026-12-31', 1),
('nota_debito',  'ND9876-543210-FEDCBA-LKJIHG-RQPONM-XWVUTS-BAZY',  'DEI-SAR-2024-001236', '000', '001', '05', 1,  1000, 1, '2026-12-31', 1),
('recibo',       'RC1111-222233-334444-555566-667777-788888-9999',  'DEI-SAR-2024-001237', '000', '001', '06', 1,  5000, 1, '2026-12-31', 1);
