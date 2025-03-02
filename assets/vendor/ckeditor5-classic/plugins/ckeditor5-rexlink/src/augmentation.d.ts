import type { RexLink } from './index';
declare module '@ckeditor/ckeditor5-core' {
    interface PluginsMap {
        [RexLink.pluginName]: RexLink;
    }
}
