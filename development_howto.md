## How I can update the CKE5 vendor?

### First step: create your working place

To update the vendor we have to create a functional cke5 custom build with each cke5 npm modules. For initialisation, customisation and for testing we have to make a working directory and to load the latest cke5 packages.

- open your terminal/bash
- create your working directory
  ```
  mkdir /ckeditor5_working_directory/
  cd /ckeditor5_working_directory/
  ```
- clone the cke5 editor repository
  ```
  git clone git@github.com:ckeditor/ckeditor5.git ckeditor5_master
  ```
- copy the `ckeditor5_master/packages/ckeditor5-build-classic/` to the working directory root
  ```
  cp -r ckeditor5_master/packages/ckeditor5-build-classic/ ckeditor5-build-classic/
  ```
- move into the classic build package directory and install npm modules
  ```
  cd ckeditor5-build-classic/
  npm install
  ```
- run the build process
  ```
  yarn run build
  ```
- check the build in your browser
  ```
  file:///ckeditor5_working_directory/ckeditor/packages/ckeditor5-build-classic/sample/index.html
  ```

### Second step: add the npm module dependencies

To update the vendor you have to care about the rex_cke5 dependency packages. This is important because from scratch the cke5 editor package contains only a set of default modules and functions, for the rex cke5 addon with all cke5 options we have to add all cke5 standard modules and the cke5 rex images and cke5 rex link module.

#### Add the @cke5 dependencies to the package.json 

- open the `package.json` file from the class build path `/ckeditor5_working_directory/ckeditor/packages/ckeditor5-build-classic/`
- add all the @cke5 modules there not by default added and add the cke5 rexlink and reximage module to the `dependencies` list
  ```json
  "dependencies": {
    "@ckeditor/ckeditor5-adapter-ckfinder": "^38.0.1",
    "@ckeditor/ckeditor5-alignment": "^38.0.1",
    "@ckeditor/ckeditor5-autoformat": "^38.0.1",
    "@ckeditor/ckeditor5-basic-styles": "^38.0.1",
    "@ckeditor/ckeditor5-block-quote": "^38.0.1",
    "@ckeditor/ckeditor5-ckbox": "^38.0.1",
    "@ckeditor/ckeditor5-ckfinder": "^38.0.1",
    "@ckeditor/ckeditor5-clipboard": "^38.0.1",
    "@ckeditor/ckeditor5-cloud-services": "^38.0.1",
    "@ckeditor/ckeditor5-code-block": "^38.0.1",
    "@ckeditor/ckeditor5-document-outline": "^38.0.1",
    "@ckeditor/ckeditor5-easy-image": "^38.0.1",
    "@ckeditor/ckeditor5-editor-classic": "^38.0.1",
    "@ckeditor/ckeditor5-essentials": "^38.0.1",
    "@ckeditor/ckeditor5-find-and-replace": "^38.0.1",
    "@ckeditor/ckeditor5-font": "^38.0.1",
    "@ckeditor/ckeditor5-heading": "^38.0.1",
    "@ckeditor/ckeditor5-highlight": "^38.0.1",
    "@ckeditor/ckeditor5-horizontal-line": "^38.0.1",
    "@ckeditor/ckeditor5-html-embed": "^38.0.1",
    "@ckeditor/ckeditor5-html-support": "^38.0.1",
    "@ckeditor/ckeditor5-image": "^38.0.1",
    "@ckeditor/ckeditor5-indent": "^38.0.1",
    "@ckeditor/ckeditor5-language": "^38.0.1",
    "@ckeditor/ckeditor5-link": "^38.0.1",
    "@ckeditor/ckeditor5-list": "^38.0.1",
    "@ckeditor/ckeditor5-media-embed": "^38.0.1",
    "@ckeditor/ckeditor5-mention": "^38.0.1",
    "@ckeditor/ckeditor5-page-break": "^38.0.1",
    "@ckeditor/ckeditor5-paragraph": "^38.0.1",
    "@ckeditor/ckeditor5-paste-from-office": "^38.0.1",
    "@ckeditor/ckeditor5-remove-format": "^38.0.1",
    "@ckeditor/ckeditor5-select-all": "^38.0.1",
    "@ckeditor/ckeditor5-source-editing": "^38.0.1",
    "@ckeditor/ckeditor5-special-characters": "^38.0.1",
    "@ckeditor/ckeditor5-style": "^38.0.1",
    "@ckeditor/ckeditor5-table": "^38.0.1",
    "@ckeditor/ckeditor5-template": "^38.0.1",
    "@ckeditor/ckeditor5-typing": "^38.0.1",
    "@phudak/ckeditor5-emoji": "^1.1.1",
    "ckeditor5-paste-plaintext": "^1.0.1",
    "ckeditor5-reximage": "^3.0.2",
    "ckeditor5-rexlink": "^3.0.4"
  }
  ```
