#!/usr/bin/env node
const fs = require('fs');
const fsp = fs.promises;
const path = require('path');
const fg = require('fast-glob');

const addonRoot = path.resolve(__dirname, '..');
const distRoot = path.join(addonRoot, 'node_modules', 'ckeditor5', 'dist');
const browserRoot = path.join(distRoot, 'browser');
const translationsRoot = path.join(distRoot, 'translations');
const modernRoot = path.join(addonRoot, 'assets', 'vendor', 'ckeditor5-modern');

async function ensureDir(dir) {
  await fsp.mkdir(dir, { recursive: true });
}

async function copyFile(src, dest) {
  await ensureDir(path.dirname(dest));
  await fsp.copyFile(src, dest);
}

async function stripSourceMapReference(filePath) {
  const content = await fsp.readFile(filePath, 'utf8');
  const stripped = content
    .replace(/\/\*# sourceMappingURL=.*?\*\/\s*$/gm, '')
    .replace(/^\s*\/\/# sourceMappingURL=.*$/gm, '');

  if (stripped !== content) {
    await fsp.writeFile(filePath, stripped, 'utf8');
  }
}

async function recreateDir(dir) {
  if (fs.existsSync(dir)) {
    await fsp.rm(dir, { recursive: true, force: true });
  }
  await ensureDir(dir);
}

async function main() {
  if (!fs.existsSync(browserRoot)) {
    throw new Error('ckeditor5/dist/browser fehlt. Erst pnpm install ausführen.');
  }

  await recreateDir(modernRoot);
  await copyFile(path.join(browserRoot, 'ckeditor5.umd.js'), path.join(modernRoot, 'ckeditor.js'));
  await copyFile(path.join(browserRoot, 'ckeditor5.umd.js.map'), path.join(modernRoot, 'ckeditor.js.map'));
  await copyFile(path.join(browserRoot, 'ckeditor5.css'), path.join(modernRoot, 'ckeditor.css'));
  await copyFile(path.join(browserRoot, 'ckeditor5.css.map'), path.join(modernRoot, 'ckeditor.css.map'));
  await copyFile(path.join(browserRoot, 'ckeditor5-content.css'), path.join(modernRoot, 'ckeditor-content.css'));
  await copyFile(path.join(browserRoot, 'ckeditor5-editor.css'), path.join(modernRoot, 'ckeditor-editor.css'));
  await stripSourceMapReference(path.join(modernRoot, 'ckeditor.js'));
  await stripSourceMapReference(path.join(modernRoot, 'ckeditor.css'));

  const outTranslations = path.join(modernRoot, 'translations');
  await recreateDir(outTranslations);

  if (fs.existsSync(translationsRoot)) {
    const files = await fg(['*.umd.js'], { cwd: translationsRoot, onlyFiles: true });
    for (const file of files) {
      const outName = file.replace(/\.umd\.js$/, '.js');
      await copyFile(path.join(translationsRoot, file), path.join(outTranslations, outName));
    }
  }

  console.log('[vendor-update] Updated assets/vendor/ckeditor5-modern');
}

main().catch((err) => {
  console.error('[vendor-update] Fehler:', err.message || err);
  process.exitCode = 1;
});
