# CKEditor5 für REDAXO CMS

Hier kommt der [CKEditor5](https://ckeditor.com) für REDAXO CMS.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/cke5.png)

## Features nach Funktionsgruppen

### Basis-Features
- Ein leistungsstarker WYSIWYG-Editor mit moderner Oberfläche
- Dark-Mode-Support für REDAXO >= 5.13
- Platzhalter für alle Backend-Sprachen
- Nur unterstützte Formate werden eingefügt

### Konfiguration und Anpassung
- Profilkonfigurator mit Drag&Drop für einfaches Zusammenklicken von Profilen
- Expertenmodus: Entwickle Profile frei im Quellcode
- Zusätzliche Optionen zur individuellen Anpassung des Editors
- Konfigurationsseite für Lizenzschlüssel zum Entfernen des "Powered by CKEditor" Banner
- API für programmatische Profilgenerierung

### Style-Management
- Style-Manager zur einfachen Verwaltung von CSS-Stilen
- Style-Gruppen für schnelles Erfassen von Stilen als JSON-Array
- CSS-Definitionen aus jedem Stil werden im Backend automatisch hinzugefügt
- Eigene Schriften können integriert und verwaltet werden
- Verbessertes Tag-Handling im Profil- und Style-Editor

### Medienintegration
- Bild-Upload in den Medienpool per Drag & Drop direkt ins Textfeld
- Bild-Upload-Kategorie pro Profil einstellbar
- Medienmanager-Typ je Profil einstellbar
- Drag & Drop Upload für CKEditor Vendor-Dateien (konfigurierbar in config.php)

### Link-Features
- Umfassendes REDAXO Link-Widget
- Linkmap-Support
- YForm-Datasets Integration
- Tel: und Mailto: Links
- Medienlinks
- Eigene Link-Decorators für benutzerdefinierte Attribute und Klassen

### Erweiterungen und Plugins
- Alle kostenlosen Anbieter-Plugins sind integriert
- Sprog-Ersetzungen via Mentions-Plugin
- AccessibilityHelper für verbesserte Barrierefreiheit
- Einfügen von Klartext
- Transformationen: z.B. Umwandlung von (c) in ©
- Auswahl für Sonderzeichen
- Neue Toolbar-Elemente: Emoji, Bookmarks, ShowBlocks

### Import und Export
- Profil-Export und -Import für einfache Migration
- Datensicherung vor Updates
- Konsistente Style-Übertragung zwischen Installationen

## Style-Gruppen verwenden

Style-Gruppen ermöglichen es, mehrere CSS-Stile als JSON-Array zu definieren und sie gemeinsam zu verwalten. Diese Funktion wurde in Version 6.3.3 eingeführt und vereinfacht die konsistente Gestaltung deiner Inhalte.

### Beispiel einer Style-Gruppe

Unter "CKEditor5" > "Profiles" > "Customise" > "Style Groups" kannst du Style-Gruppen erstellen:

```json
[
    {
        "name": "Blue Box",
        "element": "div",
        "classes": ["blue-box", "rounded", "shadow"]
    },
    {
        "name": "Highlighted Text",
        "element": "span",
        "classes": ["highlight", "bold"]
    },
    {
        "name": "Info Panel",
        "element": "section",
        "classes": ["info-panel"],
        "attributes": {
            "data-type": "info",
            "role": "note"
        }
    }
]
```

### So verwendest du Style-Gruppen:

1. Erstelle eine Style-Gruppe mit der JSON-Konfiguration
2. Optional: Füge direkt CSS-Definitionen hinzu, die automatisch im Backend geladen werden
3. Aktiviere das "Style"-Tool in deinem Editor-Profil
4. Wähle deine erstellte Style-Gruppe im Profileditor aus

### Vorteile:
- Konsistente Gestaltung über verschiedene Profile hinweg
- Zentrale Verwaltung von zusammengehörigen Stilen
- Automatische CSS-Integration im Backend
- Einfacher Export/Import zwischen REDAXO-Instanzen

### CSS-Definitionen für Style-Gruppen

Du kannst direkt CSS zu deinen Style-Gruppen hinzufügen:

```css
.blue-box {
    background-color: #e7f5ff;
    border: 1px solid #4dabf7;
    padding: 15px;
}

.blue-box.rounded {
    border-radius: 8px;
}

.highlight {
    background-color: #fff9db;
    padding: 2px 4px;
}

.info-panel {
    border-left: 4px solid #1c7ed6;
    background: #f8f9fa;
    padding: 10px 15px;
    margin: 15px 0;
}
```

## Eine kleine Demo

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor5_demo.gif)