- now we can install the npm dependencies 
  ```
  npm install
  ```
  
#### Add the dependencies to the ckeditor.js

- open the `ckeditor.ts` file from the classic build src path `/ckeditor5_working_directory/ckeditor/packages/ckeditor5-build-classic/src/`  
- add all the rex cke5 addon imports from @cke5 to the import list
  ```javascript
    import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
    import { Essentials } from '@ckeditor/ckeditor5-essentials';
    import { UploadAdapter } from '@ckeditor/ckeditor5-adapter-ckfinder';
    import { Autoformat } from '@ckeditor/ckeditor5-autoformat';
    import { Bold, Italic, Strikethrough, Underline, Code, Subscript, Superscript } from '@ckeditor/ckeditor5-basic-styles';
    import { BlockQuote } from '@ckeditor/ckeditor5-block-quote';
    import { CKBox } from '@ckeditor/ckeditor5-ckbox';
    import { CKFinder } from '@ckeditor/ckeditor5-ckfinder';
    import { EasyImage } from '@ckeditor/ckeditor5-easy-image';
    import { Heading } from '@ckeditor/ckeditor5-heading';
    import { Image, ImageCaption, ImageStyle, ImageToolbar, ImageUpload, PictureEditing, ImageResizeEditing, ImageResizeButtons, ImageResizeHandles, ImageInsert } from '@ckeditor/ckeditor5-image';
    import { Indent } from '@ckeditor/ckeditor5-indent';
    import { Link, LinkImage } from '@ckeditor/ckeditor5-link';
    import { List, ListProperties, TodoList } from '@ckeditor/ckeditor5-list';
    import { MediaEmbed } from '@ckeditor/ckeditor5-media-embed';
    import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
    import { PasteFromOffice } from '@ckeditor/ckeditor5-paste-from-office';
    import { Table, TableToolbar, TableProperties, TableCellProperties, TableCaption } from '@ckeditor/ckeditor5-table';
    import { TextTransformation } from '@ckeditor/ckeditor5-typing';
    import { CloudServices } from '@ckeditor/ckeditor5-cloud-services';
    import { Alignment } from '@ckeditor/ckeditor5-alignment';
    import { Highlight } from '@ckeditor/ckeditor5-highlight';
    import { Font } from '@ckeditor/ckeditor5-font';
    import { SpecialCharacters, SpecialCharactersCurrency, SpecialCharactersMathematical, SpecialCharactersEssentials, SpecialCharactersLatin, SpecialCharactersArrows, SpecialCharactersText } from '@ckeditor/ckeditor5-special-characters';
    import { CodeBlock } from '@ckeditor/ckeditor5-code-block';
    import { RemoveFormat } from '@ckeditor/ckeditor5-remove-format';
    import { IndentBlock } from '@ckeditor/ckeditor5-indent';
    import { HorizontalLine } from '@ckeditor/ckeditor5-horizontal-line';
    import { PageBreak } from '@ckeditor/ckeditor5-page-break';
    import { Clipboard } from '@ckeditor/ckeditor5-clipboard';
    import { HtmlEmbed } from '@ckeditor/ckeditor5-html-embed';
    import { HtmlComment } from '@ckeditor/ckeditor5-html-support';
    import { FindAndReplace } from '@ckeditor/ckeditor5-find-and-replace';
    import { SourceEditing } from '@ckeditor/ckeditor5-source-editing';
    import { GeneralHtmlSupport } from '@ckeditor/ckeditor5-html-support';
    import { Style } from '@ckeditor/ckeditor5-style';
    import { TextPartLanguage } from '@ckeditor/ckeditor5-language';
    import { SelectAll } from '@ckeditor/ckeditor5-select-all';
    import { RexLink } from 'ckeditor5-rexlink';
    import { RexImage } from 'ckeditor5-reximage';
    import { Mention } from '@ckeditor/ckeditor5-mention';
  ```
