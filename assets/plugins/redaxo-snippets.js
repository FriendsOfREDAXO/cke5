(function () {
  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  var PANEL_CLASS = 'ck-redaxo-snippets-panel';

  function insertHtml(editor, html) {
    if (!editor || typeof html !== 'string' || html.trim() === '') {
      return;
    }
    try {
      var viewFragment = editor.data.processor.toView(html);
      var modelFragment = editor.data.toModel(viewFragment);
      editor.model.insertContent(modelFragment, editor.model.document.selection);
    } catch (error) {
      console.error('cke5 snippets insert failed', error);
    }
  }

  function closeAllPanels() {
    document.querySelectorAll('.' + PANEL_CLASS).forEach(function (el) {
      el.remove();
    });
  }

  function createDropdownPanel(snippets, onSelect) {
    var panel = document.createElement('div');
    panel.className = 'ck ck-reset ck-dropdown__panel ck-dropdown__panel_se ck-dropdown__panel-visible ' + PANEL_CLASS;
    panel.style.cssText = 'position:fixed;z-index:100001;min-width:180px;max-width:320px;max-height:320px;overflow-y:auto;';

    var list = document.createElement('ul');
    list.className = 'ck ck-reset ck-list';

    snippets.forEach(function (snippet) {
      var li = document.createElement('li');
      li.className = 'ck ck-list__item';

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'ck ck-button ck-off ck-button_with-text';
      btn.style.cssText = 'width:100%;text-align:left;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
      btn.title = String(snippet.name || '');

      var icon = document.createElement('span');
      icon.className = 'ck ck-button__icon';
      icon.innerHTML = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M4 2h12v16H4V2zm1.5 1.5v13h9V3.5h-9zm1.2 2.1h6.6v1.3H6.7V5.6zm0 2.7h6.6v1.3H6.7V8.3zm0 2.7h4.1v1.3H6.7V11z"/></svg>';

      var label = document.createElement('span');
      label.className = 'ck ck-button__label';
      label.textContent = String(snippet.name || '');

      btn.appendChild(icon);
      btn.appendChild(label);

      btn.addEventListener('mousedown', function (e) {
        e.preventDefault();
      });
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        onSelect(snippet);
      });
      btn.addEventListener('mouseenter', function () {
        btn.classList.remove('ck-off');
        btn.classList.add('ck-on');
      });
      btn.addEventListener('mouseleave', function () {
        btn.classList.remove('ck-on');
        btn.classList.add('ck-off');
      });

      li.appendChild(btn);
      list.appendChild(li);
    });

    panel.appendChild(list);
    return panel;
  }

  window.CKE5_NATIVE_PLUGINS.RedaxoSnippets = function createRedaxoSnippets(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {};

    return class RedaxoSnippets extends BasePlugin {
      static get pluginName() {
        return 'RedaxoSnippets';
      }

      init() {
        if (!this.editor || !this.editor.ui || !this.editor.ui.componentFactory || !cke || typeof cke.ButtonView !== 'function') {
          return;
        }

        var factory = this.editor.ui.componentFactory;
        if (typeof factory.has === 'function' && factory.has('snippets')) {
          return;
        }

        factory.add('snippets', (locale) => {
          var button = new cke.ButtonView(locale);
          button.set({
            label: 'Snippet einfügen',
            icon: "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M4 2h12v16H4V2zm1.5 1.5v13h9V3.5h-9zm1.2 2.1h6.6v1.3H6.7V5.6zm0 2.7h6.6v1.3H6.7V8.3zm0 2.7h4.1v1.3H6.7V11z'/></svg>",
            withText: false,
            tooltip: true,
            isToggleable: true
          });

          button.on('execute', () => {
            // Toggle: close if already open
            var existing = document.querySelector('.' + PANEL_CLASS);
            if (existing) {
              closeAllPanels();
              button.isOn = false;
              return;
            }

            var snippets = this.editor.config.get('redaxoSnippets');
            if (!Array.isArray(snippets) || snippets.length === 0) {
              return;
            }

            button.isOn = true;

            var panel = createDropdownPanel(snippets, (snippet) => {
              closeAllPanels();
              button.isOn = false;
              insertHtml(this.editor, String(snippet.content || ''));
            });

            // Position below the button
            var buttonEl = button.element;
            if (buttonEl) {
              var rect = buttonEl.getBoundingClientRect();
              panel.style.top = (rect.bottom + 2) + 'px';
              panel.style.left = rect.left + 'px';
            }
            document.body.appendChild(panel);

            // Close on outside click
            var closeHandler = function (e) {
              var p = document.querySelector('.' + PANEL_CLASS);
              if (!p) {
                document.removeEventListener('mousedown', closeHandler, true);
                return;
              }
              if (!p.contains(e.target) && button.element && !button.element.contains(e.target)) {
                closeAllPanels();
                button.isOn = false;
                document.removeEventListener('mousedown', closeHandler, true);
              }
            };
            setTimeout(function () {
              document.addEventListener('mousedown', closeHandler, true);
            }, 50);

            // Close when editor data changes (snippet was inserted)
            this.editor.model.document.once('change:data', function () {
              closeAllPanels();
              button.isOn = false;
            });
          });

          return button;
        });
      }
    };
  };
})();
