/*!
  autosize 4.0.2
  license: MIT
  http://www.jacklmoore.com/autosize
*/
!function (e, t) { if ("function" == typeof define && define.amd) define(["module", "exports"], t); else if ("undefined" != typeof exports) t(module, exports); else { var n = { exports: {} }; t(n, n.exports), e.autosize = n.exports } }(this, function (e, t) { "use strict"; var n, o, p = "function" == typeof Map ? new Map : (n = [], o = [], { has: function (e) { return -1 < n.indexOf(e) }, get: function (e) { return o[n.indexOf(e)] }, set: function (e, t) { -1 === n.indexOf(e) && (n.push(e), o.push(t)) }, delete: function (e) { var t = n.indexOf(e); -1 < t && (n.splice(t, 1), o.splice(t, 1)) } }), c = function (e) { return new Event(e, { bubbles: !0 }) }; try { new Event("test") } catch (e) { c = function (e) { var t = document.createEvent("Event"); return t.initEvent(e, !0, !1), t } } function r(r) { if (r && r.nodeName && "TEXTAREA" === r.nodeName && !p.has(r)) { var e, n = null, o = null, i = null, d = function () { r.clientWidth !== o && a() }, l = function (t) { window.removeEventListener("resize", d, !1), r.removeEventListener("input", a, !1), r.removeEventListener("keyup", a, !1), r.removeEventListener("autosize:destroy", l, !1), r.removeEventListener("autosize:update", a, !1), Object.keys(t).forEach(function (e) { r.style[e] = t[e] }), p.delete(r) }.bind(r, { height: r.style.height, resize: r.style.resize, overflowY: r.style.overflowY, overflowX: r.style.overflowX, wordWrap: r.style.wordWrap }); r.addEventListener("autosize:destroy", l, !1), "onpropertychange" in r && "oninput" in r && r.addEventListener("keyup", a, !1), window.addEventListener("resize", d, !1), r.addEventListener("input", a, !1), r.addEventListener("autosize:update", a, !1), r.style.overflowX = "hidden", r.style.wordWrap = "break-word", p.set(r, { destroy: l, update: a }), "vertical" === (e = window.getComputedStyle(r, null)).resize ? r.style.resize = "none" : "both" === e.resize && (r.style.resize = "horizontal"), n = "content-box" === e.boxSizing ? -(parseFloat(e.paddingTop) + parseFloat(e.paddingBottom)) : parseFloat(e.borderTopWidth) + parseFloat(e.borderBottomWidth), isNaN(n) && (n = 0), a() } function s(e) { var t = r.style.width; r.style.width = "0px", r.offsetWidth, r.style.width = t, r.style.overflowY = e } function u() { if (0 !== r.scrollHeight) { var e = function (e) { for (var t = []; e && e.parentNode && e.parentNode instanceof Element;)e.parentNode.scrollTop && t.push({ node: e.parentNode, scrollTop: e.parentNode.scrollTop }), e = e.parentNode; return t }(r), t = document.documentElement && document.documentElement.scrollTop; r.style.height = "", r.style.height = r.scrollHeight + n + "px", o = r.clientWidth, e.forEach(function (e) { e.node.scrollTop = e.scrollTop }), t && (document.documentElement.scrollTop = t) } } function a() { u(); var e = Math.round(parseFloat(r.style.height)), t = window.getComputedStyle(r, null), n = "content-box" === t.boxSizing ? Math.round(parseFloat(t.height)) : r.offsetHeight; if (n < e ? "hidden" === t.overflowY && (s("scroll"), u(), n = "content-box" === t.boxSizing ? Math.round(parseFloat(window.getComputedStyle(r, null).height)) : r.offsetHeight) : "hidden" !== t.overflowY && (s("hidden"), u(), n = "content-box" === t.boxSizing ? Math.round(parseFloat(window.getComputedStyle(r, null).height)) : r.offsetHeight), i !== n) { i = n; var o = c("autosize:resized"); try { r.dispatchEvent(o) } catch (e) { } } } } function i(e) { var t = p.get(e); t && t.destroy() } function d(e) { var t = p.get(e); t && t.update() } var l = null; "undefined" == typeof window || "function" != typeof window.getComputedStyle ? ((l = function (e) { return e }).destroy = function (e) { return e }, l.update = function (e) { return e }) : ((l = function (e, t) { return e && Array.prototype.forEach.call(e.length ? e : [e], function (e) { return r(e) }), e }).destroy = function (e) { return e && Array.prototype.forEach.call(e.length ? e : [e], i), e }, l.update = function (e) { return e && Array.prototype.forEach.call(e.length ? e : [e], d), e }), t.default = l, e.exports = t.default });

/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

let ckedit = '.cke5_profile_edit',
  cktypes, ckimgtypes, cktabletypes, imageDragDrop, cklinktypes, CKEDIT_DEBUG = false;

function cke5_parse_slider_value(value) {
  let normalized = String(value || '').trim().toLowerCase();

  if (normalized === '' || normalized === 'none' || normalized === 'nan') {
    return 0;
  }

  normalized = normalized.replace('px', '');
  let parsed = parseInt(normalized, 10);

  return Number.isFinite(parsed) ? parsed : 0;
}

