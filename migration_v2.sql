-- NEW TABLES
CREATE TABLE IF NOT EXISTS corporate_entities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    legal_name VARCHAR(255),
    commercial_name VARCHAR(255),
    entity_type VARCHAR(100),
    jurisdiction VARCHAR(100),
    country VARCHAR(100),
    city VARCHAR(100),
    state_province VARCHAR(100),
    address TEXT,
    postal_code VARCHAR(20),
    formation_year INT,
    primary_role TEXT,
    email VARCHAR(255),
    phone VARCHAR(50),
    whatsapp VARCHAR(50),
    website VARCHAR(500),
    linkedin VARCHAR(500),
    calendly_url VARCHAR(500),
    google_maps_url TEXT,
    business_hours TEXT,
    representative_name VARCHAR(255),
    nit_tax_id VARCHAR(100),
    chamber_of_commerce VARCHAR(255),
    employee_count INT,
    operational_capacity TEXT,
    photo_url VARCHAR(500),
    logo_url VARCHAR(500),
    visibility ENUM('draft','internal','private','public','archived') DEFAULT 'public',
    sort_order INT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS leadership (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    position VARCHAR(255),
    biography TEXT,
    photo_url VARCHAR(500),
    linkedin VARCHAR(500),
    email VARCHAR(255),
    areas_of_expertise JSON,
    company VARCHAR(255),
    entity_id INT,
    country VARCHAR(100),
    sort_order INT DEFAULT 0,
    visibility ENUM('draft','internal','private','public','archived') DEFAULT 'public',
    status TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS timeline (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_value DATE,
    title VARCHAR(255),
    description TEXT,
    media_url VARCHAR(500),
    entity_id INT,
    visibility ENUM('draft','internal','private','public','archived') DEFAULT 'public',
    sort_order INT DEFAULT 0,
    status TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS case_studies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    slug VARCHAR(100),
    client_name VARCHAR(255),
    is_anonymous TINYINT DEFAULT 0,
    anonymous_label VARCHAR(255),
    industry VARCHAR(100),
    country VARCHAR(100),
    challenge TEXT,
    solution TEXT,
    implementation TEXT,
    technology TEXT,
    operations TEXT,
    results TEXT,
    metrics JSON,
    testimonial TEXT,
    testimonial_name VARCHAR(255),
    timeline TEXT,
    cover_image VARCHAR(500),
    featured TINYINT DEFAULT 0,
    visibility ENUM('draft','internal','private','public','archived') DEFAULT 'private',
    status TINYINT DEFAULT 1,
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS careers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    slug VARCHAR(100),
    department VARCHAR(100),
    country VARCHAR(100),
    city VARCHAR(100),
    location_type ENUM('remote','hybrid','onsite') DEFAULT 'remote',
    description TEXT,
    requirements TEXT,
    application_url VARCHAR(500),
    visibility ENUM('draft','internal','private','public','archived') DEFAULT 'public',
    status TINYINT DEFAULT 1,
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEED: Corporate Entities
INSERT INTO corporate_entities (legal_name, commercial_name, entity_type, jurisdiction, country, city, state_province, address, postal_code, formation_year, primary_role, email, sort_order)
VALUES 
('Intsolcom, LLC', 'INTSOLCOM LLC', 'legal_entity', 'Delaware', 'United States', 'Miami', 'Florida', '390 NE 191st St, STE 17284, Miami, FL 33179', '33179', 2026, 'Strategic business development, international contracting, product management, AI, automation, partnerships', 'info@intsolcom.com', 10),
('International Solutions Companies S.A.S.', 'Intsolcom SAS', 'legal_entity', 'Colombia', 'Colombia', 'Barranquilla', 'Atlantico', 'Carrera 53 #79-01, Barranquilla, Colombia', NULL, 2024, 'Operational delivery center — BPO operations, AI data annotation, talent management, QA, training, delivery', 'info@intsolcom.com', 20);

-- SEED: Timeline
INSERT INTO timeline (date_value, title, description, sort_order, visibility)
VALUES 
('2024-01-01', 'Foundation of Colombian Operations', 'International Solutions Companies S.A.S. established in Barranquilla, Colombia as an operational delivery center.', 10, 'public'),
('2024-06-01', 'BPO Capabilities Development', 'Launched specialized BPO services including administrative support, customer operations, and back office.', 20, 'public'),
('2025-01-01', 'AI Data Annotation Expansion', 'Expanded into large-scale AI data annotation operations including video and sports annotation.', 30, 'public'),
('2026-01-01', 'INTSOLCOM LLC Formation', 'Established Intsolcom, LLC in Delaware as the strategic business entity for international operations.', 40, 'public'),
('2026-06-01', 'Technology Product Portfolio', 'Development and launch of proprietary technology products: WONTIA CRM, MACROPONDER, and IA Annotation Manager.', 50, 'public');

-- UPDATE: Settings
UPDATE settings SET value = '390 NE 191st St, STE 17284, Miami, FL 33179' WHERE `key` = 'contact_usa_address';
UPDATE settings SET value = 'The Intsolcom business ecosystem combines strategic presence in the United States with specialized operational delivery capabilities in Colombia.' WHERE `key` = 'footer_desc';

-- UPDATE: Business Units
UPDATE business_units SET entity_type = 'legal_entity', commercial_brand = 'INTSOLCOM SAS', visibility = 'public' WHERE slug = 'intsolcom-sas';
UPDATE business_units SET entity_type = 'division', visibility = 'public' WHERE slug = 'technology-division';

-- INSERT: Marcas BPO as brand
INSERT INTO business_units (name, commercial_brand, slug, description, entity_type, hero_title, hero_subtitle, capabilities, icon, order_num, visibility) 
VALUES
('Business Operations', 'Marcas BPO', 'business-operations', 'Marcas BPO is the commercial brand for Intsolcom''s business operations ecosystem. Powered by Intsolcom SAS in Colombia, it delivers specialized BPO services, AI data operations, and operational support to clients worldwide.', 'brand', 'Business Operations & BPO Services', 'Specialized operational delivery powered by Intsolcom SAS — Colombia', 
'["Administrative Support","Sales Operations","Marketing Operations","Customer Operations","Back Office","AI Data Services"]', 
'building', 15, 'public');

-- UPDATE: Products
UPDATE products SET ownership_type = 'intsolcom_product', verification = 'confirmed' WHERE slug = 'wontia-crm';
UPDATE products SET ownership_type = 'intsolcom_product', verification = 'confirmed' WHERE slug = 'macroponder';
UPDATE products SET ownership_type = 'intsolcom_product', verification = 'verified' WHERE slug = 'ia-annotation-manager';