## Der Profil-Editor

Konfiguriere deinen Editor so, wie du ihn brauchst.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor_profile_editor_demo.gif)

## Code-Beispiele, damit's auch läuft

### Allgemeine Verwendung:

### Eingabe-Code

```php
 <textarea class="form-control cke5-editor" data-profile="default" data-lang="<?php echo \Cke5\Utils\Cke5Lang::getUserLang(); ?>" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
```

### Ausgabe-Code

```html
REX_VALUE[id="1" output="html"]
```

Über weitere Data-Attribute kannst du die Minimal- und Maximalhöhe sowie die Sprache steuern:

- data-max-height
- data-min-height
- data-lang

### Verwendung in YForm

- Im individuellen Attribute-Feld: ``` {"class":"cke5-editor","data-profile":"default","data-lang":"de"} ```
- Weitere Attribute lassen sich durch Kommas trennen

### Verwendung in MForm

```php
$mform = new MForm();
$mform->addTextAreaField(1,
        array(
        'label'=>'Text',
        'class'=>'cke5-editor',
        'data-lang'=>\Cke5\Utils\Cke5Lang::getUserLang(),
        'data-profile'=>'default')
        );
echo $mform->show();
```

### Verwendung mit MBlock

```php
$id = 1;
$mform = new MForm();
$mform->addFieldset('Accordion');
$mform->addTextField("$id.0.titel", array('label'=>'Titel'));
$mform->addTextAreaField("$id.0.text",
        array(
        'label'=>'Text',
        'class'=>'cke5-editor',
        'data-lang'=>\Cke5\Utils\Cke5Lang::getUserLang(),
        'data-profile'=>'default')
        );
echo MBlock::show($id, $mform->show());
```

## Eigene Schriften einbinden

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/fonts.png)

Damit die Schriften im Backend angezeigt werden, musst du sie als Assets laden.
Das kannst du z.B. in der `boot.php` des Projekt-AddOns oder in der `backend.css` des Theme-AddOns machen.
Die Schriften kommen in den Abschnitt *FontFamily* des Profil-Editors, in der gewohnten CSS-Schreibweise.

## Sprog-Ersetzungen – kurz und knackig

Unter `Mention & Sprog Ersetzungen` > `Sprog Ersetzungen` > `Ersetzungen` kannst du Sprog-Platzhalter mit Titel oder Beschreibung hinterlegen.
Schreibweise: `{{key}}`. Im nächsten Feld kommt der Titel.
Im Editor einfach '{{' eintippen, dann bekommst du eine Liste der Platzhalter.

## Individualisierung – mach's zu deinem Editor

Die Optik des Editors kann per CSS an die Frontend-Ausgabe angepasst werden. Dafür gibt's im Ordner `assets/addons/cke5_custom_data` eine CSS-Datei.

## CSS Content-Styles

[Styleguide hier](https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/content-styles.html)

Den Styles wird das Präfix `.ck-content` vorangestellt. Die Klasse sollte dem Ausgabeelement hinzugefügt werden und die mitgelieferte `cke5_content_styles.css` aus dem Asset-Ordner geladen werden.

Nach der Installation dieses AddOns ist die CSS-Datei /assets/addons/cke5/cke5_content_styles.css sofort einsatzbereit. Aber eine eigene Datei zu erstellen, ist vielleicht die bessere Wahl.

## CKE im Frontend