function cke5_parse_slider_json_list(value) {
  try {
    let parsed = JSON.parse(value || '[]');
    return Array.isArray(parsed) ? parsed : [];
  } catch (e) {
    return [];
  }
}

function cke5_parse_slider_ticks(element) {
  return cke5_parse_slider_json_list(element.attr('data-range-values')).map(function (tick) {
    return cke5_parse_slider_value(tick);
  });
}

function cke5_parse_slider_labels(element) {
  let labels = cke5_parse_slider_json_list(element.attr('data-range'));
  if (labels.length) {
    return labels;
  }
  return cke5_parse_slider_json_list(element.attr('data-range-values'));
}


$(document).on('rex:ready', function (event, container) {
  if (container.find(ckedit).length) {
    cke5_init_edit($(ckedit));
  }

  cke5_init_copy_buttons();
});

$(document).on('ready', function () {
  cke5_init_copy_buttons();
});

function cke5_init_copy_buttons() {
  if ($(document).data('cke5-copy-buttons-initialized') === true) {
    return;
  }
  $(document).data('cke5-copy-buttons-initialized', true);

  $(document).on('click', '.cke5-copy-btn', function () {
    let button = $(this),
      targetId = button.attr('data-cke5-copy-target'),
      target = targetId ? $('#' + targetId) : $(),
      text = target.length ? target.text().trim() : '';

    if (!text) {
      return;
    }

    cke5_copy_to_clipboard(text, button);
  });
}

function cke5_copy_to_clipboard(text, button) {
  let resetLabel = function () {
    window.setTimeout(function () {
      button.text('Kopieren');
    }, 1200);
  };

  if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
    navigator.clipboard.writeText(text).then(function () {
      button.text('Kopiert');
      resetLabel();
    }).catch(function () {
      cke5_copy_to_clipboard_fallback(text, button, resetLabel);
    });
    return;
  }

  cke5_copy_to_clipboard_fallback(text, button, resetLabel);
}

function cke5_copy_to_clipboard_fallback(text, button, resetLabel) {
  let textarea = $('<textarea>').css({
    position: 'fixed',
    left: '-9999px',
    top: '-9999px'
  }).val(text);

  $('body').append(textarea);
  textarea.trigger('focus').trigger('select');

  try {
    document.execCommand('copy');
    button.text('Kopiert');
  } catch (e) {
    button.text('Fehler');
  }

  textarea.remove();
  resetLabel();
}

