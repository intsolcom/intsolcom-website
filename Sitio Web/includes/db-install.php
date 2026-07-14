<?php
// ============================================================
// INTSOLCOM — Database Installer
// ============================================================
// Visit this file ONCE to create all tables and seed data.
// DELETE this file from the server immediately after use.
// ============================================================

require_once __DIR__ . '/config.php';

$ok = []; $err = [];

try {
    $db = db();

    // ============================================================
    // SETTINGS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        `key` VARCHAR(100) PRIMARY KEY,
        value TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ settings';

    // ============================================================
    // PAGES
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(100) NOT NULL,
        title VARCHAR(255),
        meta_title VARCHAR(255),
        meta_desc TEXT,
        status TINYINT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ pages';

    // ============================================================
    // SECTIONS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_id INT,
        type VARCHAR(50),
        sort_order INT,
        status TINYINT DEFAULT 1,
        FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ sections';

    // ============================================================
    // SECTION FIELDS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS section_fields (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section_id INT,
        field_key VARCHAR(100),
        field_value TEXT,
        UNIQUE KEY uniq_section_field (section_id, field_key),
        FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ section_fields';

    // ============================================================
    // NAV ITEMS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS nav_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        text VARCHAR(100),
        url VARCHAR(255),
        is_cta TINYINT DEFAULT 0,
        visible TINYINT DEFAULT 1,
        sort_order INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ nav_items';

    // ============================================================
    // CLIENTS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        logo_url TEXT,
        visible TINYINT DEFAULT 1,
        sort_order INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ clients';

    // ============================================================
    // TESTIMONIALS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        role VARCHAR(255),
        company VARCHAR(255),
        content TEXT,
        rating TINYINT DEFAULT 5,
        visible TINYINT DEFAULT 1,
        sort_order INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ testimonials';

    // ============================================================
    // MEDIA
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS media (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255),
        original_name VARCHAR(255),
        mime_type VARCHAR(100),
        file_size INT,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ media';

    // ============================================================
    // BUSINESS UNITS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS business_units (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        slug VARCHAR(100),
        description TEXT,
        hero_title VARCHAR(255),
        hero_subtitle VARCHAR(255),
        hero_video_id VARCHAR(50),
        capabilities JSON,
        benefits JSON,
        process JSON,
        technologies JSON,
        industries JSON,
        icon VARCHAR(50),
        order_num INT,
        status TINYINT DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ business_units';

    // ============================================================
    // PRODUCTS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        slug VARCHAR(100),
        description TEXT,
        short_desc TEXT,
        hero_title VARCHAR(255),
        hero_subtitle VARCHAR(255),
        overview TEXT,
        problem TEXT,
        solution TEXT,
        features JSON,
        screenshots JSON,
        benefits JSON,
        use_cases JSON,
        architecture TEXT,
        roadmap TEXT,
        demo_cta_url VARCHAR(500),
        demo_cta_text VARCHAR(255),
        icon VARCHAR(50),
        category VARCHAR(100),
        order_num INT,
        status TINYINT DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ products';

    // ============================================================
    // INDUSTRIES
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS industries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        slug VARCHAR(100),
        description TEXT,
        body LONGTEXT,
        icon VARCHAR(50),
        hero_title VARCHAR(255),
        hero_subtitle VARCHAR(255),
        short_desc TEXT,
        benefits JSON,
        use_cases JSON,
        order_num INT,
        status TINYINT DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ industries';

    // ============================================================
    // RESOURCES
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS resources (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        slug VARCHAR(100),
        excerpt TEXT,
        content LONGTEXT,
        cover_image VARCHAR(500),
        type VARCHAR(50) DEFAULT 'article',
        author VARCHAR(255),
        read_time INT,
        featured TINYINT DEFAULT 0,
        views INT DEFAULT 0,
        meta_title VARCHAR(255),
        meta_desc TEXT,
        status TINYINT DEFAULT 1,
        published_at DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ resources';

    // ============================================================
    // TRANSLATIONS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS translations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        src_hash CHAR(32) NOT NULL,
        src_lang VARCHAR(5) NOT NULL DEFAULT 'en',
        dst_lang VARCHAR(5) NOT NULL,
        src_text TEXT NOT NULL,
        dst_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_translation (src_hash, dst_lang)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ translations';

    // ============================================================
    // LEAD CONTACTS
    // ============================================================
    $db->exec("CREATE TABLE IF NOT EXISTS lead_contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255),
        company VARCHAR(255),
        phone VARCHAR(50),
        country VARCHAR(100),
        service_interest VARCHAR(255),
        message TEXT,
        source VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = '✓ lead_contacts';

    // ============================================================
    // SEED: PAGES
    // ============================================================
    $slugs = ['home', 'holding', 'business-units', 'technology', 'industries', 'resources', 'contact'];
    $sp = $db->prepare("INSERT IGNORE INTO pages (slug, title, status) VALUES (?, ?, 1)");
    foreach ($slugs as $slug) {
        $sp->execute([$slug, ucfirst(str_replace('-', ' ', $slug))]);
    }
    $ok[] = '✓ Pages seeded (7)';

    // ============================================================
    // SEED: NAV ITEMS
    // ============================================================
    $nav = [
        ['Technology',      '/technology',      0, 1, 10],
        ['Business Units',  '/business-units',  0, 1, 20],
        ['Industries',      '/industries',      0, 1, 30],
        ['Resources',       '/resources',       0, 1, 40],
        ['Contact',         '/contact',         1, 1, 50],
    ];
    $sn = $db->prepare("INSERT INTO nav_items (text, url, is_cta, visible, sort_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($nav as $item) {
        $sn->execute($item);
    }
    $ok[] = '✓ Nav items seeded (5)';

    // ============================================================
    // SEED: SETTINGS
    // ============================================================
    $defaultSettings = [
        'site_name'              => 'INTSOLCOM',
        'site_tagline'           => 'Technology Holding',
        'site_desc'              => 'INTSOLCOM LLC is a technology holding company that owns and operates specialized business units, software platforms and AI products.',
        'color_bg'               => '#FFFFFF',
        'color_surface'          => '#F8FAFC',
        'color_surface2'         => '#E2E8F0',
        'color_dark'             => '#0F172A',
        'color_mid'              => '#475569',
        'color_light'            => '#94A3B8',
        'color_accent'           => '#00C896',
        'color_accent_dk'        => '#00A67D',
        'color_secondary'        => '#2563EB',
        'color_purple'           => '#8B5CF6',
        'color_white'            => '#FFFFFF',
        'font_display'           => 'Inter+Display',
        'font_body'              => 'Inter',
        'nav_h'                  => '76',
        'nav_h_scrolled'         => '62',
        'nav_bg'                 => 'transparent',
        'nav_bg_scrolled'        => 'rgba(255,255,255,0.95)',
        'nav_blur'               => '20',
        'logo_text'              => 'INTSOL',
        'logo_accent'            => 'COM',
        'logo_text_color'        => '#0F172A',
        'logo_accent_color'      => '#00C896',
        'footer_desc'            => 'INTSOLCOM LLC is a technology holding company. We build and operate software platforms, AI products, and intelligent business services.',
        'footer_copyright'       => '© 2026 INTSOLCOM LLC',
        'contact_usa_phone'      => '+1 (302) 555-0199',
        'contact_usa_address'    => '1209 Orange Street, Wilmington, DE 19801',
        'contact_col_email'      => 'info@intsolcom.com',
        'contact_col_address'    => 'Carrera 53 #79-01, Barranquilla, Colombia',
        'contact_whatsapp'       => '+573005550199',
        'social_linkedin'        => 'https://linkedin.com/company/intsolcom',
    ];
    foreach ($defaultSettings as $k => $v) {
        setSetting($k, $v);
    }
    $ok[] = '✓ Settings seeded (' . count($defaultSettings) . ')';

    // ============================================================
    // SEED: HOME PAGE SECTIONS
    // ============================================================
    $homePage = getPage('home');
    if ($homePage) {
        $homePageId = $homePage['id'];
        $homeSections = [
            'hero'            => ['sort' => 10, 'fields' => []],
            'ecosystem'       => ['sort' => 20, 'fields' => []],
            'stats'           => ['sort' => 30, 'fields' => []],
            'products_grid'   => ['sort' => 40, 'fields' => []],
            'capabilities'    => ['sort' => 50, 'fields' => []],
            'industries_grid' => ['sort' => 60, 'fields' => []],
            'comparison'      => ['sort' => 70, 'fields' => []],
            'cta'             => ['sort' => 80, 'fields' => []],
            'testimonials'    => ['sort' => 90, 'fields' => []],
        ];
        $ss = $db->prepare("INSERT INTO sections (page_id, type, sort_order, status) VALUES (?, ?, ?, 1)");
        foreach ($homeSections as $type => $cfg) {
            $ss->execute([$homePageId, $type, $cfg['sort']]);
        }
        $ok[] = '✓ Home sections seeded (' . count($homeSections) . ')';
    }

    // ============================================================
    // SEED: SAMPLE BUSINESS UNITS
    // ============================================================
    $busUnits = [
        [
            'name'           => 'INTSOLCOM SAS',
            'slug'           => 'intsolcom-sas',
            'description'    => 'INTSOLCOM SAS is our Colombia-based operational hub, managing software development, AI data operations, and business process execution across Latin America. Based in Barranquilla with a satellite office in Bogotá, our Colombian entity provides nearshore technology services to North American and European clients.',
            'hero_title'     => 'Colombia Operations & Nearshore Technology',
            'hero_subtitle'  => 'Powering global technology delivery from Barranquilla, Colombia',
            'icon'           => 'building',
            'order_num'      => 10,
            'capabilities'   => json_encode([
                ['title' => 'Software Development', 'desc' => 'Full-stack engineering teams building web, mobile, and enterprise applications'],
                ['title' => 'AI Data Operations', 'desc' => 'Data labeling, annotation, and model training support for computer vision and NLP'],
                ['title' => 'QA & Testing', 'desc' => 'Automated and manual quality assurance for software products'],
                ['title' => 'IT Support', 'desc' => 'Bilingual L1/L2 technical support for enterprise clients'],
            ]),
            'benefits' => json_encode([
                ['title' => 'Time Zone Aligned', 'desc' => 'EST time zone — real-time collaboration with U.S. teams'],
                ['title' => 'Bilingual Teams', 'desc' => 'B2-C2 English proficiency across all roles'],
                ['title' => 'Cost Efficient', 'desc' => '60-70% savings vs. U.S.-based equivalent teams'],
            ]),
            'process' => json_encode([
                ['step' => 1, 'title' => 'Discovery', 'desc' => 'We map your requirements, tools, and workflows'],
                ['step' => 2, 'title' => 'Team Assembly', 'desc' => 'Pre-vetted professionals matched to your needs'],
                ['step' => 3, 'title' => 'Onboarding', 'desc' => 'Intensive training sprint on your stack and standards'],
                ['step' => 4, 'title' => 'Go Live', 'desc' => 'Full operational handover with metrics from day one'],
            ]),
            'technologies' => json_encode(['React', 'Node.js', 'Python', 'PHP', 'MySQL', 'PostgreSQL', 'AWS', 'Docker', 'TensorFlow', 'PyTorch']),
            'industries'   => json_encode(['Technology', 'Healthcare', 'Finance', 'Sports', 'E-commerce', 'Logistics']),
        ],
        [
            'name'           => 'WONTIA CRM',
            'slug'           => 'wontia-crm',
            'description'    => 'WONTIA is an intelligent CRM platform designed for service-based businesses. It combines contact management, pipeline tracking, task automation, and AI-powered insights in a unified interface. Built for teams that need more than a spreadsheet but less complexity than enterprise suites.',
            'hero_title'     => 'Intelligent CRM for Modern Teams',
            'hero_subtitle'  => 'Manage contacts, track deals, and automate workflows with AI-powered insights',
            'icon'           => 'users',
            'order_num'      => 20,
            'capabilities' => json_encode([
                ['title' => 'Contact Management', 'desc' => 'Unified contact profiles with interaction history, documents, and notes'],
                ['title' => 'Pipeline Tracking', 'desc' => 'Visual deal pipeline with drag-and-drop stages and custom workflows'],
                ['title' => 'Task Automation', 'desc' => 'Automated follow-ups, reminders, and data entry powered by AI'],
                ['title' => 'Analytics & Reports', 'desc' => 'Real-time dashboards with sales forecasts and team performance metrics'],
            ]),
            'benefits' => json_encode([
                ['title' => '50% Faster Deals', 'desc' => 'Streamlined workflows reduce sales cycle time'],
                ['title' => '360° Client View', 'desc' => 'Every interaction, document, and note in one place'],
                ['title' => 'No Learning Curve', 'desc' => 'Intuitive interface — your team is productive in hours, not weeks'],
            ]),
            'process' => json_encode([
                ['step' => 1, 'title' => 'Setup', 'desc' => 'We configure your pipeline, custom fields, and integrations'],
                ['step' => 2, 'title' => 'Import', 'desc' => 'Bulk import contacts from spreadsheets, other CRMs, or APIs'],
                ['step' => 3, 'title' => 'Train', 'desc' => 'Live training session with your team'],
                ['step' => 4, 'title' => 'Optimize', 'desc' => 'Monthly reviews to refine workflows and automation rules'],
            ]),
            'technologies' => json_encode(['PHP', 'Laravel', 'MySQL', 'Vue.js', 'Tailwind CSS', 'REST API']),
            'industries'   => json_encode(['Professional Services', 'Real Estate', 'Consulting', 'Agencies', 'Financial Services']),
        ],
        [
            'name'           => 'MACROPONDER',
            'slug'           => 'macroponder',
            'description'    => 'MACROPONDER is a decision intelligence platform that helps organizations make better strategic choices through structured analysis, scenario modeling, and data-driven recommendations. It combines decision science frameworks with AI to reduce cognitive bias and improve outcomes.',
            'hero_title'     => 'Decision Intelligence Platform',
            'hero_subtitle'  => 'Make better strategic decisions with AI-powered scenario modeling and analysis',
            'icon'           => 'brain',
            'order_num'      => 30,
            'capabilities' => json_encode([
                ['title' => 'Scenario Modeling', 'desc' => 'Build and compare multiple decision scenarios with weighted criteria'],
                ['title' => 'Bias Detection', 'desc' => 'AI identifies cognitive biases in your decision process'],
                ['title' => 'Collaborative Decisions', 'desc' => 'Team-based decision workflows with structured deliberation'],
                ['title' => 'Impact Analysis', 'desc' => 'Predict second and third-order effects of each decision path'],
            ]),
            'benefits' => json_encode([
                ['title' => 'Better Outcomes', 'desc' => 'Structured frameworks reduce decision errors by up to 40%'],
                ['title' => 'Faster Consensus', 'desc' => 'Data-backed scenarios accelerate team alignment'],
                ['title' => 'Auditable Logic', 'desc' => 'Every decision trail is documented and reviewable'],
            ]),
            'process' => json_encode([
                ['step' => 1, 'title' => 'Frame', 'desc' => 'Define the decision, criteria, and stakeholders'],
                ['step' => 2, 'title' => 'Model', 'desc' => 'Build scenarios with weighted factors and constraints'],
                ['step' => 3, 'title' => 'Analyze', 'desc' => 'AI evaluates each scenario against your success criteria'],
                ['step' => 4, 'title' => 'Decide', 'desc' => 'Team reviews recommendations and commits to action'],
            ]),
            'technologies' => json_encode(['Python', 'FastAPI', 'PostgreSQL', 'React', 'D3.js', 'TensorFlow']),
            'industries'   => json_encode(['Finance', 'Strategy Consulting', 'Government', 'Healthcare', 'Energy']),
        ],
        [
            'name'           => 'IA ANNOTATION MANAGER',
            'slug'           => 'ia-annotation-manager',
            'description'    => 'IA Annotation Manager is a comprehensive platform for managing AI data annotation projects at scale. From image and video labeling to text classification and audio transcription, it provides project management, quality control, and workforce optimization tools for annotation teams.',
            'hero_title'     => 'AI Data Annotation Platform',
            'hero_subtitle'  => 'Manage, quality-control, and scale your annotation workforce with precision',
            'icon'           => 'tags',
            'order_num'      => 40,
            'capabilities' => json_encode([
                ['title' => 'Project Management', 'desc' => 'Create and manage annotation projects with custom ontologies and guidelines'],
                ['title' => 'Quality Control', 'desc' => 'Multi-layer QC with automated consensus scoring and reviewer workflows'],
                ['title' => 'Workforce Analytics', 'desc' => 'Real-time annotator performance dashboards and productivity metrics'],
                ['title' => 'Multi-Format Support', 'desc' => 'Images, video, text, audio, and 3D point cloud annotation tools'],
            ]),
            'benefits' => json_encode([
                ['title' => '98%+ Accuracy', 'desc' => 'Structured QC processes ensure production-grade data quality'],
                ['title' => '3x Throughput', 'desc' => 'Optimized workflows and tooling dramatically increase annotation speed'],
                ['title' => 'Full Traceability', 'desc' => 'Every label traced to annotator, reviewer, and guideline version'],
            ]),
            'process' => json_encode([
                ['step' => 1, 'title' => 'Setup', 'desc' => 'Define ontology, guidelines, and quality thresholds'],
                ['step' => 2, 'title' => 'Onboard', 'desc' => 'Train and calibrate annotation team on your project'],
                ['step' => 3, 'title' => 'Annotate', 'desc' => 'Production annotation with continuous QC sampling'],
                ['step' => 4, 'title' => 'Deliver', 'desc' => 'Formatted output ready for model training or fine-tuning'],
            ]),
            'technologies' => json_encode(['Python', 'Flask', 'React', 'PostgreSQL', 'Redis', 'AWS S3', 'Docker']),
            'industries'   => json_encode(['Autonomous Vehicles', 'Sports Analytics', 'Healthcare AI', 'Retail', 'Agriculture']),
        ],
    ];

    $sbu = $db->prepare("INSERT INTO business_units (name, slug, description, hero_title, hero_subtitle, icon, order_num, capabilities, benefits, process, technologies, industries, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    foreach ($busUnits as $bu) {
        $sbu->execute([
            $bu['name'], $bu['slug'], $bu['description'],
            $bu['hero_title'], $bu['hero_subtitle'], $bu['icon'], $bu['order_num'],
            $bu['capabilities'], $bu['benefits'], $bu['process'],
            $bu['technologies'], $bu['industries'],
        ]);
    }
    $ok[] = '✓ Sample business units created (' . count($busUnits) . ')';

    // ============================================================
    // SEED: SAMPLE PRODUCTS
    // ============================================================
    $products = [
        [
            'name'          => 'WONTIA CRM',
            'slug'          => 'wontia-crm',
            'description'   => 'Intelligent CRM platform for service-based businesses. Contact management, pipeline tracking, task automation, and AI-powered insights in a unified interface.',
            'short_desc'    => 'AI-powered CRM for service businesses — manage contacts, track deals, and automate workflows.',
            'hero_title'    => 'Sell Smarter with AI-Powered CRM',
            'hero_subtitle' => 'WONTIA combines contact management, pipeline tracking, and AI insights to help your team close more deals.',
            'icon'          => 'users',
            'category'      => 'CRM',
            'order_num'     => 10,
            'overview'      => '<p>WONTIA was built for service-based businesses that have outgrown spreadsheets but don\'t need the complexity (or cost) of enterprise CRM suites. It balances power with simplicity — your team can be productive in hours, not weeks.</p><p>The platform combines traditional CRM functionality (contacts, deals, tasks) with AI-powered features like automated follow-up scheduling, deal health scoring, and next-best-action recommendations.</p>',
            'problem'       => '<p>Most service businesses struggle with fragmented customer data across spreadsheets, emails, and notebooks. Sales opportunities slip through the cracks because there\'s no system to track them. Teams waste hours on manual data entry and follow-up scheduling. Enterprise CRMs are overkill — too complex, too expensive, and require weeks of training.</p>',
            'solution'      => '<p>WONTIA provides a unified platform that is powerful enough for serious sales teams but simple enough that anyone can use it. AI handles the busywork — auto-logging interactions, suggesting follow-ups, and flagging at-risk deals — so your team focuses on selling.</p>',
            'features'      => json_encode([
                ['title' => 'Smart Contact Profiles', 'desc' => '360° view of every contact with interaction history, documents, and AI-generated insights'],
                ['title' => 'Visual Pipeline', 'desc' => 'Drag-and-drop deal stages with custom workflows and automated stage progression'],
                ['title' => 'AI Follow-ups', 'desc' => 'Intelligent reminders and automated email sequences based on deal behavior'],
                ['title' => 'Team Collaboration', 'desc' => 'Shared notes, @mentions, and activity feeds for seamless team coordination'],
                ['title' => 'Analytics Dashboard', 'desc' => 'Real-time sales forecasts, conversion rates, and team performance metrics'],
                ['title' => 'API & Integrations', 'desc' => 'Connect with email, calendar, accounting, and 50+ apps via REST API'],
            ]),
            'benefits'       => json_encode([
                ['title' => '50% Faster Sales Cycles', 'desc' => 'Automated workflows and smart reminders keep deals moving'],
                ['title' => '30% Higher Close Rates', 'desc' => 'AI deal scoring helps teams prioritize the right opportunities'],
                ['title' => 'Hours Saved Weekly', 'desc' => 'Automated data entry and follow-ups reclaim 5+ hours per rep per week'],
            ]),
            'use_cases' => json_encode([
                ['title' => 'Consulting Firms', 'desc' => 'Track client engagements from proposal to renewal with custom stage pipelines'],
                ['title' => 'Real Estate Agencies', 'desc' => 'Manage property listings, buyer/seller contacts, and transaction timelines'],
                ['title' => 'Marketing Agencies', 'desc' => 'Track leads, proposals, and retainers with client-specific workflows'],
            ]),
            'architecture'   => '<p>WONTIA runs on a modern LAMP stack with Laravel PHP framework, MySQL database, and a Vue.js frontend. The platform is deployed on AWS with auto-scaling, daily backups, and 99.9% uptime SLA. REST API enables integration with any third-party system.</p>',
            'roadmap'        => '<ul><li><strong>Q3 2026:</strong> Mobile app (iOS/Android)</li><li><strong>Q4 2026:</strong> Advanced AI deal scoring with custom ML models</li><li><strong>Q1 2027:</strong> Multi-currency and international localization</li></ul>',
            'demo_cta_url'   => '/contact',
            'demo_cta_text'  => 'Request a Demo',
        ],
        [
            'name'          => 'MACROPONDER',
            'slug'          => 'macroponder',
            'description'   => 'Decision intelligence platform that helps organizations make better strategic choices through structured analysis, scenario modeling, and data-driven recommendations.',
            'short_desc'    => 'AI-powered decision intelligence — model scenarios, detect bias, and make better strategic choices.',
            'hero_title'    => 'Decide with Confidence',
            'hero_subtitle' => 'MACROPONDER brings structured decision science and AI together to help your organization make smarter, faster, and more defensible decisions.',
            'icon'          => 'brain',
            'category'      => 'AI Platform',
            'order_num'     => 20,
            'overview'      => '<p>MACROPONDER was designed to solve a fundamental problem: most organizations make high-stakes decisions based on intuition, politics, or incomplete data. The platform combines proven decision science frameworks (decision trees, weighted scoring, Monte Carlo simulations) with modern AI to create a systematic approach to strategic choice.</p><p>Whether you\'re evaluating M&A targets, prioritizing product investments, or planning market entry, MACROPONDER provides the structure and analytical power to make decisions you can defend.</p>',
            'problem'       => '<p>Strategic decisions in organizations are plagued by cognitive biases, incomplete information, groupthink, and lack of structured analysis. Decisions get made based on the loudest voice in the room rather than the best available evidence. Post-decision, there\'s rarely an auditable trail of why a particular path was chosen. This leads to poor outcomes and an inability to learn from past decisions.</p>',
            'solution'      => '<p>MACROPONDER provides a structured decision framework that guides teams through problem framing, criteria definition, scenario modeling, and evidence-based evaluation. The AI layer detects cognitive biases in real-time, suggests missing criteria, and generates counter-arguments to pressure-test assumptions.</p>',
            'features'      => json_encode([
                ['title' => 'Decision Canvas', 'desc' => 'Visual workspace to frame problems, define criteria, and map alternatives'],
                ['title' => 'Scenario Builder', 'desc' => 'Create and compare multiple scenarios with weighted factors and sensitivity analysis'],
                ['title' => 'Bias Detector', 'desc' => 'AI identifies anchoring, confirmation bias, overconfidence, and groupthink in real-time'],
                ['title' => 'Collaborative Deliberation', 'desc' => 'Structured team workflows with anonymous input, devil\'s advocate mode, and consensus scoring'],
                ['title' => 'Impact Simulator', 'desc' => 'Model second and third-order effects using Monte Carlo and decision tree simulations'],
                ['title' => 'Decision Audit Trail', 'desc' => 'Complete record of assumptions, evidence, and reasoning for every decision'],
            ]),
            'benefits'       => json_encode([
                ['title' => '40% Fewer Decision Errors', 'desc' => 'Structured frameworks and bias detection reduce costly mistakes'],
                ['title' => '3x Faster Consensus', 'desc' => 'Data-backed scenarios eliminate emotional gridlock in team decisions'],
                ['title' => '100% Auditable', 'desc' => 'Every decision logic trail meets governance and compliance requirements'],
            ]),
            'use_cases' => json_encode([
                ['title' => 'M&A Evaluation', 'desc' => 'Compare acquisition targets across financial, strategic, and cultural dimensions'],
                ['title' => 'Product Investment', 'desc' => 'Prioritize features and initiatives with ROI modeling and risk assessment'],
                ['title' => 'Market Entry', 'desc' => 'Evaluate new markets with multi-factor analysis and scenario planning'],
            ]),
            'architecture'   => '<p>MACROPONDER is built on a Python/FastAPI backend with a React frontend, using PostgreSQL for structured data and Redis for real-time collaboration. The AI engine uses a combination of rule-based systems and LLM integration via the Anthropic API for bias detection and counter-argument generation.</p>',
            'roadmap'        => '<ul><li><strong>Q3 2026:</strong> Real-time collaborative decision rooms</li><li><strong>Q4 2026:</strong> Industry-specific decision templates (Finance, Healthcare, Energy)</li><li><strong>Q1 2027:</strong> Integration with BI tools (Power BI, Tableau) for live data import</li></ul>',
            'demo_cta_url'   => '/contact',
            'demo_cta_text'  => 'Request a Demo',
        ],
        [
            'name'          => 'IA Annotation Manager',
            'slug'          => 'ia-annotation-manager',
            'description'   => 'Comprehensive platform for managing AI data annotation projects at scale. Project management, quality control, and workforce optimization for annotation teams.',
            'short_desc'    => 'End-to-end platform for AI data annotation — manage projects, QC, and annotator performance.',
            'hero_title'    => 'Annotation Management at Scale',
            'hero_subtitle' => 'The platform built by annotation professionals, for annotation professionals. Manage projects, quality, and teams from a single dashboard.',
            'icon'          => 'tags',
            'category'      => 'AI Platform',
            'order_num'     => 30,
            'overview'      => '<p>IA Annotation Manager was born from real-world experience running large-scale annotation operations. After managing projects with 50+ annotators processing millions of data points, we built the tool we wished we had — a platform that combines project management, quality control, and workforce analytics in one place.</p><p>It supports all major annotation types: image classification, object detection, semantic segmentation, video tracking, text classification, NER, sentiment analysis, and audio transcription.</p>',
            'problem'       => '<p>Managing annotation projects is chaotic. Guidelines change. Annotator performance varies. Quality issues go undetected until model training fails. Most teams use spreadsheets, shared drives, and ad-hoc tools — none of which provide the structure needed for production-grade annotation at scale.</p>',
            'solution'      => '<p>IA Annotation Manager provides a centralized platform where project managers define ontologies, create annotation tasks, monitor quality in real-time, and track annotator performance. Automated QC sampling catches issues early, and the workforce analytics dashboard helps optimize team composition and throughput.</p>',
            'features'      => json_encode([
                ['title' => 'Project Workspace', 'desc' => 'Define ontologies, create guidelines, and set up annotation projects in minutes'],
                ['title' => 'Task Distribution', 'desc' => 'Intelligent task assignment based on annotator skills, availability, and performance'],
                ['title' => 'Multi-Layer QC', 'desc' => 'Automated consensus scoring, random sampling review, and senior reviewer escalation'],
                ['title' => 'Performance Analytics', 'desc' => 'Real-time dashboards tracking annotator accuracy, speed, and consistency'],
                ['title' => 'Annotation Tools', 'desc' => 'Built-in tools for bounding boxes, polygons, keypoints, classification, and transcription'],
                ['title' => 'Export & Integration', 'desc' => 'Export in COCO, YOLO, Pascal VOC, CSV, JSON — ready for any training pipeline'],
            ]),
            'benefits'       => json_encode([
                ['title' => '98%+ Annotation Accuracy', 'desc' => 'Structured QC with automated consensus scoring ensures data quality'],
                ['title' => '3x Throughput', 'desc' => 'Optimized task distribution and tooling accelerate annotation velocity'],
                ['title' => 'Full Traceability', 'desc' => 'Every label traced to annotator, reviewer, and guideline version for audit'],
            ]),
            'use_cases' => json_encode([
                ['title' => 'Computer Vision Teams', 'desc' => 'Manage image/video annotation for object detection, segmentation, and tracking'],
                ['title' => 'NLP Teams', 'desc' => 'Text classification, NER, sentiment analysis, and content moderation projects'],
                ['title' => 'Annotation Service Providers', 'desc' => 'Multi-client, multi-project management with workforce optimization'],
            ]),
            'architecture'   => '<p>The platform runs on a Python/Flask backend with a React frontend and PostgreSQL database. Real-time collaboration features use Redis pub/sub. File storage uses AWS S3 with signed URLs for secure access. The annotation tools integrate with Label Studio and CVAT for specialized labeling tasks.</p>',
            'roadmap'        => '<ul><li><strong>Q3 2026:</strong> 3D point cloud annotation support (LiDAR)</li><li><strong>Q4 2026:</strong> AI-assisted pre-labeling to accelerate annotation</li><li><strong>Q1 2027:</strong> Marketplace for verified annotation teams</li></ul>',
            'demo_cta_url'   => '/contact',
            'demo_cta_text'  => 'Request a Demo',
        ],
    ];

    $spd = $db->prepare("INSERT INTO products (name, slug, description, short_desc, hero_title, hero_subtitle, icon, category, order_num, overview, problem, solution, features, benefits, use_cases, architecture, roadmap, demo_cta_url, demo_cta_text, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    foreach ($products as $p) {
        $spd->execute([
            $p['name'], $p['slug'], $p['description'], $p['short_desc'],
            $p['hero_title'], $p['hero_subtitle'], $p['icon'], $p['category'], $p['order_num'],
            $p['overview'], $p['problem'], $p['solution'],
            $p['features'], $p['benefits'], $p['use_cases'],
            $p['architecture'], $p['roadmap'],
            $p['demo_cta_url'], $p['demo_cta_text'],
        ]);
    }
    $ok[] = '✓ Sample products created (' . count($products) . ')';

} catch (PDOException $e) {
    $err[] = '✕ ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>INTSOLCOM — Database Installer</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,-apple-system,sans-serif;background:#0F172A;color:#F8FAFC;display:flex;align-items:center;justify-content:center;min-height:100vh}
.box{background:#1E293B;border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:2.5rem;max-width:560px;width:100%}
h1{font-size:1.5rem;font-weight:700;margin-bottom:.3rem}
h1 span{color:#00C896}
.sub{color:rgba(255,255,255,.35);font-size:.82rem;margin-bottom:2rem}
.item{padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.84rem}
.ok{color:#00C896}
.err{color:#ef4444}
.cta{display:block;margin-top:1.75rem;background:#00C896;color:#0F172A;text-align:center;padding:1rem;border-radius:100px;font-weight:600;text-decoration:none;transition:opacity .2s}
.cta:hover{opacity:.9}
.warn{background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.25);border-radius:10px;padding:1rem;font-size:.79rem;color:#fbbf24;margin-top:1.25rem;line-height:1.5}
code{background:rgba(0,0,0,.3);padding:.15em .4em;border-radius:4px;font-size:.9em}
</style>
</head>
<body>
<div class="box">
  <h1>INTSOL<span>COM</span> — DB Installer</h1>
  <div class="sub">Technology Holding — Database Setup</div>
  <?php foreach ($ok as $m): ?><div class="item ok"><?=h($m)?></div><?php endforeach; ?>
  <?php foreach ($err as $m): ?><div class="item err"><?=h($m)?></div><?php endforeach; ?>
  <?php if (empty($err)): ?>
  <a href="/admin/" class="cta">→ Go to Admin Panel</a>
  <div class="warn">Security: Delete <code>db-install.php</code> from the server immediately after use.</div>
  <?php endif; ?>
</div>
</body>
</html>
