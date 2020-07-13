# CKEditor5 for REDAXO CMS

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor_01.png)

Integrates the [CKEditor5](https://ckeditor.com) into REDAXO CMS.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ck5.png)

## Features

- WYSIWYG-Editor
- Profile configurator with drag and drop support, profiles can be simply clicked together
- Image upload into the media pool via drag & drop into the text field
- Own fonts can be integrated and managed
- Image upload category per profile adjustable
- Media manager type adjustable per profile
- Linking images
- Placeholders for all backend languages
- Special char selection
- Pasting raw text
- Linkmap-Support
- Mediapool-Support
- MBlock-Support
- Transformations allow converting of e.g. shortcuts to speacial chars from (c) to ©
- Extra options allow you to customize the editor
- The expert mode allows you to develop profiles in source code
and much more…

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

```html
REX_VALUE[id="1" output="html"]
```

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

## Adding own fonts

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/fonts.png)

To make the specified fonts visible in the backend, they must be loaded as assets in the backend. 
This can be done, for example, with the boot.php of Project AddOn or backend.css of Theme AddOn. 
The fonts should be defined in the usual CSS notation in the *FontFamily* section of the Profile Editor. 

## Customizing

The display of the editor can be adapted to the frontend output via CSS. For this a CSS file is available in the folder 'assets/addons/cke5_custom_data

## CSS Content-Styles 

[Link to Styleguide](https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/content-styles.html)

The following Styles are prefixed with '.ck-content`. You should add this class to your output element.