function cke5_init_edit(element) {
  if (element.data('cke5-profile-edit-initialized') === true) {
    return;
  }
  element.data('cke5-profile-edit-initialized', true);

  let taginputs = element.find('input[data-tag-init=1]'),
    expert = element.find('#cke5-expert-toggle-expert-definition'),
    extra = element.find('#cke5extra-definition-input-extra-definition'),
    mentions = element.find('#cke5mentions-definition-input-mentions-definition'),
    sprog_mention = element.find('#cke5sprog-mention-definition-input-sprog-mention-definition'),
    transformation = element.find('#cke5transformation-definition-input-transformation-definition'),
    transformation_extra_area = element.find('#cke5-transformation-extra-area'),
    sprog_mention_area = element.find('#cke5-sprog-mention-area'),
    toolbar = element.find('#cke5toolbar-input'),
    balloon_toolbar = element.find('#cke5balloon-toolbar-input'),
    balloon_toolbar_custom = element.find('input[id^="cke5balloon-toolbar-custom-input"]'),
    table_toolbar = element.find('#cke5inserttable-input'),
    rexlink_toolbar = element.find('#cke5link-input'),
    name = element.find('#cke5name-input'),
    minheight = element.find('#cke5minheight-input'),
    maxheight = element.find('#cke5maxheight-input'),
    height = element.find('#cke5height-input-default-height'),
    mediapath_input = element.find('#cke5mediapath-input'),
    mediapath_hidden = element.find('#cke5mediapath-hidden'),
    mediapath_collapse = element.find('#cke5insertMediapath-collapse'),
    mediatype = element.find('#cke5mediatype-select'),
    tablecolor_area = element.find('#cke5tablecolor-area'),
    tablecolor_default = element.find('#cke5table-color-default-input-default-table-color'),
    fontcolor_area = element.find('#cke5fontcolor-area'),
    fontcolor_default = element.find('#cke5font-color-default-input-default-font-color'),
    fontbgcolor_area = element.find('#cke5fontbgcolor-area'),
    fontbgcolor_default = element.find('#cke5font-background-color-default-input-default-font-background-color'),
    ytable_area = element.find('#cke5ytable-area'),
    fontfamily_area = element.find('#cke5fontfamily-area'),
    fontfamily_default = element.find('#cke5font-family-default-input-default-font-family'),
    link_decorators = element.find('#cke5link-decorators-definition-input-link-decorators-definition'),
    media_embed_styles_area = element.find('#cke5-media-embed-styles-definition'),
    media_embed_width_styles_area = element.find('#cke5-media-embed-width-styles-definition'),
    video_styles_area = element.find('#cke5-video-styles-definition'),
    video_width_styles_area = element.find('#cke5-video-width-styles-definition'),
    imgresizeoptions_input = element.find('#cke5image-resize-option-input-default-resize-options'),
    imgresizeoptions_area = element.find('#cke5resizeoptions-area');

  cktypes = JSON.parse(element.attr('data-cktypes'));
  cklinktypes = JSON.parse(element.attr('data-cklinktypes'));
  ckimgtypes = JSON.parse(element.attr('data-ckimgtypes'));
  cktabletypes = JSON.parse(element.attr('data-cktabletypes'));

  cke5_init_modern_profile_layout(element);

  imageDragDrop = element.find('#cke5uploaddefault-input-default-upload');

  autosize($('#cke5expertDefinition-collapse textarea'));
  autosize($('#cke5extraDefinition-collapse textarea'));
  autosize($('#cke5mentionsDefinition-collapse textarea'));
  autosize($('#cke5linkDecoratorsDefinition-collapse textarea'));
  autosize($('#cke5transformationDefinition-collapse textarea'));
  autosize($('#cke5sourceEditing-collapse textarea'));

  cke5_addColorFields(tablecolor_area);
  cke5_addColorFields(fontcolor_area);
  cke5_addColorFields(fontbgcolor_area);
  cke5_addFromToFields(transformation_extra_area);
  cke5_addFontFamiliesFields(fontfamily_area);
  cke5_addIdNameFields(sprog_mention_area);
  cke5_addyTableFields(ytable_area);
  cke5_addLabelClassFields(media_embed_styles_area);
  cke5_addLabelClassFields(media_embed_width_styles_area);
  cke5_addLabelClassFields(video_styles_area);
  cke5_addLabelClassFields(video_width_styles_area);
  cke5_addResizeOptionsFields(imgresizeoptions_area);
  cke5_bootstrapToggle_collapse(tablecolor_default);
  cke5_bootstrapToggle_collapse(fontcolor_default);
  cke5_bootstrapToggle_collapse(fontbgcolor_default);
  cke5_bootstrapToggle_collapse(fontfamily_default);
  cke5_bootstrapToggle_collapse(height);
  cke5_bootstrapToggle_collapse(extra, true);
  cke5_bootstrapToggle_collapse(mentions, true);
  cke5_bootstrapToggle_collapse(sprog_mention, true);
  cke5_bootstrapToggle_collapse(transformation, true);
  cke5_bootstrapToggle_collapse(link_decorators, true);
  cke5_bootstrapToggle_collapse(imgresizeoptions_input, true);
  cke5_bootstrapToggle_collapse(balloon_toolbar_custom, true);

  if (name.length) {
    let currentProfileName = String(name.val() || '').trim().toLowerCase();
    if (currentProfileName === 'demo_default') {
      name.prop('readonly', true);
      name.prop('disabled', true);

      let hiddenProtectedName = element.find('input[type="hidden"][data-cke5-protected-name="1"]');
      if (!hiddenProtectedName.length) {
        $('<input>', {
          type: 'hidden',
          name: name.attr('name'),
          value: 'demo_default',
          'data-cke5-protected-name': '1'
        }).appendTo(element);
      }
    }

    name.alphanum({
      allowSpace: false,
      allowUpper: false,
      allowOtherCharSets: false,
      maxLength: 40,
      allow: '_',
      allowNumeric: true
    });
  }

  if (mediapath_input.length && mediapath_hidden.length) {
    mediapath_input.on("keyup change", function () {
      mediapath_hidden.val(mediapath_input.val());
      mediapath_collapse.next().find('select option:first-child').text('default /' + mediapath_input.val() + '/');
      mediapath_collapse.next().find('select').selectpicker('refresh');
    });
  }

  if (mediatype.length) {
    if (mediatype.val() === '') {
      toggle_collapse('insertMediapath', 'show');
    }
    mediatype.on('change', function () {
      if ($(this).val() === '') {
        toggle_collapse('insertMediapath', 'show');
      } else {
        toggle_collapse('insertMediapath', 'hide');
      }
    });
  }

  if (taginputs.length) {
    taginputs.each(function () {
      if ($(this).data('cke5-tag-init-done') === true) {
        return;
      }
      $(this).data('cke5-tag-init-done', true);
      // if ($(this).attr('data-default-tags') === '1') {
      //     $(this).attr('value', $(this).attr('data-defaults'))
      // }
      $(this).cke5InputTags({
        autocomplete: {
          values: JSON.parse($(this).attr('data-tags')),
        },
        create: function (e) {
          if ($(this).attr('id') === toolbar.attr('id')) {
            cke5_toolbar_create_tag('toolbar', e.tags);
          }
          if ($(this).attr('id') === balloon_toolbar.attr('id')) {
            cke5_toolbar_create_tag('balloon_toolbar', e.tags);
          }
          if ($(this).attr('id') === table_toolbar.attr('id')) {
            cke5_toolbar_create_tag('table_toolbar', e.tags);
          }
          if ($(this).attr('id') === rexlink_toolbar.attr('id')) {
            cke5_toolbar_create_tag('rexlink_toolbar', e.tags);
          }
        },
        destroy: function (e) {
          if ($(this).attr('id') === toolbar.attr('id')) {
            cke5_toolbar_destroy_tag('toolbar', e.tags);
          }
          if ($(this).attr('id') === balloon_toolbar.attr('id')) {
            cke5_toolbar_destroy_tag('balloon_toolbar', e.tags);
          }
          if ($(this).attr('id') === table_toolbar.attr('id')) {
            cke5_toolbar_destroy_tag('table_toolbar', e.tags);
          }
          if ($(this).attr('id') === rexlink_toolbar.attr('id')) {
            cke5_toolbar_destroy_tag('rexlink_toolbar', e.tags);
          }
        }
      });
      $(this).next().find('.cke5InputTags-field').keydown(function (e) {
        if (e.keyCode == 13) {
          e.preventDefault();
          return false;
        }
      });
    });
  }

  if (minheight.length) {
    let minSliderValue = cke5_parse_slider_value(minheight.val());
    let minTicks = cke5_parse_slider_ticks(minheight);
    let minTickLabels = cke5_parse_slider_labels(minheight);
    let minSliderMax = minTicks.length ? minTicks[minTicks.length - 1] : 600;

    // Prüfen, ob der Slider bereits initialisiert wurde
    if (!minheight.hasClass('slider-initialized')) {
      minheight.bootstrapSlider({
        tooltip: 'show',
        min: 0,
        max: minSliderMax,
        step: 10,
        ticks: minTicks,
        ticks_labels: minTickLabels,
      });
      minheight.addClass('slider-initialized');
    }
    minheight.bootstrapSlider('setValue', minSliderValue);
  }

  if (maxheight.length) {
    let maxSliderValue = cke5_parse_slider_value(maxheight.val());
    let maxTicks = cke5_parse_slider_ticks(maxheight);
    let maxTickLabels = cke5_parse_slider_labels(maxheight);
    let maxSliderMax = maxTicks.length ? maxTicks[maxTicks.length - 1] : 1200;

    // Prüfen, ob der Slider bereits initialisiert wurde
    if (!maxheight.hasClass('slider-initialized')) {
      maxheight.bootstrapSlider({
        tooltip: 'show',
        min: 0,
        max: maxSliderMax,
        step: 10,
        ticks: maxTicks,
        ticks_labels: maxTickLabels,
      });
      maxheight.addClass('slider-initialized');
    }
    maxheight.bootstrapSlider('setValue', maxSliderValue);
  }

  if (imageDragDrop.length) {
    if (imageDragDrop.prop('checked')) {
      toggle_collapse('imagetoolbar', 'show');
      toggle_collapse('mediacat', 'show');
    }
    imageDragDrop.change(function () {
      if ($(this).prop('checked')) {
        toggle_collapse('imagetoolbar', 'show');
        toggle_collapse('mediacat', 'show');
      } else {
        cke5_toolbar_destroy_tag('toolbar', toolbar.val().split(','))
        toggle_collapse('mediacat', 'hide');
      }
    })
  }

  // init collapse
  if (toolbar.val() !== undefined) cke5_toolbar_create_tag('toolbar', toolbar.val().split(','));
  if (balloon_toolbar.val() !== undefined) cke5_toolbar_create_tag('balloon_toolbar', balloon_toolbar.val().split(','));
  if (table_toolbar.val() !== undefined) cke5_toolbar_create_tag('table_toolbar', table_toolbar.val().split(','));
  if (rexlink_toolbar.val() !== undefined) cke5_toolbar_create_tag('rexlink_toolbar', rexlink_toolbar.val().split(','));

  element.find('.cke5InputTags-list').each(function () {
    $(this).sortable({
      update: function () {
        let _input = $(this).prev(),
            _inputid = _input.attr('data-uniqid'),
            _inputtags = window.cke5InputTags.instances[_inputid],
            _tags = {},
            tags;

        $(this).find('span').each(function (i) {
          _tags[i] = $(this).attr('data-tag');
        });

        tags = $.map(_tags, function (val) {
          return val;
        }).join(",");
        _inputtags.$element.val(tags);
        _inputtags.tags = _inputtags.$element.val().split(',');
      },
      appendTo: 'body',       // An body anhängen statt am parent
      forceHelperSize: true,  // Erzwinge Helper-Größe basierend auf Original
      forcePlaceholderSize: true,
      cursorAt: { top: 5, left: 5 }
    });
  });

  if (expert.length) {
    expert.change(function () {
      if ($(this).prop('checked')) {
        toggle_collapse('expertDefinition', 'show');
        toggle_collapse('profileEditor', 'hide');
      } else {
        toggle_collapse('expertDefinition', 'hide');
        toggle_collapse('profileEditor', 'show');
      }
    })
  }

}

