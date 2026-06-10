(function () {
  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  window.CKE5_NATIVE_PLUGINS.RedaxoMarkdownPasteToggle = function createRedaxoMarkdownPasteToggle(context) {
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

    class RedaxoMarkdownPasteCommand extends BaseCommand {
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

    return class RedaxoMarkdownPasteToggle extends BasePlugin {
      static get pluginName() {
        return 'RedaxoMarkdownPasteToggle';
      }

      init() {
        if (!this.editor || !this.editor.ui || !this.editor.ui.componentFactory || !cke || typeof cke.ButtonView !== 'function') {
          return;
        }

        var factory = this.editor.ui.componentFactory;

        if (!this.editor.commands.get('toggleMarkdownPaste')) {
          this.editor.commands.add('toggleMarkdownPaste', new RedaxoMarkdownPasteCommand(this.editor));
        }

        var command = this.editor.commands.get('toggleMarkdownPaste');
        var configuredDefault = this.editor.config.get('redaxoMarkdownPasteEnabled');
        var isDefaultEnabled = configuredDefault === true || configuredDefault === 1 || configuredDefault === '1' || configuredDefault === 'true';
        if (command) {
          try {
            if (typeof command.set === 'function') {
              command.set('value', isDefaultEnabled);
            } else {
              command.value = isDefaultEnabled;
            }
          } catch (error) {
            console.warn('cke5 markdownPaste default state could not be applied', error);
          }
        }

        var hasFactoryItem = typeof factory.has === 'function' ? factory.has('redaxoMarkdownPasteToggle') : typeof factory.names === 'function' ? Array.from(factory.names()).includes('redaxoMarkdownPasteToggle') : false;
        if (!hasFactoryItem) {
          factory.add('redaxoMarkdownPasteToggle', (locale) => {
            var button = new cke.ButtonView(locale);
            button.set({
              label: 'Markdown einfügen',
              icon: "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M2 4h4l2.5 4L11 4h4v12h-3V9.2l-2.1 3.2h-2.8L5 9.2V16H2V4z'/><path d='M15.5 11h2.5l-3.2 3.5L18 18h-2.4l-2.1-2.4 2-2.3z'/></svg>",
              withText: false,
              tooltip: true,
              isToggleable: true
            });
            if (typeof button.bind === 'function') {
              button.bind('isOn').to(command, 'value');
              button.bind('isEnabled').to(command, 'isEnabled');
            }
            button.on('execute', () => {
              this.editor.execute('toggleMarkdownPaste');
            });
            return button;
          });
        }

        if (this.editor.plugins && this.editor.plugins.has('ClipboardPipeline')) {
          var clipboardPipeline = this.editor.plugins.get('ClipboardPipeline');
          clipboardPipeline.on('inputTransformation', (event, data) => {
            if (!command || command.value !== false || !data || !data.dataTransfer || typeof data.dataTransfer.getData !== 'function') {
              return;
            }

            var markdownText = data.dataTransfer.getData('text/markdown');
            if (typeof markdownText !== 'string' || markdownText === '') {
              return;
            }

            var plainText = data.dataTransfer.getData('text/plain');
            if (typeof plainText !== 'string' || plainText === '') {
              plainText = markdownText;
            }
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
