#!/usr/bin/env node
const path = require('path');
const { execSync } = require('child_process');

const addonRoot = path.resolve(__dirname, '..');

const files = [
  'assets/cke5.js'
];

for (const rel of files) {
  const full = path.join(addonRoot, rel);
  execSync(`node --check "${full}"`, { stdio: 'inherit' });
}

console.log('[check-runtime] OK');