function cke5_init_modern_profile_layout(element) {
  let editorCollapse = element.find('#cke5profileEditor-collapse');
  if (!editorCollapse.length || editorCollapse.data('cke5-modern-layout') === true) {
    return;
  }

  editorCollapse.data('cke5-modern-layout', true);

  let root = $('<div class="cke5-profile-modern"></div>');
  let toolbar = $(
    '<div class="cke5-profile-modern-toolbar">' +
      '<ul class="nav nav-tabs" role="tablist">' +
        '<li class="active"><a href="#cke5-tab-view" role="tab" data-toggle="tab">Ansicht & Optionen</a></li>' +
        '<li><a href="#cke5-tab-content" role="tab" data-toggle="tab">Inhalte</a></li>' +
        '<li><a href="#cke5-tab-text" role="tab" data-toggle="tab">Text & Stil</a></li>' +
        '<li><a href="#cke5-tab-media" role="tab" data-toggle="tab">Medien</a></li>' +
        '<li><a href="#cke5-tab-links" role="tab" data-toggle="tab">Links</a></li>' +
        '<li><a href="#cke5-tab-advanced" role="tab" data-toggle="tab">Erweitert</a></li>' +
      '</ul>' +
      '<div class="cke5-profile-modern-search">' +
        '<input type="text" class="form-control" placeholder="Einstellung suchen..." data-cke5-profile-filter="1">' +
      '</div>' +
    '</div>'
  );

  let panes = $('<div class="tab-content cke5-profile-modern-content"></div>');
  let paneView = $('<div class="tab-pane active" id="cke5-tab-view"></div>');
  let paneContent = $('<div class="tab-pane" id="cke5-tab-content"></div>');
  let paneText = $('<div class="tab-pane" id="cke5-tab-text"></div>');
  let paneMedia = $('<div class="tab-pane" id="cke5-tab-media"></div>');
  let paneLinks = $('<div class="tab-pane" id="cke5-tab-links"></div>');
  let paneAdvanced = $('<div class="tab-pane" id="cke5-tab-advanced"></div>');

  panes.append(paneView, paneContent, paneText, paneMedia, paneLinks, paneAdvanced);
  root.append(toolbar, panes);

  editorCollapse.children().each(function () {
    let block = $(this);
    if (block.hasClass('cke5-preview-row')) {
      return;
    }

    if (
      block.hasClass('cke5_clangtabs') ||
      block.hasClass('cke5-placeholder-scope-note') ||
      block.hasClass('cke5-profile-settings-note') ||
      block.find('#cke5toolbar-input').length ||
      block.find('input[id^="cke5height-input"]').length
    ) {
      paneView.append(block);
      return;
    }

    if (block.is('#cke5style-collapse') || block.is('#cke5snippets-collapse')) {
      paneContent.append(block);
      return;
    }

    if (block.find('#cke5heading-input').length || block.is('#cke5font-collapse-parent') || block.is('#cke5highlight-collapse')) {
      paneText.append(block);
      return;
    }

    if (block.is('#cke5insertTable-collapse')) {
      paneContent.append(block);
      return;
    }

    if (block.is('#cke5embed-collapse-parent') || block.find('#cke5image-input').length) {
      paneMedia.append(block);
      return;
    }

    if (block.is('#cke5link-collapse')) {
      paneLinks.append(block);
      return;
    }

    paneAdvanced.append(block);
  });

  editorCollapse.prepend(root);

  // Keep potentially risky raw JSON options out of basic view settings.
  const extraOptionField = root.find('input[id^="cke5extra-definition-input"]').closest('dl.rex-form-group, dl.form-group');
  const extraOptionCollapse = root.find('#cke5extraDefinition-collapse').first();
  const transformationField = root.find('input[id^="cke5transformation-definition-input"]').closest('dl.rex-form-group, dl.form-group');
  const transformationCollapse = root.find('#cke5transformationDefinition-collapse').first();

  if (transformationField.length) {
    paneContent.append(transformationField);
  }
  if (transformationCollapse.length) {
    paneContent.append(transformationCollapse);
  }

  if (extraOptionField.length) {
    paneAdvanced.append(extraOptionField);
  }
  if (extraOptionCollapse.length) {
    paneAdvanced.append(extraOptionCollapse);
  }

  root.find('[data-cke5-profile-filter="1"]').on('input', function () {
    let query = ($(this).val() || '').toString().toLowerCase().trim();
    let targets = root.find('.tab-pane > fieldset, .tab-pane > .collapse, .tab-pane > .cke5_clangtabs');
    let tabs = root.find('.nav-tabs > li > a[data-toggle="tab"]');
    let paneHasMatches = {};

    targets.each(function () {
      let target = $(this);
      let pane = target.closest('.tab-pane');
      let paneId = pane.attr('id') || '';
      let hasMatch = true;

      if (query !== '') {
        let text = target.text().toLowerCase();
        hasMatch = text.indexOf(query) !== -1;
      }

      target.toggleClass('cke5-filter-hidden', !hasMatch);

      if (paneId !== '') {
        paneHasMatches[paneId] = (paneHasMatches[paneId] === true) || hasMatch;
      }
    });

    tabs.each(function () {
      let tab = $(this);
      let paneId = (tab.attr('href') || '').replace('#', '');
      let hasMatches = query === '' || paneHasMatches[paneId] === true;
      tab.parent().toggleClass('cke5-filter-empty', !hasMatches);
    });

    if (query === '') {
      tabs.parent().removeClass('cke5-filter-empty');
      return;
    }

    let activePane = root.find('.tab-pane.active').attr('id') || '';
    if (paneHasMatches[activePane] === true) {
      return;
    }

    let firstMatchTab = tabs.filter(function () {
      let paneId = ($(this).attr('href') || '').replace('#', '');
      return paneHasMatches[paneId] === true;
    }).first();

    if (firstMatchTab.length) {
      firstMatchTab.tab('show');
    }
  });
}

