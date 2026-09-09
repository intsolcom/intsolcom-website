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

-- ============================================================
-- v5: WONTIA product portfolio (3 products)
--     WONTIA AIP · WONTIA FOOD SECURITY · WONTIA IA ANNOTATION SUITE
-- ============================================================

-- Rename IA Annotation Manager -> WONTIA IA ANNOTATION SUITE
UPDATE products SET
  name          = 'WONTIA IA ANNOTATION SUITE',
  slug          = 'wontia-ia-annotation-suite',
  category      = 'AI Data',
  short_desc    = 'AI data annotation at scale. Manage projects, verify quality, and measure your annotation teams.',
  hero_title    = 'Annotation at Scale',
  hero_subtitle = 'Built by annotation professionals, for annotation professionals. Projects, quality, and teams in one place.',
  description   = 'WONTIA IA Annotation Suite is a comprehensive platform for managing AI data annotation projects at scale. Project management, quality control, and workforce optimization for annotation teams. Its dedicated landing page lives at iaam.com.',
  demo_cta_url  = 'https://iaam.com',
  demo_cta_text = 'Visit iaam.com'
WHERE slug = 'ia-annotation-manager';

-- Add WONTIA FOOD SECURITY (idempotent)
DELETE FROM products WHERE slug = 'wontia-food-security';
INSERT INTO products (name, slug, description, short_desc, hero_title, hero_subtitle, icon, category, order_num, overview, problem, solution, features, benefits, use_cases, architecture, roadmap, demo_cta_url, demo_cta_text, status)
VALUES (
  'WONTIA FOOD SECURITY',
  'wontia-food-security',
  'WONTIA Food Security applies the WONTIA + TIA intelligence core to food security: detect risk, prioritize response, coordinate action, and measure impact.',
  'Applied intelligence for food security. Detect risk, prioritize response, coordinate action, and measure impact.',
  'Food Security Intelligence',
  'The same WONTIA intelligence, applied to feeding people better.',
  'wheat', 'Food Security', 20,
  '<p>WONTIA Food Security proves that the same architecture can extend beyond business into domains that impact lives. It combines detection, understanding, prioritization, coordination, and action into one measurable response engine.</p><p>From food at risk to communities served, every decision is measured and every outcome feeds back into the system.</p>',
  '<p>Food at risk is lost every day because responses are slow, uncoordinated, and hard to measure. Teams work with disconnected data, manual prioritization, and no clear view of impact.</p>',
  '<p>WONTIA Food Security gives you one engine to detect risk, understand context, prioritize response, coordinate action, and measure impact — with TIA recommending, humans approving, and the platform executing.</p>',
  '[{"title":"Detect","desc":"Identify food at risk before it is lost"},{"title":"Understand","desc":"Context and severity scoring for every situation"},{"title":"Prioritize","desc":"TIA ranks responses by impact and urgency"},{"title":"Coordinate","desc":"Align teams, logistics, and communities in one plan"},{"title":"Act","desc":"Execute approved actions with a full audit trail"},{"title":"Measure Impact","desc":"Meals enabled, food saved, communities served"}]',
  '[{"title":"Less Food Lost","desc":"Faster detection and response protect at-risk food"},{"title":"More Meals Enabled","desc":"Prioritized coordination turns food into meals"},{"title":"Measurable Outcomes","desc":"Every action tracked from detection to impact"}]',
  '[{"title":"Redistribution Networks","desc":"Route at-risk food to communities that need it most"},{"title":"Emergency Response","desc":"Coordinate food response across organizations"},{"title":"Food Programs","desc":"Measure and optimize food security programs"}]',
  '<p>WONTIA Food Security runs on the unified WONTIA AIS architecture: AIS Core, TIA intelligence engine, domain knowledge bases, decision engine, action orchestration, and measurable outcomes — with a continuous feedback loop.</p>',
  '<ul><li><strong>In development:</strong> Food Security Response Engine</li><li><strong>Next:</strong> More domains on the same core (Health, Agriculture, Industry)</li></ul>',
  'https://wontia.com/#food-security',
  'Visit wontia.com',
  1
);

-- MACROPONDER leaves the portfolio (hidden, reversible)
UPDATE products SET status = 0 WHERE slug = 'macroponder';
UPDATE business_units SET status = 0 WHERE slug = 'macroponder';