```css
/*
 * CKEditor 5 (v12.4.0) content styles.
 * Generated on Mon, 21 Oct 2019 11:34:56 GMT.
 * For more information, check out https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/content-styles.html
 */

:root {
    --ck-image-style-spacing: 1.5em;
    --ck-todo-list-checkmark-size: 16px;
}

/* ckeditor5-basic-styles/theme/code.css */
.ck-content code {
    background-color: hsla(0, 0%, 78%, 0.3);
    padding: .15em;
    border-radius: 2px;
}
/* ckeditor5-image/theme/image.css */
.ck-content .image {
    display: table;
    clear: both;
    text-align: center;
    margin: 1em auto;
}
/* ckeditor5-image/theme/image.css */
.ck-content .image > img {
    display: block;
    margin: 0 auto;
    max-width: 100%;
    min-width: 50px;
}
/* ckeditor5-image/theme/imagecaption.css */
.ck-content .image > figcaption {
    display: table-caption;
    caption-side: bottom;
    word-break: break-word;
    color: hsl(0, 0%, 20%);
    background-color: hsl(0, 0%, 97%);
    padding: .6em;
    font-size: .75em;
    outline-offset: -1px;
}
/* ckeditor5-image/theme/imageresize.css */
.ck-content .image.image_resized {
    max-width: 100%;
    display: block;
    box-sizing: border-box;
}
/* ckeditor5-image/theme/imageresize.css */
.ck-content .image.image_resized img {
    width: 100%;
}
/* ckeditor5-image/theme/imageresize.css */
.ck-content .image.image_resized > figcaption {
    display: block;
}
/* ckeditor5-media-embed/theme/mediaembed.css */
.ck-content .media {
    clear: both;
    margin: 1em 0;
    display: block;
    min-width: 15em;
}
/* ckeditor5-list/theme/todolist.css */
.ck-content .todo-list {
    list-style: none;
}
/* ckeditor5-list/theme/todolist.css */
.ck-content .todo-list li {
    margin-bottom: 5px;
}
/* ckeditor5-list/theme/todolist.css */
.ck-content .todo-list li .todo-list {
    margin-top: 5px;
}
/* ckeditor5-list/theme/todolist.css */
.ck-content .todo-list .todo-list__label > input {
    -webkit-appearance: none;
    display: inline-block;
    position: relative;
    width: var(--ck-todo-list-checkmark-size);
    height: var(--ck-todo-list-checkmark-size);
    vertical-align: middle;
    border: 0;
    left: -25px;
    margin-right: -15px;
    right: 0;
    margin-left: 0;
}
/* ckeditor5-list/theme/todolist.css */
.ck-content .todo-list .todo-list__label > input::before {
    display: block;
    position: absolute;
    box-sizing: border-box;
    content: '';
    width: 100%;
    height: 100%;
    border: 1px solid hsl(0, 0%, 20%);
    border-radius: 2px;
    transition: 250ms ease-in-out box-shadow, 250ms ease-in-out background, 250ms ease-in-out border;
}
/* ckeditor5-list/theme/todolist.css */
.ck-content .todo-list .todo-list__label > input::after {
    display: block;
    position: absolute;
    box-sizing: content-box;
    pointer-events: none;
    content: '';
    left: calc( var(--ck-todo-list-checkmark-size) / 3 );
    top: calc( var(--ck-todo-list-checkmark-size) / 5.3 );
    width: calc( var(--ck-todo-list-checkmark-size) / 5.3 );
    height: calc( var(--ck-todo-list-checkmark-size) / 2.6 );
    border-style: solid;
    border-color: transparent;
    border-width: 0 calc( var(--ck-todo-list-checkmark-size) / 8 ) calc( var(--ck-todo-list-checkmark-size) / 8 ) 0;
    transform: rotate(45deg);
}
/* ckeditor5-list/theme/todolist.css */
.ck-content .todo-list .todo-list__label > input[checked]::before {
    background: hsl(126, 64%, 41%);
    border-color: hsl(126, 64%, 41%);
}
/* ckeditor5-list/theme/todolist.css */
.ck-content .todo-list .todo-list__label > input[checked]::after {
    border-color: hsl(0, 0%, 100%);
}
/* ckeditor5-list/theme/todolist.css */
.ck-content .todo-list .todo-list__label .todo-list__label__description {
    vertical-align: middle;
}
/* ckeditor5-table/theme/table.css */
.ck-content .table {
    margin: 1em auto;
    display: table;
}
/* ckeditor5-table/theme/table.css */
.ck-content .table table {
    border-collapse: collapse;
    border-spacing: 0;
    border: 1px double hsl(0, 0%, 70%);
}
/* ckeditor5-table/theme/table.css */
.ck-content .table table td,
.ck-content .table table th {
    min-width: 2em;
    padding: .4em;
    border-color: hsl(0, 0%, 85%);
}
/* ckeditor5-table/theme/table.css */
.ck-content .table table th {
    font-weight: bold;
    background: hsl(0, 0%, 98%);
}
/* ckeditor5-page-break/theme/pagebreak.css */
.ck-content .page-break {
    position: relative;
    clear: both;
    padding: 5px 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
/* ckeditor5-page-break/theme/pagebreak.css */
.ck-content .page-break::after {
    content: '';
    position: absolute;
    border-bottom: 2px dashed hsl(0, 0%, 77%);
    width: 100%;
}
/* ckeditor5-page-break/theme/pagebreak.css */
.ck-content .page-break__label {
    position: relative;
    z-index: 1;
    padding: .3em .6em;
    display: block;
    text-transform: uppercase;
    border: 1px solid hsl(0, 0%, 77%);
    border-radius: 2px;
    font-family: Helvetica, Arial, Tahoma, Verdana, Sans-Serif;
    font-size: 0.75em;
    font-weight: bold;
    color: hsl(0, 0%, 20%);
    background: #fff;
    box-shadow: 2px 2px 1px hsla(0, 0%, 0%, 0.15);
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
/* ckeditor5-image/theme/imagestyle.css */
.ck-content .image-style-side,
.ck-content .image-style-align-left,
.ck-content .image-style-align-center,
.ck-content .image-style-align-right {
    max-width: 50%;
}
/* ckeditor5-image/theme/imagestyle.css */
.ck-content .image-style-side {
    float: right;
    margin-left: var(--ck-image-style-spacing);
}
/* ckeditor5-image/theme/imagestyle.css */
.ck-content .image-style-align-left {
    float: left;
    margin-right: var(--ck-image-style-spacing);
}
/* ckeditor5-image/theme/imagestyle.css */
.ck-content .image-style-align-center {
    margin-left: auto;
    margin-right: auto;
}
/* ckeditor5-image/theme/imagestyle.css */
.ck-content .image-style-align-right {
    float: right;
    margin-left: var(--ck-image-style-spacing);
}
/* ckeditor5-block-quote/theme/blockquote.css */
.ck-content blockquote {
    overflow: hidden;
    padding-right: 1.5em;
    padding-left: 1.5em;
    margin-left: 0;
    margin-right: 0;
    font-style: italic;
    border-left: solid 5px hsl(0, 0%, 80%);
}
/* ckeditor5-block-quote/theme/blockquote.css */
.ck-content[dir="rtl"] blockquote {
    border-left: 0;
    border-right: solid 5px hsl(0, 0%, 80%);
}
/* ckeditor5-horizontal-line/theme/horizontalline.css */
.ck-content hr {
    border-width: 1px 0 0;
    border-style: solid;
    border-color: hsl(0, 0%, 37%);
    margin: 0;
}
@media print {
    /* ckeditor5-page-break/theme/pagebreak.css */
    .ck-content .page-break {
        padding: 0;
    }
    /* ckeditor5-page-break/theme/pagebreak.css */
    .ck-content .page-break::after {
        display: none;
    }
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