function cke5_addFromToFields(element) {
  if (element.length) {
    if (element.data('cke5-multiinput-initialized') === true) {
      return;
    }
    element.data('cke5-multiinput-initialized', true);
    let from_placeholder = element.data('from-placeholder'),
      to_name_placeholder = element.data('to-placeholder');
    element.multiInput({
      json: true,
      input: $('<div class="row inputElement">\n' +
        '<div class="form-group col-xs-6">\n' +
        '<input class="form-control" name="from" placeholder="' + from_placeholder + '" type="text">\n' +
        '</div>\n' +
        '<div class="form-group col-xs-6">\n' +
        '<input class="form-control" name="to" placeholder="' + to_name_placeholder + '" type="text">\n' +
        '</div>\n' +
        '</div>\n'),
      limit: 200,
      onElementAdd: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      },
      onElementRemove: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      }
    });
  }
}

function cke5_addIdNameFields(element) {
  if (element.length) {
    if (element.data('cke5-multiinput-initialized') === true) {
      return;
    }
    element.data('cke5-multiinput-initialized', true);
    let sprog_key_placeholder = element.data('sprog-key-placeholder') || element.data('id-placeholder') || 'Sprog Key',
      sprog_description_placeholder = element.data('sprog-description-placeholder') || element.data('name-placeholder') || 'Sprog Text';
    element.multiInput({
      json: true,
      input: $('<div class="row inputElement">\n' +
        '<div class="form-group col-xs-6">\n' +
        '<input class="form-control" name="sprog_key" placeholder="' + sprog_key_placeholder + '" type="text">\n' +
        '</div>\n' +
        '<div class="form-group col-xs-6">\n' +
        '<input class="form-control" name="sprog_description" placeholder="' + sprog_description_placeholder + '" type="text">\n' +
        '</div>\n' +
        '</div>\n'),
      limit: 200,
      onElementAdd: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      },
      onElementRemove: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      }
    });
  }
}

