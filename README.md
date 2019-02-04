# CKEditor5 for REDAXO CMS

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor_01.png)

Integrates the [CKEditor5](https://ckeditor.com) into REDAXO CMS.

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

## Demo

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor5_demo.gif)

## Profile Editor

Configure your editor as you need it.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor_profile_editor_demo.gif)

## Code Examples

### Use in general:

### Input Code

```php 
 <textarea class="form-control cke5-editor" data-profile="default" data-lang="<?php echo \Cke5\Utils\Cke5Lang::getUserLang(); ?>" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
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
        'data-lang'=>\Cke5\Utils\Cke5Lang::getUserLang(), 
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
        'data-lang'=>\Cke5\Utils\Cke5Lang::getUserLang(), 
        'data-profile'=>'default')
        );
echo MBlock::show($id, $mform->show());
```

## CSS Definitons for images

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


## For Developers

By using the API: `Cke5\Creator\Cke5ProfilesApi::addProfile`, it is possible to install own profiles beside of the profile editor. 

Example: 

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
