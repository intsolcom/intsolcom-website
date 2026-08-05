UPDATE settings SET value = '+1 (786) 386-1515' WHERE `key` = 'contact_usa_phone';
UPDATE settings SET value = 'contact@intsolcom.com' WHERE `key` = 'contact_col_email';
UPDATE settings SET value = '© 2026 INTSOLCOM, LLC. All rights reserved.' WHERE `key` = 'footer_copyright';
INSERT INTO settings (`key`, value) VALUES ('footer_tagline', 'Technology Holding • United States & Colombia') ON DUPLICATE KEY UPDATE value = VALUES(value);
