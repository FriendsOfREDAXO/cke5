# CKEditor5 für REDAXO CMS

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor_01.png)

Integriert den [CKEditor5](https://ckeditor.com) im REDAXO CMS.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ck5.png)

## Features

- WYSIWYG-Editor
- Profil-Konfigurator mit Drag&Drop-Support, Profile können einfach zusammengeklickt werden
- Bildupload in den Medienpool per Drag & Drop ins Textfeld
- Bildupload-Kategorie je Profil einstellbar
- Mediamanager-Type je Profil einstellbar
- Es können eigene Fonts eingebunden und verwaltet werden
- Responsive Oberfläche
- Linkmap-Support
- Medienpool-Support
- MBlock-Support

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
`REX_VALUE[id="1" output="html"]`

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

## CSS Definitionen für Bilder aus CKE5

```css
figure.image {
   max-width: 100%;
   margin-bottom: 1.2em;
}

figure.image img {
   max-width: 100%;
   height: auto;
}

figure.image.image-style-align-center {
   max-width: 80%;
   margin-left: auto;
   margin-right: auto;
}

figure.image.image-style-align-right,
figure.image.image-style-align-left {
   max-width: 50%;
   margin-bottom: 0.5em;
   margin-top: 0.4em;
}

figure.image.image-style-align-right img,
figure.image.image-style-align-left img {
   max-width: 100%;
}

figure.image figcaption {
   font-size: 0.8em;
   line-height: 1.24em;
   padding-bottom: 0.5em;
}

.image-style-align-left {
   float: left;
   margin-right: 1em;
}

.image-style-align-right {
   float: right;
   margin-left: 1em;
}
```

## Keyboard support


| Action | PC | Mac |
|-----|---|-----|
| Copy | <kbd>Ctrl</kbd> + <kbd>C</kbd> | <kbd>⌘</kbd> + <kbd>C</kbd> |
| Paste | <kbd>Ctrl</kbd> + <kbd>V</kbd> | <kbd>⌘</kbd> + <kbd>V</kbd> |
| Undo | <kbd>Ctrl</kbd> + <kbd>Z</kbd> | <kbd>⌘</kbd> + <kbd>Z</kbd> |
| Redo | <kbd>Ctrl</kbd> + <kbd>Y</kbd> <br> <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>Z</kbd>  | <kbd>⌘</kbd> + <kbd>Y</kbd> <br> <kbd>⌘</kbd> + <kbd>Shift</kbd> + <kbd>Z</kbd> |
| Bold | <kbd>Ctrl</kbd> + <kbd>B</kbd> | <kbd>⌘</kbd> + <kbd>B</kbd> |
| Italic | <kbd>Ctrl</kbd> + <kbd>I</kbd> | <kbd>⌘</kbd> + <kbd>I</kbd> |
| Link | <kbd>Ctrl</kbd> + <kbd>K</kbd> | <kbd>⌘</kbd> + <kbd>K</kbd> |
| Close contextual balloons and UI components like dropdowns | <kbd>Esc</kbd> | <kbd>Esc</kbd> |
| Nest the current list item (when in a list) | <kbd>Tab</kbd> | <kbd>Tab</kbd> |
| Move focus to the visible contextual balloon | <kbd>Tab</kbd> | <kbd>Tab</kbd> |
| Move focus between fields (inputs and buttons) in contextual balloons | <kbd>Tab</kbd> | <kbd>Tab</kbd> |
| Move focus to the toolbar | <kbd>Alt</kbd> + <kbd>F10</kbd> | <kbd>Alt</kbd> + <kbd>F10</kbd> <br> (may require <kbd>Fn</kbd>) |
| Navigate through the toolbar | <kbd>↑</kbd> / <kbd>→</kbd> / <kbd>↓</kbd> / <kbd>←</kbd> | <kbd>↑</kbd> / <kbd>→</kbd> / <kbd>↓</kbd> / <kbd>←</kbd> |
| Execute the currently focused button | <kbd>Enter</kbd> | <kbd>Enter</kbd> |
| Insert a hard break (e.g. a new paragraph) | <kbd>Enter</kbd> | <kbd>Enter</kbd> |
| Insert a soft break (i.e. a `<br>`) | <kbd>Shift</kbd>+<kbd>Enter</kbd> | <kbd>Shift</kbd>+<kbd>Enter</kbd> |

## Für Entwickler

Über die API ist es möglich eigene Profile abseites des Profileditors anzulegen: `Cke5\Creator\Cke5ProfilesApi::addProfile`

Beispiel: 

```php
    $create = \Cke5\Creator\Cke5ProfilesApi::addProfile(
        'full_cke',
        'Cke5 with all possible tools',
        ['heading', '|', 'fontSize', 'fontFamily', 'alignment', 'bold', 'italic', 'underline', 'super', 'sub', 'strikethrough', 'insertTable', 'code', 'link', 'rexImage', 'bulletedList', 'numberedList', 'blockQuote', 'highlight', 'emoji', 'undo', 'redo'],
        ['paragraph', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        ['left', 'right', 'center', 'justify'],
        ['imageTextAlternative', '|', 'full', 'alignLeft', 'alignCenter', 'alignRight'],
        ['tiny', 'small', 'big', 'huge'],
        ['yellowMarker', 'greenMarker', 'pinkMarker', 'blueMarker', 'redPen', 'greenPen'],
        ['tableColumn', 'tableRow', 'mergeTableCells'],
        ['internal', 'media']
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
