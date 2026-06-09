(function () {
  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  window.CKE5_NATIVE_PLUGINS.RedaxoPastePlainTextToggle = function createRedaxoPastePlainTextToggle(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {
    };
    var BaseCommand = cke && typeof cke.Command === 'function' ? cke.Command : class {
      constructor(editor) {
        this.editor = editor;
        this.isEnabled = true;
        this.value = false;
      }
    };

    class RedaxoPastePlainTextCommand extends BaseCommand {
      constructor(editor) {
        super(editor);
        if (typeof this.set === 'function') {
          this.set('isEnabled', true);
          this.set('value', false);
        } else {
          this.isEnabled = true;
          this.value = false;
        }
      }

      execute() {
        var nextValue = this.value !== true;
        if (typeof this.set === 'function') {
          this.set('value', nextValue);
        } else {
          this.value = nextValue;
        }
      }

      refresh() {
        if (typeof this.set === 'function') {
          this.set('isEnabled', true);
        } else {
          this.isEnabled = true;
        }
      }
    }

    return class RedaxoPastePlainTextToggle extends BasePlugin {
      static get pluginName() {
        return 'RedaxoPastePlainTextToggle';
      }

      init() {
        if (!this.editor || !this.editor.ui || !this.editor.ui.componentFactory || !cke || typeof cke.ButtonView !== 'function') {
          return;
        }

        var factory = this.editor.ui.componentFactory;
        if (!this.editor.commands.get('pasteAsPlainText')) {
          this.editor.commands.add('pasteAsPlainText', new RedaxoPastePlainTextCommand(this.editor));
        }

        var command = this.editor.commands.get('pasteAsPlainText');
        var configuredDefault = this.editor.config.get('pastePlainTextDefault');
        var isDefaultEnabled = configuredDefault === true || configuredDefault === 1 || configuredDefault === '1' || configuredDefault === 'true';
        if (command) {
          try {
            if (typeof command.set === 'function') {
              command.set('value', isDefaultEnabled);
            } else {
              command.value = isDefaultEnabled;
            }
          } catch (error) {
            console.warn('cke5 pastePlainText default state could not be applied', error);
          }
        }

        var hasFactoryItem = typeof factory.has === 'function' ? factory.has('pastePlainText') : typeof factory.names === 'function' ? Array.from(factory.names()).includes('pastePlainText') : false;
        if (!hasFactoryItem) {
          factory.add('pastePlainText', (locale) => {
            var button = new cke.ButtonView(locale);
            button.set({
              label: 'Unformatiert einfügen',
              icon: "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M5 2.5h7l3 3V17.5H5V2.5zm1.4 1.4v12.2h7.2V6.9H11V3.9H6.4zm5.8 0v1.6h1.6L12.2 3.9z'/><path d='M7.1 8.4h5.8v1.3H7.1V8.4zm0 2.5h5.8v1.3H7.1v-1.3zm0 2.5h3.8v1.3H7.1v-1.3z'/><path d='M2.2 6.1h1.5v2.2h2.1v1.4H3.7v2.2H2.2V9.7H0V8.3h2.2V6.1z'/></svg>",
              withText: false,
              tooltip: true,
              isToggleable: true
            });
            if (typeof button.bind === 'function') {
              button.bind('isOn').to(command, 'value');
              button.bind('isEnabled').to(command, 'isEnabled');
            }
            button.on('execute', () => {
              this.editor.execute('pasteAsPlainText');
            });
            return button;
          });
        }

        if (this.editor.plugins && this.editor.plugins.has('ClipboardPipeline')) {
          var clipboardPipeline = this.editor.plugins.get('ClipboardPipeline');
          clipboardPipeline.on('inputTransformation', (event, data) => {
            if (!command || command.value !== true || !data || !data.dataTransfer || typeof data.dataTransfer.getData !== 'function') {
              return;
            }
            var plainText = data.dataTransfer.getData('text/plain');
            if (typeof plainText !== 'string' || plainText === '') {
              return;
            }
            if (!this.editor.data || !this.editor.data.htmlProcessor || typeof this.editor.data.htmlProcessor.toView !== 'function') {
              return;
            }
            var safeHtml = plainText
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/\r\n|\r|\n/g, '<br>');
            data.content = this.editor.data.htmlProcessor.toView(safeHtml);
          }, { priority: 'highest' });
        }
      }
    };
  };
})();
