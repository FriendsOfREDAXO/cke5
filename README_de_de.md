cke5 - CKEditor5-AddOn für REDAXO
================================================================================
Integriert den [CKEditor5](https://ckeditor.com) in REDAXO

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ck5.png)


## Features
- WYSIWYG-Editor
- Profil-Konfigurator mit Drag&Drop-Support, Profile können einfach zusammengeklickt werden
- Bildupload in den Medienpool per Drag & Drop ins Textfeld
- Bildupload-Kategorie je Profil einstellbar
- Mediamanager-Type je Profil einstellbar
- Linkmap-Support
- Medienpool-Support
- MBlock-Support

## Codebeispiele

### Verwendung allgemein:

### Eingabe Code

```php 
 <textarea class="form-control cke5-editor" data-profile="default" data-lang="de" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
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
        'data-lang'=>'de', 
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
        'data-lang'=>'de', 
        'data-profile'=>'default')
        );
echo MBlock::show($id, $mform->show());
```


## Keyboard support


| Action | PC | Mac |
|-----|---|-----|
| Copy | <kbd>Ctrl</kbd> + <kbd>C</kbd> | <kbd>⌘</kbd> + <kbd>C</kbd> |
| Paste | <kbd>Ctrl</kbd> + <kbd>V</kbd> | <kbd>⌘</kbd> + <kbd>V</kbd> |
| Undo | <kbd>Ctrl</kbd> + <kbd>Z</kbd> | <kbd>⌘</kbd> + <kbd>Z</kbd> |
| Redo | <kbd>Ctrl</kbd> + <kbd>Y</kbd> <br> <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>Z</kbd>  | <kbd>⌘</kbd> + <kbd>Y</kbd> <br> <kbd>⌘</kbd> + <kbd>Shift</kbd> + <kbd>Z</kbd> |
| Bold | <kbd>Ctrl</kbd> + <kbd>B</kbd> | <kbd>⌘</kbd> + <kbd>I</kbd> |
| Italic | <kbd>Ctrl</kbd> + <kbd>I</kbd> | <kbd>⌘</kbd> + <kbd>I</kbd> |
| Link | <kbd>Ctrl</kbd> + <kbd>K</kbd> | <kbd>⌘</kbd> + <kbd>K</kbd> |
| Close contextual balloons and UI components like dropdowns | <kbd>Esc</kbd> | <kbd>Esc</kbd> |
| Nest the current list item (when in a list) | <kbd>Tab</kbd> | <kbd>Tab</kbd> |
| Move focus to the visible contextual balloon | <kbd>Tab</kbd> | <kbd>Tab</kbd> |
| Move focus between fields (inputs and buttons) in contextual balloons | <kbd>Tab</kbd> | <kbd>Tab</kbd> |
| Move focus to the toolbar | <kbd>Alt</kbd> + <kbd>F10</kbd> | <kbd>Alt</kbd> + <kbd>F10</kbd> <br> (may require <kbd>Fn</kbd>) |
| Navigate through the toolbar | <kbd>↑</kbd> / <kbd>→</kbd> / <kbd>↓</kbd> / <kbd>←</kbd> | <kbd>↑</kbd> / <kbd>→</kbd> / <kbd>↓</kbd> / <kbd>←</kbd> |
| Execute the currently focused button | <kbd>Enter</kbd> | <kbd>Enter</kbd> |




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
