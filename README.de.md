# CKEditor 5 für REDAXO

CKEditor-5-Integration für REDAXO mit profilbasierter Konfiguration, REDAXO-Medien- und Link-Dialogen, Snippets, Style-Management und Import/Export-Workflows.

## Version

Aktuelle Entwicklungsversion: `7.0.0-dev`

## Voraussetzungen

- REDAXO: `>= 5.12`
- PHP: `>= 7.2, < 9`
- Konflikte:
  - `mblock < 3.4.0`
  - `mform < 6.1.0`

## Neu in 7.0.0-dev

- Umstellung auf den offiziellen CKEditor-5-Build als Basis
- Neue native Runtime-Plugins und überarbeitete Dialoge
- Templates durch Snippets ersetzt
- Erweiterter Profil-Export/Import inklusive abhängiger Daten:
  - profiles
  - style_groups
  - styles
  - snippets
- Neue Plugin-/Build-Dokumentation in `PLUGIN_DEVELOPMENT.md`
- Bereinigung von Legacy- und verwaisten Vendor-/Runtime-Dateien

## Funktionsüberblick

### Editor und Bedienung

- Moderne CKEditor-5-Integration im REDAXO-Backend
- Theme-Unterstützung (`dark`, `auto`, `notheme`)
- Sprachabhängige Platzhalter sowie UI-/Content-Sprachsteuerung
- Höhensteuerung per Datenattributen (`data-min-height`, `data-max-height`)
- Stabile Initialisierung für dynamische/repetierende Felder (z. B. MBlock)

### Profilsystem

- Profilmanager für Editor-Konfigurationen
- Drag-and-drop-/Tag-basierter Profil-Editor
- Expertenmodus mit `expert_definition` + `expert_suboption`
- Vorschau-Seite mit Integrationsbeispielen und Profil-Details

### Styles und Snippets

- Einzelne Styles mit Element/Klassen und optionalem CSS
- Style-Gruppen mit JSON-Konfiguration und optionalem CSS
- Snippets pro Profil auswählbar (Ersatz für Templates)
- Auto-generiertes Backend-CSS aus Style-/Gruppen-Definitionen

### Medien und Links

- REDAXO-Medienintegration (`openREXMedia`) für Bildeinfügen/-ersetzen
- REDAXO-Linkintegration (`openLinkMap`, Medienlinks, `mailto:`, `tel:`, YTable)
- Bild-Upload-Endpunkt für Mediapool-Uploads
- Absicherung der Bild-Link-Funktion in der Bild-Toolbar (`linkImage`)

### Plugin-Runtime

- Native Addon-Plugins zur Laufzeit:
  - `RedaxoLinkIntegration`
  - `RedaxoMediaImage`
  - `RedaxoMediaVideo`
  - `RedaxoSnippets`
  - `RedaxoPastePlainTextToggle`
- Unterstützung externer Plugins über Registry und JS-Konfiguration
- Toolbar-Alias-Transformationen für externe Plugins

## Installation

1. Addon installieren (Installer oder Deployment).
2. REDAXO-Update/Install-Routine ausführen.
3. Unter `CKEditor 5 > Profiles` mindestens ein Profil konfigurieren.
4. Profil im Textarea über `data-profile` verwenden.

## Basisverwendung

### Minimale Textarea-Integration

```php
<textarea
  class="form-control cke5-editor"
  data-profile="default"
  data-lang="<?php echo \Cke5\Utils\Cke5Lang::getUserLang(); ?>"
  data-content-lang="<?php echo \Cke5\Utils\Cke5Lang::getOutputLang(); ?>"
  name="REX_INPUT_VALUE[1]"
>REX_VALUE[1]</textarea>
```

### Mit Höhenbegrenzung

```php
<textarea
  class="form-control cke5-editor"
  data-profile="default"
  data-min-height="220"
  data-max-height="700"
  data-lang="<?php echo \Cke5\Utils\Cke5Lang::getUserLang(); ?>"
  name="REX_INPUT_VALUE[2]"
>REX_VALUE[2]</textarea>
```

### Frontend-Ausgabe

```html
REX_VALUE[id="1" output="html"]
```

## Integrationsbeispiele

### MForm

```php
$mform = new MForm();
$mform->addTextAreaField(1, [
    'label' => 'Text',
    'class' => 'cke5-editor',
    'data-profile' => 'default',
    'data-lang' => \Cke5\Utils\Cke5Lang::getUserLang(),
    'data-content-lang' => \Cke5\Utils\Cke5Lang::getOutputLang(),
]);

echo $mform->show();
```

