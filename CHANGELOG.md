# Changelog

## Version 2.1.4

* remove php version requirement to make addon for php7 installable: https://github.com/redaxo/redaxo/issues/2204

## Version 2.1.3

* add rex:ready  @dergel
* fix duplicate by pjax call
* editor height range slider don't notice profile update fixed
* add link to profile edit in preview site
* add mform code example to preview site
* add profile info to preview site
* show profile settings add to preview site
* execute building process and add newest cke5 version 11.1.1 with media embed
* add Media embed in default profile

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
