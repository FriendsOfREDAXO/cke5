# CKE5 Build- & Plugin-Dokumentation

Diese Datei fasst den aktuellen technischen Stand für Build-Quellen, interne Plugins und externe Plugin-Integration zusammen.

## 1) Wo liegen Build-Quellen und Runtime-Dateien?

### Addon-Root
- `public/redaxo/src/addons/cke5`

### CKEditor-Vendor (modern, aus npm)
- Quelle (npm): `node_modules/ckeditor5/dist`
- Ziel (ausgeliefert): `assets/vendor/ckeditor5-modern`

### Runtime-Dateien des Addons
- Quell-Runtime (editierbar): `assets/cke5.js`
- Ausgelieferte Runtime: `public/assets/addons/cke5/cke5.js`
- Profile-JSON (generiert): `public/assets/addons/cke5/cke5profiles.js`

### Build-/Sync-Skripte
- Vendor-Update: `pnpm run vendor:update`
- Runtime-Check: `pnpm run check:runtime`
- Skriptdatei: `scripts/vendor-update.js`

## 2) Eigene (interne) Plugins im Addon

Interne Runtime-Plugins liegen als Einzeldateien unter `assets/plugins/`.

Aktuell enthalten:
- `assets/plugins/redaxo-link-integration.js`
- `assets/plugins/redaxo-media-image.js`
- `assets/plugins/redaxo-paste-plain-text-toggle.js`

Die Runtime `assets/cke5.js` enthält dafür nur noch den Loader über `window.CKE5_NATIVE_PLUGINS`.

Die Dateien werden im Backend über `Cke5AssetsProvider::provideCke5BaseData()` vor `cke5.js` eingebunden.

Wenn ein neues internes Plugin ergänzt wird:
1. Neue Plugin-Datei unter `assets/plugins/` anlegen.
2. Die Datei zusätzlich nach `public/assets/addons/cke5/plugins/` synchronisieren.
3. In `lib/Cke5/Provider/Cke5AssetsProvider.php` zur Lade-Liste hinzufügen.
4. Falls nötig, Plugin-Namen im Loader `cke5_get_native_redaxo_plugins()` in `assets/cke5.js` ergänzen.
5. `pnpm run check:runtime` ausführen.

## 3) Profiloption: Plaintext-Paste Default

### Profil-Feld
- DB-Spalte: `paste_plain_text_default` (Tabelle `rex_cke5_profiles`)
- Formular: `pages/profiles.profiles.php`

### Mapping ins Profil-JSON
- `lib/Cke5/Creator/Cke5ProfilesCreator.php`
- Ausgabe als boolesche Option: `pastePlainTextDefault`

### Runtime-Verhalten
- `assets/cke5.js` liest `editor.config.get('pastePlainTextDefault')`
- Command `pasteAsPlainText` wird initial auf aktiv/inaktiv gesetzt

## 4) Externe Plugins: können wir die erstellen und einbinden?

Ja. Es gibt bereits eine externe Plugin-Registry.

### Server-seitig registrieren (PHP)
Datei z. B. in `boot.php` eines Addons:

```php
<?php

use Cke5\PluginRegistry;

PluginRegistry::addPlugin(
    'myPlugin',
    rex_url::addonAssets('my_addon', 'js/cke5-my-plugin.js'),
    [
        'toolbarAliases' => [
            'myLegacyToolbarToken' => 'myPluginToolbarItem',
        ],
        'anyCustomConfig' => true,
    ]
);
```

### Client-seitig bereitstellen (JS)
Die externe JS-Datei muss `window.CKE5_EXTERNAL_PLUGINS[name]` registrieren.

```js
(function () {
  window.CKE5_EXTERNAL_PLUGINS = window.CKE5_EXTERNAL_PLUGINS || {};

  window.CKE5_EXTERNAL_PLUGINS.myPlugin = function initMyPlugin(ctx) {
    // ctx: { name, editor, editorId, element, profileOptions, config }
    // Hier kann man editor commands, buttons, listeners usw. registrieren.
  };

  window.CKE5_EXTERNAL_PLUGINS.myPlugin.transformOptions = function transform(ctx) {
    // ctx: { name, options, element, config }
    // Optional: toolbar/items/plugins/Config vor Editor-Init modifizieren.
    return ctx.options;
  };
})();
```

### Profil-seitig aktivieren
Im Profil muss `externalPlugins` gesetzt sein (Array oder CSV), damit das Plugin aktiv wird.

Beispiel im Expert-JSON:

```json
{
  "externalPlugins": ["myPlugin"]
}
```

## 5) Empfohlener Workflow für neue externe Plugins

1. Plugin-JS in eigenem Addon anlegen.
2. In `boot.php` per `PluginRegistry::addPlugin(...)` registrieren.
3. Profil auf `externalPlugins` erweitern.
4. Seite mit Editor öffnen und Console/Toolbar prüfen.
5. Bei Toolbar-Token-Migration `toolbarAliases` verwenden.
6. Runtime mit `pnpm run check:runtime` prüfen.

## 6) Häufige Stolpersteine

- Nur Toolbar-Token im Profil setzen reicht nicht: Das Plugin muss auch real im Build/Runtime verfügbar sein.
- Unkontrollierte „alle Plugins automatisch laden“-Ansätze führen leicht zu Konflikten (z. B. Legacy/Modern List-Plugins).
- Änderungen immer in Quell- und ausgelieferten Dateien synchron halten (`assets/cke5.js`, `assets/plugins/*.js`, `public/assets/addons/cke5/cke5.js`, `public/assets/addons/cke5/plugins/*.js`).