### MBlock

```php
$id = 1;
$mform = new MForm();
$mform->addFieldset('Accordion');
$mform->addTextField("$id.0.title", ['label' => 'Titel']);
$mform->addTextAreaField("$id.0.text", [
    'label' => 'Text',
    'class' => 'cke5-editor',
    'data-profile' => 'default',
    'data-lang' => \Cke5\Utils\Cke5Lang::getUserLang(),
    'data-content-lang' => \Cke5\Utils\Cke5Lang::getOutputLang(),
]);

echo MBlock::show($id, $mform->show());
```

### YForm (eigene Attribute)

```json
{"class":"cke5-editor","data-profile":"default","data-lang":"de","data-content-lang":"de"}
```

## Profile: Hinweise aus der Praxis

- Toolbar verwendet CKEditor-IDs (`link`, `insertImage`, `snippets`, ...).
- Legacy-Aliase werden intern migriert/normalisiert, wo nötig.
- Snippets werden pro Profil ausgewählt.
- Style-Gruppen und Styles werden pro Profil gewählt und zusammengeführt.
- Im Profil-Editor können Platzhalter pro REDAXO-Sprache gesetzt werden.

## Snippets statt Templates

Templates sind nicht mehr Teil des aktiven Workflows.
Für wiederverwendbare Inhaltsbausteine werden Snippets verwendet.

Empfohlener Ablauf:

1. Snippets unter `Profiles > Customise > Snippets` anlegen.
2. Snippets einem oder mehreren Profilen zuweisen.
3. Button `snippets` in der Toolbar aktivieren.

## Export und Import

### Export

`Profiles > Export` exportiert gewählte Profile inklusive Abhängigkeiten.

Export-Payload enthält:

- `profiles`
- `style_groups`
- `styles`
- `snippets`

### Import

`Profiles > Import` unterstützt:

- neues Bundle-Format (Profile + Abhängigkeiten)
- Legacy-Format (nur Profile)

Beim Bundle-Import erfolgt ein ID-basiertes Upsert für die abhängigen Tabellen, danach der Profilimport.

## Konfigurationsseite

`CKEditor 5 > Config` bietet:

- Lizenzschlüssel-Konfiguration
- Upload/Ersetzen der Editor-Runtime (`.js`, `.js.map`)
- Upload/Ersetzen von Übersetzungsdateien (`.js`)

Standard-Runtimepfad ist der moderne Build unter:

- `assets/addons/cke5/vendor/ckeditor5-modern/`

## API-Beispiel

Programmgesteuert ein Expertenprofil anlegen:

```php
use Cke5\Creator\Cke5ProfilesApi;

$definition = json_encode([
    'toolbar' => [
        'items' => ['heading', '|', 'bold', 'italic', 'link', 'snippets', 'undo', 'redo'],
    ],
], JSON_UNESCAPED_UNICODE);

Cke5ProfilesApi::addProfile(
    'project_expert',
    'Projekt-Expertenprofil',
    $definition,
    null
);
```

## Custom-CSS-Strategie

Kombinierbar sind:

- statisches Custom-CSS in Addon-/Projekt-Assets
- generiertes CSS aus Style-/Gruppen-Definitionen
- optionale externe CSS-Pfade pro Style/Style-Gruppe

Bei Änderungen an Styles/Style-Gruppen werden die Backend-CSS-Artefakte neu erzeugt.

## Plugin-Entwicklung

Für Build-Quellen, Runtime-Plugin-Architektur und Einbindung externer Plugins siehe:

- `PLUGIN_DEVELOPMENT.md`

## Fehlerbehebung

### Editor initialisiert nicht

- Prüfen, ob die Textarea die Klasse `cke5-editor` besitzt.
- Prüfen, ob `data-profile` auf ein vorhandenes Profil zeigt.
- Prüfen, ob `cke5profiles.js` erzeugt und geladen wurde.

### Link-Button am Bild fehlt/deaktiviert

- Prüfen, ob `linkImage` in der Bild-Toolbar enthalten ist.
- Prüfen, ob der Build das Plugin `LinkImage` enthält.
- Nach JS-Änderungen Backend hart neu laden.

### Übersetzungen werden nicht geladen

- Übersetzungspfad und Dateien in der Config-Seite prüfen.
- Prüfen, ob Profil-/Benutzersprache zu vorhandenen CKEditor-Translation-Dateien passt.

## Lizenz

Siehe `LICENSE.md`.

## Support

- Issues: https://github.com/FriendsOfREDAXO/cke5/issues
