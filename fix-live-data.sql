-- ============================================================
-- INTSOLCOM — Live data fixes (idempotent, safe to re-run)
-- Run on the VPS:  sudo mysql intsolcom < fix-live-data.sql
-- ============================================================

-- v4: Remove Industries from main navigation
DELETE FROM nav_items WHERE url = '/industries';

-- v3: Wontia AIP repositioning (CRM -> AIS ecosystem)
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
  short_desc    = 'End-to-end platform for AI data annotation — manage projects, QC, and annotator performance. Landing page coming soon at iaam.com.'
WHERE slug = 'ia-annotation-manager';

UPDATE business_units SET
  name         = 'WONTIA',
  slug         = 'wontia',
  description  = 'WONTIA is an Applied Intelligence System (AIS) powered by TIA — Technology of Applied Intelligence. Not a CRM: a single intelligence core that understands context, makes decisions, and executes actions across a growing ecosystem of domain applications.',
  hero_title   = 'Applied Intelligence System',
  hero_subtitle= 'One intelligence core. Multiple domain applications.'
WHERE slug = 'wontia-crm';

-- Text replacements in DB-driven content (testimonials, sections, settings)
UPDATE testimonials SET content = REPLACE(content, 'WONTIA CRM', 'WONTIA') WHERE content LIKE '%WONTIA CRM%';
UPDATE section_fields SET field_value = REPLACE(field_value, 'WONTIA CRM', 'Wontia AIP') WHERE field_value LIKE '%WONTIA CRM%';
UPDATE settings SET value = REPLACE(value, 'WONTIA CRM', 'Wontia AIP') WHERE value LIKE '%WONTIA CRM%';
UPDATE resources SET content = REPLACE(content, 'WONTIA CRM', 'WONTIA') WHERE content LIKE '%WONTIA CRM%';
