# CKEditor 5 für REDAXO

CKEditor-5-Integration für REDAXO mit profilbasierter Konfiguration, REDAXO-Medien- und Link-Dialogen, Snippets, Style-Management und Import/Export-Workflows.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/cke5.png)

## Version

Aktuelle Entwicklungsversion: `7.0.0-dev`

## Voraussetzungen

- REDAXO: `>= 5.19`
- PHP: `>= 8.1, < 9`
- Konflikte:
  - `mblock < 4.3.0`
  - `mform < 7.0.0`

## Neu in 7.0.0-dev

- Umstellung auf den offiziellen CKEditor-5-Build als Basis
- Neue native Runtime-Plugins und überarbeitete Dialoge
- Templates durch Snippets ersetzt
- Erweiterter Profil-Export/Import inklusive abhängiger Daten:
  - profiles
  - style_groups
  - styles
  - snippets
- Neue Entwicklerdokumentation in `dev.md`
- Bereinigung von Legacy- und verwaisten Vendor-/Runtime-Dateien
- Neue globale Defaults-Seite (`Profiles > Defaults > Global settings`) für Mentions, Sprog-Ersetzungen, yTables, Medien-Defaults und Schriftfamilien-Defaults
- Neuer Editor-Typ `classic_balloon` sowie konfigurierbare Balloon-Toolbar im Profilmanager
- Verbesserte Merge-/Fallback-Logik zwischen Profilwerten und globalen Defaults
- UX-Fixes im Profil-/Defaults-Widget-Handling (Mention-Beispiele, stabile Placeholder, robuste Toggle-/Collapse-Initialisierung)

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
  - `RedaxoSnippets`
  - `RedaxoPastePlainTextToggle`
  - `RedaxoMarkdownPasteToggle`
  - `RedaxoMinimapToggle`
  - `RedaxoVideoWidgetTest`
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

## JSON-Kochbuch (Profilfelder)

Mehrere Profilfelder erwarten JSON-Eingaben. Hier sind funktionierende Startbeispiele.

### 1) `link_decorators_definition`

Damit definierst du manuelle Link-Decorator. Ein typischer Anwendungsfall sind Bootstrap-ähnliche Link-Buttons.

```json
[
  {
    "btnPrimary": {
      "mode": "manual",
      "label": "Button Primary",
      "attributes": {
        "class": "btn btn-primary",
        "role": "button"
      }
    }
  },
  {
    "btnOutline": {
      "mode": "manual",
      "label": "Button Outline",
      "attributes": {
        "class": "btn btn-outline-secondary",
        "role": "button"
      }
    }
  },
  {
    "nofollow": {
      "mode": "manual",
      "label": "Nofollow setzen",
      "attributes": {
        "rel": "nofollow"
      }
    }
  }
]
```

Hinweis: Dieses JSON wird in `link.decorators` des generierten CKEditor-Profils zusammengeführt.

Exklusive Decorator-Gruppen (immer nur einer gleichzeitig):

Wenn mehrere manuelle Decorators gegenseitig exklusiv sein sollen (z. B. Button-Varianten, Farb-Varianten, Badge-Varianten), gib ihnen denselben Wert in `redaxoExclusiveGroup`.

Beispiel:

```json
[
  {
    "btnPrimary": {
      "mode": "manual",
      "label": "Button Primary",
      "classes": "btn btn-primary",
      "redaxoExclusiveGroup": "linkButtonStyle"
    }
  },
  {
    "btnSuccess": {
      "mode": "manual",
      "label": "Button Success",
      "classes": "btn btn-success",
      "redaxoExclusiveGroup": "linkButtonStyle"
    }
  },
  {
    "nofollow": {
      "mode": "manual",
      "label": "Nofollow setzen",
      "attributes": {
        "rel": "nofollow"
      }
    }
  }
]
```

Ergebnis: Im Link-Dialog kann immer nur ein Decorator aus `linkButtonStyle` aktiv sein. Unabhängige Decorators wie `nofollow` bleiben parallel nutzbar.

### 2) `mentions_definition`

Definiert eigene Mention-Feeds.

```json
[
  {
    "marker": "@",
    "minimumCharacters": 1,
    "feed": ["@support", "@sales", "@redaktion", "@admin"]
  },
  {
    "marker": "#",
    "minimumCharacters": 1,
    "feed": ["#news", "#release", "#event", "#faq"]
  }
]
```

### 3) `sprog_mention_definition`

Sprog-Ersetzungen sind JSON-basiert und werden über den Marker `{` angeboten.

```json
[
  { "id": "{{company}}", "text": "Friends Of REDAXO" },
  { "id": "{{support_mail}}", "text": "support@example.org" },
  { "id": "{{hotline}}", "text": "+49 000 123456" }
]
```

### 4) `image_resize_options_definition`

Definiert feste Größenoptionen für die Bild-Toolbar.

```json
[
  { "name": "resizeImage:original", "label": "Original", "value": null },
  { "name": "resizeImage:25", "label": "25%", "value": "25" },
  { "name": "resizeImage:50", "label": "50%", "value": "50" },
  { "name": "resizeImage:75", "label": "75%", "value": "75" }
]
```

Hinweis: Das Addon normalisiert die Namen intern beim Profilaufbau.

### 5) `transformation_extra`

Ergänzt zusätzliche Typing-Transformationen.

```json
[
  { "from": "->", "to": "→" },
  { "from": "<-", "to": "←" },
  { "from": "(c)", "to": "©" },
  { "from": "(r)", "to": "®" }
]
```

### 6) `html_support_allow`

Erlaubt zusätzliche Elemente/Attribute/Klassen/Styles.

```json
[
  {
    "name": "regex(/^(section|article|div)$/)",
    "attributes": true,
    "classes": true,
    "styles": true
  },
  {
    "name": "a",
    "attributes": ["target", "rel", "data-bs-toggle", "data-bs-target"],
    "classes": ["btn", "btn-primary", "btn-outline-secondary"],
    "styles": false
  }
]
```

### 7) `html_support_disallow`

Sperrt bestimmte Muster, selbst wenn sie anderweitig erlaubt sind.

```json
[
  {
    "name": "script",
    "attributes": true,
    "classes": true,
    "styles": true
  },
  {
    "name": "*",
    "attributes": ["on.*"]
  }
]
```

### 8) `extra_definition`

Erweiterte Roh-Konfiguration, die direkt in das generierte Profil-JSON gemerged wird. Mit Vorsicht einsetzen.

```json
{
  "removePlugins": ["Autoformat"],
  "heading": {
    "options": [
      { "model": "paragraph", "title": "Paragraph", "class": "ck-heading_paragraph" },
      { "model": "heading2", "view": "h2", "title": "H2", "class": "ck-heading_heading2" }
    ]
  }
}
```

Hinweis: `removePlugins` aus diesem Feld wird mit der bestehenden Liste zusammengeführt.

### Validierungs-Tipps

- Immer gültiges JSON verwenden (doppelte Anführungszeichen, keine trailing commas).
- Mit kleinen JSON-Blöcken starten und zuerst in einem Profil testen.
- Wenn etwas nicht greift, in der Profilvorschau das generierte JSON prüfen.

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
