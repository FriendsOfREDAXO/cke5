# CKEditor5 for REDAXO CMS

Intergriert den [CKEditor5](https://ckeditor.com) in REDAXO CMS.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/cke5.png)

## Features

- WYSIWYG-Editor
- Profilkonfigurator mit Drag&Drop-Unterstützung, Profile können einfach zusammengeklickt werden
- Eigene Schriften können integriert und verwaltet werden
- Bild-Upload-Kategorie pro Profil einstellbar
- Medienmanager-Typ je Profil einstellbar
- Zusätzliche Optionen erlauben es den Editor anzupassen
- Der Expertenmodus erlaubt es Profile frei im Quellcode zu entwickeln
- Platzhalter für alle Backend-Sprachen
- Dark-mode-support für REDAXO >= 5.13

Übersetzt mit www.DeepL.com/Translator (kostenlose Version)

**Custom REDAXO Link-Widget**

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/writer.png)

- Linkmap-Support
- YForm-Datasets
- Tel: and Mailto: links 
- Medienlinks
- Eigene  Link-Decorators


**Editor Features**

- Alle kostenlosen Anbieter-Plugins integriert
- nur unterstützte Formate werden eingefügt
- Einfügen von Klartext
- Transformationen erlauben die Umwandlung von z.B. Abkürzungen zu Sonderzeichen von (c) in ©
- Selektor für Sonderzeichen
- Bild-Upload in den Medienpool per Drag & Drop in das Textfeld


## Demo

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor5_demo.gif)

## Profil Editor

Konfiguriere dir deinen Editor, so wie du ihn brauchst.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor_profile_editor_demo.gif)

## Codebeispiele

### Verwendung allgemein:

### Eingabe Code

```php 
 <textarea class="form-control cke5-editor" data-profile="default" data-lang="<?php echo \Cke5\Utils\Cke5Lang::getUserLang(); ?>" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
```
### Ausgabe Code

```html
REX_VALUE[id="1" output="html"]
```

Über weitere Data-Attribute können die Minimal- und Maximalhöhe sowie die Sprache gesteuert werden: 

- data-max-height
- data-min-height
- data-lang

### Verwendung in YForm

- Im individuellen Attribute-Feld: ``` {"class":"cke5-editor","data-profile":"default","data-lang":"de"} ```
- Weitere Attribute kommagetrennt möglich

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

### Verwendunng mit MBlock

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

Damit die angegebenen Fonts im Backend sichtbar werden, müssen diese als Assets im Backend geladen werden. 
Dies kann z.B. in der boot.php Projekt-AddOn oder im backend.css des Theme-AddOn erfolgen. 
Die Schriften werden in gewohnter CSS-Schreibweise im Abschnitt *FontFamily* des Profile-Editors hinterlegt. 

## Individualisieren 

Die Darstellung des Editors kann per CSS an die Fronteid-Ausgabe angepasst werden. Hierfür steht Im Ordner `assets/addons/cke5_custom_data` eine CSS-Datei bereit

## CSS Content-Styles CSS

[Link zum Styleguide](https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/content-styles.html)

Den Styles ist das Präfix `.ck-content` vorangestellt. Die Klasse sollte dem Ausgabeelement hinzugefügt werden und die mitgelieferte `cke5_content_styles.css` aus dem Asset-Ordner geladen werden.  

Nach der Installation dieses AddOns kann die CSS-Datei /assets/addons/cke5/cke5_content_styles.css verwendet werden. Besser aber ist, sich eine eigene zu erstellen.  


## CKE im Frontend verwenden

