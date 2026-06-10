(function () {
  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  function escapeHtml(value) {
    var str = typeof value === 'string' ? value : '';
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function normalizeStyles(styles) {
    if (!Array.isArray(styles)) {
      return [];
    }

    var normalized = [];
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i];
      if (!item || typeof item !== 'object') {
        continue;
      }

      var id = typeof item.id === 'string' ? item.id.trim() : '';
      var label = typeof item.label === 'string' ? item.label.trim() : '';
      var cssClass = typeof item.class === 'string' ? item.class.trim() : '';

      if (id === '') {
        id = 'style-' + (i + 1);
      }
      if (label === '') {
        label = id;
      }

      normalized.push({
        id: id,
        label: label,
        class: cssClass
      });
    }

    return normalized;
  }

  function resolveWidgetConfig(editor) {
    var defaults = {
      preset: 'bootstrap5',
      presets: {
        uikit3: {
          label: 'UIkit 3',
          styles: [
            { id: 'clear', label: 'Clear', class: 'uk-clearfix' },
            { id: 'space-sm', label: 'Abstand S', class: 'uk-clearfix uk-margin-small-top' },
            { id: 'space-md', label: 'Abstand M', class: 'uk-clearfix uk-margin-top' }
          ]
        },
        bootstrap5: {
          label: 'Bootstrap 5',
          styles: [
            { id: 'clear', label: 'Clear', class: 'clearfix' },
            { id: 'space-sm', label: 'Abstand S', class: 'clearfix mt-2' },
            { id: 'space-md', label: 'Abstand M', class: 'clearfix mt-4' }
          ]
        },
        tailwind: {
          label: 'Tailwind',
          styles: [
            { id: 'clear', label: 'Clear', class: 'clear-both' },
            { id: 'space-sm', label: 'Abstand S', class: 'clear-both mt-2' },
            { id: 'space-md', label: 'Abstand M', class: 'clear-both mt-4' }
          ]
        }
      }
    };

    var raw = editor.config.get('redaxoClearWidget');
    if (!raw || typeof raw !== 'object') {
      return {
        preset: defaults.preset,
        presetLabel: defaults.presets[defaults.preset].label,
        styles: normalizeStyles(defaults.presets[defaults.preset].styles)
      };
    }

    var preset = typeof raw.preset === 'string' && raw.preset.trim() !== '' ? raw.preset.trim() : defaults.preset;
    var presets = raw.presets && typeof raw.presets === 'object' ? raw.presets : defaults.presets;
    var presetEntry = presets[preset] && typeof presets[preset] === 'object' ? presets[preset] : defaults.presets[defaults.preset];

    var presetLabel = typeof presetEntry.label === 'string' && presetEntry.label.trim() !== ''
      ? presetEntry.label.trim()
      : preset;

    var styles = normalizeStyles(raw.styles);
    if (styles.length === 0) {
      styles = normalizeStyles(presetEntry.styles);
    }
    if (styles.length === 0) {
      styles = normalizeStyles(defaults.presets[defaults.preset].styles);
    }

    return {
      preset: preset,
      presetLabel: presetLabel,
      styles: styles
    };
  }

  function getStyleById(styles, styleId) {
    for (var i = 0; i < styles.length; i++) {
      if (styles[i].id === styleId) {
        return styles[i];
      }
    }
    return null;
  }

  function createFormView(cke, locale) {
    if (!cke || typeof cke.View !== 'function') {
      return null;
    }

    var view = new cke.View(locale);
    view.setTemplate({
      tag: 'div',
      attributes: {
        class: ['ck', 'ck-reset_all', 'cke5-clear-dialog-form']
      }
    });

    return view;
  }

  function renderForm(formView, config, selectedStyleId) {
    if (!formView || !formView.element) {
      return;
    }

    var styleOptions = '';
    for (var i = 0; i < config.styles.length; i++) {
      var style = config.styles[i];
      var selected = style.id === selectedStyleId ? ' selected="selected"' : '';
      styleOptions += '<option value="' + escapeHtml(style.id) + '"' + selected + '>' + escapeHtml(style.label) + '</option>';
    }

    formView.element.innerHTML = '' +
      '<div class="cke5-clear-dialog-form__intro">Waehle einen Clear-/Spacing-Stil. Die Ausgabe bleibt im Frontend minimal, im Editor jedoch als Widget sichtbar.</div>' +
      '<div class="cke5-clear-dialog-form__group">' +
      '<label for="redaxo-clear-widget-style">Stil-Preset (' + escapeHtml(config.presetLabel) + ')</label>' +
      '<select id="redaxo-clear-widget-style" class="ck ck-input">' + styleOptions + '</select>' +
      '</div>';
  }

  function collectValues(formView, config, fallbackStyleId) {
    var styleId = fallbackStyleId;
    if (formView && formView.element) {
      var styleSelect = formView.element.querySelector('#redaxo-clear-widget-style');
      if (styleSelect && typeof styleSelect.value === 'string' && styleSelect.value !== '') {
        styleId = styleSelect.value;
      }
    }

    var style = getStyleById(config.styles, styleId) || config.styles[0] || { id: 'clear', label: 'Clear', class: '' };

    return {
      styleId: style.id,
      styleLabel: style.label,
      cssClass: style.class
    };
  }

  function showDialog(editor, cke, locale, config, selectedStyleId, onSubmit) {
    var dialogPlugin = editor.plugins && editor.plugins.has('Dialog') ? editor.plugins.get('Dialog') : null;
    if (!dialogPlugin) {
      onSubmit(collectValues(null, config, selectedStyleId));
      return;
    }

    var formView = createFormView(cke, locale);
    if (!formView) {
      onSubmit(collectValues(null, config, selectedStyleId));
      return;
    }

    dialogPlugin.show({
      id: 'redaxoClearWidgetDialog',
      isModal: true,
      title: 'Clear-Widget einfuegen',
      content: formView,
      onShow: function () {
        renderForm(formView, config, selectedStyleId);
      },
      actionButtons: [
        {
          label: 'Abbrechen',
          withText: true,
          onExecute: function () {
            dialogPlugin.hide();
          }
        },
        {
          label: 'Einfuegen',
          withText: true,
          onExecute: function () {
            onSubmit(collectValues(formView, config, selectedStyleId));
            dialogPlugin.hide();
          }
        }
      ]
    });
  }

  window.CKE5_NATIVE_PLUGINS.RedaxoClearWidget = function createRedaxoClearWidget(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {};
    var BaseCommand = cke && typeof cke.Command === 'function' ? cke.Command : class {
      constructor(editor) {
        this.editor = editor;
        this.isEnabled = true;
      }
    };

    class InsertRedaxoClearWidgetCommand extends BaseCommand {
      execute(options) {
        var attrs = options && typeof options === 'object' ? options : {};
        this.editor.model.change((writer) => {
          var widget = writer.createElement('redaxoClearWidget', {
            styleId: typeof attrs.styleId === 'string' ? attrs.styleId : 'clear',
            styleLabel: typeof attrs.styleLabel === 'string' ? attrs.styleLabel : 'Clear',
            cssClass: typeof attrs.cssClass === 'string' ? attrs.cssClass : ''
          });

          this.editor.model.insertContent(widget, this.editor.model.document.selection);
        });
      }

      refresh() {
        var model = this.editor.model;
        var selection = model.document.selection;
        var allowedParent = model.schema.findAllowedParent(selection.getFirstPosition(), 'redaxoClearWidget');
        if (typeof this.set === 'function') {
          this.set('isEnabled', !!allowedParent);
        } else {
          this.isEnabled = !!allowedParent;
        }
      }
    }

    return class RedaxoClearWidget extends BasePlugin {
      static get pluginName() {
        return 'RedaxoClearWidget';
      }

      static get requires() {
        return cke && typeof cke.Widget === 'function' ? [cke.Widget] : [];
      }

      init() {
        if (!this.editor || !cke) {
          return;
        }

        this._defineSchema();
        this._defineConverters();

        if (!this.editor.commands.get('insertRedaxoClearWidget')) {
          this.editor.commands.add('insertRedaxoClearWidget', new InsertRedaxoClearWidgetCommand(this.editor));
        }

        this._defineButton();
      }

      _defineSchema() {
        var schema = this.editor.model.schema;

        schema.register('redaxoClearWidget', {
          isObject: true,
          allowWhere: '$block',
          allowAttributes: ['styleId', 'styleLabel', 'cssClass']
        });
      }

      _defineConverters() {
        var editor = this.editor;
        var conversion = editor.conversion;

        conversion.for('upcast').elementToElement({
          view: {
            name: 'div',
            classes: 'redaxo-clear-widget'
          },
          model: function (viewElement, conversionApi) {
            var writer = conversionApi.writer;
            return writer.createElement('redaxoClearWidget', {
              styleId: viewElement.getAttribute('data-redaxo-clear-style-id') || 'clear',
              styleLabel: viewElement.getAttribute('data-redaxo-clear-style-label') || 'Clear',
              cssClass: viewElement.getAttribute('data-redaxo-clear-class') || ''
            });
          }
        });

        conversion.for('dataDowncast').elementToElement({
          model: 'redaxoClearWidget',
          view: function (modelElement, conversionApi) {
            var writer = conversionApi.writer;
            return createClearElement(writer, modelElement, false);
          }
        });

        conversion.for('editingDowncast').elementToElement({
          model: 'redaxoClearWidget',
          view: function (modelElement, conversionApi) {
            var writer = conversionApi.writer;
            var element = createClearElement(writer, modelElement, true);

            if (typeof cke.toWidget === 'function') {
              return cke.toWidget(element, writer, {
                label: 'REDAXO Clear Widget'
              });
            }

            return element;
          }
        });

        function createClearElement(writer, modelElement, isEditing) {
          var styleId = modelElement.getAttribute('styleId') || 'clear';
          var styleLabel = modelElement.getAttribute('styleLabel') || 'Clear';
          var cssClass = modelElement.getAttribute('cssClass') || '';

          var classes = ['redaxo-clear-widget'];
          if (isEditing) {
            classes.push('redaxo-clear-widget--editing');
          }

          var attributes = {
            class: classes.join(' '),
            'data-redaxo-clear-style-id': styleId,
            'data-redaxo-clear-style-label': styleLabel,
            'data-redaxo-clear-class': cssClass
          };

          var container = writer.createContainerElement('div', attributes);

          if (isEditing) {
            var text = writer.createText('Clear: ' + styleLabel + (cssClass !== '' ? ' (' + cssClass + ')' : ''));
            writer.insert(writer.createPositionAt(container, 0), text);
          }

          return container;
        }
      }

      _defineButton() {
        if (!this.editor.ui || !this.editor.ui.componentFactory || !cke || typeof cke.ButtonView !== 'function') {
          return;
        }

        var factory = this.editor.ui.componentFactory;
        var hasFactoryItem = typeof factory.has === 'function'
          ? factory.has('for_clear')
          : typeof factory.names === 'function'
            ? Array.from(factory.names()).includes('for_clear')
            : false;

        if (hasFactoryItem) {
          return;
        }

        factory.add('for_clear', (locale) => {
          var button = new cke.ButtonView(locale);
          button.set({
            label: 'Clear Widget',
            icon: "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M2 4h16v2H2V4zm0 5h9v2H2V9zm0 5h16v2H2v-2zm12.5-5 4.5 3-4.5 3V9z'/></svg>",
            tooltip: true
          });

          button.on('execute', () => {
            var config = resolveWidgetConfig(this.editor);
            var selectedElement = this.editor.model.document.selection.getSelectedElement();
            var selectedStyleId = selectedElement && selectedElement.name === 'redaxoClearWidget'
              ? (selectedElement.getAttribute('styleId') || 'clear')
              : (config.styles[0] ? config.styles[0].id : 'clear');

            showDialog(this.editor, cke, locale, config, selectedStyleId, (values) => {
              this.editor.execute('insertRedaxoClearWidget', values);
              this.editor.editing.view.focus();
            });
          });

          return button;
        });
      }
    };
  };
})();
