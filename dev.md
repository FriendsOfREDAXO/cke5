# CKE5 Entwicklerdokumentation (dev.md)

Dieses Dokument ist der zentrale Leitfaden zur Pflege und Weiterentwicklung des AddOns `cke5`.
Es beschreibt Architektur, tägliche Abläufe, Asset-Build, Release-Workflow und die verfügbaren APIs.

## 1. Zielbild und Architektur

Das AddOn trennt drei Ebenen sauber:

1. PHP-Backend (Profile, Konfiguration, Asset-Auslieferung, Demo-Seiten)
2. JavaScript-Runtime (`assets/cke5.js` + interne/externe Plugins)
3. Ausgelieferte Dateien in `public/assets/addons/cke5` (werden vom Browser geladen)

Wichtig: Änderungen in Quell-Dateien unter `public/redaxo/src/addons/cke5/assets` müssen immer mit den ausgelieferten Dateien unter `public/assets/addons/cke5` synchron bleiben.

## 2. Verzeichnis-Überblick

Addon-Root:

- `public/redaxo/src/addons/cke5`

Wichtige Quellpfade:

- `assets/cke5.js` (bearbeitbare Runtime)
- `assets/cke5_content_styles.css` (Frontend-Content-Styles)
- `assets/plugins/*.js` (interne Runtime-Plugins)
- `install/default_bundle.json` (Demo-/Default-Profile)
- `lib/Cke5/Creator/Cke5ProfilesCreator.php` (DB-Profil -> Runtime-JSON)
- `lib/Cke5/Provider/Cke5AssetsProvider.php` (Asset-Einbindung)
- `scripts/vendor-update.js` (Vendor-Sync)
- `scripts/check-runtime.js` (Syntax-Check Runtime)
- `scripts/add-demo-variant.js` (Demo-Profil-Varianten)
- `scripts/update-content-styles.js` (Frontend-Content-CSS aktualisieren)

Ausgelieferte Laufzeitdateien:

- `public/assets/addons/cke5/cke5.js`
- `public/assets/addons/cke5/cke5profiles.js`
- `public/assets/addons/cke5/cke5_content_styles.css`
- `public/assets/addons/cke5/plugins/*.js`

## 3. Lokales Setup für Entwicklung

Schritte:

1. In den Addon-Ordner wechseln:

   ```bash
   cd public/redaxo/src/addons/cke5
   ```

2. Node-Abhängigkeiten installieren:

   ```bash
   pnpm install
   ```

3. Runtime prüfen:

   ```bash
   pnpm run check:runtime
   ```

4. REDAXO-Backend öffnen und mit Demo-Profil testen:

   - `index.php?page=cke5/main/demo`

## 4. Täglicher Workflow (Schritt für Schritt)

### 4.1 Runtime ändern

1. Änderungen in `assets/cke5.js` machen.
2. Falls neue interne Plugins nötig sind: Datei in `assets/plugins/` anlegen.
3. Interne Plugin-Datei nach `public/assets/addons/cke5/plugins/` synchronisieren.
4. Bei Bedarf Plugin-Liste in `Cke5AssetsProvider` ergänzen.
5. `pnpm run check:runtime` ausführen.
6. Demo-Seite im Backend neu laden und Konsole prüfen.

### 4.2 Profile ändern

1. Änderungen an Profil-Mapping in `lib/Cke5/Creator/Cke5ProfilesCreator.php` vornehmen.
2. Falls neue Profilfelder nötig sind: Formularseite + DB-Migration ergänzen.
3. Profile regenerieren lassen (über AddOn-Mechanik/Backend).
4. Prüfen, dass `public/assets/addons/cke5/cke5profiles.js` den erwarteten Zustand enthält.

### 4.3 Demo-Profile pflegen

Single Source of Truth:

- `install/default_bundle.json`

Neue Variante erstellen:

```bash
pnpm run demo:variant -- --name demo_marketing --desc "Demo Marketing Profil"
```

Nützliche Optionen:

- `--from demo_light`
- `--overwrite`

Danach die Variante in `install/default_bundle.json` inhaltlich feinjustieren.

## 5. Asset-Build und Synchronisation

### 5.1 CKEditor-Vendor aktualisieren

Dieser Schritt holt die modernen CKEditor-Dateien aus npm und synchronisiert sie in den Vendor-Ordner des AddOns.

```bash
pnpm run vendor:update
```

Quelle:

- `node_modules/ckeditor5/dist`

Ziel:

- `assets/vendor/ckeditor5-modern`

### 5.2 Frontend-Content-Styles für alle Plugins aktualisieren

Für die veröffentlichte Ausgabe im Frontend wird das offizielle Content-CSS aus CKEditor 5 verwendet.

```bash
pnpm run content-styles:update
```

Quelle:

- `node_modules/ckeditor5/dist/ckeditor5-content.css`

Ziele:

- `assets/cke5_content_styles.css`
- `public/assets/addons/cke5/cke5_content_styles.css`

Hinweis:

