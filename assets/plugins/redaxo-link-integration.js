(function () {
  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  window.CKE5_NATIVE_PLUGINS.RedaxoLinkIntegration = function createRedaxoLinkIntegration(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {
    };

    return class RedaxoLinkIntegration extends BasePlugin {
      static get pluginName() {
        return 'RedaxoLinkIntegration';
      }

      init() {
        if (typeof window.cke5_enhance_link_form === 'function') {
          window.cke5_enhance_link_form(this.editor);
        }
        if (typeof window.cke5_prefer_image_toolbar_over_link_form === 'function') {
          window.cke5_prefer_image_toolbar_over_link_form(this.editor);
        }
      }
    };
  };
})();
