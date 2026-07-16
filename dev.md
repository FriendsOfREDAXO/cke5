# CKE5 Entwicklerdokumentation

Dieses Dokument beschreibt den aktuellen technischen Stand des AddOns, den Build- und Release-Workflow und vor allem die Entwicklung eigener Widgets.

## 1. Aktueller Stand (Kurzfassung)

1. Interne Video-Integration läuft als Widget.
2. Der frühere Plugin-Pfad ist entfernt.
3. Bestehende Profile mit altem Toolbar-Token bleiben kompatibel, weil das Runtime-Mapping alte Tokens auf das Widget umleitet.
4. `for_table` ist als internes Plugin aktiv und deckt Table/Column/Row/Cell-Properties ab.
5. Listen-Nummerierung nutzt einen einheitlichen Codepfad für alle Browser (inklusive Startindex und hierarchischer Dezimaldarstellung).

Konsequenz für neue Entwicklungen:

1. Für lokale Videos nur den Widget-Ansatz erweitern.
2. Keine neue Logik mehr in einem separaten, zweiten Video-Plugin aufbauen.

## 2. Architekturüberblick

Das AddOn ist in drei Ebenen aufgeteilt:

1. PHP: Profile, Backend-Formulare, Asset-Registrierung.
2. Runtime-JS: Editor-Erstellung, Plugin-Registrierung, Option-Transformation.
3. Auslieferung: Dateien unter public/assets/addons/cke5.

Wichtige Quellpfade:

1. public/redaxo/src/addons/cke5/assets/cke5.js
2. public/redaxo/src/addons/cke5/assets/plugins/
3. public/redaxo/src/addons/cke5/assets/css/cke5.css
4. public/redaxo/src/addons/cke5/lib/Cke5/Creator/Cke5ProfilesCreator.php
5. public/redaxo/src/addons/cke5/lib/Cke5/Provider/Cke5AssetsProvider.php
6. public/redaxo/src/addons/cke5/install/default_bundle.json
7. public/redaxo/src/addons/cke5/pages/profiles.customise.global.php

## 3. Lokales Setup

```bash
cd public/redaxo/src/addons/cke5
pnpm install
pnpm run check:runtime
```

Demo-Seite:

1. index.php?page=cke5/main/demo

## 4. Täglicher Entwicklungsablauf

## 4.1 Runtime-Änderungen