- and add the imported plugins to the `builtinPlugins` list
  ```javascript
  public static override builtinPlugins = [
    Essentials,
    UploadAdapter,
    Autoformat,
    Bold,
    Italic,
    BlockQuote,
    CKBox,
    CKFinder,
    CloudServices,
    EasyImage,
    Heading,
    Image,
    ImageCaption,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    Indent,
    Link,
    List,
    ListProperties,
    TodoList,
    MediaEmbed,
    Paragraph,
    PasteFromOffice,
    PictureEditing,
    Table,
    TableToolbar,
    TextTransformation,
    ImageInsert,
    ImageResizeEditing,
    ImageResizeButtons,
    ImageResizeHandles,
    Alignment,
    Highlight,
    Font,
    Strikethrough,
    Underline,
    Code,
    Subscript,
    Superscript,
    TableProperties,
    TableCellProperties,
    TableCaption,
    SpecialCharacters,
    SpecialCharactersCurrency,
    SpecialCharactersMathematical,
    SpecialCharactersLatin,
    SpecialCharactersArrows,
    SpecialCharactersText,
    SpecialCharactersEssentials,
    CodeBlock,
    RemoveFormat,
    IndentBlock,
    HorizontalLine,
    PageBreak,
    LinkImage,
    Clipboard,
    HtmlEmbed,
    HtmlComment,
    FindAndReplace,
    SourceEditing,
    GeneralHtmlSupport,
    Style,
    TextPartLanguage,
    SelectAll,
    RexLink,
    RexImage,
    Mention
  ]
  ```
- run the build process
  ```
  yarn run build
  ```
- check the build in your browser
  ```
  file:///ckeditor5_working_directory/ckeditor/packages/ckeditor5-build-classic/sample/index.html
  ```

#### What can i do? I got some issues with the build dependencies 

Due to the recent updates, we had some issues with the npm dependencies and used a trick to make sure the build was successful. Some cke5-dev dependencies create by the `npm install` run warnings and some other not solvable issues that want to destroy our last nerves. In this case, the best way is to take an unconventional approach.

1. all dependencies not yet loaded, i.e. the node modules we wanted to load via `npm install` and which were not written to the `node_modules` folder due to dependencie errors during loading, must be loaded from github.com or node.js
2. create a plugin folder in the working directory
   ```
   cd /ckeditor5_working_directory/ckeditor/packages/ckeditor5-build-classic/
   mkdir plugins/
   ```
3. the loaded plugins must now be copied into the newly created `plugins/` folder 
4. to ensure that the build will be use the sources from the `plugins/` folder it is important to change the plugin paths in the import list
   - open the `ckeditor.js` file from the classic build src path `/ckeditor5_working_directory/ckeditor/packages/ckeditor5-build-classic/src/`
   - and change the path for the imports there a copied to the `plugins/` folder
     ```
     import Rexlink from '../plugins/ckeditor5-rexlink/src/rexlink';
     import Reximageui from '../plugins/ckeditor5-reximage/src/reximageui';
     ```
5. run the build process
    ```
    yarn run build
    ```
6. check the build in your browser
    ```
    file:///ckeditor5_working_directory/ckeditor/packages/ckeditor5-build-classic/sample/index.html
    ```

### Third step: replace the old ckeditor.js files in the assets folders

Final we have to replace the old ckeditor.js and translations/*.js files in the assets folders. In case of you already have installed the cke5 redaxo addon you have to replace the vendor in the redaxo main `/assets/` folder too.

- open the main cke5 addon assets folder
  ```
  cd /ckeditor5_working_directory/redaxo_installation/assets/addons/cke5/vendor/`
  ```
- delete the old cke5 js files and move the new stuff to the vendor folder
  ```
  rm -rf ckeditor5-classic/
  cp -r /ckeditor5_working_directory/ckeditor/packages/ckeditor5-build-classic/build ckeditor5-classic/
  ```
