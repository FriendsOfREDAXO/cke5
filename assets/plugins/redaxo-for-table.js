(function () {
  'use strict';

  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  var FOR_TABLE_I18N = {
    en: {
      tableButton: 'Table',
      columnButton: 'Column',
      rowButton: 'Row',
      cellButton: 'Cell',
      panelTitleTable: 'Table properties',
      panelTitleColumn: 'Column properties',
      panelTitleRow: 'Row properties',
      panelTitleCell: 'Cell properties',
      classLabel: 'Class',
      widthLabel: 'Width',
      heightLabel: 'Height',
      textAlignLabel: 'Text align',
      verticalAlignLabel: 'Vertical align',
      keepManualHint: 'Use CSS classes for consistent table styling.',
      apply: 'Apply',
      reset: 'Reset',
      classDefault: 'Default',
      alignDefault: 'Default',
      alignLeft: 'Left',
      alignCenter: 'Center',
      alignRight: 'Right',
      alignJustify: 'Justify',
      valignDefault: 'Default',
      valignTop: 'Top',
      valignMiddle: 'Middle',
      valignBottom: 'Bottom'
    },
    de: {
      tableButton: 'Tabelle',
      columnButton: 'Spalte',
      rowButton: 'Zeile',
      cellButton: 'Zelle',
      panelTitleTable: 'Tabelleneigenschaften',
      panelTitleColumn: 'Spalteneigenschaften',
      panelTitleRow: 'Zeileneigenschaften',
      panelTitleCell: 'Zelleneigenschaften',
      classLabel: 'Klasse',
      widthLabel: 'Breite',
      heightLabel: 'Höhe',
      textAlignLabel: 'Textausrichtung',
      verticalAlignLabel: 'Vertikale Ausrichtung',
      keepManualHint: 'CSS-Klassen sorgen für ein konsistentes Tabellenlayout.',
      apply: 'Übernehmen',
      reset: 'Zurücksetzen',
      classDefault: 'Standard',
      alignDefault: 'Standard',
      alignLeft: 'Links',
      alignCenter: 'Zentriert',
      alignRight: 'Rechts',
      alignJustify: 'Blocksatz',
      valignDefault: 'Standard',
      valignTop: 'Oben',
      valignMiddle: 'Mitte',
      valignBottom: 'Unten'
    }
  };

  var TABLE_ICON = "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M2 3h16v14H2V3zm1.2 1.2v2.6h13.6V4.2H3.2zm0 3.8v2.8h4.2V8H3.2zm5.4 0v2.8h3V8h-3zm4.2 0v2.8h3.6V8h-3.6zm-9.6 4v3h4.2v-3H3.2zm5.4 0v3h3v-3h-3zm4.2 0v3h3.6v-3h-3.6z'/></svg>";
  var COLUMN_ICON = "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M2 3h16v14H2V3zm1.2 1.2v11.6h3.6V4.2H3.2zm4.8 0v11.6h3.8V4.2H8zm5 0v11.6h3.8V4.2H13z'/></svg>";
  var ROW_ICON = "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M2 3h16v14H2V3zm1.2 1.2v3h13.6v-3H3.2zm0 4.2v3.2h13.6v-3.2H3.2zm0 4.4v1.8h13.6v-1.8H3.2z'/></svg>";
  var CELL_ICON = "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M2 3h16v14H2V3zm1.2 1.2v11.6h13.6V4.2H3.2zm1.6 1.6h4.4v3.6H4.8V5.8zm5.6 0h4.8v3.6h-4.8V5.8zm-5.6 4.8h4.4v3.6H4.8v-3.6z' /><path d='M11.2 11.1h3.8v3H11.2z' /></svg>";

  function resolveLanguage(editor) {
    var rexLocale = typeof window !== 'undefined' && window.rex && typeof window.rex.backendLocale === 'string'
      ? window.rex.backendLocale
      : typeof window !== 'undefined' && window.rex && typeof window.rex.locale === 'string'
        ? window.rex.locale
        : '';
    var docLanguage = typeof document !== 'undefined' && document.documentElement && typeof document.documentElement.lang === 'string' ? document.documentElement.lang : '';
    var uiLanguage = editor && editor.locale && typeof editor.locale.uiLanguage === 'string' ? editor.locale.uiLanguage : '';
    var raw = (rexLocale || docLanguage || uiLanguage || 'en').toLowerCase();
    if (raw.indexOf('de') === 0) {
      return 'de';
    }
    return 'en';
  }

  function getI18n(editor) {
    var lang = resolveLanguage(editor);
    return FOR_TABLE_I18N[lang] || FOR_TABLE_I18N.en;
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function normalizeClassDefinitions(value) {
    if (!Array.isArray(value)) {
      return [];
    }

    var definitions = [];
    value.forEach(function (item) {
      if (!item || typeof item !== 'object') {
        return;
      }
      var label = typeof item.label === 'string' ? item.label.trim() : '';
      var className = typeof item.class === 'string' ? item.class.trim() : '';
      if (label === '' || className === '') {
        return;
      }
      definitions.push({
        label: label,
        class: className
      });
    });

    return definitions;
  }

  function getConfig(editor) {
    var raw = editor && editor.config ? editor.config.get('forTable') : null;
    var config = raw && typeof raw === 'object' ? raw : {};

    var tableClasses = normalizeClassDefinitions(config.tableClasses);
    var columnClasses = normalizeClassDefinitions(config.columnClasses);
    var rowClasses = normalizeClassDefinitions(config.rowClasses);
    var cellClasses = normalizeClassDefinitions(config.cellClasses);

    return {
      tableClasses: tableClasses,
      columnClasses: columnClasses,
      rowClasses: rowClasses,
      cellClasses: cellClasses,
      hideManualTableProperties: tableClasses.length > 0 || config.hideManualTableProperties === true,
      hideManualColumnProperties: columnClasses.length > 0 || config.hideManualColumnProperties === true,
      hideManualRowProperties: rowClasses.length > 0 || config.hideManualRowProperties === true,
      hideManualCellProperties: cellClasses.length > 0 || config.hideManualCellProperties === true
    };
  }

  function parseStyleString(styleString) {
    var map = {};
    if (typeof styleString !== 'string' || styleString.trim() === '') {
      return map;
    }

    styleString.split(';').forEach(function (pair) {
      var parts = pair.split(':');
      if (parts.length < 2) {
        return;
      }
      var key = parts.shift().trim().toLowerCase();
      var value = parts.join(':').trim();
      if (key === '') {
        return;
      }
      map[key] = value;
    });

    return map;
  }

  function toStyleString(styleMap) {
    var keys = Object.keys(styleMap || {});
    if (keys.length === 0) {
      return '';
    }

    return keys.filter(function (key) {
      return typeof key === 'string' && key !== '' && typeof styleMap[key] === 'string' && styleMap[key].trim() !== '';
    }).map(function (key) {
      return key + ': ' + styleMap[key].trim();
    }).join('; ');
  }

  function findSelectedTable(editor) {
    if (!editor || !editor.model || !editor.model.document) {
      return null;
    }

    var selection = editor.model.document.selection;
    if (!selection || typeof selection.getFirstPosition !== 'function') {
      return null;
    }

    var selectedElement = typeof selection.getSelectedElement === 'function' ? selection.getSelectedElement() : null;
    if (selectedElement && selectedElement.name === 'table') {
      return selectedElement;
    }

    var position = selection.getFirstPosition();
    return position && typeof position.findAncestor === 'function' ? position.findAncestor('table') : null;
  }

  function getSelectionTableCells(editor) {
    if (!editor || !editor.model || !editor.model.document) {
      return [];
    }

    var selection = editor.model.document.selection;
    if (!selection) {
      return [];
    }

    var cells = [];
    try {
      if (editor.plugins && typeof editor.plugins.get === 'function') {
        var tableUtils = editor.plugins.get('TableUtils');
        if (tableUtils && typeof tableUtils.getSelectionAffectedTableCells === 'function') {
          cells = Array.from(tableUtils.getSelectionAffectedTableCells(selection));
        }
      }
    } catch (error) {
      cells = [];
    }

    if (cells.length > 0) {
      return cells;
    }

    var selectedElement = typeof selection.getSelectedElement === 'function' ? selection.getSelectedElement() : null;
    if (selectedElement && selectedElement.name === 'tableCell') {
      return [selectedElement];
    }

    if (!selection.getRanges || typeof selection.getRanges !== 'function') {
      return [];
    }

    var seen = new Set();
    var fallbackCells = [];
    for (var range of selection.getRanges()) {
      for (var item of range.getItems()) {
        if (!item || item.name !== 'tableCell') {
          continue;
        }
        if (seen.has(item)) {
          continue;
        }
        seen.add(item);
        fallbackCells.push(item);
      }
    }

    return fallbackCells;
  }

  function readTableValues(table) {
    var className = table && typeof table.getAttribute === 'function' ? String(table.getAttribute('forTableClass') || '') : '';
    var styleString = table && typeof table.getAttribute === 'function' ? String(table.getAttribute('forTableStyle') || '') : '';
    var styleMap = parseStyleString(styleString);

    return {
      className: className,
      width: styleMap.width || '',
      textAlign: styleMap['text-align'] || ''
    };
  }

  function readCellValues(cell) {
    var className = cell && typeof cell.getAttribute === 'function' ? String(cell.getAttribute('forTableCellClass') || '') : '';
    var styleString = cell && typeof cell.getAttribute === 'function' ? String(cell.getAttribute('forTableCellStyle') || '') : '';
    var styleMap = parseStyleString(styleString);

    return {
      className: className,
      width: styleMap.width || '',
      height: styleMap.height || '',
      textAlign: styleMap['text-align'] || '',
      verticalAlign: styleMap['vertical-align'] || ''
    };
  }

  function getCellParentRow(cell) {
    return cell && cell.parent && cell.parent.name === 'tableRow' ? cell.parent : null;
  }

  function getCellParentTable(cell) {
    var row = getCellParentRow(cell);
    return row && row.parent && row.parent.name === 'table' ? row.parent : null;
  }

  function getCellColumnStart(cell) {
    var row = getCellParentRow(cell);
    if (!row || !row.getChildren || !cell) {
      return -1;
    }

    var index = 0;
    var children = Array.from(row.getChildren());
    for (var i = 0; i < children.length; i += 1) {
      var child = children[i];
      if (child === cell) {
        return index;
      }
      var colspan = parseInt(String(child.getAttribute && child.getAttribute('colspan') || '1'), 10);
      index += Number.isFinite(colspan) && colspan > 0 ? colspan : 1;
    }
    return -1;
  }

  function getCellsInSelectedRows(editor) {
    var cells = getSelectionTableCells(editor);
    if (cells.length === 0) {
      return [];
    }

    var rowSet = new Set();
    cells.forEach(function (cell) {
      var row = getCellParentRow(cell);
      if (row) {
        rowSet.add(row);
      }
    });

    var result = [];
    rowSet.forEach(function (row) {
      if (!row.getChildren) {
        return;
      }
      Array.from(row.getChildren()).forEach(function (rowCell) {
        if (rowCell && rowCell.name === 'tableCell') {
          result.push(rowCell);
        }
      });
    });

    return result;
  }

  function getCellsInSelectedColumns(editor) {
    var cells = getSelectionTableCells(editor);
    if (cells.length === 0) {
      return [];
    }

    var firstCell = cells[0];
    var table = getCellParentTable(firstCell);
    if (!table || !table.getChildren) {
      return [];
    }

    var selectedColumnIndexes = new Set();
    cells.forEach(function (cell) {
      var start = getCellColumnStart(cell);
      if (start < 0) {
        return;
      }
      var colspan = parseInt(String(cell.getAttribute && cell.getAttribute('colspan') || '1'), 10);
      var span = Number.isFinite(colspan) && colspan > 0 ? colspan : 1;
      for (var i = 0; i < span; i += 1) {
        selectedColumnIndexes.add(start + i);
      }
    });

    if (selectedColumnIndexes.size === 0) {
      return [];
    }

    var result = [];
    Array.from(table.getChildren()).forEach(function (row) {
      if (!row || row.name !== 'tableRow' || !row.getChildren) {
        return;
      }

      var cursor = 0;
      Array.from(row.getChildren()).forEach(function (rowCell) {
        if (!rowCell || rowCell.name !== 'tableCell') {
          return;
        }

        var colspan = parseInt(String(rowCell.getAttribute && rowCell.getAttribute('colspan') || '1'), 10);
        var span = Number.isFinite(colspan) && colspan > 0 ? colspan : 1;
        var include = false;
        for (var idx = cursor; idx < cursor + span; idx += 1) {
          if (selectedColumnIndexes.has(idx)) {
            include = true;
            break;
          }
        }
        if (include) {
          result.push(rowCell);
        }
        cursor += span;
      });
    });

    return result;
  }

  function readModeValues(cells) {
    if (!Array.isArray(cells) || cells.length === 0) {
      return null;
    }
    return readCellValues(cells[0]);
  }

  function applyCellStylesFromOptions(styleMap, options, mode) {
    if (mode === 'column') {
      styleMap.width = String(options.width || '').trim();
      styleMap['text-align'] = String(options.textAlign || '').trim();
      styleMap['vertical-align'] = String(options.verticalAlign || '').trim();
      return;
    }

    if (mode === 'row') {
      styleMap.height = String(options.height || '').trim();
      styleMap['text-align'] = String(options.textAlign || '').trim();
      styleMap['vertical-align'] = String(options.verticalAlign || '').trim();
      return;
    }

    styleMap.width = String(options.width || '').trim();
    styleMap.height = String(options.height || '').trim();
    styleMap['text-align'] = String(options.textAlign || '').trim();
    styleMap['vertical-align'] = String(options.verticalAlign || '').trim();
  }

  function setOrRemoveAttribute(writer, element, attrName, value) {
    if (!element) {
      return;
    }
    if (typeof value === 'string' && value.trim() !== '') {
      writer.setAttribute(attrName, value.trim(), element);
    } else {
      writer.removeAttribute(attrName, element);
    }
  }

  function splitClassNames(value) {
    if (typeof value !== 'string') {
      return [];
    }
    return value.split(/\s+/).map(function (item) {
      return item.trim();
    }).filter(function (item) {
      return item !== '';
    });
  }

  function applyClassTokensToView(writer, viewElement, oldValue, newValue) {
    if (!writer || !viewElement) {
      return;
    }

    var oldTokens = splitClassNames(oldValue);
    var newTokens = splitClassNames(newValue);

    if (oldTokens.length > 0) {
      writer.removeClass(oldTokens, viewElement);
    }
    if (newTokens.length > 0) {
      writer.addClass(newTokens, viewElement);
    }
  }

  function findInnerTableViewElement(viewElement) {
    if (!viewElement || !viewElement.getChildren) {
      return null;
    }

    var children = Array.from(viewElement.getChildren());
    for (var i = 0; i < children.length; i += 1) {
      var child = children[i];
      if (child && child.name === 'table') {
        return child;
      }
    }

    return null;
  }

  function registerClassAttributeConversion(editor, modelAttribute, modelElementName, mirrorToInnerTable) {
    if (!editor || !editor.conversion) {
      return;
    }

    var eventName = 'attribute:' + modelAttribute + ':' + modelElementName;

    editor.conversion.for('editingDowncast').add(function (dispatcher) {
      dispatcher.on(eventName, function (evt, data, conversionApi) {
        var viewElement = conversionApi.mapper.toViewElement(data.item);
        applyClassTokensToView(conversionApi.writer, viewElement, data.attributeOldValue, data.attributeNewValue);
        if (mirrorToInnerTable === true) {
          var innerTable = findInnerTableViewElement(viewElement);
          applyClassTokensToView(conversionApi.writer, innerTable, data.attributeOldValue, data.attributeNewValue);
        }
      });
    });

    editor.conversion.for('dataDowncast').add(function (dispatcher) {
      dispatcher.on(eventName, function (evt, data, conversionApi) {
        var viewElement = conversionApi.mapper.toViewElement(data.item);
        applyClassTokensToView(conversionApi.writer, viewElement, data.attributeOldValue, data.attributeNewValue);
        if (mirrorToInnerTable === true) {
          var innerTable = findInnerTableViewElement(viewElement);
          applyClassTokensToView(conversionApi.writer, innerTable, data.attributeOldValue, data.attributeNewValue);
        }
      });
    });
  }

  function applyStyleTokensToView(writer, viewElement, oldValue, newValue) {
    if (!writer || !viewElement) {
      return;
    }

    var oldStyles = parseStyleString(String(oldValue || ''));
    var newStyles = parseStyleString(String(newValue || ''));

    Object.keys(oldStyles).forEach(function (name) {
      writer.removeStyle(name, viewElement);
    });

    Object.keys(newStyles).forEach(function (name) {
      writer.setStyle(name, newStyles[name], viewElement);
    });
  }

  function registerStyleAttributeConversion(editor, modelAttribute, modelElementName, mirrorToInnerTable) {
    if (!editor || !editor.conversion) {
      return;
    }

    var eventName = 'attribute:' + modelAttribute + ':' + modelElementName;

    editor.conversion.for('editingDowncast').add(function (dispatcher) {
      dispatcher.on(eventName, function (evt, data, conversionApi) {
        var viewElement = conversionApi.mapper.toViewElement(data.item);
        applyStyleTokensToView(conversionApi.writer, viewElement, data.attributeOldValue, data.attributeNewValue);
        if (mirrorToInnerTable === true) {
          var innerTable = findInnerTableViewElement(viewElement);
          applyStyleTokensToView(conversionApi.writer, innerTable, data.attributeOldValue, data.attributeNewValue);
        }
      });
    });

    editor.conversion.for('dataDowncast').add(function (dispatcher) {
      dispatcher.on(eventName, function (evt, data, conversionApi) {
        var viewElement = conversionApi.mapper.toViewElement(data.item);
        applyStyleTokensToView(conversionApi.writer, viewElement, data.attributeOldValue, data.attributeNewValue);
        if (mirrorToInnerTable === true) {
          var innerTable = findInnerTableViewElement(viewElement);
          applyStyleTokensToView(conversionApi.writer, innerTable, data.attributeOldValue, data.attributeNewValue);
        }
      });
    });
  }

  function ensureStyles() {
    if (typeof document === 'undefined') {
      return;
    }
    if (document.getElementById('cke5-for-table-style')) {
      return;
    }

    var style = document.createElement('style');
    style.id = 'cke5-for-table-style';
    style.textContent = ''
      + '.ck-for-table-panel{display:flex;flex-direction:column;gap:0;box-sizing:border-box;width:min(24rem,calc(100vw - 40px));min-width:17rem;max-width:calc(100vw - 40px);background:var(--ck-color-base-background,#fff);border:1px solid var(--ck-color-base-border,#d8dde4);border-radius:4px;overflow:hidden;}'
      + '.ck-for-table-panel__header{padding:12px 14px!important;border-bottom:1px solid var(--ck-color-base-border,#d8dde4);background:var(--ck-color-base-foreground,#f7f8fa);}'
      + '.ck-for-table-panel__title{font-weight:600;font-size:13px;color:#2f4057;line-height:1.3;}'
      + '.ck-for-table-panel__content{padding:14px!important;display:flex;flex-direction:column;gap:12px;}'
      + '.ck-for-table-panel__grid{display:grid;grid-template-columns:1fr;gap:12px;align-items:end;}'
      + '.ck-for-table-panel__field{display:flex;flex-direction:column;gap:6px;min-width:0;}'
      + '.ck-for-table-panel__field label{font-size:12px;color:#4f6480;line-height:1.3;}'
      + '.ck-for-table-panel__field input,.ck-for-table-panel__field select{width:100%;max-width:100%;min-width:0;box-sizing:border-box;}'
      + '.ck-for-table-panel .ck.ck-input{width:100%!important;max-width:100%!important;min-width:0!important;box-sizing:border-box;}'
      + '.ck-for-table-panel__hint{font-size:11px;color:#5f728c;line-height:1.35;}'
      + '.ck-for-table-panel__actions{display:flex;gap:8px;justify-content:flex-end;padding:12px 14px!important;border-top:1px solid #d8e0ea;background:var(--ck-color-base-foreground,#f7f8fa);}'
      + '.ck-for-table-panel .ck.ck-button{justify-content:center;}'
      + '.ck{--for-table-cell-glow-strong:rgba(255,153,51,.98);--for-table-cell-glow-soft:rgba(255,153,51,.36);}'
      + 'body.rex-theme-dark .ck{--for-table-cell-glow-strong:rgba(255,184,107,.98);--for-table-cell-glow-soft:rgba(255,184,107,.42);}'
      + '@media (prefers-color-scheme: dark){body.rex-has-theme:not(.rex-theme-light) .ck{--for-table-cell-glow-strong:rgba(255,184,107,.98);--for-table-cell-glow-soft:rgba(255,184,107,.42);}}'
      + '@keyframes for-table-cell-glow-pulse{0%{box-shadow:inset 0 0 0 2px rgba(255,153,51,.98),0 0 0 3px rgba(255,153,51,.34);}38%{box-shadow:inset 0 0 0 2px rgba(255,210,92,.98),0 0 0 3px rgba(255,210,92,.30);}72%{box-shadow:inset 0 0 0 2px rgba(95,196,255,.92),0 0 0 3px rgba(95,196,255,.28);}100%{box-shadow:inset 0 0 0 2px rgba(255,153,51,.98),0 0 0 3px rgba(255,153,51,.34);}}'
      + '.ck .ck-content .table td.ck-editor__nested-editable.ck-editor__nested-editable_focused,.ck .ck-content .table th.ck-editor__nested-editable.ck-editor__nested-editable_focused{background-color:transparent!important;box-shadow:inset 0 0 0 2px var(--for-table-cell-glow-strong),0 0 0 3px var(--for-table-cell-glow-soft)!important;animation:for-table-cell-glow-pulse 2.6s ease-in-out infinite;}'
      + '@media (prefers-reduced-motion: reduce){.ck .ck-content .table td.ck-editor__nested-editable.ck-editor__nested-editable_focused,.ck .ck-content .table th.ck-editor__nested-editable.ck-editor__nested-editable_focused{animation:none!important;}}'
      + '.ck-for-table-dialog-host{padding:12px 14px!important;box-sizing:border-box!important;width:min(42rem,calc(100vw - 32px));max-width:min(42rem,calc(100vw - 32px));}'
      + '.ck-for-table-dialog-content{display:flex!important;flex-direction:column!important;gap:12px!important;padding:0!important;}'
      + '.ck-for-table-dialog-content .ck-for-table-panel__grid{display:grid!important;grid-template-columns:1fr!important;gap:12px!important;}'
      + '.ck-for-table-dialog-content .ck-for-table-panel__field{width:100%!important;max-width:30rem!important;}'
      + '.ck-for-table-dialog-content .ck-for-table-panel__field input,.ck-for-table-dialog-content .ck-for-table-panel__field select{width:100%!important;max-width:30rem!important;}'
      + '@media (max-width: 720px){.ck-for-table-panel{width:calc(100vw - 24px);min-width:0;max-width:calc(100vw - 24px);}.ck-for-table-panel__content{padding:12px;}.ck-for-table-panel__actions{padding:10px 12px;}}';
    document.head.appendChild(style);
  }

  function createClassOptions(definitions, i18n, selected) {
    var html = '<option value="">' + escapeHtml(i18n.classDefault) + '</option>';
    definitions.forEach(function (item) {
      var value = String(item.class || '');
      var label = String(item.label || value);
      var isSelected = value === selected ? ' selected="selected"' : '';
      html += '<option value="' + escapeHtml(value) + '"' + isSelected + '>' + escapeHtml(label) + '</option>';
    });
    return html;
  }

  function createAlignOptions(i18n, selected) {
    var options = [
      { value: '', label: i18n.alignDefault },
      { value: 'left', label: i18n.alignLeft },
      { value: 'center', label: i18n.alignCenter },
      { value: 'right', label: i18n.alignRight },
      { value: 'justify', label: i18n.alignJustify }
    ];

    return options.map(function (option) {
      var isSelected = option.value === selected ? ' selected="selected"' : '';
      return '<option value="' + escapeHtml(option.value) + '"' + isSelected + '>' + escapeHtml(option.label) + '</option>';
    }).join('');
  }

  function createVerticalAlignOptions(i18n, selected) {
    var options = [
      { value: '', label: i18n.valignDefault },
      { value: 'top', label: i18n.valignTop },
      { value: 'middle', label: i18n.valignMiddle },
      { value: 'bottom', label: i18n.valignBottom }
    ];

    return options.map(function (option) {
      var isSelected = option.value === selected ? ' selected="selected"' : '';
      return '<option value="' + escapeHtml(option.value) + '"' + isSelected + '>' + escapeHtml(option.label) + '</option>';
    }).join('');
  }

  function getPanelMeta(commandName, i18n, config) {
    switch (commandName) {
      case 'forTableProperties':
        return {
          title: i18n.panelTitleTable,
          classes: config.tableClasses,
          hideManual: config.hideManualTableProperties,
          showClass: true,
          showWidth: true,
          showHeight: false,
          showAlign: true,
          showVerticalAlign: false
        };
      case 'forTableColumnProperties':
        return {
          title: i18n.panelTitleColumn,
          classes: config.columnClasses,
          hideManual: config.hideManualColumnProperties,
          showClass: true,
          showWidth: true,
          showHeight: false,
          showAlign: true,
          showVerticalAlign: true
        };
      case 'forTableRowProperties':
        return {
          title: i18n.panelTitleRow,
          classes: config.rowClasses,
          hideManual: config.hideManualRowProperties,
          showClass: true,
          showWidth: false,
          showHeight: true,
          showAlign: true,
          showVerticalAlign: true
        };
      default:
        return {
          title: i18n.panelTitleCell,
          classes: config.cellClasses,
          hideManual: config.hideManualCellProperties,
          showClass: true,
          showWidth: true,
          showHeight: true,
          showAlign: true,
          showVerticalAlign: true
        };
    }
  }

  function renderPanel(dropdown, editor, commandName, config, i18n) {
    if (!dropdown || !dropdown.panelView || !dropdown.panelView.element) {
      return;
    }

    var command = editor.commands && typeof editor.commands.get === 'function' ? editor.commands.get(commandName) : null;
    var panel = dropdown.panelView.element;
    var values = command && command.value && typeof command.value === 'object' ? command.value : {};
    var meta = getPanelMeta(commandName, i18n, config);

    panel.style.minWidth = '18rem';
    panel.style.width = 'min(22rem, calc(100vw - 24px))';
    panel.style.maxWidth = 'calc(100vw - 24px)';

    var html = '<div class="ck-for-table-panel">';
    html += '<div class="ck-for-table-panel__header"><div class="ck-for-table-panel__title">' + escapeHtml(meta.title) + '</div></div>';
    html += '<div class="ck-for-table-panel__content">';

    if (meta.showClass && meta.classes.length > 0) {
      html += '<div class="ck-for-table-panel__field">'
        + '<label>' + escapeHtml(i18n.classLabel) + '</label>'
        + '<select class="ck ck-input" data-for-table="class">'
        + createClassOptions(meta.classes, i18n, String(values.className || ''))
        + '</select>'
        + '</div>';
      html += '<div class="ck-for-table-panel__hint">' + escapeHtml(i18n.keepManualHint) + '</div>';
    }

    if (!meta.hideManual) {
      html += '<div class="ck-for-table-panel__grid">';
      if (meta.showWidth) {
        html += '<div class="ck-for-table-panel__field">'
          + '<label>' + escapeHtml(i18n.widthLabel) + '</label>'
          + '<input class="ck ck-input" data-for-table="width" type="text" placeholder="100%, 640px" value="' + escapeHtml(values.width || '') + '">'
          + '</div>';
      }

      if (meta.showHeight) {
        html += '<div class="ck-for-table-panel__field">'
          + '<label>' + escapeHtml(i18n.heightLabel) + '</label>'
          + '<input class="ck ck-input" data-for-table="height" type="text" placeholder="40px" value="' + escapeHtml(values.height || '') + '">'
          + '</div>';
      }

      if (meta.showAlign) {
        html += '<div class="ck-for-table-panel__field">'
          + '<label>' + escapeHtml(i18n.textAlignLabel) + '</label>'
          + '<select class="ck ck-input" data-for-table="align">'
          + createAlignOptions(i18n, String(values.textAlign || ''))
          + '</select>'
          + '</div>';
      }

      if (meta.showVerticalAlign) {
        html += '<div class="ck-for-table-panel__field">'
          + '<label>' + escapeHtml(i18n.verticalAlignLabel) + '</label>'
          + '<select class="ck ck-input" data-for-table="vertical-align">'
          + createVerticalAlignOptions(i18n, String(values.verticalAlign || ''))
          + '</select>'
          + '</div>';
      }
      html += '</div>';
    }

    html += '</div>';

    html += '<div class="ck-for-table-panel__actions">'
      + '<button type="button" class="ck ck-button" data-for-table-action="reset">' + escapeHtml(i18n.reset) + '</button>'
      + '<button type="button" class="ck ck-button ck-on" data-for-table-action="apply">' + escapeHtml(i18n.apply) + '</button>'
      + '</div>';
    html += '</div>';

    panel.innerHTML = html;

    var apply = panel.querySelector('[data-for-table-action="apply"]');
    var reset = panel.querySelector('[data-for-table-action="reset"]');

    function collectOptions(resetValues) {
      if (resetValues) {
        return {
          className: '',
          width: '',
          height: '',
          textAlign: '',
          verticalAlign: ''
        };
      }

      var classField = panel.querySelector('[data-for-table="class"]');
      var widthField = panel.querySelector('[data-for-table="width"]');
      var heightField = panel.querySelector('[data-for-table="height"]');
      var alignField = panel.querySelector('[data-for-table="align"]');
      var verticalAlignField = panel.querySelector('[data-for-table="vertical-align"]');

      return {
        className: classField ? String(classField.value || '').trim() : '',
        width: widthField ? String(widthField.value || '').trim() : '',
        height: heightField ? String(heightField.value || '').trim() : '',
        textAlign: alignField ? String(alignField.value || '').trim() : '',
        verticalAlign: verticalAlignField ? String(verticalAlignField.value || '').trim() : ''
      };
    }

    if (apply) {
      apply.addEventListener('click', function () {
        if (!command || !command.isEnabled) {
          return;
        }
        editor.execute(commandName, collectOptions(false));
        editor.editing.view.focus();
        dropdown.isOpen = false;
      });
    }

    if (reset) {
      reset.addEventListener('click', function () {
        if (!command || !command.isEnabled) {
          return;
        }
        editor.execute(commandName, collectOptions(true));
        editor.editing.view.focus();
        dropdown.isOpen = false;
      });
    }
  }

  function renderDialogContent(container, meta, i18n, values) {
    if (!container) {
      return;
    }

    var html = '<div class="ck-for-table-dialog-content">';

    if (meta.showClass && meta.classes.length > 0) {
      html += '<div class="ck-for-table-panel__field">'
        + '<label>' + escapeHtml(i18n.classLabel) + '</label>'
        + '<select class="ck ck-input" data-for-table="class">'
        + createClassOptions(meta.classes, i18n, String(values.className || ''))
        + '</select>'
        + '</div>';
      html += '<div class="ck-for-table-panel__hint">' + escapeHtml(i18n.keepManualHint) + '</div>';
    }

    if (!meta.hideManual) {
      html += '<div class="ck-for-table-panel__grid">';
      if (meta.showWidth) {
        html += '<div class="ck-for-table-panel__field">'
          + '<label>' + escapeHtml(i18n.widthLabel) + '</label>'
          + '<input class="ck ck-input" data-for-table="width" type="text" placeholder="100%, 640px" value="' + escapeHtml(values.width || '') + '">'
          + '</div>';
      }

      if (meta.showHeight) {
        html += '<div class="ck-for-table-panel__field">'
          + '<label>' + escapeHtml(i18n.heightLabel) + '</label>'
          + '<input class="ck ck-input" data-for-table="height" type="text" placeholder="40px" value="' + escapeHtml(values.height || '') + '">'
          + '</div>';
      }

      if (meta.showAlign) {
        html += '<div class="ck-for-table-panel__field">'
          + '<label>' + escapeHtml(i18n.textAlignLabel) + '</label>'
          + '<select class="ck ck-input" data-for-table="align">'
          + createAlignOptions(i18n, String(values.textAlign || ''))
          + '</select>'
          + '</div>';
      }

      if (meta.showVerticalAlign) {
        html += '<div class="ck-for-table-panel__field">'
          + '<label>' + escapeHtml(i18n.verticalAlignLabel) + '</label>'
          + '<select class="ck ck-input" data-for-table="vertical-align">'
          + createVerticalAlignOptions(i18n, String(values.verticalAlign || ''))
          + '</select>'
          + '</div>';
      }
      html += '</div>';
    }

    html += '</div>';
    container.innerHTML = html;
  }

  function collectOptionsFromElement(container, resetValues) {
    if (resetValues) {
      return {
        className: '',
        width: '',
        height: '',
        textAlign: '',
        verticalAlign: ''
      };
    }

    var classField = container.querySelector('[data-for-table="class"]');
    var widthField = container.querySelector('[data-for-table="width"]');
    var heightField = container.querySelector('[data-for-table="height"]');
    var alignField = container.querySelector('[data-for-table="align"]');
    var verticalAlignField = container.querySelector('[data-for-table="vertical-align"]');

    return {
      className: classField ? String(classField.value || '').trim() : '',
      width: widthField ? String(widthField.value || '').trim() : '',
      height: heightField ? String(heightField.value || '').trim() : '',
      textAlign: alignField ? String(alignField.value || '').trim() : '',
      verticalAlign: verticalAlignField ? String(verticalAlignField.value || '').trim() : ''
    };
  }

  function openPropertiesDialog(editor, cke, commandName, config, i18n) {
    var command = editor && editor.commands && typeof editor.commands.get === 'function' ? editor.commands.get(commandName) : null;
    if (!command || !command.isEnabled) {
      return;
    }

    var dialog = editor.plugins && typeof editor.plugins.get === 'function' ? editor.plugins.get('Dialog') : null;
    if (!dialog || !cke || typeof cke.View !== 'function') {
      return;
    }

    var values = command.value && typeof command.value === 'object' ? command.value : {};
    var meta = getPanelMeta(commandName, i18n, config);
    var contentView = new cke.View(editor.locale);
    contentView.setTemplate({
      tag: 'div',
      attributes: {
        class: ['ck-for-table-dialog-host']
      }
    });

    dialog.show({
      id: 'forTable:' + commandName,
      title: meta.title,
      content: contentView,
      actionButtons: [
        {
          label: i18n.reset,
          withText: true,
          onExecute: function () {
            editor.execute(commandName, collectOptionsFromElement(contentView.element, true));
            editor.editing.view.focus();
            dialog.hide();
          }
        },
        {
          label: i18n.apply,
          class: 'ck-button-action',
          withText: true,
          onExecute: function () {
            editor.execute(commandName, collectOptionsFromElement(contentView.element, false));
            editor.editing.view.focus();
            dialog.hide();
          }
        }
      ],
      onShow: function () {
        renderDialogContent(contentView.element, meta, i18n, values);
        var firstInput = contentView.element.querySelector('input,select');
        if (firstInput && typeof firstInput.focus === 'function') {
          firstInput.focus();
        }
      },
      onHide: function () {
        if (contentView && typeof contentView.destroy === 'function') {
          contentView.destroy();
        }
      }
    });
  }

  window.CKE5_NATIVE_PLUGINS.RedaxoForTable = function createRedaxoForTable(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {};
    var BaseCommand = cke && typeof cke.Command === 'function' ? cke.Command : class {
      constructor(editor) {
        this.editor = editor;
        this.isEnabled = true;
        this.value = null;
      }
      refresh() {}
      execute() {}
    };

    class ForTablePropertiesCommand extends BaseCommand {
      refresh() {
        var table = findSelectedTable(this.editor);
        this.isEnabled = !!table;
        this.value = table ? readTableValues(table) : null;
      }

      execute(options) {
        options = options || {};
        var editor = this.editor;
        var table = findSelectedTable(editor);
        if (!table) {
          return;
        }

        editor.model.change(function (writer) {
          setOrRemoveAttribute(writer, table, 'forTableClass', options.className || '');

          var styleMap = parseStyleString(String(table.getAttribute('forTableStyle') || ''));
          styleMap.width = String(options.width || '').trim();
          styleMap['text-align'] = String(options.textAlign || '').trim();

          var styleString = toStyleString(styleMap);
          setOrRemoveAttribute(writer, table, 'forTableStyle', styleString);
        });
      }
    }

    class ForTableColumnPropertiesCommand extends BaseCommand {
      refresh() {
        var cells = getCellsInSelectedColumns(this.editor);
        this.isEnabled = cells.length > 0;
        this.value = readModeValues(cells);
      }

      execute(options) {
        options = options || {};
        var editor = this.editor;
        var cells = getCellsInSelectedColumns(editor);
        if (cells.length === 0) {
          return;
        }

        editor.model.change(function (writer) {
          cells.forEach(function (cell) {
            setOrRemoveAttribute(writer, cell, 'forTableCellClass', options.className || '');

            var styleMap = parseStyleString(String(cell.getAttribute('forTableCellStyle') || ''));
            applyCellStylesFromOptions(styleMap, options, 'column');

            var styleString = toStyleString(styleMap);
            setOrRemoveAttribute(writer, cell, 'forTableCellStyle', styleString);
          });
        });
      }
    }

    class ForTableRowPropertiesCommand extends BaseCommand {
      refresh() {
        var cells = getCellsInSelectedRows(this.editor);
        this.isEnabled = cells.length > 0;
        this.value = readModeValues(cells);
      }

      execute(options) {
        options = options || {};
        var editor = this.editor;
        var cells = getCellsInSelectedRows(editor);
        if (cells.length === 0) {
          return;
        }

        editor.model.change(function (writer) {
          cells.forEach(function (cell) {
            setOrRemoveAttribute(writer, cell, 'forTableCellClass', options.className || '');

            var styleMap = parseStyleString(String(cell.getAttribute('forTableCellStyle') || ''));
            applyCellStylesFromOptions(styleMap, options, 'row');

            var styleString = toStyleString(styleMap);
            setOrRemoveAttribute(writer, cell, 'forTableCellStyle', styleString);
          });
        });
      }
    }

    class ForTableCellPropertiesCommand extends BaseCommand {
      refresh() {
        var cells = getSelectionTableCells(this.editor);
        this.isEnabled = cells.length > 0;
        this.value = cells.length > 0 ? readCellValues(cells[0]) : null;
      }

      execute(options) {
        options = options || {};
        var editor = this.editor;
        var cells = getSelectionTableCells(editor);
        if (cells.length === 0) {
          return;
        }

        editor.model.change(function (writer) {
          cells.forEach(function (cell) {
            setOrRemoveAttribute(writer, cell, 'forTableCellClass', options.className || '');

            var styleMap = parseStyleString(String(cell.getAttribute('forTableCellStyle') || ''));
            applyCellStylesFromOptions(styleMap, options, 'cell');

            var styleString = toStyleString(styleMap);
            setOrRemoveAttribute(writer, cell, 'forTableCellStyle', styleString);
          });
        });
      }
    }

    return class RedaxoForTable extends BasePlugin {
      static get pluginName() {
        return 'RedaxoForTable';
      }

      init() {
        if (!this.editor || !cke) {
          return;
        }

        ensureStyles();

        var editor = this.editor;
        var config = getConfig(editor);
        var i18n = getI18n(editor);

        if (!editor.model || !editor.model.schema || typeof editor.model.schema.extend !== 'function') {
          return;
        }

        editor.model.schema.extend('table', {
          allowAttributes: ['forTableClass', 'forTableStyle']
        });
        editor.model.schema.extend('tableCell', {
          allowAttributes: ['forTableCellClass', 'forTableCellStyle']
        });

        registerStyleAttributeConversion(editor, 'forTableStyle', 'table', true);
        editor.conversion.for('downcast').attributeToAttribute({ model: 'forTableCellStyle', view: 'style' });

        editor.conversion.for('upcast').attributeToAttribute({ view: { name: 'figure', classes: 'table', key: 'class' }, model: 'forTableClass' });
        editor.conversion.for('upcast').attributeToAttribute({ view: { name: 'figure', classes: 'table', key: 'style' }, model: 'forTableStyle' });
        editor.conversion.for('upcast').attributeToAttribute({ view: { name: 'table', key: 'class' }, model: 'forTableClass' });
        editor.conversion.for('upcast').attributeToAttribute({ view: { name: 'table', key: 'style' }, model: 'forTableStyle' });
        editor.conversion.for('upcast').attributeToAttribute({ view: { name: 'td', key: 'class' }, model: 'forTableCellClass' });
        editor.conversion.for('upcast').attributeToAttribute({ view: { name: 'td', key: 'style' }, model: 'forTableCellStyle' });
        editor.conversion.for('upcast').attributeToAttribute({ view: { name: 'th', key: 'class' }, model: 'forTableCellClass' });
        editor.conversion.for('upcast').attributeToAttribute({ view: { name: 'th', key: 'style' }, model: 'forTableCellStyle' });

        registerClassAttributeConversion(editor, 'forTableClass', 'table', true);
        registerClassAttributeConversion(editor, 'forTableCellClass', 'tableCell', false);

        editor.commands.add('forTableProperties', new ForTablePropertiesCommand(editor));
        editor.commands.add('forTableColumnProperties', new ForTableColumnPropertiesCommand(editor));
        editor.commands.add('forTableRowProperties', new ForTableRowPropertiesCommand(editor));
        editor.commands.add('forTableCellProperties', new ForTableCellPropertiesCommand(editor));

        if (!editor.ui || !editor.ui.componentFactory) {
          return;
        }

        var factory = editor.ui.componentFactory;

        function createPropertiesButton(locale, commandName, label, icon) {
          var command = editor.commands.get(commandName);
          var dropdownFactory = typeof cke.createDropdown === 'function'
            ? cke.createDropdown
            : (typeof window !== 'undefined' && window.CKEDITOR && typeof window.CKEDITOR.createDropdown === 'function'
              ? window.CKEDITOR.createDropdown
              : null);
          var ButtonCtor = typeof cke.ButtonView === 'function'
            ? cke.ButtonView
            : (typeof window !== 'undefined' && window.CKEDITOR && typeof window.CKEDITOR.ButtonView === 'function'
              ? window.CKEDITOR.ButtonView
              : null);

          if (typeof dropdownFactory === 'function') {
            var dropdown = dropdownFactory(locale);
            dropdown.buttonView.set({
              label: label,
              icon: icon,
              withText: false,
              tooltip: true
            });

            var updateDropdown = function () {
              dropdown.isEnabled = !!(command && command.isEnabled);
              if (dropdown.isOpen) {
                renderPanel(dropdown, editor, commandName, config, i18n);
              }
            };

            dropdown.on('change:isOpen', function () {
              if (dropdown.isOpen) {
                window.requestAnimationFrame(function () {
                  renderPanel(dropdown, editor, commandName, config, i18n);
                });
              }
            });

            if (command && typeof command.on === 'function') {
              command.on('change:isEnabled', updateDropdown);
              command.on('change:value', updateDropdown);
            }
            if (editor.model && editor.model.document && editor.model.document.selection) {
              editor.model.document.selection.on('change:range', function () {
                if (command && typeof command.refresh === 'function') {
                  command.refresh();
                }
                updateDropdown();
              });
            }

            if (command && typeof command.refresh === 'function') {
              command.refresh();
            }
            updateDropdown();

            return dropdown;
          }

          if (typeof ButtonCtor !== 'function') {
            return null;
          }

          var button = new ButtonCtor(locale);
          button.set({
            label: label,
            icon: icon,
            withText: false,
            tooltip: true
          });

          var updateButton = function () {
            button.isEnabled = !!(command && command.isEnabled);
          };

          button.on('execute', function () {
            if (command && command.isEnabled) {
              editor.execute(commandName, {
                className: '',
                width: '',
                height: '',
                textAlign: '',
                verticalAlign: ''
              });
              editor.editing.view.focus();
            }
          });

          if (command && typeof command.on === 'function') {
            command.on('change:isEnabled', updateButton);
          }
          if (editor.model && editor.model.document && editor.model.document.selection) {
            editor.model.document.selection.on('change:range', function () {
              if (command && typeof command.refresh === 'function') {
                command.refresh();
              }
              updateButton();
            });
          }

          if (command && typeof command.refresh === 'function') {
            command.refresh();
          }
          updateButton();

          return button;
        }

        if (!factory.has('forTableProperties')) {
          factory.add('forTableProperties', function (locale) {
            return createPropertiesButton(locale, 'forTableProperties', i18n.tableButton, TABLE_ICON);
          });
        }

        if (!factory.has('forTableColumnProperties')) {
          factory.add('forTableColumnProperties', function (locale) {
            return createPropertiesButton(locale, 'forTableColumnProperties', i18n.columnButton, COLUMN_ICON);
          });
        }

        if (!factory.has('forTableRowProperties')) {
          factory.add('forTableRowProperties', function (locale) {
            return createPropertiesButton(locale, 'forTableRowProperties', i18n.rowButton, ROW_ICON);
          });
        }

        if (!factory.has('forTableCellProperties')) {
          factory.add('forTableCellProperties', function (locale) {
            return createPropertiesButton(locale, 'forTableCellProperties', i18n.cellButton, CELL_ICON);
          });
        }
      }
    };
  };
})();