- Eigene Design-Anpassungen nicht direkt in der generierten Basisdatei pflegen, sondern als zusätzliche Override-Datei danach laden.

### 5.3 Runtime-Syntax prüfen

```bash
pnpm run check:runtime
```

Der Check validiert aktuell mindestens `assets/cke5.js`.

## 6. Install-/Update-Verhalten

Install- und Updatepfad verwenden bewusst Bundle-Import statt alter SQL-Seeds.

Wesentliche Punkte:

1. `install/default_bundle.json` wird importiert.
2. Demo-Profile (`demo_default`, `demo_light`, `demo_full_expert`) werden bei Install/Update gezielt überschrieben.
3. `data.sql` wird nicht mehr als primäre Quelle verwendet.

## 7. API-Dokumentation

## 7.1 PHP-API: Externe Plugin-Registrierung

Klasse:

- `Cke5\PluginRegistry`

Verwendung in `boot.php` eines anderen AddOns:

```php
<?php

use Cke5\PluginRegistry;

PluginRegistry::addPlugin(
    'myPlugin',
    rex_url::addonAssets('my_addon', 'js/cke5-my-plugin.js'),
    [
        'toolbarAliases' => [
            'legacyToken' => 'myPluginToolbarItem',
        ],
        'myCustomConfig' => true,
    ]
);
```

Bedeutung der Parameter:

1. Plugin-Key (`myPlugin`)
2. URL zur JS-Datei
3. Optionales Konfigurationsarray (z. B. Toolbar-Aliases)

## 7.2 JavaScript-API: Externe Plugins

Externe Datei muss sich in `window.CKE5_EXTERNAL_PLUGINS` registrieren:

```js
(function () {
  window.CKE5_EXTERNAL_PLUGINS = window.CKE5_EXTERNAL_PLUGINS || {};

  window.CKE5_EXTERNAL_PLUGINS.myPlugin = function initMyPlugin(ctx) {
    // ctx: { name, editor, editorId, element, profileOptions, config }
  };

  window.CKE5_EXTERNAL_PLUGINS.myPlugin.transformOptions = function transform(ctx) {
    // ctx: { name, options, element, config }
    return ctx.options;
  };
})();
```

Lebenszyklus:

1. `transformOptions(ctx)` vor Editor-Erstellung
2. `initMyPlugin(ctx)` nach Editor-Erstellung

## 7.3 Profil-API im Expert-JSON

Externe Plugins pro Profil aktivieren:

```json
{
  "externalPlugins": ["myPlugin"]
}
```

Alternativ sind CSV-Werte möglich, je nach bestehendem Profilformat.

## 7.4 Interne Runtime-Hooks (Auszug)

Wichtige globale Registries:

1. `window.CKE5_NATIVE_PLUGINS` (interne AddOn-Plugins)
2. `window.CKE5_EXTERNAL_PLUGINS` (fremde AddOn-Plugins)

Wichtiger Kontext bei Plugin-Callbacks:

1. `editor`
2. `editorId`
3. `element`
4. `profileOptions`
5. `config`

## 8. Qualitätssicherung vor Commit

Minimal-Checkliste:

1. `pnpm run check:runtime` ist grün.
2. Demo-Seite lädt ohne JS-Fehler.
3. Bei Runtime-Änderungen: Quell- und ausgelieferte Datei synchron.
4. Bei CSS-Änderungen: `pnpm run content-styles:update` ausgeführt.
5. Bei Profiländerungen: Ergebnis in `cke5profiles.js` geprüft.

## 9. Release-Vorbereitung

Empfohlene Reihenfolge:

1. Version in `package.yml` anpassen.
2. Changelog aktualisieren.
3. Builds/Syncs ausführen:
   - `pnpm run vendor:update`
   - `pnpm run content-styles:update`
   - `pnpm run check:runtime`
4. Backend-Funktionscheck (Demo, Profile, Konfiguration).
5. Commit und Push.

## 10. Typische Fehlerbilder und Lösungen

1. Profiloption wirkt nicht im Editor:
   - Prüfen, ob `Cke5ProfilesCreator` die Option in JSON ausgibt.
   - Prüfen, ob Runtime-Option tatsächlich aus `cke5profiles.js` ankommt.

2. Plugin-Toolbar-Button fehlt:
   - Plugin ist nicht geladen oder Token stimmt nicht.
   - Bei Legacy-Token `toolbarAliases` setzen.

3. Änderungen sichtbar im Source, aber nicht im Browser:
   - Prüfen, ob `public/assets/addons/cke5/*` synchronisiert wurde.
   - Browser-Hard-Reload durchführen.

4. Frontend sieht anders aus als Editor:
   - `ck-content` Klasse am Ausgabeelement prüfen.
   - `cke5_content_styles.css` korrekt eingebunden?

## 11. Kurzreferenz: wichtigste Kommandos

```bash
pnpm install
pnpm run vendor:update
pnpm run content-styles:update
pnpm run check:runtime
pnpm run demo:variant -- --name demo_example --desc "Demo Beispiel"
```
