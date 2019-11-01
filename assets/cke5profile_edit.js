/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

let ckedit = '.cke5_profile_edit',
    cktypes, ckimgtypes, imageDragDrop;


$(document).on('rex:ready', function (event, container) {
    if (container.find(ckedit).length) {
        cke5_init_edit($(ckedit));
    }
});

function cke5_init_edit(element) {

    let taginputs = element.find('input[data-tag-init=1]'),
        toolbar = element.find('#cke5toolbar-input'),
        name = element.find('#cke5name-input'),
        minheight = element.find('#cke5minheight-input'),
        maxheight = element.find('#cke5maxheight-input'),
        height = element.find('#cke5height-input-default-height'),
        mediapath_input = element.find('#cke5mediapath-input'),
        mediapath_hidden = element.find('#cke5mediapath-hidden'),
        mediapath_collapse = element.find('#cke5insertMediapath-collapse'),
        mediatype = element.find('#cke5mediatype-select'),
        fontcolor_area = element.find('#cke5fontcolor-area'),
        fontcolor_default = element.find('#cke5font-color-default-input-default-font-color'),
        fontbgcolor_area = element.find('#cke5fontbgcolor-area'),
        fontbgcolor_default = element.find('#cke5font-background-color-default-input-default-font-background-color'),
        fontfamily_area = element.find('#cke5fontfamily-area'),
        fontfamily_default = element.find('#cke5font-family-default-input-default-font-family');

    cktypes = JSON.parse(element.attr('data-cktypes'));
    ckimgtypes = JSON.parse(element.attr('data-ckimgtypes'));

    imageDragDrop = element.find('#cke5uploaddefault-input-default-upload');

    cke5_addColorFields(fontcolor_area);
    cke5_addColorFields(fontbgcolor_area);
    cke5_addFontFamiliesFields(fontfamily_area);
    cke5_bootstrapToggle_collapse(fontcolor_default);
    cke5_bootstrapToggle_collapse(fontbgcolor_default);
    cke5_bootstrapToggle_collapse(fontfamily_default);
    cke5_bootstrapToggle_collapse(height);

    if (name.length) {
        name.alphanum({
            allowSpace: false,
            allowUpper: false,
            allowOtherCharSets: false,
            maxLength: 18,
            allow: '_-',
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
            if ($(this).attr('data-default-tags') === '1') {
                $(this).attr('value', $(this).attr('data-defaults'))
            }
            $(this).cke5InputTags({
                autocomplete: {
                    values: JSON.parse($(this).attr('data-tags')),
                },
                create: function (e) {
                    if ($(this).attr('id') === toolbar.attr('id')) {
                        cke5_toolbar_create_tag('toolbar', e.tags);
                    }
                },
                destroy: function (e) {
                    if ($(this).attr('id') === toolbar.attr('id')) {
                        cke5_toolbar_destroy_tag('toolbar', e.tags);
                    }
                }
            });
        });
    }

    if (minheight.length) {
        let $minval = minheight.val();
        minheight.bootstrapSlider({
            tooltip: 'show',
            min: 0,
            max: 600,
            step: 10,
            ticks: JSON.parse(minheight.attr('data-range-values')),
            ticks_labels: JSON.parse(minheight.attr('data-range')),
        });
        minheight.bootstrapSlider('setValue', $minval);
    }

    if (maxheight.length) {
        let $maxval = maxheight.val();
        maxheight.bootstrapSlider({
            tooltip: 'show',
            min: 0,
            max: 600,
            step: 10,
            ticks: JSON.parse(maxheight.attr('data-range-values')),
            ticks_labels: JSON.parse(maxheight.attr('data-range')),
        });
        maxheight.bootstrapSlider('setValue', $maxval);
    }

    if (imageDragDrop.length) {
        if (imageDragDrop.prop('checked')) {
            element.find('#cke5mediacat-collapse').addClass('in');
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
                _inputtags.$element.attr('value', tags);
                _inputtags.tags = _inputtags.$element.val().split(',');
            }
        });
    });
}

function cke5_addColorFields(element) {
    if (element.length) {
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
                // console.log(plugin.elementCount);
            }
        });
    }
}

function cke5_addFontFamiliesFields(element) {
    if (element.length) {
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
                // console.log(plugin.elementCount);
            }
        });
    }
}

function cke5_toolbar_create_tag(typename, tags) {
    cktypes.forEach(function (type) {
        if ($.inArray(type, tags) !== -1 && typename === 'toolbar') {
            toggle_collapse(type, 'show');
        }
    });
    ckimgtypes.forEach(function (type) {
        if ($.inArray(type, tags) !== -1 && typename === 'toolbar') {
            toggle_collapse('imagetoolbar', 'show');
        }
    });
}

function cke5_toolbar_destroy_tag(typename, tags) {
    let imghide = 0;
    cktypes.forEach(function (type) {
        if ($.inArray(type, tags) !== -1) {
        } else {
            if (typename === 'toolbar') {
                toggle_collapse(type, 'hide');
            }
        }
    });
    ckimgtypes.forEach(function (type) {
        if ($.inArray(type, tags) !== -1) {
        } else {
            if (typename === 'toolbar' && !imageDragDrop.prop('checked')) {
                imghide++;
            }
        }
    });
    if (imghide === 2) {
        toggle_collapse('imagetoolbar', 'hide');
    }
}

function cke5_bootstrapToggle_collapse(element) {
    if (element.length) {
        element.parent().attr('for', '');
        if (!element.prop('checked')) {
            $('#cke5' + element.data('collapse-target') + '-collapse').addClass('in');
            window.dispatchEvent(new Event('resize'));
        }
        element.on('change', function () {
            let toogle_it = 'show';
            if ($(this).prop('checked')) {
                toogle_it = 'hide';
            }
            toggle_collapse(element.data('collapse-target'), toogle_it);
        });
    }
}

function toggle_collapse(typename, direction) { // direction => [show,hide]
    let element = $('#cke5' + typename + '-collapse');
    if (element.length) {
        element.collapse(direction);
        window.dispatchEvent(new Event('resize'));
    }
}
