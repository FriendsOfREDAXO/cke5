import type { PasteAsPlainText, PasteAsPlainTextUI, PasteAsPlainTextCommand } from './index';
declare module '@ckeditor/ckeditor5-core' {
    interface PluginsMap {
        [PasteAsPlainText.pluginName]: PasteAsPlainText;
        [PasteAsPlainTextUI.pluginName]: PasteAsPlainTextUI;
    }
    interface CommandsMap {
        pasteAsPlainText: PasteAsPlainTextCommand;
    }
}
