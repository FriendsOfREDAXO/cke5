(function () {
  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/\"/g, '&quot;');
  }

  function normalizeMediaPath(path) {
    if (typeof path !== 'string' || path === '') {
      return '/media/';
    }
    if (/^(https?:)?\/\//i.test(path) || path.charAt(0) === '/') {
      return path;
    }
    return '/media/' + path.replace(/^media\//, '');
  }

  function insertHtml(editor, html) {
    try {
      var viewFragment = editor.data.processor.toView(html);
      var modelFragment = editor.data.toModel(viewFragment);
      editor.model.insertContent(modelFragment, editor.model.document.selection);
    } catch (error) {
      console.error('cke5 video insert failed', error);
    }
  }

  function getVideoDefaults(editor) {
    var defaults = editor.config.get('redaxoVideo.defaults') || {};
    var mapped = {
      controls: defaults.controls !== false,
      autoplay: defaults.autoplay === true,
      muted: defaults.muted === true,
      loop: defaults.loop === true,
      playsinline: defaults.playsinline !== false
    };

    if (mapped.autoplay && !mapped.muted) {
      mapped.muted = true;
    }

    return mapped;
  }

  function getVideoPositionStyles(editor) {
    var styles = editor.config.get('redaxoVideo.styles') || editor.config.get('mediaEmbed.styles') || [];
    if (!Array.isArray(styles) || styles.length === 0) {
      return [{ label: 'Standard', class: 'video--pos-default' }];
    }
    return styles;
  }

  function getVideoWidthStyles(editor) {
    var widths = editor.config.get('redaxoVideo.widthStyles') || [];
    if (!Array.isArray(widths) || widths.length === 0) {
      return [
        { label: 'Auto', class: 'video--w-auto' },
        { label: '50%', class: 'video--w-50' },
        { label: '75%', class: 'video--w-75' },
        { label: '100%', class: 'video--w-100' }
      ];
    }
    return widths;
  }

  function createVideoFormView(cke, locale) {
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

  function renderVideoForm(view, positionStyles, widthStyles, defaults, selectedPositionClass, selectedWidthClass) {
    if (!view || !view.element) {
      return;
    }

    var positionOptions = '';
    if (Array.isArray(positionStyles) && positionStyles.length > 0) {
      positionOptions = positionStyles.map(function (item, index) {
        var label = item && item.label ? String(item.label) : ('Style ' + (index + 1));
        var value = item && item.class ? String(item.class) : '';
        return '<option value="' + escapeHtml(value) + '">' + escapeHtml(label) + '</option>';
      }).join('');
    } else {
      positionOptions = '<option value="video--pos-default">Standard</option>';
    }

    var widthOptions = '';
    if (Array.isArray(widthStyles) && widthStyles.length > 0) {
      widthOptions = widthStyles.map(function (item, index) {
        var label = item && item.label ? String(item.label) : ('Breite ' + (index + 1));
        var value = item && item.class ? String(item.class) : '';
        return '<option value="' + escapeHtml(value) + '">' + escapeHtml(label) + '</option>';
      }).join('');
    } else {
      widthOptions = '<option value="video--w-auto">Auto</option>';
    }

    view.element.innerHTML = '' +
      '<div class="cke5-video-dialog-form__intro">Darstellung und Verhalten des Videos festlegen.</div>' +
      '<div class="cke5-video-dialog-form__group">' +
        '<label for="cke5-video-position-select">Position</label>' +
        '<select id="cke5-video-position-select" class="ck ck-input">' + positionOptions + '</select>' +
      '</div>' +
      '<div class="cke5-video-dialog-form__group">' +
        '<label for="cke5-video-width-select">Breite</label>' +
        '<select id="cke5-video-width-select" class="ck ck-input">' + widthOptions + '</select>' +
      '</div>' +
      '<div class="cke5-video-dialog-form__subtitle">Optionen</div>' +
      '<div class="cke5-video-dialog-form__checks">' +
        '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="cke5-video-opt-controls"> <span>Steuerelemente anzeigen</span></label>' +
        '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="cke5-video-opt-autoplay"> <span>Automatisch starten</span></label>' +
        '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="cke5-video-opt-muted"> <span>Stumm</span></label>' +
        '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="cke5-video-opt-loop"> <span>Schleife</span></label>' +
        '<label class="cke5-video-dialog-form__check"><input type="checkbox" id="cke5-video-opt-playsinline"> <span>Inline auf Mobilgeräten</span></label>' +
      '</div>';

    var controlsEl = view.element.querySelector('#cke5-video-opt-controls');
    var autoplayEl = view.element.querySelector('#cke5-video-opt-autoplay');
    var mutedEl = view.element.querySelector('#cke5-video-opt-muted');
    var loopEl = view.element.querySelector('#cke5-video-opt-loop');
    var playsinlineEl = view.element.querySelector('#cke5-video-opt-playsinline');
    var positionEl = view.element.querySelector('#cke5-video-position-select');
    var widthEl = view.element.querySelector('#cke5-video-width-select');

    if (positionEl && typeof selectedPositionClass === 'string' && selectedPositionClass !== '') {
      positionEl.value = selectedPositionClass;
    }
    if (widthEl && typeof selectedWidthClass === 'string' && selectedWidthClass !== '') {
      widthEl.value = selectedWidthClass;
    }

    if (positionEl && positionEl.value !== (typeof selectedPositionClass === 'string' ? selectedPositionClass : '')) {
      positionEl.setAttribute('data-selected-style', selectedPositionClass || '');
    }
    if (widthEl && widthEl.value !== (typeof selectedWidthClass === 'string' ? selectedWidthClass : '')) {
      widthEl.setAttribute('data-selected-style', selectedWidthClass || '');
    }

    controlsEl.checked = defaults.controls;
    autoplayEl.checked = defaults.autoplay;
    mutedEl.checked = defaults.muted;
    loopEl.checked = defaults.loop;
    playsinlineEl.checked = defaults.playsinline;

    autoplayEl.addEventListener('change', function () {
      if (autoplayEl.checked) {
        mutedEl.checked = true;
      }
    });
  }

  function collectVideoFormValues(view) {
    if (!view || !view.element) {
      return {
        positionClass: 'video--pos-default',
        widthClass: 'video--w-auto',
        options: {
          controls: true,
          autoplay: false,
          muted: false,
          loop: false,
          playsinline: true
        }
      };
    }

    var positionEl = view.element.querySelector('#cke5-video-position-select');
    var widthEl = view.element.querySelector('#cke5-video-width-select');
    var controlsEl = view.element.querySelector('#cke5-video-opt-controls');
    var autoplayEl = view.element.querySelector('#cke5-video-opt-autoplay');
    var mutedEl = view.element.querySelector('#cke5-video-opt-muted');
    var loopEl = view.element.querySelector('#cke5-video-opt-loop');
    var playsinlineEl = view.element.querySelector('#cke5-video-opt-playsinline');

    var options = {
      controls: controlsEl ? !!controlsEl.checked : true,
      autoplay: autoplayEl ? !!autoplayEl.checked : false,
      muted: mutedEl ? !!mutedEl.checked : false,
      loop: loopEl ? !!loopEl.checked : false,
      playsinline: playsinlineEl ? !!playsinlineEl.checked : true
    };

    if (options.autoplay && !options.muted) {
      options.muted = true;
    }

    return {
      positionClass: positionEl ? positionEl.value : 'video--pos-default',
      widthClass: widthEl ? widthEl.value : 'video--w-auto',
      options: options
    };
  }

  function showVideoDialog(editor, cke, locale, formView, positionStyles, widthStyles, defaults, onInsert) {
    var mode = arguments.length > 7 && arguments[7] ? arguments[7] : 'insert';
    var selectedPositionClass = arguments.length > 8 && typeof arguments[8] === 'string' ? arguments[8] : '';
    var selectedWidthClass = arguments.length > 9 && typeof arguments[9] === 'string' ? arguments[9] : '';
    var dialogPlugin = editor.plugins && editor.plugins.has('Dialog') ? editor.plugins.get('Dialog') : null;
    if (!dialogPlugin || !formView) {
      onInsert('', defaults);
      return;
    }

    var t = (locale && typeof locale.t === 'function') ? locale.t : function (label) {
      return label;
    };

    dialogPlugin.show({
      id: 'redaxoMediaVideo',
      title: mode === 'edit' ? t('Video bearbeiten') : t('Video einfügen'),
      isModal: true,
      content: formView,
      onShow: function () {
        renderVideoForm(formView, positionStyles, widthStyles, defaults, selectedPositionClass, selectedWidthClass);
        var selectEl = formView.element ? formView.element.querySelector('#cke5-video-position-select') : null;
        if (selectEl) {
          selectEl.focus();
        }
      },
      actionButtons: [
        {
          label: t('Abbrechen'),
          withText: true,
          onExecute: function () {
            dialogPlugin.hide();
          }
        },
        {
          label: mode === 'edit' ? t('Speichern') : t('Einfügen'),
          withText: true,
          class: 'ck-button-action',
          onExecute: function () {
            var values = collectVideoFormValues(formView);
            onInsert(values.positionClass, values.widthClass, values.options);
            dialogPlugin.hide();
          }
        }
      ]
    });
  }

  function getClosestVideoElements(editor) {
    var editableEl = editor && editor.ui && typeof editor.ui.getEditableElement === 'function' ? editor.ui.getEditableElement() : null;
    if (!editableEl) {
      return null;
    }

    var domSelection = window.getSelection ? window.getSelection() : null;
    var anchor = domSelection && domSelection.anchorNode ? domSelection.anchorNode : null;
    var anchorElement = null;
    if (anchor) {
      anchorElement = anchor.nodeType === 1 ? anchor : anchor.parentElement;
    }

    var videoEl = null;
    if (anchorElement && editableEl.contains(anchorElement)) {
      if (anchorElement.closest) {
        videoEl = anchorElement.closest('video');
      }
      if (!videoEl && anchorElement.querySelector) {
        videoEl = anchorElement.querySelector('video');
      }
    }

    if (!videoEl) {
      videoEl = editableEl.querySelector('.ck-widget_selected video, figure.for-video video');
    }

    if (!videoEl) {
      return null;
    }

    var figureEl = videoEl.closest ? videoEl.closest('figure') : null;
    return { video: videoEl, figure: figureEl };
  }

  function readVideoStyleAttribute(figureEl, attributeName) {
    if (!figureEl || !figureEl.getAttribute) {
      return '';
    }

    return figureEl.getAttribute(attributeName) || '';
  }

  function getSelectedVideoData(editor, positionStyles, widthStyles) {
    var elements = getClosestVideoElements(editor);
    if (!elements || !elements.video) {
      return null;
    }

    var videoEl = elements.video;
    var figureEl = elements.figure;
    var src = videoEl.getAttribute('src') || '';
    if (!src) {
      return null;
    }

    var availablePositionStyles = Array.isArray(positionStyles) ? positionStyles : [];
    var availableWidthStyles = Array.isArray(widthStyles) ? widthStyles : [];
    var positionClass = readVideoStyleAttribute(figureEl, 'data-redaxo-video-position');
    var widthClass = readVideoStyleAttribute(figureEl, 'data-redaxo-video-width');

    if (figureEl && figureEl.classList && availablePositionStyles.length > 0) {
      for (var i = 0; i < availablePositionStyles.length; i++) {
        var cls = availablePositionStyles[i] && availablePositionStyles[i].class ? String(availablePositionStyles[i].class).trim() : '';
        if (cls !== '' && figureEl.classList.contains(cls)) {
          positionClass = cls;
          break;
        }
      }
    }

    if (figureEl && figureEl.classList && availableWidthStyles.length > 0) {
      for (var j = 0; j < availableWidthStyles.length; j++) {
        var widthCls = availableWidthStyles[j] && availableWidthStyles[j].class ? String(availableWidthStyles[j].class).trim() : '';
        if (widthCls !== '' && figureEl.classList.contains(widthCls)) {
          widthClass = widthCls;
          break;
        }
      }
    }

    if (figureEl && figureEl.classList) {
      if (positionClass === '') {
        if (figureEl.classList.contains('video--pos-left')) {
          positionClass = 'video--pos-left';
        } else if (figureEl.classList.contains('video--pos-center')) {
          positionClass = 'video--pos-center';
        } else if (figureEl.classList.contains('video--pos-right')) {
          positionClass = 'video--pos-right';
        } else if (figureEl.classList.contains('video--pos-default')) {
          positionClass = 'video--pos-default';
        }
      }

      if (widthClass === '') {
        if (figureEl.classList.contains('video--w-auto')) {
          widthClass = 'video--w-auto';
        } else if (figureEl.classList.contains('video--w-50')) {
          widthClass = 'video--w-50';
        } else if (figureEl.classList.contains('video--w-75')) {
          widthClass = 'video--w-75';
        } else if (figureEl.classList.contains('video--w-100')) {
          widthClass = 'video--w-100';
        }
      }

      if (positionClass === '') {
        if (figureEl.classList.contains('video--left-50')) {
          positionClass = 'video--pos-left';
        } else if (figureEl.classList.contains('video--right-50')) {
          positionClass = 'video--pos-right';
        } else if (figureEl.classList.contains('video--center-50') || figureEl.classList.contains('video--center-75')) {
          positionClass = 'video--pos-center';
        } else if (figureEl.classList.contains('video--default')) {
          positionClass = 'video--pos-default';
        }
      }

      if (widthClass === '') {
        if (figureEl.classList.contains('video--left-50') || figureEl.classList.contains('video--right-50') || figureEl.classList.contains('video--center-50')) {
          widthClass = 'video--w-50';
        } else if (figureEl.classList.contains('video--center-75')) {
          widthClass = 'video--w-75';
        } else if (figureEl.classList.contains('video--full')) {
          widthClass = 'video--w-100';
        } else if (figureEl.classList.contains('video--default')) {
          widthClass = 'video--w-auto';
        }
      }
    }

    if (positionClass === '') {
      positionClass = 'video--pos-default';
    }
    if (widthClass === '') {
      widthClass = 'video--w-auto';
    }

    return {
      src: src,
      positionClass: positionClass,
      widthClass: widthClass,
      options: {
        controls: videoEl.hasAttribute('controls'),
        autoplay: videoEl.hasAttribute('autoplay'),
        muted: videoEl.hasAttribute('muted'),
        loop: videoEl.hasAttribute('loop'),
        playsinline: videoEl.hasAttribute('playsinline')
      }
    };
  }

  function hasVideoSourceInData(editor, src) {
    if (!src || !editor || typeof editor.getData !== 'function') {
      return false;
    }

    try {
      return editor.getData().indexOf(src) !== -1;
    } catch (error) {
      return false;
    }
  }

  function replaceSelectedVideo(editor, html) {
    var srcMatch = html.match(/src="([^"]+)"/);
    var src = srcMatch && srcMatch[1] ? srcMatch[1] : '';
    if (src !== '') {
      var domData = editor.getData();
      var parser = new DOMParser();
      var doc = parser.parseFromString(domData, 'text/html');
      var videos = Array.from(doc.querySelectorAll('video'));
      var wrapper = null;

      videos.some(function (video) {
        if ((video.getAttribute('src') || '') !== src) {
          return false;
        }

        var candidate = video.closest('figure.for-video');
        if (!candidate) {
          return false;
        }

        while (candidate.parentElement && candidate.parentElement.matches('figure.for-video')) {
          candidate = candidate.parentElement;
        }

        wrapper = candidate;
        return true;
      });

      if (wrapper) {
        wrapper.outerHTML = html;
        editor.setData(doc.body.innerHTML);
        return;
      }
    }

    insertHtml(editor, html);
  }

  function buildVideoHtml(src, positionClass, widthClass, options) {
    var figureClass = ['for-video', positionClass, widthClass].filter(function (entry) {
      return typeof entry === 'string' && entry !== '';
    }).join(' ');

    var attrs = ['src="' + escapeHtml(src) + '"'];
    if (options.controls) {
      attrs.push('controls');
    }
    if (options.autoplay) {
      attrs.push('autoplay');
    }
    if (options.muted) {
      attrs.push('muted');
    }
    if (options.loop) {
      attrs.push('loop');
    }
    if (options.playsinline) {
      attrs.push('playsinline');
    }

    return '<figure class="' + escapeHtml(figureClass) + '" data-redaxo-video-position="' + escapeHtml(positionClass) + '" data-redaxo-video-width="' + escapeHtml(widthClass) + '"><video ' + attrs.join(' ') + '></video></figure>';
  }

  window.CKE5_NATIVE_PLUGINS.RedaxoMediaVideo = function createRedaxoMediaVideo(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {};

    return class RedaxoMediaVideo extends BasePlugin {
      static get pluginName() {
        return 'RedaxoMediaVideo';
      }

      init() {
        if (!this.editor || !this.editor.ui || !this.editor.ui.componentFactory || !cke || typeof cke.ButtonView !== 'function') {
          return;
        }

        var factory = this.editor.ui.componentFactory;
        if (typeof factory.has === 'function' && factory.has('for_video')) {
          return;
        }

        this._videoFormView = createVideoFormView(cke, this.editor.locale);
        this._selectedVideoData = null;

        var editableEl = this.editor.ui && typeof this.editor.ui.getEditableElement === 'function' ? this.editor.ui.getEditableElement() : null;
        if (editableEl) {
          var syncSelectionState = () => {
            var positionStyles = getVideoPositionStyles(this.editor);
            var widthStyles = getVideoWidthStyles(this.editor);
            var data = getSelectedVideoData(this.editor, positionStyles, widthStyles);
            this._selectedVideoData = data && hasVideoSourceInData(this.editor, data.src) ? data : null;
          };

          editableEl.addEventListener('mouseup', syncSelectionState);
          editableEl.addEventListener('keyup', syncSelectionState);
          editableEl.addEventListener('touchend', syncSelectionState);
        }

        factory.add('for_video', (locale) => {
          var button = new cke.ButtonView(locale);
          button.set({
            label: 'Lokales Video',
            icon: "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M3 4h10a1 1 0 011 1v2.2l3.2-2.1c.6-.4 1.3 0 1.3.7v8.4c0 .7-.7 1.1-1.3.7L14 13.8V16a1 1 0 01-1 1H3a1 1 0 01-1-1V5a1 1 0 011-1zm1 2v9h8V6H4zm4 1.7l3.2 2.3L8 12.3V7.7z'/></svg>",
            withText: false,
            tooltip: true
          });

          button.on('execute', () => {
            var positionStyles = getVideoPositionStyles(this.editor);
            var widthStyles = getVideoWidthStyles(this.editor);
            var selectedVideo = getSelectedVideoData(this.editor, positionStyles, widthStyles);
            if (!selectedVideo && this._selectedVideoData && hasVideoSourceInData(this.editor, this._selectedVideoData.src)) {
              selectedVideo = this._selectedVideoData;
            }

            if (selectedVideo) {
              showVideoDialog(this.editor, cke, locale, this._videoFormView, positionStyles, widthStyles, {
                controls: selectedVideo.options.controls,
                autoplay: selectedVideo.options.autoplay,
                muted: selectedVideo.options.muted,
                loop: selectedVideo.options.loop,
                playsinline: selectedVideo.options.playsinline
              }, (positionClass, widthClass, options) => {
                var resolvedPosition = positionClass || selectedVideo.positionClass || 'video--pos-default';
                var resolvedWidth = widthClass || selectedVideo.widthClass || 'video--w-auto';
                var html = buildVideoHtml(selectedVideo.src, resolvedPosition, resolvedWidth, options);
                replaceSelectedVideo(this.editor, html);
              }, 'edit', selectedVideo.positionClass || 'video--pos-default', selectedVideo.widthClass || 'video--w-auto');
              return;
            }

            if (typeof window.openREXMedia !== 'function' || typeof window.jQuery !== 'function') {
              return;
            }

            var allowedTypes = 'mp4,webm,ogg,mov,m4v';
            var query = '&args[types]=' + allowedTypes;
            var popup = window.openREXMedia('cke5_mediavideo', query);

            window.jQuery(popup).off('rex:selectMedia.cke5video').on('rex:selectMedia.cke5video', (event, filename) => {
              event.preventDefault();
              if (popup && typeof popup.close === 'function') {
                popup.close();
              }

              var imageConfig = this.editor.config.get('image') || {};
              var mediaPath = normalizeMediaPath(imageConfig.rexmedia_path || '/media/');
              var src = mediaPath + filename;
              var defaults = getVideoDefaults(this.editor);

              showVideoDialog(this.editor, cke, locale, this._videoFormView, positionStyles, widthStyles, defaults, (positionClass, widthClass, options) => {
                var html = buildVideoHtml(src, positionClass, widthClass, options);
                insertHtml(this.editor, html);
              }, 'insert');
            });
          });

          return button;
        });
      }
    };
  };
})();