[siehe: REDAXO Tricks](https://friendsofredaxo.github.io/tricks/snippets/ckeditor_im_frontend)



## Keyboard support

Below is a list of the most important keystrokes supported by CKEditor 5 and its features:

<table>
	<thead>
		<tr>
			<th>Action</th>
			<th>PC</th>
			<th>Mac</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Copy</td>
			<td><kbd>Ctrl</kbd> + <kbd>C</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>C</kbd></td>
		</tr>
		<tr>
			<td>Paste</td>
			<td><kbd>Ctrl</kbd> + <kbd>V</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>V</kbd></td>
		</tr>
		<tr>
			<td>Undo</td>
			<td><kbd>Ctrl</kbd> + <kbd>Z</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>Z</kbd></td>
		</tr>
		<tr>
			<td>Redo</td>
			<td><kbd>Ctrl</kbd> + <kbd>Y</kbd> <br> <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>Z</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>Y</kbd> <br> <kbd>⌘</kbd> + <kbd>Shift</kbd> + <kbd>Z</kbd></td>
		</tr>
		<tr>
			<td>Bold</td>
			<td><kbd>Ctrl</kbd> + <kbd>B</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>B</kbd></td>
		</tr>
		<tr>
			<td>Italic</td>
			<td><kbd>Ctrl</kbd> + <kbd>I</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>I</kbd></td>
		</tr>
		<tr>
			<td>Link</td>
			<td><kbd>Ctrl</kbd> + <kbd>K</kbd></td>
			<td><kbd>⌘</kbd> + <kbd>K</kbd></td>
		</tr>
		<tr>
			<td>Insert a hard break (e.g. a new paragraph)</td>
			<td colspan="2"><kbd>Enter</kbd></td>
		</tr>
		<tr>
			<td>Insert a soft break (i.e. a <code>&lt;br&gt;</code>)</td>
			<td colspan="2"><kbd>Shift</kbd> + <kbd>Enter</kbd></td>
		</tr>
		<tr>
			<td>Nest the current list item (when in a list)</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
		<tr>
			<th colspan="3">When a widget is selected (for example: image, table, horizontal line, etc.)</th>
		</tr>
		<tr>
			<td>Insert a new paragraph directly after a widget</td>
			<td colspan="2"><kbd>Enter</kbd></td>
		</tr>
		<tr>
			<td>Insert a new paragraph directly before a widget</td>
			<td colspan="2"><kbd>Shift</kbd> + <kbd>Enter</kbd></td>
		</tr>
		<tr>
			<td>Display the caret to allow typing directly before a widget</td>
			<td colspan="2"><kbd>↑</kbd> / <kbd>←</kbd></td>
		</tr>
		<tr>
			<td>Display the caret to allow typing directly after a widget</td>
			<td colspan="2"><kbd>↓</kbd> / <kbd>→</kbd></td>
		</tr>
		<tr>
			<th colspan="3">In a table cell</th>
		</tr>
		<tr>
			<td>Move the selection to the next cell</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
		<tr>
			<td>Move the selection to the previous cell</td>
			<td colspan="2"><kbd>Shift</kbd> + <kbd>Tab</kbd></td>
		</tr>
		<tr>
			<td>Insert a new table row (when in the last cell of a table)</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
        <tr>
            <td>Navigate through the table</td>
            <td colspan="2"><kbd>↑</kbd> / <kbd>→</kbd> / <kbd>↓</kbd> / <kbd>←</kbd></td>
        </tr>
	</tbody>
</table>

#### User interface and navigation

Use the following keystrokes for more efficient navigation in the CKEditor 5 user interface:

<table>
	<thead>
		<tr>
			<th>Action</th>
			<th>PC</th>
			<th>Mac</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Close contextual balloons and UI components like dropdowns</td>
			<td colspan="2"><kbd>Esc</kbd></td>
		</tr>
		<tr>
			<td>Move focus to the visible contextual balloon</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
		<tr>
			<td>Move focus between fields (inputs and buttons) in contextual balloons</td>
			<td colspan="2"><kbd>Tab</kbd></td>
		</tr>
		<tr>
			<td>Move focus to the toolbar</td>
			<td><kbd>Alt</kbd> + <kbd>F10</kbd></td>
			<td><kbd>Alt</kbd> + <kbd>F10</kbd> <br> (may require <kbd>Fn</kbd>)</td>
		</tr>
		<tr>
			<td>Navigate through the toolbar</td>
			<td colspan="2"><kbd>↑</kbd> / <kbd>→</kbd> / <kbd>↓</kbd> / <kbd>←</kbd></td>
		</tr>
		<tr>
			<td>Execute the currently focused button</td>
			<td colspan="2"><kbd>Enter</kbd></td>
		</tr>
	</tbody>
</table>





## Für Entwickler


### Beispiel Extra Optionen

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
            },

        ]
    }
}
```


### Beispiel für benutzerdefinierte Link-Dekorator
*Achtung, die Keys müssen in Kleinbuchstaben geschrieben werden*

```js
{
    "newtab": {
        "mode": "manual",
        "label": "Open in a new tab",
        "attributes": {
            "target": "_blank",
            "rel": "noopener noreferrer"
        }
    }
}
```
```js
{
    "arrowclass": {
        "mode": "manual",
        "label": "Link mit CSS Klasse",
        "defaultValue": "true",
        "classes": "arrow"
    }
}
```


### YForm links

Um die  generierten Urls wie `rex_news://1` zu ersetzen, muss das folgende Skript in die `boot.php` des `project` AddOns eingefügt werden.
Der Code für die Urls muss modifiziert werden. 

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
                    // Example, if the Urls are generated via Url-AddOn  
                    $id = $matches[3];
                    if ($id) {
                       return rex_getUrl('', '', ['news' => $id]); 
                    }
                    break;
                case 'person':
                    // ein anderes Beispiel 
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

Über die API ist es möglich eigene Profile abseites des Profileditors anzulegen: `Cke5\Creator\Cke5ProfilesApi::addProfile`

Example: 

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

## Autoformat deaktivieren

Das Autoformat-Feature (mardown code replacement) kann man deaktivieren, indem man folgende Option in den Abschnitt Extra Options einfügt: 

```json
{"removePlugins": ["Autoformat"]}
```

## Bugtracker

If you have found a error or maybe you have an idea, You can create a [Issue](https://github.com/FriendsOfREDAXO/cke5/issues). 
Before you create a new issue, please search if there already exists an issue with your request, and read the [Issue Guidelines (englisch)](https://github.com/necolas/issue-guidelines) from [Nicolas Gallagher](https://github.com/necolas/).


## Changelog

see [CHANGELOG.md](https://github.com/FriendsOfREDAXO/cke5/blob/master/CHANGELOG.md)

## Licenses

AddOn:[MIT LICENSE](https://github.com/FriendsOfREDAXO/cke5/blob/master/LICENSE)
CKEDITOR [GPL LICENSE](https://github.com/ckeditor/ckeditor5/blob/master/LICENSE.md)


## Author

**Friends Of REDAXO**

* http://www.redaxo.org
* https://github.com/FriendsOfREDAXO

**Projekt-Lead**

[Joachim Dörr](https://github.com/joachimdoerr)

**Initiator:**

[KLXM Crossmedia / Thomas Skerbis](https://klxm.de)
