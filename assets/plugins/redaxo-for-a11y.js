/**
 * redaxo-for-a11y.js – Barrierefreiheits-Check für CKEditor 5 (FriendsOfREDAXO)
 *
 * Portierung des TinyMCE for_a11y-Plugins auf das CKE5-Native-Plugin-System.
 * Analyse läuft auf editor.getData()-HTML; Navigation + Highlight im Live-DOM.
 * Quickfixes via editor.setData() nach DOM-Patch.
 *
 * Toolbar-Token: for_a11y
 */
(function () {
  'use strict';

  window.CKE5_NATIVE_PLUGINS = window.CKE5_NATIVE_PLUGINS || {};

  /* ─────────────── Konstanten ─────────────── */

  var DEFAULT_GENERIC_LINK_TEXTS = [
    'hier', 'klick hier', 'klicken', 'klicke hier', 'klicken sie hier',
    'mehr', 'mehr erfahren', 'mehr lesen',
    'weiter', 'weiterlesen', 'weiter lesen',
    'link', 'dieser link', 'mehr infos', 'infos',
    'read more', 'click here', 'more', 'here', 'learn more', 'details'
  ];

  var SEVERITY_ORDER = { error: 0, warn: 1, info: 2 };
  var SEVERITY_LABEL = { error: 'Fehler', warn: 'Warnung', info: 'Hinweis' };
  var SEVERITY_COLOR = { error: '#c62828', warn: '#ef6c00', info: '#1565c0' };
  var SEVERITY_BG    = { error: 'rgba(229,57,53,.12)', warn: 'rgba(251,140,0,.14)', info: 'rgba(30,136,229,.12)' };

  var FOR_A11Y_ICON = "<svg viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'><path fill='#1976d2' d='M12 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z'/></svg>";

  /* ─────────────── Analyse-Helpers ─────────────── */

  function truncate(s, n) {
    n = n || 80;
    var t = String(s || '').replace(/\s+/g, ' ').trim();
    return t.length > n ? t.slice(0, n - 1) + '…' : t;
  }

  function escHtml(s) {
    return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function shortHtml(el) {
    var tag = el.tagName.toLowerCase();
    var txt = truncate(el.textContent || '', 60);
    if (tag === 'img') {
      var src = el.getAttribute('src') || '';
      var alt = el.getAttribute('alt');
      return '<img src="' + truncate(src, 40) + '"' + (alt === null ? '' : ' alt="' + truncate(alt, 20) + '"') + '>';
    }
    if (tag === 'a') {
      var href = el.getAttribute('href') || '';
      return '<a href="' + truncate(href, 40) + '">' + (txt || '(leer)') + '</a>';
    }
    if (tag === 'table') return '<table>…</table>';
    return '<' + tag + '>' + (txt || '(leer)') + '</' + tag + '>';
  }

  function hasVisibleText(el) {
    var clone = el.cloneNode(true);
    Array.from(clone.querySelectorAll('img')).forEach(function (n) { n.remove(); });
    return ((clone.textContent || '').trim().length > 0);
  }

  function accessibleName(el) {
    var label = (el.getAttribute('aria-label') || '').trim();
    if (label) return label;
    var title = (el.getAttribute('title') || '').trim();
    var text  = (el.textContent || '').trim();
    if (text) return text;
    if (title) return title;
    var img = el.querySelector('img');
    if (img) { var alt = (img.getAttribute('alt') || '').trim(); if (alt) return alt; }
    return '';
  }

  function isInTextLink(img) {
    var link = img.closest('a');
    if (!link) return false;
    return hasVisibleText(link);
  }

  /* ─────────────── Haupt-Analyse ─────────────── */

  function runAudit(body) {
    var findings = [];

    /* Bilder */
    Array.from(body.querySelectorAll('img')).forEach(function (img) {
      var hasAlt      = img.hasAttribute('alt');
      var alt         = img.getAttribute('alt') || '';
      var role        = (img.getAttribute('role') || '').trim().toLowerCase();
      var isDecorative = role === 'presentation' || role === 'none';
      var inTextLink  = isInTextLink(img);

      if (!hasAlt) {
        findings.push({
          id: inTextLink ? 'img-missing-alt-in-link' : 'img-missing-alt',
          severity: inTextLink ? 'warn' : 'error',
          title: inTextLink ? 'Bild in Textlink ohne alt-Attribut' : 'Bild ohne alt-Attribut',
          message: inTextLink
            ? 'Das Bild ist in einem Link mit sichtbarem Text. Setze alt="" (leeres alt), damit Screenreader den Link-Text nicht doppelt ausgeben.'
            : 'Jedes Bild braucht ein alt-Attribut. Für rein dekorative Bilder: alt="" oder role="presentation".',
          element: img, preview: shortHtml(img)
        });
        return;
      }

      var altTrim = alt.trim();
      if (hasAlt && altTrim.length > 0 && inTextLink) {
        findings.push({ id: 'img-alt-in-text-link', severity: 'warn',
          title: 'Bild in Textlink mit gefülltem alt',
          message: 'Ein leeres alt="" ist hier meist besser, damit der Link-Text nicht verdoppelt wird.',
          element: img, preview: shortHtml(img) });
        return;
      }
      if (hasAlt && altTrim.length === 0 && !inTextLink && !isDecorative) {
        findings.push({ id: 'img-empty-alt', severity: 'warn',
          title: 'Leeres alt-Attribut ohne Dekorations-Kontext',
          message: 'Leeres alt ist nur für rein dekorative Bilder gedacht. Ist das Bild informativ, ergänze einen beschreibenden alt-Text.',
          element: img, preview: shortHtml(img) });
      }
      if (altTrim.length > 150) {
        findings.push({ id: 'img-alt-too-long', severity: 'warn',
          title: 'alt-Text zu lang (' + altTrim.length + ' Zeichen)',
          message: 'Halte alt-Texte prägnant (< 150 Zeichen). Sehr lange Beschreibungen gehören in den Fließtext oder eine Bildunterschrift.',
          element: img, preview: shortHtml(img) });
      }
      if (/^(img[_-]?\d+|dsc[_-]?\d+|screenshot[\s_-]|bild[_\s-]?\d+|foto[_\s-]?\d+|[a-z0-9_-]+\.(jpe?g|png|gif|webp|svg|avif))$/i.test(altTrim)) {
        findings.push({ id: 'img-alt-filename', severity: 'warn',
          title: 'alt-Text sieht wie ein Dateiname aus',
          message: 'Der alt-Text „' + truncate(altTrim, 50) + '" wirkt wie ein Dateiname. Beschreibe stattdessen, was auf dem Bild zu sehen ist.',
          element: img, preview: shortHtml(img) });
      }
      if (/^(bild|foto|grafik|abbildung|image|picture|photo)\s+(von|mit|eines?|einer|der|des|the|of)\b/i.test(altTrim)) {
        findings.push({ id: 'img-alt-redundant', severity: 'info',
          title: 'Redundanter Präfix im alt-Text',
          message: 'Screenreader kündigen Bilder bereits als „Grafik" an. Präfixe wie „Bild von …" sind doppelt.',
          element: img, preview: shortHtml(img) });
      }
    });

    /* Links */
    var links = Array.from(body.querySelectorAll('a[href]'));
    var textMap = Object.create(null);
    links.forEach(function (a) {
      var accName    = accessibleName(a);
      var text       = (a.textContent || '').trim();
      var normalized = text.toLowerCase();
      var href       = (a.getAttribute('href') || '').trim();

      if (!accName) {
        findings.push({ id: 'link-no-accname', severity: 'error',
          title: 'Link ohne Beschriftung',
          message: 'Der Link hat keinen sichtbaren Text, kein aria-label und kein Bild mit alt. Screenreader können das Ziel nicht benennen.',
          element: a, preview: shortHtml(a) });
        return;
      }
      if (text && DEFAULT_GENERIC_LINK_TEXTS.indexOf(normalized) !== -1) {
        findings.push({ id: 'link-generic-text', severity: 'warn',
          title: 'Generischer Linktext: „' + truncate(text, 30) + '"',
          message: 'Linktexte sollten das Ziel beschreiben, damit sie auch aus dem Kontext gerissen verständlich sind.',
          element: a, preview: shortHtml(a) });
      }
      if ((a.getAttribute('target') || '').toLowerCase() === '_blank') {
        var hint = /neue[rm]?\s*(fenster|tab)|new\s*(window|tab)|öffnet in|opens in/;
        var aria = (a.getAttribute('aria-label') || '') + ' ' + (a.getAttribute('title') || '');
        if (!hint.test(normalized) && !hint.test(aria.toLowerCase())) {
          findings.push({ id: 'link-new-window', severity: 'info',
            title: 'Link öffnet in neuem Fenster',
            message: 'Gib Nutzer:innen einen Hinweis im Linktext, aria-label oder title (z. B. „(öffnet in neuem Fenster)").',
            element: a, preview: shortHtml(a) });
        }
      }
      if (text && /^(https?:\/\/|www\.)\S+$/i.test(text)) {
        findings.push({ id: 'link-raw-url', severity: 'warn',
          title: 'URL als Linktext',
          message: 'Screenreader lesen URLs Zeichen für Zeichen vor. Ersetze den Linktext durch eine kurze Beschreibung des Ziels.',
          element: a, preview: shortHtml(a) });
      }
      if (text && href) {
        var key = text.toLowerCase().replace(/\s+/g, ' ');
        if (!textMap[key]) textMap[key] = [];
        textMap[key].push(href);
      }
      var m = href.match(/\.(pdf|docx?|xlsx?|pptx?|odt|zip|epub|csv)(?:$|[?#])/i);
      if (m && text) {
        var fmt = m[1].toUpperCase().replace(/^DOCX?$/, 'DOC').replace(/^XLSX?$/, 'XLS');
        var hay = (text + ' ' + (a.getAttribute('aria-label') || '')).toLowerCase();
        if (hay.indexOf(fmt.toLowerCase()) === -1 && hay.indexOf(m[1].toLowerCase()) === -1) {
          findings.push({ id: 'link-file-format', severity: 'info',
            title: 'Download-Link ohne Format-Hinweis (' + fmt + ')',
            message: 'Ergänze das Format im Linktext (z. B. „Jahresbericht 2023 (' + fmt + ')"), damit Nutzer:innen wissen, was sie herunterladen.',
            element: a, preview: shortHtml(a) });
        }
      }
    });
    Object.keys(textMap).forEach(function (key) {
      var hrefs = textMap[key];
      var unique = hrefs.filter(function (v, i, a) { return a.indexOf(v) === i; });
      if (unique.length > 1) {
        var el = body.querySelector('a');
        links.forEach(function (a) { if ((a.textContent || '').trim().toLowerCase().replace(/\s+/g, ' ') === key) el = a; });
        findings.push({ id: 'link-duplicate-text', severity: 'info',
          title: 'Gleicher Linktext – ' + unique.length + ' verschiedene Ziele',
          message: 'Der Linktext „' + truncate(key, 40) + '" zeigt an ' + unique.length + ' Stellen auf unterschiedliche Seiten.',
          element: el, preview: shortHtml(el) });
      }
    });

    /* Überschriften */
    var headings = Array.from(body.querySelectorAll('h1, h2, h3, h4, h5, h6'));
    var prevLevel = 0;
    headings.forEach(function (h) {
      var level = parseInt(h.tagName.substring(1), 10);
      var text  = (h.textContent || '').trim();
      if (!text) {
        findings.push({ id: 'heading-empty', severity: 'warn',
          title: 'Leere ' + h.tagName + '-Überschrift',
          message: 'Überschriften sollten nicht leer sein.',
          element: h, preview: shortHtml(h) });
      }
      if (prevLevel > 0 && level > prevLevel + 1) {
        findings.push({ id: 'heading-skip', severity: 'warn',
          title: 'Hierarchie-Sprung: ' + h.tagName + ' nach h' + prevLevel,
          message: 'Überschriften sollten nicht mehr als eine Ebene überspringen (von h' + prevLevel + ' direkt zu ' + h.tagName + ').',
          element: h, preview: shortHtml(h) });
      }
      var letters = text.replace(/[^\p{L}]/gu, '');
      if (letters.length >= 6 && letters === letters.toUpperCase() && letters !== letters.toLowerCase()) {
        findings.push({ id: 'heading-allcaps', severity: 'warn',
          title: h.tagName + ' komplett in VERSALIEN',
          message: 'Schreibe die Überschrift in normaler Gross-/Kleinschreibung. Für Versalien-Optik: CSS text-transform im Frontend.',
          element: h, preview: shortHtml(h) });
      }
      prevLevel = level;
    });

    /* Absätze */
    var paragraphs = Array.from(body.querySelectorAll('p'));
    var blankRun = [];
    function flushBlank() {
      if (blankRun.length >= 2) {
        findings.push({ id: 'blank-paragraphs', severity: 'info',
          title: blankRun.length + ' leere Absätze hintereinander',
          message: 'Leere Absätze werden von Screenreadern als „leer" angekündigt. Entferne sie und erzeuge Abstände via CSS.',
          element: blankRun[0], preview: shortHtml(blankRun[0]) });
      }
      blankRun = [];
    }
    paragraphs.forEach(function (p) {
      var plain = (p.textContent || '').replace(/\u00A0/g, ' ').trim();
      if (!plain) { blankRun.push(p); return; }
      flushBlank();

      if (/ {2,}/.test((p.textContent || '').replace(/\u00A0/g, ' '))) {
        findings.push({ id: 'text-too-many-spaces', severity: 'info',
          title: 'Mehrfach-Leerzeichen im Absatz',
          message: 'Mehrere aufeinanderfolgende Leerzeichen erschweren das Lesen. Reduziere sie auf ein Leerzeichen.',
          element: p, preview: shortHtml(p) });
      }
      if (plain.length <= 120) {
        var hasNonBold = Array.from(p.childNodes).some(function (n) {
          if (n.nodeType === 3) return (n.nodeValue || '').trim().length > 0;
          if (n.nodeType !== 1) return false;
          var tag = n.tagName.toLowerCase();
          if (tag === 'strong' || tag === 'b' || tag === 'br' || tag === 'wbr') return false;
          return (n.textContent || '').trim().length > 0;
        });
        if (!hasNonBold && p.querySelector('strong, b') && !/[.!?…]$/.test(plain)) {
          findings.push({ id: 'text-bold-as-heading', severity: 'warn',
            title: 'Fetter Absatz als Pseudo-Überschrift',
            message: 'Ein fett gesetzter Absatz ohne Satzzeichen wirkt wie eine Überschrift, ist für Screenreader aber Fließtext. Wandle ihn in eine echte Überschrift (h2/h3/…) um.',
            element: p, preview: shortHtml(p) });
        }
      }
      if (/^\s*([-*•·●○►▶→]|\d{1,2}[.\)]|[a-z][.\)])\s+/i.test(plain) && !p.closest('ul, ol')) {
        findings.push({ id: 'list-fake', severity: 'info',
          title: 'Absatz beginnt wie ein Listeneintrag',
          message: 'Nutze den Listen-Button (Aufzählung/Nummerierung), damit Screenreader die Liste als solche erkennen.',
          element: p, preview: shortHtml(p) });
      }
    });
    flushBlank();

    /* Listen */
    Array.from(body.querySelectorAll('ul, ol')).forEach(function (l) {
      var items = Array.from(l.children).filter(function (c) { return c.tagName.toLowerCase() === 'li'; });
      if (items.length === 1) {
        findings.push({ id: 'list-single-item', severity: 'info',
          title: l.tagName.toLowerCase() + '-Liste mit nur einem Eintrag',
          message: 'Eine Liste mit einem einzigen Eintrag ist semantisch unnötig. Wandle sie in einen normalen Absatz um.',
          element: l, preview: shortHtml(l) });
      }
    });

    /* Tabellen */
    Array.from(body.querySelectorAll('table')).forEach(function (t) {
      var ths     = t.querySelectorAll('th');
      var caption = t.querySelector('caption');
      if (!ths.length) {
        findings.push({ id: 'table-no-th', severity: 'warn',
          title: 'Tabelle ohne <th>',
          message: 'Datentabellen brauchen mindestens eine Kopfzelle (<th>), damit Screenreader Zeilen/Spalten ordnen können.',
          element: t, preview: shortHtml(t) });
      }
      if (!caption) {
        findings.push({ id: 'table-no-caption', severity: 'info',
          title: 'Tabelle ohne <caption>',
          message: 'Eine <caption> beschreibt den Inhalt der Tabelle und hilft bei der Orientierung.',
          element: t, preview: shortHtml(t) });
      }
    });

    /* iframes */
    Array.from(body.querySelectorAll('iframe')).forEach(function (f) {
      if (!(f.getAttribute('title') || '').trim()) {
        findings.push({ id: 'iframe-no-title', severity: 'warn',
          title: 'iframe ohne title',
          message: 'iframes brauchen ein title-Attribut, das den Inhalt für Screenreader beschreibt.',
          element: f, preview: shortHtml(f) });
      }
    });

    findings.sort(function (a, b) {
      var s = SEVERITY_ORDER[a.severity] - SEVERITY_ORDER[b.severity];
      if (s !== 0) return s;
      var pos = a.element.compareDocumentPosition(b.element);
      if (pos & Node.DOCUMENT_POSITION_FOLLOWING) return -1;
      if (pos & Node.DOCUMENT_POSITION_PRECEDING)  return 1;
      return 0;
    });

    return findings;
  }

  /* ─────────────── Navigation im Live-Editor-DOM ─────────────── */

  function findInEditableDom(editable, dataEl) {
    var tag = dataEl.tagName.toLowerCase();
    // Suche per uniquem Merkmal
    if (tag === 'img') {
      var src = (dataEl.getAttribute('src') || '').replace(/^\.\.\//, '');
      var found = null;
      Array.from(editable.querySelectorAll('img')).some(function (img) {
        var s = (img.getAttribute('src') || '');
        if (s.indexOf(src.split('/').pop()) !== -1) { found = img; return true; }
        return false;
      });
      return found;
    }
    if (tag === 'a') {
      var href = dataEl.getAttribute('href') || '';
      var txt  = (dataEl.textContent || '').trim();
      var found = null;
      Array.from(editable.querySelectorAll('a[href]')).some(function (a) {
        if (a.getAttribute('href') === href && (a.textContent || '').trim() === txt) { found = a; return true; }
        return false;
      });
      return found;
    }
    if (/^h[1-6]$/.test(tag)) {
      var txt = (dataEl.textContent || '').trim();
      var found = null;
      Array.from(editable.querySelectorAll(tag)).some(function (h) {
        if ((h.textContent || '').trim() === txt) { found = h; return true; }
        return false;
      });
      return found;
    }
    if (tag === 'p') {
      var txt = (dataEl.textContent || '').trim().slice(0, 60);
      var found = null;
      Array.from(editable.querySelectorAll('p')).some(function (p) {
        if ((p.textContent || '').trim().slice(0, 60) === txt) { found = p; return true; }
        return false;
      });
      return found;
    }
    if (tag === 'table') {
      var dataTables = Array.from(dataEl.ownerDocument.querySelectorAll('table'));
      var idx = dataTables.indexOf(dataEl);
      var editTables = editable.querySelectorAll('table');
      return editTables[idx] || null;
    }
    return null;
  }

  var currentHighlight = null;
  function highlightElement(editable, dataEl) {
    if (currentHighlight) {
      currentHighlight.style.outline = '';
      currentHighlight.style.backgroundColor = '';
      currentHighlight = null;
    }
    var el = findInEditableDom(editable, dataEl);
    if (!el) return;
    el.style.outline = '3px solid #e91e63';
    el.style.backgroundColor = 'rgba(233,30,99,.08)';
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    currentHighlight = el;
  }

  /* ─────────────── Quickfixes (via setData) ─────────────── */

  function applyQuickfix(finding, editor) {
    var html = editor.getData();
    var doc  = new DOMParser().parseFromString(html, 'text/html');
    var tag  = finding.element.tagName.toLowerCase();
    var fixed = false;

    if (finding.id === 'blank-paragraphs') {
      // Alle leeren Absätze am Fundort entfernen
      Array.from(doc.body.querySelectorAll('p')).forEach(function (p) {
        if (!(p.textContent || '').trim()) p.remove();
      });
      fixed = true;
    }

    if (finding.id === 'text-too-many-spaces') {
      var txt = (finding.element.textContent || '').trim().slice(0, 50);
      Array.from(doc.body.querySelectorAll('p')).forEach(function (p) {
        if ((p.textContent || '').trim().slice(0, 50) === txt) {
          var collapse = function (node) {
            if (node.nodeType === 3) { node.textContent = (node.textContent || '').replace(/ {2,}/g, ' '); return; }
            node.childNodes.forEach(collapse);
          };
          collapse(p);
        }
      });
      fixed = true;
    }

    if (finding.id === 'list-single-item') {
      var idx = Array.from(finding.element.ownerDocument.querySelectorAll('ul, ol')).indexOf(finding.element);
      var lists = doc.body.querySelectorAll('ul, ol');
      var l = lists[idx];
      if (l) {
        var li = l.querySelector('li');
        if (li) {
          var p = doc.createElement('p');
          p.innerHTML = li.innerHTML;
          l.parentNode.replaceChild(p, l);
          fixed = true;
        }
      }
    }

    if (finding.id === 'list-fake' && tag === 'p') {
      var srcTxt = (finding.element.textContent || '').trim().slice(0, 60);
      Array.from(doc.body.querySelectorAll('p')).forEach(function (p) {
        if ((p.textContent || '').trim().slice(0, 60) !== srcTxt) return;
        var m = (p.textContent || '').trim().match(/^([-*•·●○►▶→]|\d{1,2}[.\)]|[a-z][.\)])\s+(.*)$/i);
        if (!m) return;
        var isOl = /^(\d{1,2}[.\)]|[a-z][.\)])$/i.test(m[1]);
        var ul = doc.createElement(isOl ? 'ol' : 'ul');
        var li = doc.createElement('li');
        li.textContent = m[2];
        ul.appendChild(li);
        p.parentNode.replaceChild(ul, p);
        fixed = true;
      });
    }

    if (fixed) {
      editor.setData(doc.body.innerHTML);
      return true;
    }
    return false;
  }

  var QUICKFIX_IDS = {
    'blank-paragraphs': 'Leere Absätze entfernen',
    'text-too-many-spaces': 'Leerzeichen reduzieren',
    'list-single-item': 'In Absatz umwandeln',
    'list-fake': 'In echte Liste umwandeln'
  };

  /* ─────────────── Panel-UI ─────────────── */

  var panel = null;

  function removePanel() {
    if (panel && panel.parentNode) panel.parentNode.removeChild(panel);
    panel = null;
    if (currentHighlight) {
      currentHighlight.style.outline = '';
      currentHighlight.style.backgroundColor = '';
      currentHighlight = null;
    }
  }

  var PANEL_CSS = [
    '.for-a11y-ck-panel{position:fixed;z-index:9999;width:430px;max-width:calc(100vw - 20px);',
    'background:#fff;color:#222;border-radius:8px;',
    'box-shadow:0 10px 40px rgba(0,0,0,.22),0 2px 8px rgba(0,0,0,.12);',
    'font:13px/1.5 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;',
    'display:flex;flex-direction:column;user-select:none;}',

    '.for-a11y-ck-panel__drag{display:flex;align-items:center;gap:8px;padding:8px 12px;',
    'background:linear-gradient(135deg,#1565c0,#1976d2);color:#fff;',
    'border-top-left-radius:8px;border-top-right-radius:8px;cursor:move;',
    'font-weight:600;font-size:12px;letter-spacing:.3px;}',
    '.for-a11y-ck-panel__drag-title{flex:1 1 auto;}',
    '.for-a11y-ck-panel__drag-icon{display:flex;align-items:center;flex-shrink:0;}',
    '.for-a11y-ck-panel__drag-icon svg{width:18px;height:18px;}',
    '.for-a11y-ck-panel__close{background:0;border:0;color:#fff;cursor:pointer;font-size:18px;padding:0 4px;line-height:1;opacity:.85;}',
    '.for-a11y-ck-panel__close:hover{opacity:1;}',

    '.for-a11y-ck-panel__body{padding:14px 16px;user-select:text;max-height:50vh;overflow:auto;}',

    '.for-a11y-ck-panel__foot{padding:8px 12px;border-top:1px solid #eee;',
    'display:flex;gap:6px;flex-wrap:wrap;align-items:center;background:#fafafa;',
    'border-bottom-left-radius:8px;border-bottom-right-radius:8px;}',

    '.for-a11y-ck-btn{font:inherit;padding:6px 12px;border:1px solid #d0d0d0;',
    'background:#fff;color:#222;border-radius:4px;cursor:pointer;font-size:13px;line-height:1.2;}',
    '.for-a11y-ck-btn:hover:not(:disabled){background:#f0f0f0;}',
    '.for-a11y-ck-btn:disabled{opacity:.4;cursor:not-allowed;}',
    '.for-a11y-ck-btn--nav{min-width:36px;padding:6px 10px;}',
    '.for-a11y-ck-btn--fix{background:#1976d2;color:#fff;border-color:#1976d2;font-weight:600;}',
    '.for-a11y-ck-btn--fix:hover:not(:disabled){background:#1565c0;border-color:#1565c0;}',
    '.for-a11y-ck-spacer{flex:1 1 auto;}',

    '.for-a11y-ck-progress{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.6;margin-bottom:8px;}',
    '.for-a11y-ck-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;',
    'text-transform:uppercase;letter-spacing:.5px;padding:3px 10px;border-radius:999px;margin-bottom:8px;}',
    '.for-a11y-ck-title{margin:0 0 8px;font-size:15px;font-weight:600;}',
    '.for-a11y-ck-rule{font-size:10px;opacity:.5;font-family:Menlo,Consolas,monospace;margin-bottom:10px;display:block;}',
    '.for-a11y-ck-msg{margin:0 0 10px;font-size:13px;line-height:1.55;}',
    '.for-a11y-ck-preview-label{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.6;margin-bottom:4px;}',
    '.for-a11y-ck-preview{margin:0;font-family:Menlo,Consolas,monospace;font-size:11px;',
    'background:#f6f6f6;color:#222;padding:8px 10px;border-radius:4px;white-space:pre-wrap;word-break:break-all;border-left:3px solid #bbb;}',

    '.for-a11y-ck-ok{text-align:center;padding:20px 0;}',
    '.for-a11y-ck-ok__icon{font-size:40px;margin-bottom:8px;}',
    '.for-a11y-ck-ok h3{margin:0 0 4px;font-size:16px;font-weight:600;}',
    '.for-a11y-ck-ok p{margin:0;opacity:.7;font-size:12px;}',

    'body.rex-theme-dark .for-a11y-ck-panel{background:#2d2d2d;color:#eee;}',
    'body.rex-theme-dark .for-a11y-ck-panel__foot{background:#222;border-top-color:#3a3a3a;}',
    'body.rex-theme-dark .for-a11y-ck-btn{background:#3a3a3a;color:#eee;border-color:#4a4a4a;}',
    'body.rex-theme-dark .for-a11y-ck-btn:hover:not(:disabled){background:#4a4a4a;}',
    'body.rex-theme-dark .for-a11y-ck-preview{background:#222;color:#eee;}',
    '@media(prefers-color-scheme:dark){',
    'body.rex-has-theme:not(.rex-theme-light) .for-a11y-ck-panel{background:#2d2d2d;color:#eee;}',
    'body.rex-has-theme:not(.rex-theme-light) .for-a11y-ck-panel__foot{background:#222;border-top-color:#3a3a3a;}',
    'body.rex-has-theme:not(.rex-theme-light) .for-a11y-ck-btn{background:#3a3a3a;color:#eee;border-color:#4a4a4a;}',
    'body.rex-has-theme:not(.rex-theme-light) .for-a11y-ck-preview{background:#222;color:#eee;}',
    '}'
  ].join('');

  function injectStyles() {
    if (document.getElementById('for-a11y-ck-styles')) return;
    var s = document.createElement('style');
    s.id = 'for-a11y-ck-styles';
    s.textContent = PANEL_CSS;
    document.head.appendChild(s);
  }

  function showPanel(findings, editor, editable, triggerEl) {
    removePanel();
    injectStyles();

    var idx = 0;

    panel = document.createElement('div');
    panel.className = 'for-a11y-ck-panel';
    panel.style.cssText = 'top:80px;right:20px;';

    // Drag-Handle
    var drag = document.createElement('div');
    drag.className = 'for-a11y-ck-panel__drag';
    drag.innerHTML = '<span class="for-a11y-ck-panel__drag-icon" aria-hidden="true">' + FOR_A11Y_ICON.replace(/#1976d2/g, '#fff') + '</span>'
      + '<span class="for-a11y-ck-panel__drag-title">Barrierefreiheit</span>'
      + '<button class="for-a11y-ck-panel__close" title="Schließen">✕</button>';
    drag.querySelector('.for-a11y-ck-panel__close').addEventListener('click', removePanel);
    panel.appendChild(drag);

    // Body
    var body = document.createElement('div');
    body.className = 'for-a11y-ck-panel__body';
    panel.appendChild(body);

    // Footer
    var foot = document.createElement('div');
    foot.className = 'for-a11y-ck-panel__foot';
    panel.appendChild(foot);

    document.body.appendChild(panel);

    // Drag
    (function () {
      var startX, startY, startLeft, startTop, dragging = false;
      panel.style.position = 'fixed';

      // Position relativ zum auslösenden Button berechnen
      if (triggerEl) {
        var rect = triggerEl.getBoundingClientRect();
        var panelW = 430;
        var left = rect.left + rect.width / 2 - panelW / 2;
        // Innerhalb des Viewports halten
        left = Math.max(10, Math.min(left, window.innerWidth - panelW - 10));
        panel.style.left = left + 'px';
        panel.style.top  = Math.min(rect.bottom + 8, window.innerHeight - 200) + 'px';
      } else {
        panel.style.left = Math.max(10, window.innerWidth - 450) + 'px';
        panel.style.top  = '80px';
      }
      drag.addEventListener('mousedown', function (e) {
        if (e.target.classList.contains('for-a11y-ck-panel__close')) return;
        dragging = true;
        startX   = e.clientX;
        startY   = e.clientY;
        startLeft = parseInt(panel.style.left, 10) || 0;
        startTop  = parseInt(panel.style.top, 10) || 0;
        e.preventDefault();
      });
      document.addEventListener('mousemove', function (e) {
        if (!dragging) return;
        panel.style.left = (startLeft + e.clientX - startX) + 'px';
        panel.style.top  = (startTop + e.clientY - startY) + 'px';
      });
      document.addEventListener('mouseup', function () { dragging = false; });
    })();

    function renderFinding() {
      body.innerHTML = '';
      foot.innerHTML = '';

      if (!findings.length) {
        body.innerHTML = '<div class="for-a11y-ck-ok">'
          + '<div class="for-a11y-ck-ok__icon">✅</div>'
          + '<h3>Keine Probleme gefunden</h3>'
          + '<p>Der Inhalt hat alle geprüften Barrierefreiheits-Regeln bestanden.</p>'
          + '</div>';
        var closeBtn = document.createElement('button');
        closeBtn.className = 'for-a11y-ck-btn';
        closeBtn.textContent = 'Schließen';
        closeBtn.addEventListener('click', removePanel);
        foot.appendChild(closeBtn);
        return;
      }

      var f = findings[idx];

      // Progress
      var prog = document.createElement('div');
      prog.className = 'for-a11y-ck-progress';
      prog.textContent = (idx + 1) + ' / ' + findings.length + ' · '
        + findings.filter(function (x) { return x.severity === 'error'; }).length + ' Fehler · '
        + findings.filter(function (x) { return x.severity === 'warn'; }).length + ' Warnungen · '
        + findings.filter(function (x) { return x.severity === 'info'; }).length + ' Hinweise';
      body.appendChild(prog);

      // Badge
      var badge = document.createElement('div');
      badge.className = 'for-a11y-ck-badge';
      badge.style.cssText = 'background:' + SEVERITY_BG[f.severity] + ';color:' + SEVERITY_COLOR[f.severity];
      badge.textContent = SEVERITY_LABEL[f.severity];
      body.appendChild(badge);

      // Title
      var title = document.createElement('h3');
      title.className = 'for-a11y-ck-title';
      title.textContent = f.title;
      body.appendChild(title);

      // Rule ID
      var rule = document.createElement('span');
      rule.className = 'for-a11y-ck-rule';
      rule.textContent = f.id;
      body.appendChild(rule);

      // Message
      var msg = document.createElement('p');
      msg.className = 'for-a11y-ck-msg';
      msg.textContent = f.message;
      body.appendChild(msg);

      // Preview
      var prevLabel = document.createElement('div');
      prevLabel.className = 'for-a11y-ck-preview-label';
      prevLabel.textContent = 'Element';
      body.appendChild(prevLabel);
      var prev = document.createElement('pre');
      prev.className = 'for-a11y-ck-preview';
      prev.style.borderLeftColor = SEVERITY_COLOR[f.severity];
      prev.textContent = f.preview;
      body.appendChild(prev);

      // Highlight im Editor
      highlightElement(editable, f.element);

      // Footer: Navigation
      var prevBtn = document.createElement('button');
      prevBtn.className = 'for-a11y-ck-btn for-a11y-ck-btn--nav';
      prevBtn.textContent = '‹';
      prevBtn.title = 'Vorheriges';
      prevBtn.disabled = idx === 0;
      prevBtn.addEventListener('click', function () { if (idx > 0) { idx--; renderFinding(); } });
      foot.appendChild(prevBtn);

      var nextBtn = document.createElement('button');
      nextBtn.className = 'for-a11y-ck-btn for-a11y-ck-btn--nav';
      nextBtn.textContent = '›';
      nextBtn.title = 'Nächstes';
      nextBtn.disabled = idx === findings.length - 1;
      nextBtn.addEventListener('click', function () { if (idx < findings.length - 1) { idx++; renderFinding(); } });
      foot.appendChild(nextBtn);

      var spacer = document.createElement('span');
      spacer.className = 'for-a11y-ck-spacer';
      foot.appendChild(spacer);

      // Quickfix
      if (QUICKFIX_IDS[f.id]) {
        var fixBtn = document.createElement('button');
        fixBtn.className = 'for-a11y-ck-btn for-a11y-ck-btn--fix';
        fixBtn.textContent = '⚡ ' + QUICKFIX_IDS[f.id];
        fixBtn.addEventListener('click', function () {
          if (applyQuickfix(f, editor)) {
            // Neu analysieren nach Fix
            var html = editor.getData();
            var doc  = new DOMParser().parseFromString(html, 'text/html');
            var newFindings = runAudit(doc.body);
            findings.length = 0;
            newFindings.forEach(function (nf) { findings.push(nf); });
            if (idx >= findings.length) idx = Math.max(0, findings.length - 1);
            renderFinding();
          }
        });
        foot.appendChild(fixBtn);
      }

      // Neu prüfen
      var recheckBtn = document.createElement('button');
      recheckBtn.className = 'for-a11y-ck-btn';
      recheckBtn.textContent = '↻ Neu prüfen';
      recheckBtn.addEventListener('click', function () {
        var html = editor.getData();
        var doc  = new DOMParser().parseFromString(html, 'text/html');
        var newFindings = runAudit(doc.body);
        findings.length = 0;
        newFindings.forEach(function (nf) { findings.push(nf); });
        idx = 0;
        renderFinding();
      });
      foot.appendChild(recheckBtn);
    }

    renderFinding();
  }

  /* ─────────────── Plugin-Einstiegspunkt ─────────────── */

  window.CKE5_NATIVE_PLUGINS.RedaxoForA11y = function createRedaxoForA11y(context) {
    var cke = context && context.cke ? context.cke : null;
    var BasePlugin = cke && typeof cke.Plugin === 'function' ? cke.Plugin : class {};

    return class RedaxoForA11y extends BasePlugin {
      static get pluginName() { return 'RedaxoForA11y'; }

      init() {
        var editor = this.editor;
        if (!cke || !editor.ui || !editor.ui.componentFactory) return;

        editor.ui.componentFactory.add('for_a11y', function (locale) {
          if (typeof cke.ButtonView !== 'function') return null;

          var button = new cke.ButtonView(locale);
          button.set({
            label: 'Barrierefreiheit prüfen',
            icon: FOR_A11Y_ICON,
            withText: false,
            tooltip: true,
            class: 'ck-for-a11y-button'
          });

          button.on('execute', function (evt) {
            var editable = editor.ui.getEditableElement();
            if (!editable) return;

            var html     = editor.getData();
            var doc      = new DOMParser().parseFromString(html, 'text/html');
            var findings = runAudit(doc.body);
            // Button-DOM-Element für Positions-Berechnung ermitteln
            var triggerEl = null;
            try {
              var el = button.element;
              if (el && el.getDomElement) el = el.getDomElement();
              triggerEl = el || null;
            } catch (_e) { /* noop */ }
            showPanel(findings, editor, editable, triggerEl);
          });

          return button;
        });
      }

      destroy() {
        removePanel();
      }
    };
  };

})();
