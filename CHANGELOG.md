# Changelog

## Version 5.0.0

* vendor update to v31
* add `toggleTableCaption` feature
* add `fullscreen` plugin and as well add `|` to table toolbar
* extend emoji add `EmojiPeople`, `EmojiNature`, `EmojiPlaces`, `EmojiFood`, `EmojiActivity`, `EmojiObjects`, `EmojiSymbols`, `EmojiFlags` group
* add `sourceEditing` and `htmlSupport`
* fix some ugly profile editor javascript issues
* add `inline`, `side`, `alignBlockLeft`, `alignBlockRight`, `toggleImageCaption` for images
* add better profile editor structure
* add option to group buttons in the image toolbar
* fix name issue, disallow usage of minus `-` char
* fix wrong translation key usage in profile editor
* fix removePlugin issue by extra option usage (for example add to extra options: `{"removePlugins": ["Autoformat"]}`)
* fix profile recreation by update
* fix dark mode switching

### Breaking Change

* The imageStyle property `full` was replaced by vendor with `block`
* Supports only REDAXO >= 5.12
* Older addons of mform < 6.1 and mblock < 3.4 are no longer supported


### Update Notice

The profiles are not changed during an update. 

The following should be paid attention to when updating: 

- If the option `full` was used in the image toolbars, it should be replaced by `block`. If necessary, the output CSS must be adjusted. 

- The source code editing needs at least the instruction `HtmlSupport Allow` to work. 
The following code can be used for this: 

```json
[
    {
        "name": "regex(/.*/)",
        "attributes": true,
        "classes": true,
        "styles": true
    }
]
```

## Version 4.3.0

* vendor update to 27.0.0
* dark-mode
* some small fixes

## Version 4.2.0

* vendor update to 23.1.0
* add htmlEmbed
* add htmlEmbed preview optional

## Version 4.1.0

* vendor update to 22.0.0
* add autolink options
* add image resize option buttons
* add list style option

## Version 4.0.0

* New REDAXO Link-Widget supports: Linkmap, Media, YForm-Tables, Tel:, Mail
* vendor update to v20.0.0 and support some new features https://github.com/FriendsOfREDAXO/cke5/issues/104
* switch for expert or profile editor mode
* add textarea for special options for profile editor mode
* added codeBlock and codeBlock-languages
* add special characters plugins
* plugin past plaintext added https://github.com/Mistralys/ckeditor5-paste-plaintext
* image link as image toolbar plugin added
* add tableColors to profile editor 
* load only needed plugins https://github.com/FriendsOfREDAXO/cke5/issues/106
* add link decorators https://github.com/FriendsOfREDAXO/cke5/issues/90
* fix reinstall bug https://github.com/FriendsOfREDAXO/cke5/issues/102
* option to set placeholder for each lang
* fix some lang ui bugs https://github.com/FriendsOfREDAXO/cke5/issues/82
* Api and Cke5DatabaseHandler create profile now work with expert mode
* add demo profiles for expert and profile-editor mode
* add group toolbar elements feature https://ckeditor.com/docs/ckeditor5/latest/api/module_core_editor_editorconfig-EditorConfig.html#member-toolbar
* ensure that enter will not submit form in tag lists https://github.com/FriendsOfREDAXO/cke5/issues/87

## Version 3.7.1

* Backend css fix @alexplusde 
* Traducción en castellan @nandes2062

## Version 3.7.0

* added todoList
* added page break
* added cke5 custom css file

## Version 3.6.0

* check if translation file exist and recreate in case of not exist
* update editor vendor to 15.0.0
* added fonts selection in profile editor
* mblock demo database removed
* profile editor js extended 
* added hr

## Version 3.5.0

* update editor vendor to 12.3.0
* added outdent and indent
* added content language- and rtl-support
* added default font size option
* set mediaEmbed provider as collapse input filed
* update default profiles
* added mform and html code example to editor preview
* added getOutputLang to Cke5Lang class
* added custom fontcolor and font backgroundcolor editor 

## Version 3.4.0

* update editor vendor to 12.1.0
* added font-color
* added font-background-color
* added remove format

## Version 3.3.0

* added mediaEmbed providers to profile editor
* update cke5 vendor to v12.0.0
* remove help.php

## Version 3.2.1

* user cke5-rex_link v1.2.1
* fix media path not used in drag&drop image plugin
* toggle for media categories in profile settings

## Version 3.2.0

