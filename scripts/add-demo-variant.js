#!/usr/bin/env node
const fs = require('fs');
const path = require('path');

const addonRoot = path.resolve(__dirname, '..');
const bundlePath = path.join(addonRoot, 'install', 'default_bundle.json');

function readBundle() {
  if (!fs.existsSync(bundlePath)) {
    throw new Error('default_bundle.json wurde nicht gefunden: ' + bundlePath);
  }

  const raw = fs.readFileSync(bundlePath, 'utf8');
  const parsed = JSON.parse(raw);
  if (!parsed || !Array.isArray(parsed.profiles)) {
    throw new Error('Ungueltiges Bundle: profiles fehlt oder ist kein Array.');
  }

  return parsed;
}

function writeBundle(bundle) {
  fs.writeFileSync(bundlePath, JSON.stringify(bundle, null, 2) + '\n', 'utf8');
}

function cloneProfile(profile) {
  return JSON.parse(JSON.stringify(profile));
}

function normalizeName(name) {
  const trimmed = String(name || '').trim();
  if (trimmed === '') {
    throw new Error('Bitte einen Profilnamen angeben, z. B. demo_marketing.');
  }
  return trimmed.startsWith('demo_') ? trimmed : 'demo_' + trimmed;
}

function parseArgs(argv) {
  const args = {
    name: '',
    desc: '',
    from: 'demo_default',
    overwrite: false,
  };

  for (let i = 2; i < argv.length; i++) {
    const a = argv[i];
    if (a === '--name' || a === '-n') {
      args.name = argv[++i] || '';
    } else if (a === '--desc' || a === '-d') {
      args.desc = argv[++i] || '';
    } else if (a === '--from' || a === '-f') {
      args.from = argv[++i] || 'demo_default';
    } else if (a === '--overwrite') {
      args.overwrite = true;
    } else if (a === '--help' || a === '-h') {
      args.help = true;
    }
  }

  return args;
}

function printHelp() {
  console.log('Verwendung: node ./scripts/add-demo-variant.js --name demo_marketing [--desc "..."] [--from demo_default] [--overwrite]');
}

function main() {
  const args = parseArgs(process.argv);

  if (args.help) {
    printHelp();
    return;
  }

  const targetName = normalizeName(args.name);
  const sourceName = normalizeName(args.from);
  const bundle = readBundle();

  const source = bundle.profiles.find((p) => p && p.name === sourceName);
  if (!source) {
    throw new Error('Quellprofil nicht gefunden: ' + sourceName);
  }

  const existingIndex = bundle.profiles.findIndex((p) => p && p.name === targetName);
  if (existingIndex >= 0 && !args.overwrite) {
    throw new Error('Zielprofil existiert bereits: ' + targetName + ' (mit --overwrite ersetzen)');
  }

  const variant = cloneProfile(source);
  variant.name = targetName;
  variant.description = args.desc && String(args.desc).trim() !== ''
    ? String(args.desc).trim()
    : 'Demo Variant ' + targetName.replace(/^demo_/, '');

  if (existingIndex >= 0) {
    bundle.profiles[existingIndex] = variant;
  } else {
    bundle.profiles.push(variant);
  }

  writeBundle(bundle);
  console.log('[demo-variant] gespeichert:', targetName, '(Quelle:', sourceName + ')');
}

try {
  main();
} catch (error) {
  console.error('[demo-variant] Fehler:', error.message || error);
  process.exitCode = 1;
}
