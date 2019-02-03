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
        height = element.find('#cke5height-input-default-height');

    cktypes = JSON.parse(element.attr('data-cktypes'));
    ckimgtypes = JSON.parse(element.attr('data-ckimgtypes'));

    imageDragDrop = element.find('#cke5uploaddefault-input-default-upload');

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

    if (taginputs.length) {
        taginputs.each(function(){
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

    if (height.length) {
        height.parent().attr('for', '');
        height.bootstrapToggle();

        if (!height.prop('checked')) {
            element.find('#cke5minmax-collapse').addClass('in');
            window.dispatchEvent(new Event('resize'));
        }

        height.on('change', function () {
            let toogle_it = 'show';
            if ($(this).prop('checked')) {
                toogle_it = 'hide';
            }
            toggle_collapse('minmax', toogle_it);
        });
    }

    if (imageDragDrop.length) {
        imageDragDrop.change(function () {
            if ($(this).prop('checked')) {
                toggle_collapse('imagetoolbar', 'show');
            } else {
                cke5_toolbar_destroy_tag('toolbar', toolbar.val().split(','))
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

function toggle_collapse(typename, direction) { // direction => [show,hide]
    let element = $('#cke5' + typename + '-collapse');
    if (element.length) {
        element.collapse(direction);
        window.dispatchEvent(new Event('resize'));
    }
}