function cke5_addColorFields(element) {
  if (element.length) {
    if (element.data('cke5-multiinput-initialized') === true) {
      return;
    }
    element.data('cke5-multiinput-initialized', true);
    let color_placeholder = element.data('color-placeholder'),
      color_name_placeholder = element.data('color-name-placeholder'),
      has_border_label = element.data('has-border-label');
    element.multiInput({
      json: true,
      input: $('<div class="row inputElement">\n' +
        '<div class="form-group col-xs-5">\n' +
        '<input class="form-control color" name="color" placeholder="' + color_placeholder + '" type="text" readonly>\n' +
        '</div>\n' +
        '<div class="form-group col-xs-4">\n' +
        '<input class="form-control color-name" name="label" placeholder="' + color_name_placeholder + '" type="text">\n' +
        '</div>\n' +
        '<div class="form-group col-xs-3">\n' +
        '<label><input class="border" name="hasBorder" type="checkbox"> ' + has_border_label + '</label>\n' +
        '</div>\n' +
        '</div>\n'),
      limit: 50,
      onElementAdd: function (el, plugin) {
        let input_color = el.find('input.color'),
          input_border = el.find('input.border');
        input_color.css('background', input_color.val());
        input_color.colpick({
          onChange: function (hsb, hex, rgb, el, bySetColor) {
            $(el).val('rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')');
            $(el).css('background', $(el).val());
            // $(el).val('#'+hex);
          },
          onSubmit: function (hsb, hex, rgb, el, bySetColor) {
            // $(el).val('#'+hex);
            $(el).val('rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')');
            $(el).css('background', $(el).val());
            $(el).colpickHide();
          }
        });
        if (input_border.val() === 'true') {
          input_border.prop('checked', true);
        }
        input_border.val(JSON.parse(input_border.is(':checked')));
        input_border.change(function () {
          input_border.val(JSON.parse(input_border.is(':checked')));
          plugin.saveElementsValues();
        });
        input_border.bootstrapToggle();
      },
      onElementRemove: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      }
    });
  }
}

