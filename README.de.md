# CKEditor5 für REDAXO CMS

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor_01.png)

Integriert den [CKEditor5](https://ckeditor.com) im REDAXO CMS.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ck5.png)

## Features

- WYSIWYG-Herausgeber
- Profilkonfigurator mit Drag&Drop-Unterstützung, Profile können einfach zusammengeklickt werden
- Bild-Upload in den Medienpool per Drag & Drop in das Textfeld
- Eigene Schriften können integriert und verwaltet werden
- Bild-Upload-Kategorie pro Profil einstellbar
- Medienmanager-Typ pro Profil einstellbar
- Bilder verlinken
- Platzhalter für alle Backend-Sprachen
- Auswahl von Sonderzeichen
- Einfügen von Rohtext
- Linkmap-Unterstützung
- Mediapool-Unterstützung
- MBlock-Unterstützung
- Transformationen erlauben die Umwandlung von z.B. Abkürzungen zu Sonderzeichen von (c) in ©
- Zusätzliche Optionen erlauben es Ihnen, den Editor anzupassen
- Der Expertenmodus erlaubt es, Profile im Quellcode zu entwickeln
und vieles mehr...

Übersetzt mit www.DeepL.com/Translator (kostenlose Version)

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

Die Darstellung des Editors kann per CSS an die Fronteid-Ausgabe angepasst werden. Hierfür steht Im Ordner 'assets/addons/cke5_custom_data' eine CSS-Datei bereit

## CSS Content-Styles CSS

[Link zum Styleguide](https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/content-styles.html)

Den Styles ist das Präfix `.ck-content` vorangestellt. Die Klasse sollte dem Ausgabeelement hinzugefügt werden und die mitgelieferte `cke5_content_styles.css` aus dem Asset-Ordner geladen werden.  



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

<style>
.keyboard-shortcuts th {
	text-align: center;
}
.keyboard-shortcuts td:nth-of-type(1) {
	text-align: right;
}
.keyboard-shortcuts td:nth-of-type(2), .keyboard-shortcuts td:nth-of-type(3) {
	width: 30%;
}
</style>


## Für Entwickler

Über die API ist es möglich eigene Profile abseites des Profileditors anzulegen: `Cke5\Creator\Cke5ProfilesApi::addProfile`

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
             "styles": ["full", "alignLeft", "alignRight", "alignCenter"]
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



## Bugtracker

Du hast einen Fehler gefunden oder ein nettes Feature parat? [Lege ein Issue an](https://github.com/FriendsOfREDAXO/cke5/issues). Bevor du ein neues Issue erstellst, suche bitte ob bereits eines mit deinem Anliegen existiert und lese die [Issue Guidelines (englisch)](https://github.com/necolas/issue-guidelines) von [Nicolas Gallagher](https://github.com/necolas/).


## Changelog

siehe [CHANGELOG.md](https://github.com/FriendsOfREDAXO/cke5/blob/master/CHANGELOG.md)

## Lizenzen

AddOn:[MIT LICENSE](https://github.com/FriendsOfREDAXO/cke5/blob/master/LICENSE)
CKEDITOR [GPL LICENSE](https://github.com/ckeditor/ckeditor5/blob/master/LICENSE.md)


## Autor

**Friends Of REDAXO**

* http://www.redaxo.org
* https://github.com/FriendsOfREDAXO

**Projekt-Lead**

[Joachim Dörr](https://github.com/joachimdoerr)

**Initiator:**

[KLXM Crossmedia / Thomas Skerbis](https://klxm.de)
