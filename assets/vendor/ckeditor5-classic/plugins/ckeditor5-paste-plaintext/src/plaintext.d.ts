/**
 * @module PasteAsPlainText/PasteAsPlainText
 */
import { Plugin } from '@ckeditor/ckeditor5-core';
import PasteAsPlainTextUI from './plaintextui';
import PasteAsPlainTextCommand from './plaintextcommand';
/**
 * The main plugin class with the logic to use only the
 * plain text from the clipboard input.
 */
export default class PasteAsPlainText extends Plugin {
    static get pluginName(): 'PasteAsPlainText';
    static get requires(): readonly [typeof PasteAsPlainTextUI, typeof PasteAsPlainTextCommand];
    init(): void;
}
