(function () {
  'use strict';

  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  var FOR_LISTS_I18N = {
    en: {
      button: 'List Style',
      buttonWithValuePrefix: 'List style: ',
      ariaLabel: 'List styles',
      promptTitle: 'Choose list style (enter value):',
      startIndexLabel: 'Start index',
      startIndexPromptTitle: 'Choose start index for numbered list (>= 1):',
      defaultKeyword: 'default',
      groupLabels: { default: 'Default', numbered: 'Numbered', bulleted: 'Bulleted' },
      styles: [
        { value: '', label: 'Default', shortLabel: 'Default', title: 'Default', listType: 'any' },
        { value: 'decimal', label: 'Decimal', shortLabel: '1.', title: 'Decimal', listType: 'numbered' },
        { value: 'decimal-leading-zero', label: '01.', shortLabel: '01.', title: 'Decimal leading zero', listType: 'numbered' },
        { value: 'lower-latin', label: 'a.', shortLabel: 'a.', title: 'Lower latin', listType: 'numbered' },
        { value: 'upper-latin', label: 'A.', shortLabel: 'A.', title: 'Upper latin', listType: 'numbered' },
        { value: 'lower-roman', label: 'i.', shortLabel: 'i.', title: 'Lower roman', listType: 'numbered' },
        { value: 'upper-roman', label: 'I.', shortLabel: 'I.', title: 'Upper roman', listType: 'numbered' },
        { value: 'disc', label: '•', shortLabel: '•', title: 'Disc', listType: 'bulleted' },
        { value: 'circle', label: '○', shortLabel: '○', title: 'Circle', listType: 'bulleted' },
        { value: 'square', label: '▪', shortLabel: '▪', title: 'Square', listType: 'bulleted' }
      ]
    },
    de: {
      button: 'Listenstil',
      buttonWithValuePrefix: 'Listenstil: ',
      ariaLabel: 'Listenstile',
      promptTitle: 'Listenstil wählen (Wert eingeben):',
      startIndexLabel: 'Startindex',
      startIndexPromptTitle: 'Startindex für nummerierte Liste wählen (>= 1):',
      defaultKeyword: 'standard',
      groupLabels: { default: 'Standard', numbered: 'Nummeriert', bulleted: 'Aufzählung' },
      styles: [
        { value: '', label: 'Standard', shortLabel: 'Std', title: 'Standard', listType: 'any' },
        { value: 'decimal', label: 'Dezimal', shortLabel: '1.', title: 'Dezimal', listType: 'numbered' },
        { value: 'decimal-leading-zero', label: '01.', shortLabel: '01.', title: 'Dezimal mit führender Null', listType: 'numbered' },
        { value: 'lower-latin', label: 'a.', shortLabel: 'a.', title: 'Kleinbuchstaben', listType: 'numbered' },
        { value: 'upper-latin', label: 'A.', shortLabel: 'A.', title: 'Großbuchstaben', listType: 'numbered' },
        { value: 'lower-roman', label: 'i.', shortLabel: 'i.', title: 'Römisch klein', listType: 'numbered' },
        { value: 'upper-roman', label: 'I.', shortLabel: 'I.', title: 'Römisch groß', listType: 'numbered' },
        { value: 'disc', label: '•', shortLabel: '•', title: 'Punkte', listType: 'bulleted' },
        { value: 'circle', label: '○', shortLabel: '○', title: 'Kreise', listType: 'bulleted' },
        { value: 'square', label: '▪', shortLabel: '▪', title: 'Quadrate', listType: 'bulleted' }
      ]
    },
    es: {
      button: 'Estilo de lista',
      buttonWithValuePrefix: 'Estilo de lista: ',
      ariaLabel: 'Estilos de lista',
      promptTitle: 'Elegir estilo de lista (introducir valor):',
      startIndexLabel: 'Índice inicial',
      startIndexPromptTitle: 'Elegir índice inicial para lista numerada (>= 1):',
      defaultKeyword: 'predeterminado',
      groupLabels: { default: 'Predeterminado', numbered: 'Numerado', bulleted: 'Viñetas' },
      styles: [
        { value: '', label: 'Predeterminado', shortLabel: 'Pred', title: 'Predeterminado', listType: 'any' },
        { value: 'decimal', label: 'Decimal', shortLabel: '1.', title: 'Decimal', listType: 'numbered' },
        { value: 'decimal-leading-zero', label: '01.', shortLabel: '01.', title: 'Decimal con cero inicial', listType: 'numbered' },
        { value: 'lower-latin', label: 'a.', shortLabel: 'a.', title: 'Latino minúscula', listType: 'numbered' },
        { value: 'upper-latin', label: 'A.', shortLabel: 'A.', title: 'Latino mayúscula', listType: 'numbered' },
        { value: 'lower-roman', label: 'i.', shortLabel: 'i.', title: 'Romano minúscula', listType: 'numbered' },
        { value: 'upper-roman', label: 'I.', shortLabel: 'I.', title: 'Romano mayúscula', listType: 'numbered' },
        { value: 'disc', label: '•', shortLabel: '•', title: 'Viñetas', listType: 'bulleted' },
        { value: 'circle', label: '○', shortLabel: '○', title: 'Círculo', listType: 'bulleted' },
        { value: 'square', label: '▪', shortLabel: '▪', title: 'Cuadrado', listType: 'bulleted' }
      ]
    },
    sv: {
      button: 'Liststil',
      buttonWithValuePrefix: 'Liststil: ',
      ariaLabel: 'Liststilar',
      promptTitle: 'Välj liststil (ange värde):',
      startIndexLabel: 'Startindex',
      startIndexPromptTitle: 'Välj startindex för numrerad lista (>= 1):',
      defaultKeyword: 'standard',
      groupLabels: { default: 'Standard', numbered: 'Numrerad', bulleted: 'Punktlista' },
      styles: [
        { value: '', label: 'Standard', shortLabel: 'Std', title: 'Standard', listType: 'any' },
        { value: 'decimal', label: 'Decimal', shortLabel: '1.', title: 'Decimal', listType: 'numbered' },
        { value: 'decimal-leading-zero', label: '01.', shortLabel: '01.', title: 'Decimal med inledande nolla', listType: 'numbered' },
        { value: 'lower-latin', label: 'a.', shortLabel: 'a.', title: 'Små bokstäver', listType: 'numbered' },
        { value: 'upper-latin', label: 'A.', shortLabel: 'A.', title: 'Stora bokstäver', listType: 'numbered' },
        { value: 'lower-roman', label: 'i.', shortLabel: 'i.', title: 'Små romerska', listType: 'numbered' },
        { value: 'upper-roman', label: 'I.', shortLabel: 'I.', title: 'Stora romerska', listType: 'numbered' },
        { value: 'disc', label: '•', shortLabel: '•', title: 'Punkt', listType: 'bulleted' },
        { value: 'circle', label: '○', shortLabel: '○', title: 'Cirkel', listType: 'bulleted' },
        { value: 'square', label: '▪', shortLabel: '▪', title: 'Kvadrat', listType: 'bulleted' }
      ]
    }
  };

  function resolveLanguage(editor) {
    var uiLanguage = editor && editor.locale && typeof editor.locale.uiLanguage === 'string'
      ? editor.locale.uiLanguage
      : '';
    var docLanguage = typeof document !== 'undefined' && document.documentElement && typeof document.documentElement.lang === 'string'
      ? document.documentElement.lang
      : '';
    var raw = (uiLanguage || docLanguage || 'en').toLowerCase();
    if (raw.indexOf('de') === 0) {
      return 'de';
    }
    if (raw.indexOf('es') === 0) {
      return 'es';
    }
    if (raw.indexOf('sv') === 0) {
      return 'sv';
    }
    return 'en';
  }

  function getI18n(editor) {
    var lang = resolveLanguage(editor);
    return FOR_LISTS_I18N[lang] || FOR_LISTS_I18N.en;
  }

  function getListStyleDefinitions(editor) {
    return getI18n(editor).styles;
  }

  function getListStyleGroups(editor) {
    var i18n = getI18n(editor);
    var styles = getListStyleDefinitions(editor);
    var defaultStyle = styles.filter(function (style) {
      return style.value === '';
    });
    var numberedStyles = styles.filter(function (style) {
      return style.listType === 'numbered';
    });
    var bulletedStyles = styles.filter(function (style) {
      return style.listType === 'bulleted';
    });

    return [
      { key: 'default', label: i18n.groupLabels.default, styles: defaultStyle },
      { key: 'numbered', label: i18n.groupLabels.numbered, styles: numberedStyles },
      { key: 'bulleted', label: i18n.groupLabels.bulleted, styles: bulletedStyles }
    ];
  }

  function getCurrentListType(editor, command) {
    var type = command && typeof command.listType === 'string' ? command.listType : '';
    type = normalizeListType(type);
    if (type === 'numbered' || type === 'bulleted') {
      return type;
    }

    var selectedBlock = getFirstSelectedListBlock(editor);
    if (!selectedBlock) {
      return '';
    }

    return normalizeListType(String(selectedBlock.getAttribute('listType') || '').trim());
  }

  function getVisibleListStyles(editor, command) {
    var styles = getListStyleDefinitions(editor).filter(function (style) {
      return style.value === '';
    });
    var currentType = getCurrentListType(editor, command);

    if (currentType === 'numbered') {
      styles = styles.concat(getListStyleDefinitions(editor).filter(function (style) {
        return style.listType === 'numbered';
      }));
    } else if (currentType === 'bulleted') {
      styles = styles.concat(getListStyleDefinitions(editor).filter(function (style) {
        return style.listType === 'bulleted';
      }));
    } else {
      styles = styles.concat(getListStyleDefinitions(editor).filter(function (style) {
        return style.listType === 'numbered' || style.listType === 'bulleted';
      }));
    }

    return styles;
  }

  var FOR_LISTS_ICON = "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><circle cx='3.2' cy='4.2' r='1.05'/><circle cx='3.2' cy='9.3' r='1.05'/><circle cx='3.2' cy='14.4' r='1.05'/><path d='M6.2 3.4h10v1.4h-10V3.4zm0 5.1h10v1.4h-10V8.5zm0 5.1h10v1.4h-10v-1.4z'/></svg>";

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function renderForListsPanel(dropdown, editor, command, i18n) {
    if (!dropdown || !dropdown.panelView || !dropdown.panelView.element) {
      return;
    }

    var panel = dropdown.panelView.element;
    var html = '';
    var visibleStyles = getVisibleListStyles(editor, command);
    var currentType = getCurrentListType(editor, command);
    var listStartCommand = editor && editor.commands && typeof editor.commands.get === 'function'
      ? editor.commands.get('listStart')
      : null;
    var canEditStartIndex = currentType === 'numbered' && !!(listStartCommand && listStartCommand.isEnabled);
    var currentStartIndex = getCurrentListStartIndex(editor, listStartCommand);

    if (canEditStartIndex) {
      html += '<div class="ck-for-lists-actions">'
        + '<button type="button" class="ck ck-button ck-for-lists-action" data-for-list-action="start-index" '
        + 'title="' + escapeHtml(i18n.startIndexLabel) + '" '
        + 'aria-label="' + escapeHtml(i18n.startIndexLabel) + '">'
        + escapeHtml(i18n.startIndexLabel + ': ' + String(currentStartIndex))
        + '</button>'
        + '</div>';
    }

    visibleStyles.forEach(function (style) {
      var styleValue = normalizeStyleValue(style.value || '');
      var enabled = !!(command && command.isEnabled);
      if (styleValue !== '' && currentType) {
        enabled = enabled && isStyleAllowedForListType(style, currentType);
      }

      html += '<button type="button" class="ck ck-button ck-for-lists-tile' + (styleValue === normalizeStyleValue(command && command.value || '') ? ' ck-on' : '') + '" '
        + 'data-for-list-value="' + escapeHtml(style.value) + '" '
        + 'title="' + escapeHtml(style.title || style.label) + '" '
        + 'aria-label="' + escapeHtml(style.title || style.label) + '" '
        + (enabled ? '' : 'disabled="disabled" ')
        + '>';
      html += '<span class="ck-for-lists-tile__icon">' + escapeHtml(style.shortLabel || style.label) + '</span>';
      html += '<span class="ck-for-lists-tile__preview"><span></span><span></span><span></span></span>';
      html += '</button>';
    });

    panel.innerHTML = '<div class="ck-for-lists-panel" data-current-list-type="' + escapeHtml(currentType || '') + '">' + html + '</div>';

    Array.from(panel.querySelectorAll('[data-for-list-value]')).forEach(function (button) {
      button.addEventListener('click', function () {
        if (!command || !command.isEnabled) {
          return;
        }
        var value = normalizeStyleValue(button.getAttribute('data-for-list-value') || '');
        editor.execute('forListsStyle', { value: value });
        editor.editing.view.focus();
        dropdown.isOpen = false;
      });
    });

    Array.from(panel.querySelectorAll('[data-for-list-action="start-index"]')).forEach(function (button) {
      button.addEventListener('click', function () {
        if (!listStartCommand || !listStartCommand.isEnabled || typeof window.prompt !== 'function') {
          return;
        }

        var rawInput = window.prompt(i18n.startIndexPromptTitle, String(getCurrentListStartIndex(editor, listStartCommand)));
        if (typeof rawInput !== 'string') {
          return;
        }

        var normalized = rawInput.trim();
        if (normalized === '') {
          normalized = '1';
        }

        var parsed = parseInt(normalized, 10);
        if (!Number.isFinite(parsed) || parsed < 1) {
          return;
        }

        editor.execute('listStart', { startIndex: parsed });
        editor.editing.view.focus();
        dropdown.isOpen = false;
      });
    });
  }

  function getCurrentListStartIndex(editor, listStartCommand) {
    if (listStartCommand && typeof listStartCommand.value === 'number' && Number.isFinite(listStartCommand.value) && listStartCommand.value >= 1) {
      return listStartCommand.value;
    }

    var firstBlock = getFirstSelectedListBlock(editor);
    if (firstBlock) {
      var attrValue = Number(firstBlock.getAttribute('listStart') || 0);
      if (Number.isFinite(attrValue) && attrValue >= 1) {
        return attrValue;
      }
    }

    return 1;
  }

  var ORDERED_STYLE_TO_TYPE = {
    decimal: '1',
    'lower-latin': 'a',
    'upper-latin': 'A',
    'lower-roman': 'i',
    'upper-roman': 'I'
  };

  function normalizeStyleValue(rawValue) {
    if (typeof rawValue !== 'string') {
      return '';
    }
    var value = rawValue.trim().toLowerCase();
    if (value === '') {
      return '';
    }
    if (value === 'lower-alpha') {
      return 'lower-latin';
    }
    if (value === 'upper-alpha') {
      return 'upper-latin';
    }
    var allowed = FOR_LISTS_I18N.en.styles.map(function (entry) {
      return entry.value;
    });
    return allowed.indexOf(value) !== -1 ? value : '';
  }

  function getStyleLabel(editor, value) {
    var listStyleDefinitions = getListStyleDefinitions(editor);
    var normalized = normalizeStyleValue(value);
    for (var i = 0; i < listStyleDefinitions.length; i += 1) {
      if (listStyleDefinitions[i].value === normalized) {
        return listStyleDefinitions[i].shortLabel || listStyleDefinitions[i].label;
      }
    }
    return listStyleDefinitions[0].shortLabel || listStyleDefinitions[0].label;
  }

  function isListBlock(block) {
    if (!block || typeof block.getAttribute !== 'function') {
      return false;
    }
    var listType = String(block.getAttribute('listType') || '').trim();
    return listType === 'numbered' || listType === 'bulleted' || listType === 'customNumbered' || listType === 'customBulleted' || listType === 'todo';
  }

  function normalizeListType(listType) {
    var value = String(listType || '').trim();
    if (value === 'customNumbered') {
      return 'numbered';
    }
    if (value === 'customBulleted') {
      return 'bulleted';
    }
    return value;
  }

  function getFirstSelectedListBlock(editor) {
    if (!editor || !editor.model || !editor.model.document) {
      return null;
    }

    var selection = editor.model.document.selection;
    if (!selection) {
      return null;
    }

    var blocks = Array.from(selection.getSelectedBlocks ? selection.getSelectedBlocks() : []);
    for (var i = 0; i < blocks.length; i += 1) {
      var block = blocks[i];
      if (isListBlock(block)) {
        return block;
      }
    }

    var pos = selection.getFirstPosition ? selection.getFirstPosition() : null;
    if (!pos) {
      return null;
    }

    var parent = pos.parent;
    while (parent) {
      if (isListBlock(parent)) {
        return parent;
      }
      if (!parent.parent) {
        break;
      }
      parent = parent.parent;
    }

    return null;
  }

  function getSelectedListBlocks(editor) {
    if (!editor || !editor.model || !editor.model.document) {
      return [];
    }

    var selection = editor.model.document.selection;
    if (!selection) {
      return [];
    }

    var selected = [];
    var blocks = Array.from(selection.getSelectedBlocks ? selection.getSelectedBlocks() : []);
    blocks.forEach(function (block) {
      if (isListBlock(block)) {
        selected.push(block);
      }
    });

    if (selected.length > 0) {
      return selected;
    }

    var first = getFirstSelectedListBlock(editor);
    return first ? [first] : [];
  }

  function getListBlocksInRoot(editor) {
    if (!editor || !editor.model || !editor.model.document || typeof editor.model.createRangeIn !== 'function') {
      return [];
    }
    var root = editor.model.document.getRoot();
    if (!root) {
      return [];
    }

    var blocks = [];
    for (var item of editor.model.createRangeIn(root).getItems()) {
      if (isListBlock(item)) {
        blocks.push(item);
      }
    }
    return blocks;
  }

  function getCurrentLevelListBlocks(editor, seedBlock) {
    if (!seedBlock) {
      return [];
    }

    var allListBlocks = getListBlocksInRoot(editor);
    if (allListBlocks.length === 0) {
      return [];
    }

    var seedIndex = allListBlocks.indexOf(seedBlock);
    if (seedIndex === -1) {
      return [seedBlock];
    }

    var seedType = String(seedBlock.getAttribute('listType') || '').trim();
    var seedIndent = Number(seedBlock.getAttribute('listIndent') || 0);

    var start = seedIndex;
    while (start - 1 >= 0) {
      var prev = allListBlocks[start - 1];
      var prevType = String(prev.getAttribute('listType') || '').trim();
      var prevIndent = Number(prev.getAttribute('listIndent') || 0);

      if (prevIndent < seedIndent) {
        break;
      }
      if (prevIndent === seedIndent && prevType !== seedType) {
        break;
      }
      start -= 1;
    }

    var end = seedIndex;
    while (end + 1 < allListBlocks.length) {
      var next = allListBlocks[end + 1];
      var nextType = String(next.getAttribute('listType') || '').trim();
      var nextIndent = Number(next.getAttribute('listIndent') || 0);

      if (nextIndent < seedIndent) {
        break;
      }
      if (nextIndent === seedIndent && nextType !== seedType) {
        break;
      }
      end += 1;
    }

    var target = [];
    for (var i = start; i <= end; i += 1) {
      var block = allListBlocks[i];
      var blockType = String(block.getAttribute('listType') || '').trim();
      var blockIndent = Number(block.getAttribute('listIndent') || 0);
      if (blockIndent === seedIndent && blockType === seedType) {
        target.push(block);
      }
    }

    return target;
  }

  function clearListStyleAttributes(editor, filterType) {
    var selectedBlocks = getSelectedListBlocks(editor);
    if (selectedBlocks.length === 0) {
      return;
    }

    editor.model.change(function (writer) {
      selectedBlocks.forEach(function (block) {
        var type = normalizeListType(String(block.getAttribute('listType') || '').trim());
        if (filterType && type !== filterType) {
          return;
        }
        writer.removeAttribute('forListStyle', block);
        writer.removeAttribute('listStyle', block);
      });
    });
  }

  function installTypeSwitchReset(editor) {
    if (!editor || editor._forListsTypeSwitchResetInstalled) {
      return;
    }
    editor._forListsTypeSwitchResetInstalled = true;

    var hook = function (commandName, targetType) {
      var cmd = editor.commands && typeof editor.commands.get === 'function' ? editor.commands.get(commandName) : null;
      if (!cmd || typeof cmd.on !== 'function') {
        return;
      }

      var hadOppositeType = false;
      var oppositeType = targetType === 'numbered' ? 'bulleted' : 'numbered';

      cmd.on('execute', function () {
        hadOppositeType = getSelectedListBlocks(editor).some(function (block) {
          var type = normalizeListType(String(block.getAttribute('listType') || '').trim());
          return type === oppositeType;
        });
      }, { priority: 'high' });

      cmd.on('execute', function () {
        if (!hadOppositeType) {
          return;
        }
        clearListStyleAttributes(editor, targetType);
      }, { priority: 'low' });
    };

    hook('numberedList', 'numbered');
    hook('customNumberedList', 'numbered');
    hook('bulletedList', 'bulleted');
    hook('customBulletedList', 'bulleted');
  }

  function isStyleAllowedForListType(style, listType) {
    if (style.listType === 'any') {
      return true;
    }
    return style.listType === listType;
  }

  function findAncestorElementByName(viewElement, names) {
    if (!Array.isArray(names) || names.length === 0) {
      return null;
    }
    var current = viewElement;
    while (current) {
      if (typeof current.is === 'function') {
        for (var i = 0; i < names.length; i += 1) {
          if (current.is('element', names[i])) {
            return current;
          }
        }
      }
      current = current.parent;
    }
    return null;
  }

  function getListStyleFromViewElement(viewElement) {
    if (!viewElement) {
      return '';
    }

    var styleValue = '';
    if (typeof viewElement.getStyle === 'function') {
      styleValue = normalizeStyleValue(viewElement.getStyle('list-style-type') || '');
    }

    if (styleValue !== '') {
      return styleValue;
    }

    if (typeof viewElement.getAttribute === 'function' && typeof viewElement.is === 'function' && viewElement.is('element', 'ol')) {
      var typeValue = String(viewElement.getAttribute('type') || '').trim();
      if (typeValue === 'a') {
        return 'lower-latin';
      }
      if (typeValue === 'A') {
        return 'upper-latin';
      }
      if (typeValue === 'i') {
        return 'lower-roman';
      }
      if (typeValue === 'I') {
        return 'upper-roman';
      }
      if (typeValue === '1') {
        return 'decimal';
      }
    }

    return '';
  }

  function setModelStyleForConvertedRange(modelRange, conversionApi, styleValue) {
    if (!modelRange || !conversionApi || !conversionApi.writer) {
      return;
    }

    var writer = conversionApi.writer;
    var listBlocks = [];

    for (var item of modelRange.getItems()) {
      if (isListBlock(item)) {
        listBlocks.push(item);
      }
    }

    if (listBlocks.length === 0) {
      return;
    }

    var minIndent = null;
    listBlocks.forEach(function (listBlock) {
      var indentValue = Number(listBlock.getAttribute('listIndent') || 0);
      if (minIndent === null || indentValue < minIndent) {
        minIndent = indentValue;
      }
    });

    var normalized = normalizeStyleValue(styleValue);
    listBlocks.forEach(function (listBlock) {
      var indentValue = Number(listBlock.getAttribute('listIndent') || 0);
      if (indentValue !== minIndent) {
        return;
      }

      if (normalized === '') {
        writer.removeAttribute('forListStyle', listBlock);
      } else {
        writer.setAttribute('forListStyle', normalized, listBlock);
      }
    });
  }

  function syncEditingViewListStyle(data, conversionApi) {
    if (!data || !conversionApi || !conversionApi.mapper || !conversionApi.writer) {
      return;
    }

    var viewItem = conversionApi.mapper.toViewElement(data.item);
    if (!viewItem) {
      return;
    }

    var listItemElement = findAncestorElementByName(viewItem, ['li']);
    if (!listItemElement) {
      return;
    }
    var listElement = findAncestorElementByName(listItemElement, ['ol', 'ul']);

    var writer = conversionApi.writer;
    var value = normalizeStyleValue(data.attributeNewValue || '');

    if (value === '') {
      writer.removeStyle('list-style-type', listItemElement);
      writer.removeAttribute('type', listItemElement);

      if (listElement && typeof listElement.is === 'function' && listElement.is('element', 'ol')) {
        // Container-Fallback entfernen, wenn wir wieder auf Standard gehen.
        writer.removeStyle('list-style-type', listElement);
      }
      return;
    }

    writer.setStyle('list-style-type', value, listItemElement);
    writer.removeAttribute('type', listItemElement);

    if (listElement && typeof listElement.is === 'function' && listElement.is('element', 'ol')) {
      writer.setStyle('list-style-type', value, listElement);
    }
  }

  function syncDataViewListStyle(data, conversionApi) {
    if (!data || !conversionApi || !conversionApi.mapper || !conversionApi.writer) {
      return;
    }

    var viewItem = conversionApi.mapper.toViewElement(data.item);
    if (!viewItem) {
      return;
    }

    var listItemElement = findAncestorElementByName(viewItem, ['li']);
    if (!listItemElement) {
      return;
    }

    var listElement = findAncestorElementByName(listItemElement, ['ol', 'ul']);
    if (!listElement) {
      return;
    }

    var writer = conversionApi.writer;
    var value = normalizeStyleValue(data.attributeNewValue || '');

    if (value === '') {
      writer.removeStyle('list-style-type', listElement);
      return;
    }

    writer.setStyle('list-style-type', value, listElement);
  }

  function collectModelListStyleMap(editor) {
    var map = {};
    if (!editor || !editor.model || !editor.model.document || typeof editor.model.createRangeIn !== 'function') {
      return map;
    }

    var root = editor.model.document.getRoot();
    if (!root) {
      return map;
    }

    for (var item of editor.model.createRangeIn(root).getItems()) {
      if (!item || typeof item.getAttribute !== 'function') {
        continue;
      }
      var listItemId = String(item.getAttribute('listItemId') || '').trim();
      if (listItemId === '') {
        continue;
      }
      var style = normalizeStyleValue(String(item.getAttribute('forListStyle') || item.getAttribute('listStyle') || ''));
      if (style !== '') {
        map[listItemId] = style;
      }
    }

    return map;
  }

  function syncEditingDomListStyles(editor) {
    if (!editor || !editor.ui || typeof editor.ui.getEditableElement !== 'function') {
      return;
    }

    var editable = editor.ui.getEditableElement();
    if (!editable) {
      return;
    }

    // Vorherige Synchronisationen entfernen, damit keine veralteten Styles stehen bleiben.
    Array.from(editable.querySelectorAll('ol, ul')).forEach(function (list) {
      list.style.removeProperty('list-style-type');
    });

    var styleMap = collectModelListStyleMap(editor);
    Object.keys(styleMap).forEach(function (id) {
      var li = editable.querySelector('li[data-list-item-id="' + id + '"]');
      if (!li || !li.parentElement) {
        return;
      }
      var list = li.parentElement;
      var tag = list.tagName ? list.tagName.toLowerCase() : '';
      if (tag !== 'ol' && tag !== 'ul') {
        return;
      }
      list.style.listStyleType = styleMap[id];
    });
  }

  function installEditingDomStyleSync(editor) {
    if (!editor || editor._forListsDomSyncInstalled) {
      return;
    }
    editor._forListsDomSyncInstalled = true;

    var schedule = function () {
      window.requestAnimationFrame(function () {
        syncEditingDomListStyles(editor);
      });
    };

    if (editor.model && editor.model.document) {
      editor.model.document.on('change:data', schedule);
    }

    schedule();
  }

  window.CKE5_NATIVE_PLUGINS.RedaxoForLists = function createRedaxoForLists(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {};
    var BaseCommand = cke && typeof cke.Command === 'function' ? cke.Command : class {
      constructor(editor) {
        this.editor = editor;
        this.isEnabled = true;
        this.value = '';
      }
      refresh() {}
      execute() {}
    };

    class ForListsStyleCommand extends BaseCommand {
      refresh() {
        var selection = this.editor && this.editor.model && this.editor.model.document ? this.editor.model.document.selection : null;
        var selectionListType = selection && typeof selection.getAttribute === 'function'
          ? String(selection.getAttribute('listType') || '').trim()
          : '';
        var normalizedSelectionListType = normalizeListType(selectionListType);

        if (normalizedSelectionListType === 'numbered' || normalizedSelectionListType === 'bulleted') {
          this.isEnabled = true;
          this.value = normalizeStyleValue(String(selection.getAttribute('forListStyle') || selection.getAttribute('listStyle') || ''));
          this.listType = normalizedSelectionListType;
          return;
        }

        var listBlock = getFirstSelectedListBlock(this.editor);
        if (!listBlock) {
          this.isEnabled = false;
          this.value = '';
          this.listType = null;
          return;
        }

        this.isEnabled = true;
        this.value = normalizeStyleValue(String(listBlock.getAttribute('forListStyle') || listBlock.getAttribute('listStyle') || ''));
        this.listType = normalizeListType(String(listBlock.getAttribute('listType') || '').trim()) || null;
      }

      execute(options) {
        options = options || {};
        var requested = normalizeStyleValue(options.value || '');
        var selectedBlocks = getSelectedListBlocks(this.editor);
        if (selectedBlocks.length === 0) {
          return;
        }

        var firstItem = selectedBlocks[0];
        var listType = normalizeListType(String(firstItem.getAttribute('listType') || '').trim());
        if (listType !== 'numbered' && listType !== 'bulleted') {
          return;
        }

        var targetBlocks = getCurrentLevelListBlocks(this.editor, firstItem);
        if (targetBlocks.length === 0) {
          targetBlocks = [firstItem];
        }

        var selectedStyleDefinition = null;
        var listStyleDefinitions = getListStyleDefinitions(this.editor);
        listStyleDefinitions.forEach(function (entry) {
          if (entry.value === requested) {
            selectedStyleDefinition = entry;
          }
        });

        if (selectedStyleDefinition && !isStyleAllowedForListType(selectedStyleDefinition, listType)) {
          return;
        }

        this.editor.model.change(function (writer) {
          targetBlocks.forEach(function (item) {
            var itemListType = normalizeListType(String(item.getAttribute('listType') || '').trim());
            if (itemListType !== listType) {
              return;
            }
            if (requested === '') {
              writer.removeAttribute('forListStyle', item);
              writer.removeAttribute('listStyle', item);
            } else {
              writer.setAttribute('forListStyle', requested, item);
              writer.setAttribute('listStyle', requested, item);
            }
          });
        });
      }
    }

    function patchGetDataOutput(editor) {
      if (!editor || editor._forListsGetDataPatched || typeof editor.getData !== 'function') {
        return;
      }
      editor._forListsGetDataPatched = true;

      var originalGetData = editor.getData.bind(editor);
      editor.getData = function () {
        var html = originalGetData();
        if (typeof html !== 'string' || html.indexOf('data-list-item-id') === -1) {
          return html;
        }

        var map = {};
        if (editor.model && editor.model.document && typeof editor.model.createRangeIn === 'function') {
          var root = editor.model.document.getRoot();
          if (root) {
            for (var item of editor.model.createRangeIn(root).getItems()) {
              if (!isListBlock(item) || typeof item.getAttribute !== 'function') {
                continue;
              }
              var listItemId = String(item.getAttribute('listItemId') || '').trim();
              if (listItemId === '') {
                continue;
              }
              var style = normalizeStyleValue(String(item.getAttribute('forListStyle') || item.getAttribute('listStyle') || ''));
              if (style !== '') {
                map[listItemId] = style;
              }
            }
          }
        }

        var ids = Object.keys(map);
        if (ids.length === 0) {
          return html;
        }

        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');
        ids.forEach(function (id) {
          var li = doc.querySelector('li[data-list-item-id="' + id + '"]');
          if (!li || !li.parentElement) {
            return;
          }
          var list = li.parentElement;
          var tag = list.tagName ? list.tagName.toLowerCase() : '';
          if (tag !== 'ol' && tag !== 'ul') {
            return;
          }

          var styleValue = map[id];
          list.style.listStyleType = styleValue;

          if (tag === 'ol') {
            list.removeAttribute('type');
          }
        });

        return doc.body.innerHTML;
      };
    }

    return class RedaxoForLists extends BasePlugin {
      static get pluginName() {
        return 'RedaxoForLists';
      }

      init() {
        if (!this.editor || !cke) {
          return;
        }

        var editor = this.editor;

        if (!editor.model || !editor.model.schema || typeof editor.model.schema.extend !== 'function') {
          return;
        }

        var hasListBlockSchema = typeof editor.model.schema.isRegistered !== 'function' || editor.model.schema.isRegistered('$block');

        if (hasListBlockSchema) {
          editor.model.schema.extend('$block', {
            allowAttributes: ['forListStyle']
          });
        }

        editor.commands.add('forListsStyle', new ForListsStyleCommand(editor));

        if (hasListBlockSchema) {
          editor.conversion.for('upcast').add(function (dispatcher) {
            dispatcher.on('element:ol', function (evt, data, conversionApi) {
              var styleValue = getListStyleFromViewElement(data.viewItem);
              if (styleValue === '') {
                return;
              }
              setModelStyleForConvertedRange(data.modelRange, conversionApi, styleValue);
            });

            dispatcher.on('element:ul', function (evt, data, conversionApi) {
              var styleValue = getListStyleFromViewElement(data.viewItem);
              if (styleValue === '') {
                return;
              }
              setModelStyleForConvertedRange(data.modelRange, conversionApi, styleValue);
            });

            dispatcher.on('element:li', function (evt, data, conversionApi) {
              var styleValue = getListStyleFromViewElement(data.viewItem);
              if (styleValue === '') {
                return;
              }
              setModelStyleForConvertedRange(data.modelRange, conversionApi, styleValue);
            });
          });

          editor.conversion.for('dataDowncast').add(function (dispatcher) {
            dispatcher.on('attribute:forListStyle:$block', function (evt, data, conversionApi) {
              syncDataViewListStyle(data, conversionApi);
            });
          });

          editor.conversion.for('editingDowncast').add(function (dispatcher) {
            dispatcher.on('attribute:forListStyle:$block', function (evt, data, conversionApi) {
              syncEditingViewListStyle(data, conversionApi);
            });
          });
        }

        if (!editor.ui || !editor.ui.componentFactory) {
          patchGetDataOutput(editor);
          installEditingDomStyleSync(editor);
          installTypeSwitchReset(editor);
          return;
        }

        patchGetDataOutput(editor);
        installEditingDomStyleSync(editor);
        installTypeSwitchReset(editor);

        var factory = editor.ui.componentFactory;
        if (typeof factory.has === 'function' && factory.has('for_lists')) {
          return;
        }

        factory.add('for_lists', function (locale) {
          var command = editor.commands.get('forListsStyle');
          var i18n = getI18n(editor);
          var listStyleDefinitions = getListStyleDefinitions(editor);

          if (typeof cke.createDropdown !== 'function' || typeof cke.ButtonView !== 'function') {
            if (typeof cke.ButtonView !== 'function') {
              return null;
            }

            var fallbackButton = new cke.ButtonView(locale);
            fallbackButton.set({
              label: i18n.button,
              icon: FOR_LISTS_ICON,
              withText: false,
              tooltip: true
            });

            function syncFallbackState() {
              if (!command) {
                fallbackButton.isEnabled = false;
                fallbackButton.label = i18n.button;
                return;
              }
              fallbackButton.isEnabled = !!command.isEnabled;
              fallbackButton.label = i18n.button;
            }

            if (command && typeof command.on === 'function') {
              command.on('change:isEnabled', syncFallbackState);
              command.on('change:value', syncFallbackState);
            }
            if (editor.model && editor.model.document && editor.model.document.selection) {
              editor.model.document.selection.on('change:range', function () {
                if (command && typeof command.refresh === 'function') {
                  command.refresh();
                }
                syncFallbackState();
              });
            }
            if (command && typeof command.refresh === 'function') {
              command.refresh();
            }
            syncFallbackState();

            fallbackButton.on('execute', function () {
              if (!command || !command.isEnabled || typeof window.prompt !== 'function') {
                return;
              }
              var current = normalizeStyleValue(command.value || '');
              var optionsText = listStyleDefinitions.map(function (entry) {
                return (entry.value === '' ? i18n.defaultKeyword : entry.value) + ': ' + entry.label;
              }).join('\n');
              var input = window.prompt(i18n.promptTitle + '\n\n' + optionsText, current === '' ? i18n.defaultKeyword : current);
              if (typeof input !== 'string') {
                return;
              }
              var raw = input.trim().toLowerCase();
              var value = raw === i18n.defaultKeyword ? '' : normalizeStyleValue(raw);
              editor.execute('forListsStyle', { value: value });
              editor.editing.view.focus();
            });

            return fallbackButton;
          }

          var dropdown = cke.createDropdown(locale);

          dropdown.buttonView.set({
            label: i18n.button,
            icon: FOR_LISTS_ICON,
            withText: false,
            tooltip: true,
            class: 'ck-for-lists-button'
          });

          dropdown.panelView.on('change:isRendered', function () {
            renderForListsPanel(dropdown, editor, command, i18n);
          });

          dropdown.on('change:isOpen', function () {
            if (dropdown.isOpen) {
              window.requestAnimationFrame(function () {
                renderForListsPanel(dropdown, editor, command, i18n);
              });
            }
          });

          function updateDropdownState() {
            if (!command) {
              dropdown.isEnabled = false;
              dropdown.buttonView.label = i18n.button;
              return;
            }

            dropdown.isEnabled = !!command.isEnabled;
            dropdown.buttonView.label = i18n.button;

            if (dropdown.panelView && dropdown.panelView.element) {
              renderForListsPanel(dropdown, editor, command, i18n);
            }
          }

          if (command && typeof command.on === 'function') {
            command.on('change:isEnabled', updateDropdownState);
            command.on('change:value', updateDropdownState);
          }

          if (editor.model && editor.model.document && editor.model.document.selection) {
            editor.model.document.selection.on('change:range', function () {
              if (command && typeof command.refresh === 'function') {
                command.refresh();
              }
              updateDropdownState();
            });
          }

          if (command && typeof command.refresh === 'function') {
            command.refresh();
          }
          updateDropdownState();

          return dropdown;
        });
      }
    };
  };
})();
