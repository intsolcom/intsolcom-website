'use strict';
// ============================================================
// INTSOLCOM — Secrets generator (zero dependencies)
// Generates: DB password, BYT encryption key, API tokens,
// and the one-liner to create the admin password hash (bcrypt)
// via PHP on the server.
//
// Usage: node scripts/generate-secrets.js
// ============================================================
const crypto = require('crypto');

function randHex(bytes) {
  return crypto.randomBytes(bytes).toString('hex');
}

function randBase64(bytes) {
  return crypto.randomBytes(bytes).toString('base64');
}

function strongPassword(len = 24) {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*()-_=+?';
  let out = '';
  const buf = crypto.randomBytes(len);
  for (let i = 0; i < len; i++) out += chars[buf[i] % chars.length];
  return out;
}

console.log('============================================================');
console.log(' INTSOLCOM — Credential rotation values (save securely!)    ');
console.log(' Generated: ' + new Date().toISOString());
console.log('============================================================\n');

console.log('[1] BYT_ENCRYPTION_KEY (64 hex chars) — config.php');
console.log('    ' + randHex(32) + '\n');

console.log('[2] DB_PASS (new MySQL password) — config.php + Hostinger');
console.log('    ' + strongPassword(24) + '\n');

console.log('[3] API_BLOG_TOKEN (blog REST API) — config.php');
console.log('    ' + randBase64(32) + '\n');

console.log('[4] Admin password — set ADMIN_PASS_HASH in config.php');
console.log('    Suggested plaintext: ' + strongPassword(18));
console.log('    Generate the hash ON THE SERVER (PHP):');
console.log('      php -r "echo password_hash(\'YOUR_PASSWORD\', PASSWORD_BCRYPT);"');
console.log('    Then add to config.php:');
console.log("      define('ADMIN_PASS_HASH', '\u00242y\u002410\u0024...');\n");

console.log('[5] LinkedIn / social tokens — re-save from the admin panel');
console.log('    (they will be encrypted at rest with bytEncrypt)\n');

console.log('============================================================');
console.log(' After rotating:');
console.log('  1. Update config.php on the server (never commit it)');
console.log('  2. Update DB password in Hostinger panel (MySQL users)');
console.log('  3. Revoke the old GitHub PAT (gho_...) exposed in logs');
console.log('  4. Rotate Anthropic/DeepSeek API keys in their consoles');
console.log('  5. Clear OPcache and test /admin login');
console.log('============================================================');
