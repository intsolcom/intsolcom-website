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
('2026-06-01', 'Technology Product Portfolio', 'Development and launch of proprietary technology products: Wontia AIP, MACROPONDER, and IA Annotation Manager.', 50, 'public');

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

-- ============================================================
-- v3: WONTIA REPOSITIONING — CRM → Wontia AIP (AIS ecosystem)
-- ============================================================
UPDATE products SET
  name         = 'Wontia AIP',
  slug         = 'wontia-aip',
  icon         = 'brain',
  category     = 'AI Platform',
  description  = 'Wontia is an Applied Intelligence System (AIS) powered by TIA — Technology of Applied Intelligence. It understands context, makes decisions, and executes actions across a growing ecosystem of domain applications: Wontia Business, Web Intelligence, Food Security, and more. Not a CRM — an intelligence layer where CRM is just one capability.',
  short_desc   = 'Applied Intelligence System (AIS) powered by TIA — one intelligence core with a growing ecosystem of domain apps.',
  hero_title   = 'Applied Intelligence System',
  hero_subtitle= 'Wontia AIP is an Applied Intelligence System powered by TIA. One intelligence core. Multiple domain applications.',
  overview     = '<p>Wontia AIP is the Applied Intelligence System (AIS) of the INTSOLCOM technology portfolio, powered by TIA — Technology of Applied Intelligence. It goes beyond software that captures and displays data: it understands context, reasons about situations, makes decisions, and executes actions.</p><p>Around the WONTIA + TIA core, an expanding ecosystem of domain applications delivers outcomes for Business, Web Intelligence, Food Security, and — in the near future — Health, Agriculture, Industry, Logistics, and Education.</p><p>INTSOLCOM provides this introduction; the full experience, demos, and access live on <strong>wontia.com</strong>.</p>',
  problem      = '<p>Most business software only captures and displays data — it informs, but it does not operate. Teams juggle disconnected tools: a CRM here, an analytics dashboard there, manual workflows in between. The result is slow, siloed operations and decisions made on stale context.</p>',
  solution     = '<p>Wontia AIP provides a single intelligence layer — TIA — that understands text, voice, and commands; connects to your tools and data; recommends the optimal action with transparent reasoning; and executes it within boundaries you authorize, with a full audit trail.</p>',
  features     = '[{"title":"TIA Core","desc":"Technology of Applied Intelligence: understanding, reasoning, learning, and context awareness"},{"title":"TIA Command Center","desc":"Operate the system via text or voice commands — not a chatbot, an operational interface"},{"title":"Domain Apps","desc":"Wontia Business, Web Intelligence, Food Security, and more — one core, many domains"},{"title":"AI Agents","desc":"Autonomous agents that execute, verify, and learn within authorized boundaries"},{"title":"Human-in-the-Loop","desc":"Recommend, approve, execute — humans stay in control of every action"},{"title":"Full Audit Trail","desc":"Every recommendation, approval, and execution logged with transparent reasoning"}]',
  benefits     = '[{"title":"One Intelligence Core","desc":"The same WONTIA + TIA architecture powers every domain application"},{"title":"Authorized Execution","desc":"Permission-based access and policy enforcement at every layer"},{"title":"Measurable Outcomes","desc":"KPIs, impact metrics, and a continuous improvement feedback loop"}]',
  use_cases    = '[{"title":"Business Operations","desc":"Wontia Business: customer, sales, and operations intelligence in one environment"},{"title":"Web Operations","desc":"Wontia Web Intelligence: understand and operate your digital presence"},{"title":"Food Security","desc":"Wontia Food Security: detect, prioritize, coordinate, and measure impact"}]',
  architecture = '<p>Wontia AIS is built on a unified layered architecture: WONTIA AIS Core (identity, permissions, tenants, integrations, APIs), TIA Core (intelligence engine), Domain Intelligence (vertical models and knowledge bases), Tools & Data connectors, a Decision Engine, Action Orchestration, and Measurable Outcomes — with a continuous feedback loop.</p>',
  roadmap      = '<ul><li><strong>Available today:</strong> Wontia Business, Wontia Web Intelligence</li><li><strong>In development:</strong> Wontia Food Security</li><li><strong>Future domains:</strong> Health, Agriculture, Industry, Logistics, Education</li></ul>',
  demo_cta_url = 'https://wontia.com',
  demo_cta_text= 'Visit wontia.com'
WHERE slug = 'wontia-crm';

UPDATE products SET
  demo_cta_url  = 'https://iaam.com',
  demo_cta_text = 'Visit iaam.com',
  short_desc    = 'End-to-end platform for AI data annotation — manage projects, QC, and annotator performance. Landing page coming soon at iaam.com.',
  roadmap       = '<ul><li><strong>Q3 2026:</strong> Dedicated landing page at iaam.com</li><li><strong>Q3 2026:</strong> 3D point cloud annotation support (LiDAR)</li><li><strong>Q4 2026:</strong> AI-assisted pre-labeling to accelerate annotation</li><li><strong>Q1 2027:</strong> Marketplace for verified annotation teams</li></ul>'
WHERE slug = 'ia-annotation-manager';

-- Business unit: WONTIA CRM → WONTIA (AIS)
UPDATE business_units SET
  name         = 'WONTIA',
  slug         = 'wontia',
  description  = 'WONTIA is an Applied Intelligence System (AIS) powered by TIA — Technology of Applied Intelligence. Not a CRM: a single intelligence core that understands context, makes decisions, and executes actions across a growing ecosystem of domain applications.',
  hero_title   = 'Applied Intelligence System',
  hero_subtitle= 'One intelligence core. Multiple domain applications.'
WHERE slug = 'wontia-crm';

-- Blog/resource posts: update WONTIA CRM positioning
UPDATE resources SET
  title       = 'From CRM to Applied Intelligence: The Evolution of WONTIA',
  excerpt     = 'WONTIA evolved from a CRM into an Applied Intelligence System (AIS) powered by TIA — an intelligence core with domain apps for Business, Web, Food Security, and more.',
  content     = '<h2>Why We Moved Beyond the CRM</h2><p>Most CRMs fail because they are too complex, too rigid, or too expensive. But the deeper insight was this: CRM is just one capability. Companies do not need another CRM — they need an intelligence layer that understands context, makes decisions, and executes actions across every domain of their operation.</p><h2>Introducing the AIS</h2><p>WONTIA became an Applied Intelligence System powered by TIA — Technology of Applied Intelligence. TIA is not a chatbot. It is an operational intelligence core that understands text, voice, and commands, and executes across systems with full transparency and auditability.</p><h2>One Intelligence. Multiple Domains.</h2><p>The same WONTIA + TIA architecture powers Wontia Business, Wontia Web Intelligence, and Wontia Food Security — with Health, Agriculture, Industry, Logistics, and Education on the roadmap.</p><h2>Recommend, Approve, Execute</h2><p>Enterprise-grade governance is built into every layer: TIA recommends with transparent reasoning, humans approve within defined boundaries, and the platform executes with a complete audit trail.</p>',
  meta_title  = 'From CRM to Applied Intelligence — WONTIA AIS',
  meta_desc   = 'How WONTIA evolved from a CRM into an Applied Intelligence System (AIS) powered by TIA.'
WHERE slug = 'building-crm-that-works-wontia-lessons';

UPDATE resources SET
  content = REPLACE(content, 'Our WONTIA CRM uses AI to score deals.', 'Our Wontia AIS (Applied Intelligence System) powers decisions and actions across business operations.')
WHERE slug = 'ai-business-operations-2026';
