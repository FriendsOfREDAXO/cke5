# CKEditor5 for REDAXO CMS

Integrates the [CKEditor5](https://ckeditor.com) into REDAXO CMS.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/cke5.png)

## Features by Functional Groups

### Core Features
- A powerful WYSIWYG editor with modern interface
- Dark mode support for REDAXO >= 5.13
- Placeholders for all backend languages
- Only supported formats are inserted

### Configuration and Customization
- Profile configurator with Drag&Drop for easy profile creation
- Expert mode for free source code profile development
- Additional options to customize the editor to your needs
- Configuration page for license key to remove the "Powered by CKEditor" banner
- API for programmatic profile generation

### Style Management
- Style-Manager for easy style administration
- Style groups for quick capture of styles as JSON array
- CSS definitions from each style are automatically added to the backend
- Custom fonts can be integrated and managed
- Improved tag handling in profile and style editor

### Media Integration
- Image upload to the media pool via drag & drop directly into the text field
- Image upload category configurable per profile
- Media manager type settable per profile 
- Drag & Drop upload for CKEditor vendor files (configurable in config.php)

### Link Features
- Comprehensive REDAXO Link-Widget
- Linkmap-Support
- YForm-Datasets integration
- Tel: and Mailto: links
- Media links
- Custom link decorators for custom attributes and classes

### Extensions and Plugins
- All free provider plugins are integrated
- Sprog replacements via the mentions plugin
- AccessibilityHelper for better accessibility
- Insert plain text
- Transformations: e.g. converting (c) to ©
- Selection for special characters
- New toolbar elements: Emoji, Bookmarks, ShowBlocks

### Import and Export
- Profile export and import for easy migration
- Data backup before updates
- Consistent style transfer between installations

## Tips for Backup and Migration

### Exporting and Importing Profiles

To transfer your CKEditor5 profiles between REDAXO installations:

1. Go to "CKEditor5" > "Profiles" > "Export"
2. Select the profiles you want to export
3. Click "Export" to download the JSON file
4. In the target instance: Go to "CKEditor5" > "Profiles" > "Import"
5. Select the JSON file and import it

This ensures your carefully created profiles won't be lost when upgrading or migrating to a new installation.

### Best Practices for Backup

- Always export your profiles before updating the addon
- Store backups of your custom styles in a separate JSON file
- Document special configurations in your project documentation
- Note that CSS files in `custom_data` won't be overwritten during updates

## Usage Examples with Code Snippets

### Basic Integration with Custom Height 

```php
<textarea class="form-control cke5-editor" 
          data-profile="default" 
          data-min-height="200" 
          data-max-height="600"
          data-lang="<?php echo \Cke5\Utils\Cke5Lang::getUserLang(); ?>" 
          name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
```

### Multi-language Support

```php
// Show placeholders according to the current language
$cke5Lang = rex_i18n::getLocale();
echo '<textarea class="cke5-editor" data-profile="default" data-lang="'.$cke5Lang.'" name="content"></textarea>';
```

### Using Style Groups

Style groups allow for quick assignment of multiple CSS properties at once:

```json
{
    "name": "Highlight-Box",
    "element": "div",
    "classes": ["highlight-box", "padding-20"],
    "attributes": {
        "data-info": "custom-highlight"
    }
}
```

## Using Style Groups

Style groups allow you to define multiple CSS styles as a JSON array and manage them together. This feature was introduced in version 6.3.3 and simplifies the consistent design of your content.

### Example of a Style Group

Under "CKEditor5" > "Profiles" > "Customise" > "Style Groups" you can create style groups:

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

### How to use Style Groups:

1. Create a style group with the JSON configuration
2. Optional: Add CSS definitions directly, which will be automatically loaded in the backend
3. Enable the "Style" tool in your editor profile
4. Select your created style group in the profile editor

### Benefits:
- Consistent design across different profiles
- Central management of related styles
- Automatic CSS integration in the backend
- Easy export/import between REDAXO instances

### CSS Definitions for Style Groups

You can add CSS directly to your style groups:

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

## A little demo

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor5_demo.gif)

## The Profile Editor

Configure your editor just the way you need it.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/ckeditor_profile_editor_demo.gif)

## Code examples to get you started

### General Usage:

### Input Code

```php
 <textarea class="form-control cke5-editor" data-profile="default" data-lang="<?php echo \Cke5\Utils\Cke5Lang::getUserLang(); ?>" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
```

### Output Code

```html
REX_VALUE[id="1" output="html"]
```

You can control the minimum and maximum height as well as the language via further data attributes:

- data-max-height
- data-min-height
- data-lang

### Usage in YForm

