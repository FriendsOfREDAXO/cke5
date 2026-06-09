(function () {
  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  window.CKE5_NATIVE_PLUGINS.RedaxoMediaImage = function createRedaxoMediaImage(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {
    };

    return class RedaxoMediaImage extends BasePlugin {
      static get pluginName() {
        return 'RedaxoMediaImage';
      }

      init() {
        if (!this.editor || !this.editor.ui || !this.editor.ui.componentFactory || !cke || typeof cke.ButtonView !== 'function') {
          return;
        }

        var factory = this.editor.ui.componentFactory;
        if (typeof factory.has === 'function' && factory.has('redaxoMedia')) {
          return;
        }

        factory.add('redaxoMedia', (locale) => {
          var button = new cke.ButtonView(locale);
          button.set({
            label: 'Bild aus Medienpool',
            icon: "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M1.5 3h17v14h-17V3zm1.6 1.6v10.8h13.8V4.6H3.1zm1.5 8.8h10.8l-2.8-3.2-2.1 2.3-1.8-1.9-4.1 2.8zm2.3-5.8a1.7 1.7 0 110 3.4 1.7 1.7 0 010-3.4z'/></svg>",
            withText: false,
            tooltip: true
          });
          button.extendTemplate({
            attributes: {
              class: ['ck-redaxo-media-button']
            }
          });
          button.on('execute', () => {
            if (typeof window.openREXMedia !== 'function' || typeof window.jQuery !== 'function') {
              return;
            }

            var imageConfig = this.editor.config.get('image') || {};
            var mediaTypes = typeof imageConfig.rexmedia_types === 'string' && imageConfig.rexmedia_types !== '' ? imageConfig.rexmedia_types : 'jpg,jpeg,png,gif,bmp,tiff,svg,webp,heic,heif';
            var query = '&args[types]=' + mediaTypes;
            if (typeof imageConfig.rexmedia_category !== 'undefined') {
              query += '&rex_file_category=' + imageConfig.rexmedia_category;
            }

            var popup = window.openREXMedia('cke5_mediaimage', query);
            var mediaPath = '/media/';
            if (typeof imageConfig.rexmedia_manager_type === 'string' && imageConfig.rexmedia_manager_type !== '') {
              mediaPath = '/index.php?rex_media_type=' + imageConfig.rexmedia_manager_type + '&rex_media_file=';
            } else if (typeof imageConfig.rexmedia_path === 'string' && imageConfig.rexmedia_path !== '') {
              mediaPath = imageConfig.rexmedia_path;
            }

            window.jQuery(popup).off('rex:selectMedia.cke5').on('rex:selectMedia.cke5', (event, filename) => {
              event.preventDefault();
              if (popup && typeof popup.close === 'function') {
                popup.close();
              }

              var source = mediaPath + filename;
              var selectedElement = this.editor.model.document.selection.getSelectedElement();
              var imageUtils = this.editor.plugins && this.editor.plugins.has('ImageUtils') ? this.editor.plugins.get('ImageUtils') : null;
              if (imageUtils && typeof imageUtils.isImage === 'function' && imageUtils.isImage(selectedElement) && this.editor.commands.get('replaceImageSource')) {
                this.editor.execute('replaceImageSource', { source: source });
              } else {
                this.editor.execute('insertImage', { source: source });
              }
            });
          });
          return button;
        });
      }
    };
  };
})();
