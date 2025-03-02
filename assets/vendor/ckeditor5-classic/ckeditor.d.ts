/**
 * @license Copyright (c) 2003-2025, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-licensing-options
 */
import { ClassicEditor as ClassicEditorBase } from '@ckeditor/ckeditor5-editor-classic';
import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { CKFinderUploadAdapter } from '@ckeditor/ckeditor5-adapter-ckfinder';
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
import { Mention } from '@ckeditor/ckeditor5-mention';
import { ShowBlocks } from '@ckeditor/ckeditor5-show-blocks';
import { Bookmark } from '@ckeditor/ckeditor5-bookmark';
import { Emoji } from '@ckeditor/ckeditor5-emoji';
import { PasteAsPlainText } from './plugins/ckeditor5-paste-plaintext';
import { RexLink } from './plugins/ckeditor5-rexlink';
import { RexImage } from './plugins/ckeditor5-reximage';
export default class ClassicEditor extends ClassicEditorBase {
    static builtinPlugins: (typeof PasteAsPlainText | typeof TextTransformation | typeof Clipboard | typeof SelectAll | typeof Essentials | typeof CKFinderUploadAdapter | typeof Autoformat | typeof Superscript | typeof Subscript | typeof Bold | typeof Code | typeof Italic | typeof Strikethrough | typeof Underline | typeof BlockQuote | typeof CloudServices | typeof CKBox | typeof CKFinder | typeof EasyImage | typeof Paragraph | typeof Heading | typeof Image | typeof ImageCaption | typeof ImageInsert | typeof ImageStyle | typeof ImageToolbar | typeof ImageUpload | typeof ImageResizeEditing | typeof Indent | typeof IndentBlock | typeof Link | typeof LinkImage | typeof List | typeof ListProperties | typeof TodoList | typeof MediaEmbed | typeof PasteFromOffice | typeof Table | typeof TableCaption | typeof TableCellProperties | typeof TableProperties | typeof TableToolbar | typeof Alignment | typeof Highlight | typeof Font | typeof SpecialCharacters | typeof SpecialCharactersText | typeof SpecialCharactersArrows | typeof SpecialCharactersEssentials | typeof SpecialCharactersLatin | typeof SpecialCharactersCurrency | typeof SpecialCharactersMathematical | typeof CodeBlock | typeof RemoveFormat | typeof HorizontalLine | typeof PageBreak | typeof HtmlEmbed | typeof GeneralHtmlSupport | typeof HtmlComment | typeof FindAndReplace | typeof SourceEditing | typeof Style | typeof TextPartLanguage | typeof Mention | typeof ShowBlocks | typeof Bookmark | typeof Emoji | typeof RexLink | typeof RexImage | typeof PictureEditing | typeof ImageResizeButtons | typeof ImageResizeHandles)[];
    static defaultConfig: {
        toolbar: {
            items: string[];
        };
        image: {
            toolbar: string[];
        };
        table: {
            contentToolbar: string[];
        };
        language: string;
    };
}
