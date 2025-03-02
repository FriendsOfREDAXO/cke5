/**
 * @module PasteAsPlainText/PasteAsPlainTextCommand
 */
import { Command } from 'ckeditor5/src/core';
/**
 * Handles the toggled state of the button.
 */
export default class PasteAsPlainTextCommand extends Command {
    value: boolean;
    /**
     * @inheritDoc
     */
    refresh(): void;
    /**
     * @inheritDoc
     */
    execute(): void;
}
