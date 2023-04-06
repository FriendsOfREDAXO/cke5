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
  git clone git@github.com:ckeditor/ckeditor5.git ckeditor
  ```
- move into the classic build package and install npm modules
  ```
  cd ckeditor/packages/ckeditor5-build-classic/
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
    "@ckeditor/ckeditor5-typing": "^35.3.0",
    "@ckeditor/ckeditor5-alignment": "^35.3.0",
    "@ckeditor/ckeditor5-clipboard": "^35.3.0",
    "@ckeditor/ckeditor5-code-block": "^35.3.0",
    "@ckeditor/ckeditor5-find-and-replace": "^35.3.0",
    "@ckeditor/ckeditor5-font": "^35.3.0",
    "@ckeditor/ckeditor5-highlight": "^35.3.0",
    "@ckeditor/ckeditor5-horizontal-line": "^35.3.0",
    "@ckeditor/ckeditor5-html-embed": "^35.3.0",
    "@ckeditor/ckeditor5-html-support": "^35.3.0",
    "@ckeditor/ckeditor5-language": "^35.3.0",
    "@ckeditor/ckeditor5-page-break": "^35.3.0",
    "@ckeditor/ckeditor5-remove-format": "^35.3.0",
    "@ckeditor/ckeditor5-select-all": "^35.3.0",
    "@ckeditor/ckeditor5-source-editing": "^35.3.0",
    "@ckeditor/ckeditor5-special-characters": "^35.3.0",
    "@ckeditor/ckeditor5-style": "^35.3.0",
    "ckeditor5-reximage": "^1.2.3",
    "ckeditor5-rexlink": "^2.1.0",
    "@phudak/ckeditor5-emoji": "^1.1.1"
    ...
  }
  ```
- now we can install the npm dependencies 
  ```
  npm install
  ```
  
#### Add the dependencies to the ckeditor.js

- open the `ckeditor.js` file from the classic build src path `/ckeditor5_working_directory/ckeditor/packages/ckeditor5-build-classic/src/`  
- add all the rex cke5 addon imports from @cke5 to the import list
  ```javascript
  import ImageResize from '@ckeditor/ckeditor5-image/src/imageresize';
  import ImageInsert from '@ckeditor/ckeditor5-image/src/imageinsert';
  import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
  import Highlight from '@ckeditor/ckeditor5-highlight/src/highlight';
  import Font from '@ckeditor/ckeditor5-font/src/font';
  import Strikethrough from '@ckeditor/ckeditor5-basic-styles/src/strikethrough';
  import Underline from '@ckeditor/ckeditor5-basic-styles/src/underline';
  import Code from '@ckeditor/ckeditor5-basic-styles/src/code';
  import Subscript from '@ckeditor/ckeditor5-basic-styles/src/subscript';
  import Superscript from '@ckeditor/ckeditor5-basic-styles/src/superscript';
  import TableProperties from '@ckeditor/ckeditor5-table/src/tableproperties';
  import TableCellProperties from '@ckeditor/ckeditor5-table/src/tablecellproperties';
  import TableCaption from '@ckeditor/ckeditor5-table/src/tablecaption';
  import SpecialCharacters from '@ckeditor/ckeditor5-special-characters/src/specialcharacters';
  import SpecialCharactersCurrency from '@ckeditor/ckeditor5-special-characters/src/specialcharacterscurrency';
  import SpecialCharactersMathematical from '@ckeditor/ckeditor5-special-characters/src/specialcharactersmathematical';
  import SpecialCharactersEssentials from '@ckeditor/ckeditor5-special-characters/src/specialcharactersessentials';
  import SpecialCharactersLatin from '@ckeditor/ckeditor5-special-characters/src/specialcharacterslatin';
  import SpecialCharactersArrows from '@ckeditor/ckeditor5-special-characters/src/specialcharactersarrows';
  import SpecialCharactersText from '@ckeditor/ckeditor5-special-characters/src/specialcharacterstext';
  import CodeBlock from '@ckeditor/ckeditor5-code-block/src/codeblock';
  import RemoveFormat from '@ckeditor/ckeditor5-remove-format/src/removeformat';
  import IndentBlock from '@ckeditor/ckeditor5-indent/src/indentblock';
  import TodoList from '@ckeditor/ckeditor5-list/src/todolist';
  import HorizontalLine from '@ckeditor/ckeditor5-horizontal-line/src/horizontalline';
  import PageBreak from '@ckeditor/ckeditor5-page-break/src/pagebreak';
  import LinkImage from '@ckeditor/ckeditor5-link/src/linkimage';
  import Clipboard from '@ckeditor/ckeditor5-clipboard/src/clipboard';
  import ListProperties from '@ckeditor/ckeditor5-list/src/listproperties';
  import HtmlEmbed from '@ckeditor/ckeditor5-html-embed/src/htmlembed';
  import HtmlComment from '@ckeditor/ckeditor5-html-support/src/htmlcomment';
  import FindAndReplace from '@ckeditor/ckeditor5-find-and-replace/src/findandreplace';
  import SourceEditing from '@ckeditor/ckeditor5-source-editing/src/sourceediting';
  import GeneralHtmlSupport from '@ckeditor/ckeditor5-html-support/src/generalhtmlsupport';
  import Style from '@ckeditor/ckeditor5-style/src/style';
  import Rexlink from 'ckeditor5-rexlink/src/rexlink';
  import Reximageui from 'ckeditor5-reximage/src/reximageui';
  import Emoji from '@phudak/ckeditor5-emoji/src/emoji';
  import EmojiPeople from '@phudak/ckeditor5-emoji/src/emoji-people';
  import EmojiNature from '@phudak/ckeditor5-emoji/src/emoji-nature';
  import EmojiFood from '@phudak/ckeditor5-emoji/src/emoji-food';
  import EmojiActivity from '@phudak/ckeditor5-emoji/src/emoji-activity';
  import EmojiObjects from '@phudak/ckeditor5-emoji/src/emoji-objects';
  import EmojiPlaces from '@phudak/ckeditor5-emoji/src/emoji-places';
  import EmojiSymbols from '@phudak/ckeditor5-emoji/src/emoji-symbols';
  import EmojiFlags from '@phudak/ckeditor5-emoji/src/emoji-flags';
  import PasteAsPlainText from 'ckeditor5-paste-plaintext/src/plaintext';
  ```
- and add the imported plugins to the `builtinPlugins` list
  ```javascript
  ClassicEditor.builtinPlugins = [
    ...
    ImageInsert,
    ImageResize,
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
    TodoList,
    HorizontalLine,
    PageBreak,
    LinkImage,
    Clipboard,
    ListProperties,
    HtmlEmbed,
    HtmlComment,
    FindAndReplace,
    SourceEditing,
    GeneralHtmlSupport,
    Rexlink,
    Reximageui,
    Emoji,
    EmojiPeople,
    EmojiNature,
    EmojiPlaces,
    EmojiFood,
    EmojiActivity,
    EmojiObjects,
    EmojiSymbols,
    EmojiFlags,
    Style
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