- In the individual attribute field: ``` {"class":"cke5-editor","data-profile":"default","data-lang":"en"} ```
- Further attributes possible, separated by commas

### Usage in MForm

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

### Usage with MBlock

```php
$id = 1;
$mform = new MForm();
$mform->addFieldset('Accordion');
$mform->addTextField("$id.0.titel", array('label'=>'Title'));
$mform->addTextAreaField("$id.0.text",
        array(
        'label'=>'Text',
        'class'=>'cke5-editor',
        'data-lang'=>\Cke5\Utils\Cke5Lang::getUserLang(),
        'data-profile'=>'default')
        );
echo MBlock::show($id, $mform->show());
```

## Embedding Custom Fonts

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/cke5/assets/fonts.png)

To make the specified fonts visible in the backend, they must be loaded as assets in the backend.
This can be done, for example, in the `boot.php` of the project AddOn or in the `backend.css` of the theme AddOn.
The fonts are stored in the *FontFamily* section of the profile editor, in the usual CSS notation.

## Sprog Replacements – Quick & Dirty

Under `Mention & Sprog Replacements` > `Sprog Replacements` > `Replacements` you can store Sprog placeholders with title or description.
Syntax: `{{key}}`. The title goes in the next field.
Just type '{{' in the editor to get a list of placeholders.

## Customization – Make it your Editor

The appearance of the editor can be adapted to the frontend output via CSS. For this purpose, there is a CSS file in the `assets/addons/cke5_custom_data` folder.

## CSS Content-Styles

[Styleguide here](https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/content-styles.html)

The styles are prefixed with `.ck-content`. The class should be added to the output element and the included `cke5_content_styles.css` from the asset folder loaded.

After installing this AddOn, the CSS file /assets/addons/cke5/cke5_content_styles.css is immediately ready for use. But creating your own file might be the better choice.

## CKE in the Frontend

[Check it out: REDAXO Tricks](https://friendsofredaxo.github.io/tricks/snippets/ckeditor_im_frontend)

## Keyboard Shortcuts

Here are the most important keyboard shortcuts for CKEditor 5 and its features:

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

Use these keyboard shortcuts for more efficient navigation through the CKEditor 5 interface:

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

## For Developers

### Example Extra Options

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

### Example for custom Link-Decorators
*Important: Keys must be lowercase*

```js
[{
    "newtab": {
        "mode": "manual",
        "label": "Open in a new tab",
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
        "label": "Link with CSS Class",
        "defaultValue": "true",
        "classes": "arrow"
    }
}]
```

Or multiple:
```js
[{
    "openInNewTab": {
        "mode": "manual",
        "label": "Open in a new tab",
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

The AddOn provides the [Mentions plugin](https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html). You can configure this freely.
Here's an example:

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

### YForm links

To replace generated URLs like `rex_news://1`, add the following script to the `boot.php` of the `project` AddOn.
The code for the URLs must be adjusted.

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
                    // Example if the URLs are generated via Url-AddOn
                    $id = $matches[3];
                    if ($id) {
                       return rex_getUrl('', '', ['news' => $id]);
                    }
                    break;
                case 'person':
                    // Another Example
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

Via the API you can create your own profiles apart from the profile editor: `Cke5\Creator\Cke5ProfilesApi::addProfile`

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

### HTML-Support

Source-Editing Plugin has an update.
After an update from a very old version, the basic setting for the plugin may be missing in the HtmlSupport section.

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

### Disable Autoformat

You can disable the autoformat feature (markdown code replacement) by adding this option to the Extra Options section:

```json
{"removePlugins": ["Autoformat"]}
```

## Bugtracker

Found an error or got an idea? Create an [Issue](https://github.com/FriendsOfREDAXO/cke5/issues).
Before creating a new issue, please search if a similar one already exists. And read the [Issue Guidelines (english)](https://github.com/necolas/issue-guidelines) from [Nicolas Gallagher](https://github.com/necolas/).

## Changelog

See [CHANGELOG.md](https://github.com/FriendsOfREDAXO/cke5/blob/master/CHANGELOG.md).

## Licenses

AddOn:[MIT LICENSE](https://github.com/FriendsOfREDAXO/cke5/blob/master/LICENSE)
CKEDITOR [GPL LICENSE](https://github.com/ckeditor/ckeditor5/blob/master/LICENSE.md)

## Authors

**Friends Of REDAXO**

* http://www.redaxo.org
* https://github.com/FriendsOfREDAXO

**Project Lead**

[Joachim Dörr](https://github.com/joachimdoerr)

**Initiator:**

[KLXM Crossmedia / Thomas Skerbis](https://klxm.de)
