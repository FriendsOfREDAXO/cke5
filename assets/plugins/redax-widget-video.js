(function () {
  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  function normalizeMediaPath(path, fallbackBase) {
    var base = typeof fallbackBase === 'string' && fallbackBase !== '' ? fallbackBase : '/media/';
    if (typeof path !== 'string' || path === '') {
      return base;
    }
    if (/^(https?:)?\/\//i.test(path) || path.charAt(0) === '/') {
      return path;
    }
    return base.replace(/\/$/, '') + '/' + path.replace(/^\//, '');
  }

  function getVideoDefaults(editor) {
    var defaults = editor.config.get('redaxoVideo.defaults') || {};
    var positionStyles = editor.config.get('redaxoVideo.styles') || [];
    var widthStyles = editor.config.get('redaxoVideo.widthStyles') || [];

    var firstPositionClass = Array.isArray(positionStyles) && positionStyles.length > 0 && positionStyles[0] && positionStyles[0].class
      ? String(positionStyles[0].class)
      : 'video--pos-default';
    var firstWidthClass = Array.isArray(widthStyles) && widthStyles.length > 0 && widthStyles[0] && widthStyles[0].class
      ? String(widthStyles[0].class)
      : 'video--w-auto';

    var mediaPath = editor.config.get('image.rexmedia_path') || '/media/';

    return {
      src: '/media/demo-video.mp4',
      positionClass: firstPositionClass,
      widthClass: firstWidthClass,
      controls: defaults.controls !== false,
      autoplay: defaults.autoplay === true,
      muted: defaults.muted === true,
      loop: defaults.loop === true,
      playsinline: defaults.playsinline !== false,
      mediaPath: normalizeMediaPath(mediaPath, '/media/')
    };
  }

  function readSelectedWidgetData(editor) {
    var selection = editor.model.document.selection;
    var selectedElement = selection ? selection.getSelectedElement() : null;
    if (!selectedElement || selectedElement.name !== 'redaxoVideoWidgetTest') {
      return null;
    }

    return {
      src: selectedElement.getAttribute('src') || '',
      positionClass: selectedElement.getAttribute('positionClass') || 'video--pos-default',
      widthClass: selectedElement.getAttribute('widthClass') || 'video--w-auto',
      controls: !!selectedElement.getAttribute('controls'),
      autoplay: !!selectedElement.getAttribute('autoplay'),
      muted: !!selectedElement.getAttribute('muted'),
      loop: !!selectedElement.getAttribute('loop'),
      playsinline: !!selectedElement.getAttribute('playsinline')
    };
  }

  function createFormView(cke, locale) {
    if (!cke || typeof cke.View !== 'function') {
      return null;
    }

    var view = new cke.View(locale);
    view.setTemplate({
      tag: 'div',
      attributes: {
        class: ['ck', 'ck-reset_all', 'cke5-video-dialog-form']
      }
    });

    return view;
  }

  function renderForm(formView, state) {
    if (!formView || !formView.element) {
      return;
    }

    var mediaPoolInputId = 'REX_MEDIA_cke5_video_widget_test_' + Math.random().toString(16).slice(2);
    formView.element.innerHTML = '' +
      '<div class="cke5-video-dialog-form__intro">Videoquelle, Layout und Verhalten für das Widget festlegen.</div>' +
      '<div class="cke5-video-dialog-form__group">' +
      '<label for="redaxo-video-widget-test-src">Video-Quelle</label>' +
      '<input id="redaxo-video-widget-test-src" class="ck ck-input" type="text" value="' + (state.src || '') + '" placeholder="/media/video.mp4 oder https://...">' +
      '</div>' +
      '<div class="cke5-video-dialog-form__group cke5-video-dialog-form__group--button">' +
      '<input id="' + mediaPoolInputId + '" type="text" value="" class="cke5-video-dialog-form__hidden-input" aria-hidden="true">' +
      '<button type="button" id="redaxo-video-widget-test-mediapool" class="ck ck-button cke5-video-dialog-form__button">Aus Medienpool wählen</button>' +
      '</div>' +
      '<div class="cke5-video-dialog-form__row">' +
      '<div class="cke5-video-dialog-form__group">' +
      '<label for="redaxo-video-widget-test-position">Position</label>' +
      '<select id="redaxo-video-widget-test-position" class="ck ck-input">' +
      '<option value="video--pos-default">Standard</option>' +
      '<option value="video--pos-left">Links</option>' +
      '<option value="video--pos-center">Zentriert</option>' +
      '<option value="video--pos-right">Rechts</option>' +
      '</select>' +
      '</div>' +
      '<div class="cke5-video-dialog-form__group">' +
      '<label for="redaxo-video-widget-test-width">Breite</label>' +
      '<select id="redaxo-video-widget-test-width" class="ck ck-input">' +
      '<option value="video--w-auto">Auto</option>' +
      '<option value="video--w-50">50%</option>' +
      '<option value="video--w-75">75%</option>' +
      '<option value="video--w-100">100%</option>' +
      '</select>' +
      '</div>' +
      '</div>' +
      '<div class="cke5-video-dialog-form__subtitle">Optionen</div>' +
      '<div class="cke5-video-dialog-form__checks">' +
      '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="redaxo-video-widget-test-controls"> <span>Steuerelemente anzeigen</span></label>' +
      '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="redaxo-video-widget-test-autoplay"> <span>Autoplay</span></label>' +
      '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="redaxo-video-widget-test-muted"> <span>Muted</span></label>' +
      '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="redaxo-video-widget-test-loop"> <span>Loop</span></label>' +
      '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="redaxo-video-widget-test-playsinline"> <span>Playsinline</span></label>' +
      '</div>' +
      '<div class="cke5-video-dialog-form__group">' +
      '<label class="cke5-video-dialog-form__hint">Beim Aktivieren von Autoplay wird Muted automatisch gesetzt.</label>' +
      '</div>';

    var srcInput = formView.element.querySelector('#redaxo-video-widget-test-src');
    var positionInput = formView.element.querySelector('#redaxo-video-widget-test-position');
    var widthInput = formView.element.querySelector('#redaxo-video-widget-test-width');
    var controlsInput = formView.element.querySelector('#redaxo-video-widget-test-controls');
    var autoplayInput = formView.element.querySelector('#redaxo-video-widget-test-autoplay');
    var mutedInput = formView.element.querySelector('#redaxo-video-widget-test-muted');
    var loopInput = formView.element.querySelector('#redaxo-video-widget-test-loop');
    var playsinlineInput = formView.element.querySelector('#redaxo-video-widget-test-playsinline');
    var mediaPoolButton = formView.element.querySelector('#redaxo-video-widget-test-mediapool');
    var mediaPoolInput = formView.element.querySelector('#' + mediaPoolInputId);

    if (positionInput) {
      positionInput.value = state.positionClass || 'video--pos-default';
    }
    if (widthInput) {
      widthInput.value = state.widthClass || 'video--w-auto';
    }
    if (controlsInput) {
      controlsInput.checked = state.controls === true;
    }
    if (autoplayInput) {
      autoplayInput.checked = state.autoplay === true;
    }
    if (mutedInput) {
      mutedInput.checked = state.muted === true;
    }
    if (loopInput) {
      loopInput.checked = state.loop === true;
    }
    if (playsinlineInput) {
      playsinlineInput.checked = state.playsinline !== false;
    }

    if (autoplayInput && mutedInput) {
      autoplayInput.addEventListener('change', function () {
        if (autoplayInput.checked) {
          mutedInput.checked = true;
        }
      });
    }

    function applySelectedFilename(filename) {
      if (!srcInput) {
        return;
      }
      var selected = typeof filename === 'string' ? filename.trim() : '';
      if (selected === '') {
        return;
      }
      mediaPoolInput.value = selected;
      srcInput.value = normalizeMediaPath(selected, state.mediaPath || '/media/');
    }

    if (mediaPoolButton && mediaPoolInput) {
      var mediaSelectEvent = 'rex:selectMedia.redaxoVideoWidgetTest';
      if (typeof window.jQuery === 'function') {
        window.jQuery(window).off(mediaSelectEvent).on(mediaSelectEvent, function (event, filename) {
          if (event && typeof event.preventDefault === 'function') {
            event.preventDefault();
          }
          applySelectedFilename(filename);
        });
      }

      mediaPoolButton.addEventListener('click', function () {
        if (typeof window.openMediaPool === 'function') {
          var previousValue = mediaPoolInput.value || '';
          try {
            window.openMediaPool(mediaPoolInputId);

            var attempts = 0;
            var interval = window.setInterval(function () {
              attempts += 1;
              var currentValue = mediaPoolInput.value || '';
              if (currentValue !== '' && currentValue !== previousValue) {
                window.clearInterval(interval);
                applySelectedFilename(currentValue);
                return;
              }
              if (attempts > 120) {
                window.clearInterval(interval);
              }
            }, 250);
          } catch (error) {
            console.warn('openMediaPool failed for video widget test', error);
          }
        }
      });

      mediaPoolInput.addEventListener('change', function () {
        applySelectedFilename(mediaPoolInput.value || '');
      });
    }
  }

  function collectFormValues(formView, defaults) {
    if (!formView || !formView.element) {
      return defaults;
    }

    var srcInput = formView.element.querySelector('#redaxo-video-widget-test-src');
    var positionInput = formView.element.querySelector('#redaxo-video-widget-test-position');
    var widthInput = formView.element.querySelector('#redaxo-video-widget-test-width');
    var controlsInput = formView.element.querySelector('#redaxo-video-widget-test-controls');
    var autoplayInput = formView.element.querySelector('#redaxo-video-widget-test-autoplay');
    var mutedInput = formView.element.querySelector('#redaxo-video-widget-test-muted');
    var loopInput = formView.element.querySelector('#redaxo-video-widget-test-loop');
    var playsinlineInput = formView.element.querySelector('#redaxo-video-widget-test-playsinline');

    var values = {
      src: srcInput && srcInput.value !== '' ? String(srcInput.value).trim() : defaults.src,
      positionClass: positionInput ? positionInput.value : defaults.positionClass,
      widthClass: widthInput ? widthInput.value : defaults.widthClass,
      controls: controlsInput ? !!controlsInput.checked : defaults.controls,
      autoplay: autoplayInput ? !!autoplayInput.checked : defaults.autoplay,
      muted: mutedInput ? !!mutedInput.checked : defaults.muted,
      loop: loopInput ? !!loopInput.checked : defaults.loop,
      playsinline: playsinlineInput ? !!playsinlineInput.checked : defaults.playsinline
    };

    if (values.autoplay && !values.muted) {
      values.muted = true;
    }

    return values;
  }

  function showDialog(editor, cke, locale, defaults, onSubmit) {
    var dialogPlugin = editor.plugins && editor.plugins.has('Dialog') ? editor.plugins.get('Dialog') : null;
    if (!dialogPlugin) {
      onSubmit(defaults);
      return;
    }

    var formView = createFormView(cke, locale);
    if (!formView) {
      onSubmit(defaults);
      return;
    }

    dialogPlugin.show({
      id: 'redaxoVideoWidgetTestDialog',
      isModal: true,
      title: 'Video Widget konfigurieren',
      content: formView,
      onShow: function () {
        renderForm(formView, defaults);
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
          label: 'Einfügen',
          withText: true,
          class: 'cke5-video-dialog-submit',
          onExecute: function () {
            onSubmit(collectFormValues(formView, defaults));
            dialogPlugin.hide();
          }
        }
      ]
    });
  }

  window.CKE5_NATIVE_PLUGINS.RedaxWidgetVideo = function createRedaxWidgetVideo(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {
    };
    var BaseCommand = cke && typeof cke.Command === 'function' ? cke.Command : class {
      constructor(editor) {
        this.editor = editor;
        this.isEnabled = true;
      }
    };

    class InsertRedaxWidgetVideoCommand extends BaseCommand {
      execute(options) {
        var attrs = options && typeof options === 'object' ? options : {};
        var src = typeof attrs.src === 'string' && attrs.src !== '' ? attrs.src : '/media/demo-video.mp4';
        var controls = attrs.controls !== false;
        var muted = attrs.muted === true;
        var autoplay = attrs.autoplay === true;
        var loop = attrs.loop === true;
        var playsinline = attrs.playsinline !== false;

        this.editor.model.change((writer) => {
          var widget = writer.createElement('redaxoVideoWidgetTest', {
            src: src,
            positionClass: typeof attrs.positionClass === 'string' && attrs.positionClass !== '' ? attrs.positionClass : 'video--pos-default',
            widthClass: typeof attrs.widthClass === 'string' && attrs.widthClass !== '' ? attrs.widthClass : 'video--w-auto',
            controls: controls ? '1' : '',
            autoplay: autoplay ? '1' : '',
            muted: muted ? '1' : '',
            loop: loop ? '1' : '',
            playsinline: playsinline ? '1' : ''
          });

          this.editor.model.insertContent(widget, this.editor.model.document.selection);
        });
      }

      refresh() {
        var model = this.editor.model;
        var selection = model.document.selection;
        var allowedParent = model.schema.findAllowedParent(selection.getFirstPosition(), 'redaxoVideoWidgetTest');
        if (typeof this.set === 'function') {
          this.set('isEnabled', !!allowedParent);
        } else {
          this.isEnabled = !!allowedParent;
        }
      }
    }

    return class RedaxWidgetVideo extends BasePlugin {
      static get pluginName() {
        return 'RedaxWidgetVideo';
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

        if (!this.editor.commands.get('insertRedaxWidgetVideo')) {
          this.editor.commands.add('insertRedaxWidgetVideo', new InsertRedaxWidgetVideoCommand(this.editor));
        }

        this._defineButton();
      }

      _defineSchema() {
        var schema = this.editor.model.schema;

        schema.register('redaxoVideoWidgetTest', {
          isObject: true,
          allowWhere: '$block',
          allowAttributes: ['src', 'positionClass', 'widthClass', 'controls', 'autoplay', 'muted', 'loop', 'playsinline']
        });
      }

      _defineConverters() {
        var editor = this.editor;
        var conversion = editor.conversion;

        conversion.for('upcast').elementToElement({
          view: {
            name: 'figure',
            classes: 'redaxo-video-widget-test'
          },
          model: function (viewElement, conversionApi) {
            var writer = conversionApi.writer;
            var src = '';
            var controls = '';
            var autoplay = '';
            var muted = '';
            var loop = '';
            var playsinline = '';
            var positionClass = viewElement.getAttribute('data-redaxo-video-position') || 'video--pos-default';
            var widthClass = viewElement.getAttribute('data-redaxo-video-width') || 'video--w-auto';

            if (viewElement.hasClass('video--pos-left')) {
              positionClass = 'video--pos-left';
            } else if (viewElement.hasClass('video--pos-center')) {
              positionClass = 'video--pos-center';
            } else if (viewElement.hasClass('video--pos-right')) {
              positionClass = 'video--pos-right';
            }

            if (viewElement.hasClass('video--w-50')) {
              widthClass = 'video--w-50';
            } else if (viewElement.hasClass('video--w-75')) {
              widthClass = 'video--w-75';
            } else if (viewElement.hasClass('video--w-100')) {
              widthClass = 'video--w-100';
            }

            var children = Array.from(viewElement.getChildren());
            for (var i = 0; i < children.length; i++) {
              var child = children[i];
              if (!child || typeof child.is !== 'function' || !child.is('element', 'video')) {
                continue;
              }
              src = child.getAttribute('src') || '';
              controls = child.getAttribute('controls') ? '1' : '';
              autoplay = child.getAttribute('autoplay') ? '1' : '';
              muted = child.getAttribute('muted') ? '1' : '';
              loop = child.getAttribute('loop') ? '1' : '';
              playsinline = child.getAttribute('playsinline') ? '1' : '';
              break;
            }

            if (src === '') {
              src = '/media/demo-video.mp4';
            }

            return writer.createElement('redaxoVideoWidgetTest', {
              src: src,
              positionClass: positionClass,
              widthClass: widthClass,
              controls: controls,
              autoplay: autoplay,
              muted: muted,
              loop: loop,
              playsinline: playsinline
            });
          }
        });

        conversion.for('dataDowncast').elementToElement({
          model: 'redaxoVideoWidgetTest',
          view: function (modelElement, conversionApi) {
            var writer = conversionApi.writer;
            return createVideoFigure(writer, modelElement);
          }
        });

        conversion.for('editingDowncast').elementToElement({
          model: 'redaxoVideoWidgetTest',
          view: function (modelElement, conversionApi) {
            var writer = conversionApi.writer;
            var figure = createVideoFigure(writer, modelElement);

            if (typeof cke.toWidget === 'function') {
              return cke.toWidget(figure, writer, {
                label: 'REDAXO Video Widget (Test)'
              });
            }

            return figure;
          }
        });

        function createVideoFigure(writer, modelElement) {
          var positionClass = modelElement.getAttribute('positionClass') || 'video--pos-default';
          var widthClass = modelElement.getAttribute('widthClass') || 'video--w-auto';
          var figureClasses = ['for-video', 'redaxo-video-widget-test'];
          if (typeof positionClass === 'string' && positionClass !== '') {
            figureClasses.push(positionClass);
          }
          if (typeof widthClass === 'string' && widthClass !== '') {
            figureClasses.push(widthClass);
          }

          var figure = writer.createContainerElement('figure', {
            class: figureClasses.join(' '),
            'data-redaxo-video-position': positionClass,
            'data-redaxo-video-width': widthClass
          });

          var videoAttrs = {
            src: modelElement.getAttribute('src') || '/media/demo-video.mp4',
            class: 'redaxo-video-widget-test__video'
          };

          if (modelElement.getAttribute('controls')) {
            videoAttrs.controls = 'controls';
          }
          if (modelElement.getAttribute('autoplay')) {
            videoAttrs.autoplay = 'autoplay';
          }
          if (modelElement.getAttribute('muted')) {
            videoAttrs.muted = 'muted';
          }
          if (modelElement.getAttribute('loop')) {
            videoAttrs.loop = 'loop';
          }
          if (modelElement.getAttribute('playsinline')) {
            videoAttrs.playsinline = 'playsinline';
          }

          var video = writer.createEmptyElement('video', videoAttrs);
          writer.insert(writer.createPositionAt(figure, 0), video);

          return figure;
        }
      }

      _defineButton() {
        if (!this.editor.ui || !this.editor.ui.componentFactory || !cke || typeof cke.ButtonView !== 'function') {
          return;
        }

        var factory = this.editor.ui.componentFactory;
        var hasFactoryItem = typeof factory.has === 'function' ? factory.has('for_video') : typeof factory.names === 'function' ? Array.from(factory.names()).includes('for_video') : false;
        if (hasFactoryItem) {
          return;
        }

        factory.add('for_video', (locale) => {
          var button = new cke.ButtonView(locale);
          button.set({
            label: 'Video Widget (Test)',
            icon: "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M3 4.5h9.5v11H3v-11zM4.5 6v8h6.5V6H4.5zm9 1.2l3.5-2.2v10l-3.5-2.2V7.2z'/></svg>",
            tooltip: true
          });

          button.on('execute', () => {
            var defaults = getVideoDefaults(this.editor);
            var selected = readSelectedWidgetData(this.editor);
            if (selected && selected.src) {
              defaults.src = selected.src;
              defaults.positionClass = selected.positionClass || defaults.positionClass;
              defaults.widthClass = selected.widthClass || defaults.widthClass;
              defaults.controls = selected.controls;
              defaults.autoplay = selected.autoplay;
              defaults.muted = selected.muted;
              defaults.loop = selected.loop;
              defaults.playsinline = selected.playsinline;
            }

            showDialog(this.editor, cke, locale, defaults, (values) => {
              this.editor.execute('insertRedaxWidgetVideo', values);
              this.editor.editing.view.focus();
            });
          });

          return button;
        });
      }
    };
  };
})();