function cke5_addyTableFields(element) {
  if (element.length) {
    if (element.data('cke5-multiinput-initialized') === true) {
      return;
    }
    element.data('cke5-multiinput-initialized', true);
    let title_placeholder = element.data('ytable-title-placeholder'),
      table_placeholder = element.data('ytable-table-placeholder'),
      column_placeholder = element.data('ytable-column-placeholder');
    element.multiInput({
      json: true,
      input: $('<div class="row inputElement">\n' +
        '<div class="form-group col-xs-4">\n' +
        '<input class="form-control" name="table" placeholder="' + table_placeholder + '" type="text">\n' +
        '</div>\n' +
        '<div class="form-group col-xs-4">\n' +
        '<input class="form-control" name="column" placeholder="' + column_placeholder + '" type="text">\n' +
        '</div>\n' +
        '<div class="form-group col-xs-4">\n' +
        '<input class="form-control" name="title" placeholder="' + title_placeholder + '" type="text">\n' +
        '</div>\n' +
        '</div>\n'),
      limit: 20,
      onElementAdd: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      },
      onElementRemove: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      }
    });
  }
}

function cke5_addResizeOptionsFields(element) {
  if (element.length) {
    if (element.data('cke5-multiinput-initialized') === true) {
      return;
    }
    element.data('cke5-multiinput-initialized', true);
    let name_placeholder = element.data('name-placeholder'),
      icon_placeholder = element.data('icon-placeholder'),
      value_placeholder = element.data('value-placeholder');
    element.multiInput({
      json: true,
      input: $('<div class="row inputElement">\n' +
        '<div class="form-group col-xs-4">\n' +
        '<input class="form-control" name="name" placeholder="' + name_placeholder + '" type="text">\n' +
        '</div>\n' +
        '<div class="form-group col-xs-4">\n' +
        '<input class="form-control" name="value" placeholder="' + value_placeholder + '" type="text">\n' +
        '</div>\n' +
        '<div class="form-group col-xs-4">\n' +
        // '<select name="icon" size="1" class="form-control selectpicker">\n' +
        // '<select name="icon" size="1" class="form-control">\n' +
        // '        <option value="original" selected="selected">aktive (https Protokol)</option>\n' +
        // '        <option value="http">aktive (http Protokol)</option>\n' +
        // '        <option value="0">inaktiv</option>\n' +
        // '</select>' +
        '<input class="form-control" name="icon" placeholder="' + icon_placeholder + '" type="text">\n' +
        '</div>\n' +
        '</div>\n'),
      limit: 20,
      onElementAdd: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      },
      onElementRemove: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      }
    });
  }
}

function cke5_addLabelClassFields(element) {
  if (element.length) {
    if (element.data('cke5-multiinput-initialized') === true) {
      return;
    }
    element.data('cke5-multiinput-initialized', true);
    let label_placeholder = element.data('label-placeholder'),
      class_placeholder = element.data('class-placeholder');
    element.multiInput({
      json: true,
      input: $('<div class="row inputElement">\n' +
        '<div class="form-group col-xs-6">\n' +
        '<input class="form-control" name="label" placeholder="' + label_placeholder + '" type="text">\n' +
        '</div>\n' +
        '<div class="form-group col-xs-6">\n' +
        '<input class="form-control" name="class" placeholder="' + class_placeholder + '" type="text">\n' +
        '</div>\n' +
        '</div>\n'),
      limit: 20,
      onElementAdd: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      },
      onElementRemove: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      }
    });
  }
}

function cke5_addFontFamiliesFields(element) {
  if (element.length) {
    if (element.data('cke5-multiinput-initialized') === true) {
      return;
    }
    element.data('cke5-multiinput-initialized', true);
    let color_placeholder = element.data('family-placeholder');
    element.multiInput({
      json: true,
      input: $('<div class="row inputElement">\n' +
        '<div class="form-group col-xs-12">\n' +
        '<input class="form-control color" name="family" placeholder="' + color_placeholder + '" type="text">\n' +
        '</div>\n' +
        '</div>\n'),
      limit: 50,
      onElementAdd: function (el, plugin) {
        // let input_font = el.find('input.family');
      },
      onElementRemove: function (el, plugin) {
        if (CKEDIT_DEBUG) console.log(plugin.elementCount);
      }
    });
  }
}

function cke5_toolbar_create_tag(typename, tags) {
  cktypes.forEach(function (type) {
    if ($.inArray(type, tags) !== -1 && (typename === 'toolbar' || typename === 'balloon_toolbar')) {
      switch (type) {
        case 'bulletedList':
        case 'numberedList':
          case 'for_lists':
          toggle_collapse('liststyle', 'show');
          if (type === 'numberedList') {
            toggle_collapse('numberedList', 'show');
          }
          break;
        case 'snippets':
          toggle_collapse('snippets', 'show');
        break;
        case 'for_video':
        case 'for_video_widget_test':
          toggle_collapse('mediaEmbed', 'show');
          toggle_collapse('for_video', 'show');
        break;
        case 'for_clear':
          toggle_collapse('for_clear', 'show');
        break;
        default:
          toggle_collapse(type, 'show');
      }
    }
  });
  ckimgtypes.forEach(function (type) {
    if ($.inArray(type, tags) !== -1 && typename === 'toolbar') {
      toggle_collapse('imagetoolbar', 'show');
    }
  });
  cklinktypes.forEach(function (type) {
    if ($.inArray(type, tags) !== -1 && typename === 'rexlink_toolbar') {
      toggle_collapse(type, 'show');
    }
  });
  cktabletypes.forEach(function (type) {
    if ($.inArray(type, tags) !== -1 && typename === 'table_toolbar') {
      if (CKEDIT_DEBUG) console.log([typename, type]);
      if (CKEDIT_DEBUG) console.log(tags);
      if (CKEDIT_DEBUG) console.log('#########');
      switch (type) {
        case 'tableProperties':
        case 'tableCellProperties':
          toggle_collapse('tableColor', 'show', false, true);
          break;
      }
    }
  });
}