[Schau mal hier: REDAXO Tricks](https://friendsofredaxo.github.io/tricks/snippets/ckeditor_im_frontend)

## Tastenkürzel

Hier die wichtigsten Tastenkürzel für CKEditor 5 und seine Features:

<table>
	<thead>
		<tr>
			<th>Aktion</th>
			<th>PC</th>
			<th>Mac</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Kopieren</td>
			<td><kbd>Strg</kbd> + <kbd>C</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>C</kbd></td>
		</tr>
		<tr>
			<td>Einfügen</td>
			<td><kbd>Strg</kbd> + <kbd>V</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>V</kbd></td>
		</tr>
		<tr>
			<td>Rückgängig</td>
			<td><kbd>Strg</kbd> + <kbd>Z</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>Z</kbd></td>
		</tr>
		<tr>
			<td>Wiederherstellen</td>
			<td><kbd>Strg</kbd> + <kbd>Y</kbd> <br> <kbd>Strg</kbd> + <kbd>Umschalt</kbd> + <kbd>Z</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>Y</kbd> <br> <kbd>⌘</kbd> + <kbd>Umschalt</kbd> + <kbd>Z</kbd></td>
		</tr>
		<tr>
			<td>Fett</td>
			<td><kbd>Strg</kbd> + <kbd>B</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>B</kbd></td>
		</tr>
		<tr>
			<td>Kursiv</td>
			<td><kbd>Strg</kbd> + <kbd>I</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>I</kbd></td>
		</tr>
		<tr>
			<td>Link</td>
			<td><kbd>Strg</kbd> + <kbd>K</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>K</kbd></td>
		</tr>
		<tr>
			<td>Harter Zeilenumbruch (z.B. neuer Absatz)</td>
			<td colspan="2"><kbd>Enter</kbd></td>
		</tr>
		<tr>
			<td>Weicher Zeilenumbruch (<code>&lt;br&gt;</code>)</td>
			<td colspan="2"><kbd>Umschalt</kbd> + <kbd>Enter</kbd></td>
		</tr>
		<tr>
			<td>Aktuellen Listeneintrag einrücken (wenn man sich in einer Liste befindet)</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
		<tr>
			<th colspan="3">Wenn ein Widget ausgewählt ist (z.B. Bild, Tabelle, horizontale Linie usw.)</th>
		</tr>
		<tr>
			<td>Neuen Absatz direkt nach einem Widget einfügen</td>
			<td colspan="2"><kbd>Enter</kbd></td>
		</tr>
		<tr>
			<td>Neuen Absatz direkt vor einem Widget einfügen</td>
			<td colspan="2"><kbd>Umschalt</kbd> + <kbd>Enter</kbd></td>
		</tr>
		<tr>
			<td>Den Cursor anzeigen, um direkt vor einem Widget schreiben zu können</td>
			<td colspan="2"><kbd>↑</kbd> / <kbd>←</kbd></td>
		</tr>
		<tr>
			<td>Den Cursor anzeigen, um direkt nach einem Widget schreiben zu können</td>
			<td colspan="2"><kbd>↓</kbd> / <kbd>→</kbd></td>
		</tr>
		<tr>
			<th colspan="3">In einer Tabellenzelle</th>
		</tr>
		<tr>
			<td>Die Auswahl zur nächsten Zelle verschieben</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
		<tr>
			<td>Die Auswahl zur vorherigen Zelle verschieben</td>
			<td colspan="2"><kbd>Umschalt</kbd> + <kbd>Tab</kbd></td>
		</tr>
		<tr>
			<td>Neue Tabellenzeile einfügen (wenn man sich in der letzten Zelle einer Tabelle befindet)</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
        <tr>
            <td>Durch die Tabelle navigieren</td>
            <td colspan="2"><kbd>↑</kbd> / <kbd>→</kbd> / <kbd>↓</kbd> / <kbd>←</kbd></td>
        </tr>
	</tbody>
</table>

#### Benutzeroberfläche und Navigation

Mit diesen Tastenkürzeln navigierst du effizienter durch die CKEditor 5 Oberfläche:

<table>
	<thead>
		<tr>
			<th>Aktion</th>
			<th>PC</th>
			<th>Mac</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Kontext-Ballons und UI-Komponenten wie Dropdowns schließen</td>
			<td colspan="2"><kbd>Esc</kbd></td>
		</tr>
		<tr>
			<td>Fokus zum sichtbaren Kontext-Ballon verschieben</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
		<tr>
			<td>Fokus zwischen Feldern (Eingabefelder und Buttons) in Kontext-Ballons verschieben</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
		<tr>
			<td>Fokus zur Toolbar verschieben</td>
			<td><kbd>Alt</kbd> + <kbd>F10</kbd></td>
			<td><kbd>Alt</kbd> + <kbd>F10</kbd> <br> (kann <kbd>Fn</kbd> erfordern)</td>
		</tr>
		<tr>
			<td>Durch die Toolbar navigieren</td>
			<td colspan="2"><kbd>↑</kbd> / <kbd>→</kbd> / <kbd>↓</kbd> / <kbd>←</kbd></td>
		</tr>
		<tr>
			<td>Die aktuell fokussierte Schaltfläche ausführen</td>
			<td colspan="2"><kbd>Enter</kbd></td>
		</tr>
	</tbody>
</table>

## Für Entwickler

### Beispiel für Extra-Optionen

```json
{
    "removePlugins": ["Autoformat"],
    "heading": {
        "options": [{
                "model": "paragraph",
                "title": "Paragraph",
                "class": "ck-heading_paragraph"
            },
            {
                "model": "paragrap1tl",
                "view": {
                    "name": "span",
                    "classes": "uk-text-large"
                },
                "title": "Fließtext groß",
                "class": "ck-heading_paragraph"
            },
            {
                "model": "heading1",
                "view": {
                    "name": "h1",
                    "classes": "uk-animation-fade uk-heading-large"
                },
                "title": "Überschrift 1 sehr groß",
                "class": "ck-heading_heading1"
            }

        ]
    }
}
```

### Beispiel für benutzerdefinierte Link-Dekorator
*Wichtig: Die Keys müssen klein geschrieben werden*

```js
[{
    "newtab": {
        "mode": "manual",
        "label": "In neuem Tab öffnen",
        "attributes": {
            "target": "_blank",
            "rel": "noopener noreferrer"
        }
    }
}]
```
```js
[{
    "arrowclass": {
        "mode": "manual",
        "label": "Link mit CSS Klasse",
        "defaultValue": "true",
        "classes": "arrow"
    }
}]
```

Oder mehrere:
```js
[{
    "openInNewTab": {
        "mode": "manual",
        "label": "In neuem Tab öffnen",
        "defaultValue": true,
        "attributes": {
            "target": "_blank",
            "rel": "noopener noreferrer"
        }
    }
},
{
    "isGallery": {
        "mode": "manual",
        "label": "Gallery link",
        "attributes": {
            "class": "button light",
        }
    }
}]
```

### Mentions

Das AddOn liefert das [Mentions-Plugin](https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html) mit. Das kannst du frei konfigurieren.
Hier ein Beispiel:

```json
[{
    "marker": "@",
    "feed": [
        "@test",
        "@test2"
    ],
    "minimumCharacters": "0"
}]
```

### YForm-Links

Um generierte URLs wie `rex_news://1` zu ersetzen, füge das folgende Skript in die `boot.php` des `project` AddOns ein.
Der Code für die URLs muss angepasst werden.

```php
rex_extension::register('OUTPUT_FILTER', function(\rex_extension_point $ep) {
    return preg_replace_callback(
        '@((rex_news|rex_person))://(\d+)(?:-(\d+))?/?@i',
        function ($matches) {
            // table = $matches[1]
            // id = $matches[3]
            $url = '';
            switch ($matches[1]) {
                case 'news':
                    // Beispiel, wenn die URLs via Url-AddOn generiert werden
                    $id = $matches[3];
                    if ($id) {
                       return rex_getUrl('', '', ['news' => $id]);
                    }
                    break;
                case 'person':
                    // Ein anderes Beispiel
                    $url = '/index.php?person='.$matches[3];
                    break;
            }
            return $url;
        },
        $ep->getSubject()
    );
}, rex_extension::NORMAL);
```

### Profile API

Über die API kannst du eigene Profile abseits des Profileditors erstellen: `Cke5\Creator\Cke5ProfilesApi::addProfile`

Beispiel:

```php
    $create = \Cke5\Creator\Cke5ProfilesApi::addProfile(
        'profile_name_cke5',
        'API created Cke5 profile',
        '{
           "toolbar": ["link", "rexImage", "|", "undo", "redo", "|", "selectAll", "insertTable", "code", "codeBlock"],
           "removePlugins": ["Alignment", "Font", "FontFamily", "MediaEmbed", "Bold", "Italic", "BlockQuote", "Heading", "Alignment", "Highlight", "Strikethrough", "Underline", "Subscript", "Superscript", "Emoji", "RemoveFormat", "TodoList", "HorizontalLine", "PageBreak"],
           "link": {"rexlink": ["internal", "media"]},
           "image": {
             "toolbar": ["imageTextAlternative", "|", "imageStyle:full", "imageStyle:alignLeft", "imageStyle:alignRight", "imageStyle:alignCenter"],
             "styles": ["block", "alignLeft", "alignRight", "alignCenter"]
           },
           "table": {"toolbar": ["tableColumn", "tableRow", "mergeTableCells", "tableProperties", "tableCellProperties"]},
           "rexImage": {"media_path": "\/media\/"},
           "ckfinder": {"uploadUrl": ".\/index.php?cke5upload=1&media_path=media"},
           "placeholder_en": "Placeholder EN",
           "placeholder_de": "Placeholder DE",
           "codeBlock": {
             "languages": [{"language": "plaintext", "label": "Plain text", "class": ""}, {
               "language": "php",
               "label": "PHP",
               "class": "php-code"
             }]
           }
         }',
        '[{"min-height": 100}, {"max-height": 280}]'
    );
    echo (is_string($create)) ? $create : 'successful profile created';
```

### HTML-Support

Source-Editing Plugin hat ein Update bekommen.
Nach einem Update von einer sehr alten Version fehlt evtl. die Grundeinstellung für das PlugIn im Abschnitt HtmlSupport.

```JSON
[
    {
        "name": "regex(/.*/)",
        "attributes": true,
        "classes": true,
        "styles": true
    }
]
```

### Autoformat deaktivieren

Du kannst das Autoformat-Feature (Markdown-Code-Ersatz) deaktivieren, indem du diese Option in den Abschnitt Extra Options einfügst:

```json
{"removePlugins": ["Autoformat"]}
```

## Bugtracker

Fehler gefunden oder eine Idee? Erstelle ein [Issue](https://github.com/FriendsOfREDAXO/cke5/issues).
Bevor du ein neues Issue erstellst, such bitte, ob es schon ein ähnliches gibt. Und lies dir die [Issue Guidelines (englisch)](https://github.com/necolas/issue-guidelines) von [Nicolas Gallagher](https://github.com/necolas/) durch.

## Changelog

Schau mal im [CHANGELOG.md](https://github.com/FriendsOfREDAXO/cke5/blob/master/CHANGELOG.md) nach.

## Lizenzen

AddOn: [MIT LICENSE](https://github.com/FriendsOfREDAXO/cke5/blob/master/LICENSE)
CKEDITOR: [GPL LICENSE](https://github.com/ckeditor/ckeditor5/blob/master/LICENSE.md)

## Wer's gemacht hat

**Friends Of REDAXO**

* http://www.redaxo.org
* https://github.com/FriendsOfREDAXO

**Projektleitung**

[Joachim Dörr](https://github.com/joachimdoerr)

**Initiator:**

[KLXM Crossmedia / Thomas Skerbis](https://klxm.de)
