(function () {
  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  window.CKE5_NATIVE_PLUGINS.RedaxoMinimapToggle = function createRedaxoMinimapToggle(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {
    };
    var BaseCommand = cke && typeof cke.Command === 'function' ? cke.Command : class {
      constructor(editor) {
        this.editor = editor;
        this.isEnabled = true;
        this.value = true;
      }
    };

    class RedaxoMinimapCommand extends BaseCommand {
      constructor(editor) {
        super(editor);
        if (typeof this.set === 'function') {
          this.set('isEnabled', true);
          this.set('value', true);
        } else {
          this.isEnabled = true;
          this.value = true;
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

    return class RedaxoMinimapToggle extends BasePlugin {
      static get pluginName() {
        return 'RedaxoMinimapToggle';
      }

      init() {
        if (!this.editor || !this.editor.ui || !this.editor.ui.componentFactory || !cke || typeof cke.ButtonView !== 'function') {
          return;
        }

        var factory = this.editor.ui.componentFactory;

        if (!this.editor.commands.get('toggleRedaxoMinimap')) {
          this.editor.commands.add('toggleRedaxoMinimap', new RedaxoMinimapCommand(this.editor));
        }

        var command = this.editor.commands.get('toggleRedaxoMinimap');
        var configuredDefault = this.editor.config.get('redaxoMinimapEnabled');
        var isDefaultEnabled = configuredDefault === true || configuredDefault === 1 || configuredDefault === '1' || configuredDefault === 'true';

        var resolveMinimapContainer = function () {
          var minimapConfig = this.editor.config.get('minimap');
          var minimapContainer = minimapConfig && minimapConfig.container ? minimapConfig.container : null;
          if (typeof minimapContainer === 'string') {
            minimapContainer = document.querySelector(minimapContainer);
          }
          if (!minimapContainer && this.editor && this.editor.sourceElement && this.editor.sourceElement.id) {
            minimapContainer = document.getElementById(this.editor.sourceElement.id + '__minimap');
          }
          return minimapContainer || null;
        }.bind(this);

        if (command) {
          if (typeof command.set === 'function') {
            command.set('isEnabled', true);
            command.set('value', isDefaultEnabled);
          } else {
            command.isEnabled = true;
            command.value = isDefaultEnabled;
          }
        }

        var applyVisibility = function () {
          var minimapContainer = resolveMinimapContainer();
          if (!minimapContainer) {
            return;
          }
          var minimapWrapper = minimapContainer.parentElement;
          var hasWrapper = minimapWrapper && minimapWrapper.classList;
          if (command && command.value === false) {
            minimapContainer.classList.add('is-hidden');
            if (hasWrapper) {
              minimapWrapper.classList.add('is-minimap-hidden');
            }
          } else {
            minimapContainer.classList.remove('is-hidden');
            if (hasWrapper) {
              minimapWrapper.classList.remove('is-minimap-hidden');
            }
          }

          if (command) {
            if (typeof command.set === 'function') {
              command.set('isEnabled', true);
            } else {
              command.isEnabled = true;
            }
          }
        };

        applyVisibility();

        if (command && typeof command.on === 'function') {
          command.on('change:value', applyVisibility);
        }

        var hasFactoryItem = typeof factory.has === 'function' ? factory.has('redaxoMinimapToggle') : typeof factory.names === 'function' ? Array.from(factory.names()).includes('redaxoMinimapToggle') : false;
        if (!hasFactoryItem) {
          factory.add('redaxoMinimapToggle', (locale) => {
            var button = new cke.ButtonView(locale);
            button.set({
              label: 'Minimap',
              icon: "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><rect x='2' y='3' width='16' height='14' rx='1.5'/><rect x='5' y='6' width='7' height='1.4' fill='#fff'/><rect x='5' y='9' width='6' height='1.4' fill='#fff'/><rect x='5' y='12' width='5' height='1.4' fill='#fff'/><rect x='13.5' y='6' width='2.5' height='8' fill='#fff'/></svg>",
              withText: false,
              tooltip: true,
              isToggleable: true
            });
            if (typeof button.bind === 'function') {
              button.bind('isOn').to(command, 'value');
              button.bind('isEnabled').to(command, 'isEnabled');
            }
            button.on('execute', () => {
              this.editor.execute('toggleRedaxoMinimap');
            });
            return button;
          });
        }
      }
    };
  };
})();
