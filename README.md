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
- Define your own link decorator
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

CKE5 uses some own styles. 

[Link to Styleguide](https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/content-styles.html)

The Styles are prefixed with `.ck-content`. You should add this class to your output element and load the included `cke5_content_styles.css` form the assets folder.  


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



## For Developers

By using the API: `Cke5\Creator\Cke5ProfilesApi::addProfile`, it is possible to install own profiles beside of the profile editor. 

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