function cke5_toolbar_destroy_tag(typename, tags) {
  let imghide = 0,
    liststylehide = 0,
    fonthide = 0,
    embedhide = 0,
    tabhide = 0;
  cktypes.forEach(function (type) {
    if ($.inArray(type, tags) !== -1) {
    } else {
      if (typename === 'toolbar' || typename === 'balloon_toolbar') {
        switch (type) {
          case 'bulletedList':
          case 'numberedList':
          case 'for_lists':
            liststylehide++;
            if (liststylehide === 3) {
              toggle_collapse('liststyle', 'hide');
            }
            if (type === 'numberedList') {
              toggle_collapse('numberedList', 'hide');
            }
            break;
          case 'htmlEmbed':
          case 'mediaEmbed':
          case 'for_video':
          case 'for_video_widget_test':
            embedhide++;
            if (CKEDIT_DEBUG) console.log(embedhide + ' - ' + type);
            if (type === 'for_video_widget_test') {
              toggle_collapse('for_video', 'hide', (embedhide === 3));
            } else {
              toggle_collapse(type, 'hide', (embedhide === 3));
            }
            break;
          case 'for_clear':
            toggle_collapse('for_clear', 'hide');
            break;
          case 'fontSize':
          case 'fontFamily':
          case 'fontColor':
          case 'fontBackgroundColor':
            fonthide++;
            toggle_collapse(type, 'hide', (fonthide === 4));
            break;
          default:
            if (type === 'snippets') {
              toggle_collapse('snippets', 'hide');
            } else {
              toggle_collapse(type, 'hide');
            }
        }
      }
    }
  });
  ckimgtypes.forEach(function (type) {
    if ($.inArray(type, tags) !== -1) {
    } else {
      if (typename === 'toolbar' && !imageDragDrop.prop('checked')) {
        imghide++;
        if (imghide === 2) {
          toggle_collapse('imagetoolbar', 'hide');
        }
      }
    }
  });
  cklinktypes.forEach(function (type) {
    if ($.inArray(type, tags) !== -1) {
    } else {
      if (typename === 'rexlink_toolbar') {
        toggle_collapse(type, 'hide');
      }
    }
  });
  cktabletypes.forEach(function (type) {
    if ($.inArray(type, tags) !== -1) {
    } else {
      if (typename === 'table_toolbar') {
        tabhide++;
        if (tabhide === 2) {
          toggle_collapse('tableColor', 'hide');
        }
      }
    }
  });
}

function cke5_bootstrapToggle_collapse(element, invert = false) {
  if (element.length) {
    element.parent().attr('for', '');
    if (!element.prop('checked') && !invert) {
      $('#cke5' + element.data('collapse-target') + '-collapse').addClass('in');
      window.dispatchEvent(new Event('resize'));
    }
    if (element.prop('checked') && invert) {
      $('#cke5' + element.data('collapse-target') + '-collapse').addClass('in');
      window.dispatchEvent(new Event('resize'));
    }
    element.on('change', function () {
      let toogle_it = 'show';
      if ($(this).prop('checked') && !invert) {
        toogle_it = 'hide';
      }
      if (!$(this).prop('checked') && invert) {
        toogle_it = 'hide';
      }
      toggle_collapse(element.data('collapse-target'), toogle_it);
    });
  }
}

function toggle_collapse(typename, direction, hideParent = false, single = false) { // direction => [show,hide]
  let element = $('#cke5' + typename + '-collapse');
  let parent = element.parent().parent();
  if (element.length) {
    if (CKEDIT_DEBUG) console.log([typename, direction, hideParent]);

    if (hideParent) {
      if (CKEDIT_DEBUG) console.log(parent);
    }

    if (single === false && parent.length && parent.hasClass('collapse') && parent.attr('id') != 'cke5profileEditor-collapse') {
      if (
        (!parent.hasClass('in') && direction === 'show') ||
        (hideParent && direction === 'hide')
      ) {
        if (CKEDIT_DEBUG) console.log([typename, direction, hideParent]);
        if (CKEDIT_DEBUG) console.log(element);
        if (CKEDIT_DEBUG) console.log(parent);
        parent.collapse(direction);
      }
    }

    element.collapse(direction);
    window.dispatchEvent(new Event('resize'));
  }
}