1. Änderung in assets/cke5.js oder assets/plugins/*.js.
2. Bei neuen internen Plugins Einbindung in Cke5AssetsProvider ergänzen.
3. Toolbar-Token-Mapping in assets/cke5.js prüfen.
4. pnpm run check:runtime ausführen.
5. Demo und Profilseite testen.

## 4.2 Profil-Mapping

1. Mapping in Cke5ProfilesCreator anpassen.
2. Bei neuen Feldern Backend-Formular und Persistenz ergänzen.
3. Prüfen, ob das JSON-Profil die neuen Werte korrekt enthält.

Hinweis zu globalen Defaults:

1. Globale Defaults werden in Cke5ProfilesCreator als Fallback gemerged.
2. Profilwerte haben Vorrang, wenn explizit gesetzt.
3. Für Schalter-basierte globale Defaults immer prüfen, ob Enable-Flags korrekt ausgewertet werden.

## 4.3 Default-Bundle

Single Source of Truth für Demo- und Beispielprofile:

1. install/default_bundle.json

Neue Demo-Variante:

```bash
pnpm run demo:variant -- --name demo_example --desc "Demo Beispiel"
```

## 5. Build und Synchronisation

### Vendor aktualisieren

Der Vendor-Update läuft reproduzierbar über das Addon-`package.json`:

```bash
cd public/redaxo/src/addons/cke5
pnpm install
pnpm run vendor:update
```

Aktueller Vendor-Stand:

1. CKEditor 5 `48.3.0`
2. generated runtime assets under `assets/vendor/ckeditor5-modern`

Das Script aktualisiert anschließend beide Stände:

1. `public/redaxo/src/addons/cke5/assets/vendor/ckeditor5-modern`
2. `public/assets/addons/cke5/vendor/ckeditor5-modern`

Hinweis für neue Plugins oder Runtime-Anpassungen:

1. Änderungen zuerst in `public/redaxo/src/addons/cke5/assets/...` vornehmen.
2. Wenn nötig die ausgelieferte Kopie unter `public/assets/addons/cke5/...` nachziehen oder per Build-Script synchronisieren.
3. Danach `pnpm run check:runtime` ausführen.

Wenn du die ausgelieferten Runtime-Dateien ebenfalls neu bauen musst, danach zusätzlich:

```bash
pnpm run check:runtime
```

Content-Styles synchronisieren:

```bash
pnpm run content-styles:update
```

Runtime-Syntax prüfen:

```bash
pnpm run check:runtime
```

## 6. Interne und externe Plugin-Registrierung

## 6.1 Interne Native-Plugins

Interne Plugins registrieren sich in window.CKE5_NATIVE_PLUGINS.

Beispielstruktur:

```js
(function () {
   window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

   window.CKE5_NATIVE_PLUGINS.MyNativePlugin = function createMyNativePlugin(ctx) {
      const cke = ctx && ctx.cke ? ctx.cke : null;
      const BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {};

      return class MyNativePlugin extends BasePlugin {
         static get pluginName() {
            return 'MyNativePlugin';
         }

         init() {
            // Editor-Integration
         }
      };
   };
})();
```

## 6.2 Externe Plugins

PHP-Registrierung:

```php
<?php

use Cke5\PluginRegistry;

PluginRegistry::addPlugin(
      'myPlugin',
      rex_url::addonAssets('my_addon', 'js/cke5-my-plugin.js'),
      [
            'toolbarAliases' => [
                  'legacyToken' => 'myToolbarToken',
            ],
      ]
);
```

JS-Registrierung:

```js
(function () {
   window.CKE5_EXTERNAL_PLUGINS = window.CKE5_EXTERNAL_PLUGINS || {};

   window.CKE5_EXTERNAL_PLUGINS.myPlugin = function initMyPlugin(ctx) {
      // ctx enthält editor, editorId, element, profileOptions und config
   };

   window.CKE5_EXTERNAL_PLUGINS.myPlugin.transformOptions = function transform(ctx) {
      return ctx.options;
   };
})();
```

## 7. Widgets bauen: Praxisleitfaden

Widget-Entwicklung in CKEditor 5 folgt immer demselben Muster:

1. Schema registrieren.
2. Upcast definieren (HTML nach Model).
3. DataDowncast definieren (Model nach gespeichertes HTML).
4. EditingDowncast definieren (Model nach editierbares Widget).
5. Befehl und Toolbar-Button bereitstellen.

Minimaler Ablauf:

```js
schema.register('myWidget', {
   isObject: true,
   allowWhere: '$block',
   allowAttributes: ['title', 'variant']
});

conversion.for('upcast').elementToElement({
   view: { name: 'section', classes: 'my-widget' },
   model: (viewElement, { writer }) => writer.createElement('myWidget', {
      title: viewElement.getAttribute('data-title') || '',
      variant: viewElement.getAttribute('data-variant') || 'default'
   })
});

conversion.for('dataDowncast').elementToElement({
   model: 'myWidget',
   view: (modelElement, { writer }) => writer.createContainerElement('section', {
      class: 'my-widget',
      'data-title': modelElement.getAttribute('title') || '',
      'data-variant': modelElement.getAttribute('variant') || 'default'
   })
});

conversion.for('editingDowncast').elementToElement({
   model: 'myWidget',
   view: (modelElement, { writer }) => {
      const section = writer.createContainerElement('section', { class: 'my-widget' });
      return cke.toWidget(section, writer, { label: 'My Widget' });
   }
});
```

## 8. Beispiel: UIkit3 Card Widget

Zielmarkup:

```html
<div class="uk-card uk-card-default uk-card-body" data-cke-widget="uikit-card" data-title="Titel" data-text="Text"></div>
```

Modellattribute:

1. title
2. text
3. style (default, primary, secondary)

Toolbar-Flow:

1. Button öffnet Dialog.
2. Dialog schreibt title, text und style.
3. Command fügt myUikitCard ins Model ein.

DataDowncast:

1. Klasse anhand style wählen.
2. Daten in data-Attribute spiegeln.
3. Optional strukturierten Inhalt erzeugen, z. B. h3 + p.

## 9. Beispiel: Accordion Widget

Zielmarkup:

```html
<div class="uk-accordion" data-cke-widget="uikit-accordion" data-items='[{"title":"A","body":"..."}]'></div>
```

Empfehlung:

1. Items als JSON-String im Attribut speichern.
2. Im Upcast defensiv parsen (try/catch).
3. Beim Downcast nur validierte Daten serialisieren.

Typischer Command:

1. Dialog sammelt Einträge.
2. Command schreibt items als String.
3. EditingDowncast zeigt eine kompakte Vorschau mit Anzahl der Einträge.

## 10. Widget-Best-Practices

1. Upcast robust halten: fehlende Attribute mit Defaults auffüllen.
2. Keine Inline-Skripte in Widget-HTML.
3. HTML so schreiben, dass Frontend-Ausgabe ohne Editor-Kontext stabil bleibt.
4. Bei komplexen Widgets klare data-Attribute statt impliziter DOM-Heuristik nutzen.
5. Für Dialoge eigene CSS-Klassen verwenden, nicht auf Vendor-Klassen verlassen.
6. Theme-aware entwickeln: Light-Defaults plus identische Dark-Werte für expliziten und Auto-Dark-Mode.

## 11. Theme-Aware Muster (REDAXO)

Empfohlenes Muster:

```css
.my-widget-dialog {
   --x-bg: #ffffff;
   --x-text: #1a1a1a;
   --x-border: #d0d0d0;
}

body.rex-theme-dark .my-widget-dialog {
   --x-bg: #1f2933;
   --x-text: #e8f0f7;
   --x-border: #3a4654;
}

@media (prefers-color-scheme: dark) {
   body.rex-has-theme:not(.rex-theme-light) .my-widget-dialog {
      --x-bg: #1f2933;
      --x-text: #e8f0f7;
      --x-border: #3a4654;
   }
}
```

## 12. Qualitätssicherung vor Commit

1. pnpm run check:runtime ist grün.
2. Demo-Seite läuft ohne JS-Fehler.
3. Dialoge auf Desktop und Mobile geprüft.
4. Light- und Dark-Mode geprüft.
5. Bestehende Profile mit Legacy-Tokens geprüft.
6. Profilmanager und Defaults-Seite auf Widget-Placeholder und Toggle-/Collapse-Verhalten geprüft.
7. Table-Advanced-Felder für Table/Column/Row/Cell im Profil-Editor geprüft.
8. Nummerierte Listen auf Safari/Chromium/Firefox mit Startindex und verschachtelten Ebenen geprüft.

## 13. Release-Checkliste

1. package.yml Version erhöhen.
2. Changelog aktualisieren.
3. vendor:update, content-styles:update und check:runtime ausführen.
4. Demo und Profil-Editor durchtesten.
5. Commit und Push.

## 14. Schnellreferenz Befehle

```bash
pnpm install
pnpm run vendor:update
pnpm run content-styles:update
pnpm run check:runtime
pnpm run demo:variant -- --name demo_example --desc "Demo Beispiel"
```

## 15. Learnings aus for_table (wichtig)

Diese Punkte sind aus der praktischen Nacharbeit am Tabellen-Feature entstanden und gelten ab jetzt als verbindliche Leitplanken.

### 15.1 CKEditor UI Library beachten

1. Für neue UI-Komponenten im Dropdown grundsätzlich die CKEditor-UI-Patterns aus der offiziellen Doku nutzen (Views, Template-Binding, Fokus-Handling).
2. Direkte DOM-Manipulation über fremde Elemente vermeiden. Wenn DOM nötig ist, dann nur innerhalb der eigenen View-Struktur.
3. Dropdown-Inhalte müssen in einen eigenen Content-Wrapper mit definiertem Sizing, damit Inputs/Selects nicht aus dem Panel laufen.
4. Aktionen im Panel müssen nach Ausführung immer den Editor fokussieren (`editor.editing.view.focus()`).

### 15.2 Tabellen-Klassen und Conversion

1. Reines `attributeToAttribute` auf `class` reicht nicht immer, wenn andere Features ebenfalls Klassen setzen.
2. Für Tabellen-/Zellklassen eigene Downcast-Handler verwenden, die Klassen-Tokens mergen statt Klassen komplett zu überschreiben.
3. Tabellenstil-Klasse auf `figure.table` und auf das innere `table` spiegeln, damit bestehende CSS-Selektoren in Projekten sofort greifen.
4. Nach jeder Änderung über `editor.getData()` prüfen, ob die erwarteten Klassen tatsächlich im Output landen.

### 15.3 Profil-/Toolbar-Kompatibilität

1. Legacy-Tokens weiter unterstützen:
   1. `tableProperties` -> `forTableProperties`
   2. `tableColumnProperties` -> `forTableColumnProperties`
   3. `tableRowProperties` -> `forTableRowProperties`
   4. `tableCellProperties` -> `forTableCellProperties`
2. Wenn `forTableProperties` in bestehenden Profilen gesetzt ist, Row/Column-Properties automatisch ergänzen, damit Altprofile ohne manuelle Migration funktionieren.

### 15.4 Synchronisation Quelle -> Auslieferung ist Pflicht

1. Änderungen immer zuerst unter `public/redaxo/src/addons/cke5/assets/...` vornehmen.
2. Danach die ausgelieferten Dateien unter `public/assets/addons/cke5/...` synchronisieren.
3. Ohne diesen Schritt zeigt der Browser oft alten Code, obwohl die Quell-Datei korrekt ist.

### 15.5 Zusätzliche QA für Tabellen-UI

Vor Commit gezielt prüfen:

1. Dialog-Breiten auf Desktop und Mobile (kein Überlauf, kein gequetschtes Zweispalten-Layout).
2. Apply/Reset-Verhalten für Tabelle, Spalte, Zeile, Zelle.
3. Klassenwirkung visuell im Editor und technisch im HTML-Output.
4. Verhalten mit und ohne definierte `table_classes_definition`, `table_column_classes_definition`, `table_row_classes_definition`, `table_cell_classes_definition`.

### 15.6 Learnings aus dem Follow-up nach PR #224

1. Persistenz und UI müssen immer gemeinsam erweitert werden: neue Profilfelder gehören in DB-Schema, Formularseiten und Initialisierungs-JS.
2. Für `forTableStyle` ist ein eigener Downcast-Handler robuster als ein reines `attributeToAttribute`, wenn Wrapper und inneres `table` konsistent dieselben Styles erhalten sollen.
3. Dialog-Hosts ohne starre Mindestbreite bauen, damit mobile Viewports nicht horizontal überlaufen.
4. Bei Listen-Bugs einen einheitlichen Browser-Codepfad bevorzugen, wenn dadurch Verhalten und Wartbarkeit stabiler werden.
