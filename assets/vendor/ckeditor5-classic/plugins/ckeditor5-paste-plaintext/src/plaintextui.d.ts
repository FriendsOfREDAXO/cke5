/**
 * @module PasteAsPlainText/PasteAsPlainTextUI
 */
import { Plugin } from '@ckeditor/ckeditor5-core';
/**
 * Handles registering the toggleable button in the
 * editor's UI so it can be added to the toolbar, with
 * the name `pastePlainText`.
 */
export default class PasteAsPlainTextUI extends Plugin {
    /**
     * @inheritDoc
     */
    static get pluginName(): 'PasteAsPlainTextUI';
    /**
     * @inheritDoc
     */
    init(): void;
}