* clang support for rex_link-button
* set /.media/ by definition
* ckeditor5_reximage plugin updated for cke5 version 11.2.0
* image support issue in mblock and multiple initialized editors fixed
* use default values for editor tags by data parameters
* fix smooth scrolling effect by height collapse toggle

## Version 3.1.0

* execute building process and added new cke5 version 11.2.0 
https://ckeditor.com/blog/CKEditor-5-v11.2.0-with-paste-from-Word-and-file-manager-support-released/
* add new toolbar elements to creation array 
* remove ckeditor5-supersub module there will replace by:
  
```
@ckeditor/ckeditor5-basic-styles/src/subscript
@ckeditor/ckeditor5-basic-styles/src/superscript
```

*NOTICE:* `sub`, `sup` will not work anymore

* update issue from 2.0.1 to 3.0.x fixed
* added sql update statement in `update.php` for all profiles there contains `sub`, `sup`. There will replace `sub`, `sup` with `subscript`, `superscript`.

## Version 3.0.0

* add rex:ready  @dergel
* fix duplicate by pjax call
* editor height range slider don't notice profile update fixed
* add link to profile edit in preview site
* add mform code example to preview site
* add profile info to preview site
* show profile settings add to preview site
* execute building process and add newest cke5 version 11.1.1 with media embed
* add Media embed in default profile
* remove php version requirement to make addon for php7 installable: https://github.com/redaxo/redaxo/issues/2204
* remove supersub plugin, add supsub plugin
* `super` toolbar key was replaced with `sup`
* add embed media example to demo page

## Version 2.1.2

* fix given profile sub_settings was ignored

## Version 2.1.1

* add uninstall.php @TobiasKrais
* add gif-demos @crydotsnake 

## Version 2.1.0

* update ckeditor 5 vendors to version 11
* fix many problems with rex_link and rex_images plugins for ckeditor5 v11
* add super-sub plugin https://github.com/mjadobson/ckeditor5-super-sub
* add super and sub to profile editor

## Version 2.0.1

* fix php compatibility problems
* fix locale will change bug in profile editor
* fix provide exception problem

## Version 2.0.0

* @ckeditor5 classic build update to v10.1.0
* add @ckeditor5 table plugin
* add options for @ckeditor5 table-toolbar in profile builder
* hide image toolbar settings in profile builder is imageUpload or rexImage disable
* update Keyboard support tables in readme.md's
* to don't lose custom profiles after update recreate profiles by update
* execute sql by update to v2.0.0
* use better style like github for keyboard support table
* add preview in profile builder
* add preview page with code example
* add text license to mblock demo page
* `Cke5\Creator\Cke5ProfilesApi::addProfile` api was add to create profiles without user interface

```php
    $create = \Cke5\Creator\Cke5ProfilesApi::addProfile(
        'full_cke',
        'Cke5 with all possible tools',
        ['heading', '|', 'fontSize', 'fontFamily', 'alignment', 'bold', 'italic', 'underline', 'strikethrough', 'insertTable', 'code', 'link', 'rexImage', 'bulletedList', 'numberedList', 'blockQuote', 'highlight', 'emoji', 'undo', 'redo'],
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

## Version 1.1.5

* fixed: image width on help page
* fixed: lang != language change key-name in js and profile settings
* add all supported backend languages in one file
* update editor build to fix language conflicts
* fixed: save ever only default settings by add profile
* add get user default lang helper
* use user default lang in examples

## Version 1.1.3

* fixed: js error in Demo caused by missing mblock installation
* swedish translation: thx to: @interweave-media

## Version 1.1.2

* fixed: remove ck element by MBlock add not ever work 
* fix many reinit register callback bug by pjax callback event
* Translate README -> thanks @crydotsnake

## Version 1.1.0

* README updated, module examples, shortcut add and many other great changes -> special thanks @skerbis
* Add short profile
* drag & drop for items in profile edit
* media-manager-type support in profiles
* media-category support in profiles for drag & drop image upload
* profile edit js and cke5 init js slitted in tow files
* drag & drop image upload optional for profiles

## Version 1.0.0 

* Add data sql to install default profile
* Translate to english
* Add mblock not available message
* Add mblock demo site - use wikipedia texts and images -> special thanks
* Add default demo site - use wikipedia texts and images -> special thanks
* Photo by Patrick Fore https://unsplash.com/photos/mMcqMYJfopo -> special thanks
* mblock support added
* cke5 rexlink plugin add https://github.com/basecondition/ckeditor5-rexlink
* cke5 reximage plugin add https://github.com/basecondition/ckeditor5-reximage
* drag&drop support for cke5 added 
* add editor files
* add all addon files
