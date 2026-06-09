#!/usr/bin/env node
const fs = require('fs');
const path = require('path');

const addonRoot = path.resolve(__dirname, '..');
const projectPublicRoot = path.resolve(addonRoot, '..', '..', '..', '..');

const sourceCss = path.join(addonRoot, 'node_modules', 'ckeditor5', 'dist', 'ckeditor5-content.css');
const targets = [
  path.join(addonRoot, 'assets', 'cke5_content_styles.css'),
  path.join(projectPublicRoot, 'assets', 'addons', 'cke5', 'cke5_content_styles.css')
];

if (!fs.existsSync(sourceCss)) {
  console.error('[content-styles:update] Quelle nicht gefunden:', sourceCss);
  process.exit(1);
}

const css = fs.readFileSync(sourceCss, 'utf8');

for (const target of targets) {
  fs.mkdirSync(path.dirname(target), { recursive: true });
  fs.writeFileSync(target, css, 'utf8');
  console.log('[content-styles:update] Aktualisiert:', target);
}

console.log('[content-styles:update] Fertig.');
