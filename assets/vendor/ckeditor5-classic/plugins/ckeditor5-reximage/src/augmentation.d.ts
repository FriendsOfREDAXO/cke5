import type { RexImage } from './index';
declare module '@ckeditor/ckeditor5-core' {
    interface PluginsMap {
        [RexImage.pluginName]: RexImage;
    }
}
