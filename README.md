cke5 - CKEditor5 AddOn for REDAXO cms 
================================================================================
Integrates the [CKEditor5](https://ckeditor.com) for REDAXO

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ck5.png)


## Features
- WYSIWYG-Editor
- Profile configurator with drag and drop support, profiles can be simply clicked together
- Image upload into the media pool via drag & drop into the text field
- Image upload category per profile adjustable
- Media manager type adjustable per profile
- Linkmap-Support
- Mediapool-Support
- MBlock-Support

## Code Examples

### Use in general:

### Input Code

```php 
 <textarea class="form-control cke5-editor" data-profile="default" data-lang="de" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
```
### Output Code
`REX_VALUE[id="1" output="html"]`

Further data attributes can be used to control the minimum and maximum height as well as the language:

- data-max-height
- data-min-height
- data-lang

### Use in MForm
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

### Use in MBlock

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
