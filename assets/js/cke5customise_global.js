$(document).on('rex:ready', function (event, container) {
  let root = container.find('.cke5_global_settings');
  if (!root.length) {
    return;
  }

  function syncCollapseState($toggle) {
    let target = String($toggle.attr('data-collapse-target') || '').trim();
    if (!target) {
      return;
    }

    let selector = '#cke5' + target + '-collapse';
    let $panel = root.find(selector);
    if (!$panel.length) {
      return;
    }

    let isChecked = $toggle.is(':checked');
    if (isChecked) {
      $panel.addClass('in').show();
    } else {
      $panel.removeClass('in').hide();
    }
  }

  function initLocalCollapse($toggle) {
    if (!$toggle.length) {
      return;
    }

    syncCollapseState($toggle);
    $toggle.on('change', function () {
      syncCollapseState($(this));
    });
  }

  let quickEdit = root.find('input[id^="cke5global-quickedit-enabled-input"]');
  let mentions = root.find('input[id^="cke5global-mentions-enabled-input"]');
  let sprog = root.find('input[id^="cke5global-sprog-enabled-input"]');
  let ytable = root.find('input[id^="cke5global-ytable-enabled-input"]');
  let media = root.find('input[id^="cke5global-media-enabled-input"]');
  let fontFamilyDefault = root.find('input[id^="cke5global-font-family-default-input"]');
  let clearWidget = root.find('input[id^="cke5global-clear-widget-enabled-input"]');

  if (typeof cke5_bootstrapToggle_collapse === 'function') {
    cke5_bootstrapToggle_collapse(quickEdit);
    cke5_bootstrapToggle_collapse(mentions, true);
    cke5_bootstrapToggle_collapse(sprog, true);
    cke5_bootstrapToggle_collapse(ytable, true);
    cke5_bootstrapToggle_collapse(media, true);
    cke5_bootstrapToggle_collapse(fontFamilyDefault);
    cke5_bootstrapToggle_collapse(clearWidget, true);
  } else {
    initLocalCollapse(quickEdit);
    initLocalCollapse(mentions);
    initLocalCollapse(sprog);
    initLocalCollapse(ytable);
    initLocalCollapse(media);
    initLocalCollapse(fontFamilyDefault);
    initLocalCollapse(clearWidget);
  }

  let fontFamilyArea = root.find('#cke5-global-fontfamily-area');
  if (typeof cke5_addFontFamiliesFields === 'function') {
    cke5_addFontFamiliesFields(fontFamilyArea);
  } else if (fontFamilyArea.length && fontFamilyArea.data('cke5-multiinput-initialized') !== true && typeof $.fn.multiInput === 'function') {
    let familyPlaceholder = String(fontFamilyArea.attr('data-family-placeholder') || 'Font family');
    fontFamilyArea.data('cke5-multiinput-initialized', true);
    fontFamilyArea.multiInput({
      json: true,
      input: $('<div class="row inputElement">\n' +
        '<div class="form-group col-xs-12">\n' +
        '<input class="form-control" name="family" placeholder="' + familyPlaceholder + '" type="text">\n' +
        '</div>\n' +
        '</div>\n')
    });
  }

  if (typeof cke5_addIdNameFields === 'function') {
    cke5_addIdNameFields(root.find('#cke5-global-sprog-area'));
  } else {
    let sprogArea = root.find('#cke5-global-sprog-area');
    if (sprogArea.length && sprogArea.data('cke5-multiinput-initialized') !== true && typeof $.fn.multiInput === 'function') {
      let idPlaceholder = String(sprogArea.attr('data-sprog-key-placeholder') || sprogArea.attr('data-id-placeholder') || 'Key');
      let namePlaceholder = String(sprogArea.attr('data-sprog-description-placeholder') || sprogArea.attr('data-name-placeholder') || 'Beschreibung');
      sprogArea.data('cke5-multiinput-initialized', true);
      sprogArea.multiInput({
        json: true,
        input: $('<div class="row inputElement">\n' +
          '<div class="form-group col-xs-6">\n' +
          '<input class="form-control" name="sprog_key" placeholder="' + idPlaceholder + '" type="text">\n' +
          '</div>\n' +
          '<div class="form-group col-xs-6">\n' +
          '<input class="form-control" name="sprog_description" placeholder="' + namePlaceholder + '" type="text">\n' +
          '</div>\n' +
          '</div>\n')
      });
    }
  }

  if (typeof cke5_addyTableFields === 'function') {
    cke5_addyTableFields(root.find('#cke5-global-ytable-area'));
  } else {
    let ytableArea = root.find('#cke5-global-ytable-area');
    if (ytableArea.length && ytableArea.data('cke5-multiinput-initialized') !== true && typeof $.fn.multiInput === 'function') {
      ytableArea.data('cke5-multiinput-initialized', true);
      ytableArea.multiInput({
        json: true,
        input: $('<div class="row inputElement">\n' +
          '<div class="form-group col-xs-4">\n' +
          '<input class="form-control" name="table" placeholder="Tabelle" type="text">\n' +
          '</div>\n' +
          '<div class="form-group col-xs-4">\n' +
          '<input class="form-control" name="column" placeholder="Spalte" type="text">\n' +
          '</div>\n' +
          '<div class="form-group col-xs-4">\n' +
          '<input class="form-control" name="title" placeholder="Titel" type="text">\n' +
          '</div>\n' +
          '</div>\n')
      });
    }
  }
});
