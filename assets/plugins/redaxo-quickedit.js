(function () {
  'use strict';

  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  /**
   * Public extension registry.
   * REDAXO addons can push command objects before the editor is initialised:
   *
   *   window.CKE5_QUICKEDIT_COMMANDS = window.CKE5_QUICKEDIT_COMMANDS || [];
   *   window.CKE5_QUICKEDIT_COMMANDS.push({
   *     id:      'myCmd',
   *     label:   'Mein Befehl',
   *     keys:    ['mein', 'cmd'],   // optional filter aliases
   *     icon:    '★',
   *     execute: function(editor) { editor.execute('myCommand'); }
   *   });
   */
  window.CKE5_QUICKEDIT_COMMANDS = window.CKE5_QUICKEDIT_COMMANDS || [];

  var DEFAULT_COMMANDS = [
    { id: 'redaxoMediaImage', label: 'Bild aus Medienpool', keys: ['bild', 'media', 'medienpool', 'img'], icon: '🖼', toolbarItem: 'redaxoMedia', toolbarAny: ['redaxoMedia', 'insertImage', 'rexImage', 'imageUpload', 'uploadImage', 'imageInsert'] },
    { id: 'redaxoClearWidget', label: 'Clear-Widget', keys: ['clear', 'clearfix', 'abstand', 'spacing'], icon: '↦', command: 'insertRedaxoClearWidget', toolbarItem: 'for_clear' },
    { id: 'quickTable',     label: 'Quicktabelle',    keys: ['tabelle', 'tab', 'qt'], icon: '⊞',  command: 'insertTable', commandArgs: { rows: 2, columns: 2 }, toolbarItem: 'insertTable' },
    { id: 'heading1',       label: 'Überschrift 1',   keys: ['h1'],            icon: 'H1',  command: 'heading',       commandArgs: { value: 'heading1'  }, toolbarItem: 'heading' },
    { id: 'heading2',       label: 'Überschrift 2',   keys: ['h2'],            icon: 'H2',  command: 'heading',       commandArgs: { value: 'heading2'  }, toolbarItem: 'heading' },
    { id: 'heading3',       label: 'Überschrift 3',   keys: ['h3'],            icon: 'H3',  command: 'heading',       commandArgs: { value: 'heading3'  }, toolbarItem: 'heading' },
    { id: 'heading4',       label: 'Überschrift 4',   keys: ['h4'],            icon: 'H4',  command: 'heading',       commandArgs: { value: 'heading4'  }, toolbarItem: 'heading' },
    { id: 'paragraph',      label: 'Absatz',          keys: ['p', 'text'],     icon: 'P',   command: 'heading',       commandArgs: { value: 'paragraph' }, toolbarItem: 'heading' },
    { id: 'bulletedList',   label: 'Aufzählung',      keys: ['ul', 'liste'],   icon: '•',   command: 'bulletedList',  toolbarItem: 'bulletedList' },
    { id: 'numberedList',   label: 'Numm. Liste',     keys: ['ol', '1.'],      icon: '1.',  command: 'numberedList',  toolbarItem: 'numberedList' },
    { id: 'todoList',       label: 'To-do-Liste',     keys: ['todo', 'check'], icon: '☑',   command: 'todoList',      toolbarItem: 'todoList' },
    { id: 'blockQuote',     label: 'Zitat',           keys: ['zitat', 'quote'], icon: '❝',  command: 'blockQuote',    toolbarItem: 'blockQuote' },
    { id: 'horizontalLine', label: 'Trennlinie',      keys: ['hr', 'linie'],   icon: '─',   command: 'horizontalLine', toolbarItem: 'horizontalLine' },
    { id: 'codeBlock',      label: 'Code-Block',      keys: ['code', 'cb'],    icon: '‹›',  command: 'codeBlock',     toolbarItem: 'codeBlock' },
    { id: 'htmlEmbed',      label: 'HTML einbetten',  keys: ['html', 'embed'], icon: '</>', command: 'htmlEmbed',     toolbarItem: 'htmlEmbed' },
  ];

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  window.CKE5_NATIVE_PLUGINS.RedaxoQuickEdit = function createRedaxoQuickEdit(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {};

    return class RedaxoQuickEdit extends BasePlugin {
      static get pluginName() {
        return 'RedaxoQuickEdit';
      }

      init() {
        if (!this.editor) {
          return;
        }

        var editor = this.editor;
        function isEnabled(value, fallback) {
          if (typeof value === 'undefined' || value === null) {
            return fallback;
          }
          if (typeof value === 'boolean') {
            return value;
          }
          if (typeof value === 'number') {
            return value === 1;
          }
          var normalized = String(value).trim().toLowerCase();
          if (normalized === '') {
            return fallback;
          }
          if (normalized === '1' || normalized === 'true' || normalized === 'on' || normalized === 'yes') {
            return true;
          }
          if (normalized === '0' || normalized === 'false' || normalized === 'off' || normalized === 'no') {
            return false;
          }
          return fallback;
        }

        if (!isEnabled(editor.config.get('redaxoQuickEditEnabled'), true)) {
          return;
        }

        var state = {
          menuEl: null,
          visible: false,
          commands: [],
          selected: 0,
          sectionOffset: -1,
          toolbarItems: []
        };

        var triggerChars = ['/', '§'];

        function getToolbarItems() {
          var toolbarConfig = editor.config.get('toolbar');
          if (!toolbarConfig) {
            return [];
          }
          var items = Array.isArray(toolbarConfig.items) ? toolbarConfig.items : Array.isArray(toolbarConfig) ? toolbarConfig : [];
          return items.filter(function (item) {
            return typeof item === 'string' && item !== '|';
          });
        }

        function hasToolbarItem(itemName) {
          if (typeof itemName !== 'string' || itemName === '') {
            return false;
          }
          if (state.toolbarItems.includes(itemName)) {
            return true;
          }
          if (itemName === 'redaxoMedia' && editor.ui && editor.ui.componentFactory && typeof editor.ui.componentFactory.has === 'function') {
            return editor.ui.componentFactory.has('redaxoMedia') && state.toolbarItems.some(function (item) {
              return item === 'redaxoMedia' || item === 'insertImage' || item === 'rexImage' || item === 'imageUpload' || item === 'uploadImage' || item === 'imageInsert';
            });
          }
          return false;
        }

        function getAllowedHeadingValues() {
          var headingConfig = editor.config.get('heading');
          if (!headingConfig || !Array.isArray(headingConfig.options)) {
            return [];
          }

          var values = [];
          headingConfig.options.forEach(function (option) {
            if (typeof option === 'string') {
              values.push(option);
              return;
            }
            if (option && typeof option.model === 'string') {
              values.push(option.model);
            }
          });

          return values;
        }

        function isCommandAvailable(cmd) {
          if (!cmd || typeof cmd !== 'object') {
            return false;
          }
          var toolbarItem = typeof cmd.toolbarItem === 'string' && cmd.toolbarItem !== '' ? cmd.toolbarItem : cmd.id;
          var hasToolbarMatch = hasToolbarItem(toolbarItem);
          if (!hasToolbarMatch && Array.isArray(cmd.toolbarAny)) {
            hasToolbarMatch = cmd.toolbarAny.some(function (item) {
              return hasToolbarItem(item);
            });
          }
          if (!hasToolbarMatch) {
            return false;
          }

          if (cmd.id === 'redaxoMediaImage' && typeof window.openREXMedia !== 'function') {
            return false;
          }

          if (cmd.id === 'redaxoMediaImage') {
            return true;
          }

          if (cmd.command === 'heading' && cmd.commandArgs && typeof cmd.commandArgs.value === 'string') {
            var allowedHeadings = getAllowedHeadingValues();
            if (allowedHeadings.length > 0 && !allowedHeadings.includes(cmd.commandArgs.value)) {
              return false;
            }
          }

          if (typeof cmd.execute === 'function') {
            return true;
          }
          if (!cmd.command || !editor.commands.get(cmd.command)) {
            return false;
          }
          return true;
        }

        function getAllCommands() {
          var ext = Array.isArray(window.CKE5_QUICKEDIT_COMMANDS) ? window.CKE5_QUICKEDIT_COMMANDS : [];
          return DEFAULT_COMMANDS.concat(
            ext.filter(function (c) {
              return c && typeof c.id === 'string' && typeof c.label === 'string';
            })
          ).filter(isCommandAvailable);
        }

        function findTriggerIndex(text) {
          for (var i = text.length - 1; i >= 0; i--) {
            var char = text.charAt(i);
            if (!triggerChars.includes(char)) {
              continue;
            }

            if (char === '/') {
              var previousChar = i > 0 ? text.charAt(i - 1) : '';
              if (previousChar === '/' || previousChar === ':') {
                continue;
              }

              var tokenStart = text.lastIndexOf(' ', i - 1) + 1;
              var tokenBeforeTrigger = text.slice(tokenStart, i);
              if (tokenBeforeTrigger.indexOf('://') !== -1) {
                continue;
              }
            }

            return i;
          }
          return -1;
        }

        function hideMenu() {
          if (state.menuEl && state.menuEl.parentNode) {
            state.menuEl.parentNode.removeChild(state.menuEl);
          }
          state.menuEl = null;
          state.visible = false;
          state.commands = [];
          state.selected = 0;
          state.sectionOffset = -1;
        }

        function getTextBeforeCursor() {
          try {
            var sel = editor.model.document.selection;
            var pos = sel.getFirstPosition();
            if (!pos || !pos.parent) {
              return { text: '', offset: 0 };
            }
            var startPos = editor.model.createPositionAt(pos.parent, 0);
            var range = editor.model.createRange(startPos, pos);
            var text = '';
            for (var item of range.getItems()) {
              if (item.is('$text') || item.is('$textProxy')) {
                text += item.data;
              }
            }
            return { text: text, offset: pos.offset };
          } catch (e) {
            return { text: '', offset: 0 };
          }
        }

        function getCaretRect() {
          try {
            var domSel = window.getSelection();
            if (!domSel || domSel.rangeCount === 0) {
              return null;
            }
            var rect = domSel.getRangeAt(0).getBoundingClientRect();
            if (rect && (rect.width > 0 || rect.height > 0)) {
              return rect;
            }
            // Fallback: parent element rect
            var node = domSel.focusNode;
            if (node) {
              var el = node.nodeType === 1 ? node : node.parentElement;
              if (el) {
                return el.getBoundingClientRect();
              }
            }
            return null;
          } catch (e) {
            return null;
          }
        }

        function updateHighlight() {
          if (!state.menuEl) {
            return;
          }
          state.menuEl.querySelectorAll('.cke5-qe-item').forEach(function (item, i) {
            item.classList.toggle('is-active', i === state.selected);
          });
          var active = state.menuEl.querySelector('.cke5-qe-item.is-active');
          if (active) {
            active.scrollIntoView({ block: 'nearest' });
          }
        }

        function renderMenu(commands, rect) {
          if (!commands.length) {
            hideMenu();
            return;
          }

          if (!state.menuEl) {
            state.menuEl = document.createElement('div');
            state.menuEl.className = 'cke5-quickedit-menu';
            document.body.appendChild(state.menuEl);

            state.menuEl.addEventListener('mousedown', function (e) {
              var item = e.target.closest('.cke5-qe-item');
              if (item) {
                e.preventDefault();
                var idx = parseInt(item.dataset.index, 10);
                if (state.commands[idx]) {
                  executeCommand(state.commands[idx]);
                }
              }
            });
          }

          state.commands = commands;
          state.selected = 0;

          state.menuEl.innerHTML = commands.map(function (cmd, i) {
            return '<div class="cke5-qe-item' + (i === 0 ? ' is-active' : '') + '" data-index="' + i + '">' +
              '<span class="cke5-qe-icon">' + escapeHtml(cmd.icon || '◆') + '</span>' +
              '<span class="cke5-qe-label">' + escapeHtml(cmd.label) + '</span>' +
              '</div>';
          }).join('');

          positionMenu(rect);
          state.visible = true;
        }

        function positionMenu(rect) {
          if (!state.menuEl || !rect) {
            return;
          }
          var left = rect.left;
          var top = rect.bottom + 6;
          var menuW = Math.max(state.menuEl.offsetWidth || 240, 200);

          if (left + menuW > window.innerWidth - 8) {
            left = window.innerWidth - menuW - 8;
          }
          if (top + 80 > window.innerHeight - 8) {
            top = Math.max(8, rect.top - 8 - state.menuEl.offsetHeight);
          }

          state.menuEl.style.left = Math.max(8, left) + 'px';
          state.menuEl.style.top = Math.max(8, top) + 'px';
        }

        function repositionMenu() {
          if (!state.visible || !state.menuEl) {
            return;
          }
          var rect = getCaretRect();
          if (!rect) {
            hideMenu();
            return;
          }
          positionMenu(rect);
        }

        function executeMediaPoolImage() {
          if (typeof window.openREXMedia !== 'function' || typeof window.jQuery !== 'function') {
            return;
          }

          var imageConfig = editor.config.get('image') || {};
          var mediaTypes = typeof imageConfig.rexmedia_types === 'string' && imageConfig.rexmedia_types !== ''
            ? imageConfig.rexmedia_types
            : 'jpg,jpeg,png,gif,bmp,tiff,svg,webp,heic,heif';
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

          window.jQuery(popup).off('rex:selectMedia.cke5.quickedit').on('rex:selectMedia.cke5.quickedit', function (event, filename) {
            event.preventDefault();
            if (popup && typeof popup.close === 'function') {
              popup.close();
            }

            var source = mediaPath + filename;
            var selectedElement = editor.model.document.selection.getSelectedElement();
            var imageUtils = editor.plugins && editor.plugins.has('ImageUtils') ? editor.plugins.get('ImageUtils') : null;
            if (imageUtils && typeof imageUtils.isImage === 'function' && imageUtils.isImage(selectedElement) && editor.commands.get('replaceImageSource')) {
              editor.execute('replaceImageSource', { source: source });
            } else if (editor.commands.get('insertImage')) {
              editor.execute('insertImage', { source: source });
            } else if (editor.commands.get('imageInsert')) {
              editor.execute('imageInsert', { source: source });
            }
          });
        }

        function executeCommand(cmd) {
          var sectionOff = state.sectionOffset;
          hideMenu();

          // Remove trigger + any query text typed after it
          editor.model.change(function (writer) {
            try {
              var sel = editor.model.document.selection;
              var pos = sel.getFirstPosition();
              if (!pos || !pos.parent || sectionOff < 0) {
                return;
              }
              var deleteRange = writer.createRange(
                writer.createPositionAt(pos.parent, sectionOff),
                writer.createPositionAt(pos.parent, pos.offset)
              );
              writer.remove(deleteRange);
            } catch (e) {}
          });

          // Let model settle, then execute command
          window.setTimeout(function () {
            try {
              if (cmd.id === 'redaxoMediaImage') {
                executeMediaPoolImage();
              } else if (typeof cmd.execute === 'function') {
                cmd.execute(editor);
              } else if (cmd.command && editor.commands.get(cmd.command)) {
                if (cmd.commandArgs) {
                  editor.execute(cmd.command, cmd.commandArgs);
                } else {
                  editor.execute(cmd.command);
                }
              }
            } catch (e) {}
            editor.editing.view.focus();
          }, 0);
        }

        // ── Trigger detection ─────────────────────────────────────────
        editor.model.document.on('change:data', function () {
          state.toolbarItems = getToolbarItems();
          var result = getTextBeforeCursor();
          var text = result.text;
          var sectionIdx = findTriggerIndex(text);

          if (sectionIdx === -1) {
            hideMenu();
            return;
          }

          var query = text.slice(sectionIdx + 1).toLowerCase();

          // Hide when user typed a space (real word) or query too long
          if (query.indexOf(' ') !== -1 || query.length > 15) {
            hideMenu();
            return;
          }

          state.sectionOffset = sectionIdx;

          var all = getAllCommands();
          var matched = query === ''
            ? all
            : all.filter(function (cmd) {
                if (cmd.label.toLowerCase().indexOf(query) !== -1) {
                  return true;
                }
                return Array.isArray(cmd.keys) && cmd.keys.some(function (k) {
                  return k.toLowerCase().indexOf(query) !== -1;
                });
              });

          if (!matched.length) {
            hideMenu();
            return;
          }

          var rect = getCaretRect();
          if (!rect) {
            hideMenu();
            return;
          }

          renderMenu(matched, rect);
        });

        window.addEventListener('scroll', repositionMenu, true);
        window.addEventListener('resize', repositionMenu);

        // ── Keyboard navigation ───────────────────────────────────────
        editor.editing.view.document.on('keydown', function (evt, data) {
          if (!state.visible) {
            return;
          }

          var k = data.keyCode;

          if (k === 27) { // Escape — hide, keep trigger as-is
            hideMenu();
            data.preventDefault();
            evt.stop();
            return;
          }

          if (k === 40) { // Arrow down
            state.selected = Math.min(state.selected + 1, state.commands.length - 1);
            updateHighlight();
            data.preventDefault();
            evt.stop();
            return;
          }

          if (k === 38) { // Arrow up
            state.selected = Math.max(state.selected - 1, 0);
            updateHighlight();
            data.preventDefault();
            evt.stop();
            return;
          }

          if (k === 9) { // Tab — cycle
            state.selected = (state.selected + 1) % state.commands.length;
            updateHighlight();
            data.preventDefault();
            evt.stop();
            return;
          }

          if (k === 13) { // Enter — execute
            if (state.commands[state.selected]) {
              executeCommand(state.commands[state.selected]);
              data.preventDefault();
              evt.stop();
            }
            return;
          }
        }, { priority: 'highest' });

        // Hide on outside click
        document.addEventListener('mousedown', function (e) {
          if (state.menuEl && !state.menuEl.contains(e.target)) {
            hideMenu();
          }
        }, true);

        editor.on('destroy', function () {
          window.removeEventListener('scroll', repositionMenu, true);
          window.removeEventListener('resize', repositionMenu);
          hideMenu();
        });
      }
    };
  };
})();
