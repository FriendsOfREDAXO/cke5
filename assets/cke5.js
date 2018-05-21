/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

let ckeditors = {};

var ckareas = '.cke5-editor',
    ckedit = '.cke5_profile_edit',
    cktypes = ['heading', 'fontSize', 'fontFamily', 'alignment', 'link', 'highlight'];

$(document).on('ready pjax:success', function () {

    if ($(ckareas).length) {
        cke5_init_all($(ckareas));
        mblock_module.registerCallback('reindex_end', function () {
            if ($(ckareas).length) {
                if (mblock_module.lastAction == 'add_item') {
                    cke5_destroy(mblock_module.affectedItem.find(ckareas));
                    cke5_init_all(mblock_module.affectedItem.find(ckareas));
                }
            }
        });
    }

    if ($(ckedit).length) {
        cke5_init_edit();
    }

});

function cke5_init_all(elements) {
    elements.each(function () {
        cke5_init($(this));
    });
}

function cke5_init(element) {

    var unique_id = 'ck' + Math.random().toString(16).slice(2),
        options = {},
        sub_options = {},
        profile_set = element.attr('data-profile'),
        min_height = element.attr('data-min-height'),
        max_height = element.attr('data-max-height'),
        lang = element.attr('data-lang');

    element.attr('id', unique_id);

    if (typeof profile_set === 'undefined' || !profile_set) { } else {
        if (profile_set in cke5profiles) {
            options = cke5profiles[profile_set];
        }
        if (profile_set in cke5suboptions) {
            sub_options = cke5suboptions[profile_set];
        }
    }
    if (typeof min_height === 'undefined' || !min_height) { } else {
        sub_options['min-height'] = min_height;
    }
    if (typeof max_height === 'undefined' || !max_height) { } else {
        sub_options['max-height'] = max_height;
    }
    if (typeof lang === 'undefined' || !lang) { } else {
        options['lang'] = lang;
    }

    console.log(options);

    // init editor
    ClassicEditor.create(document.querySelector('#' + unique_id), options)
        .then(editor => {
            ckeditors[unique_id] = editor; // Save for later use.
            cke5_pastinit(element, sub_options);
        })
        .catch(error => {
            console.error(error);
        });
}

function cke5_destroy(elements) {
    elements.each(function () {
        var next = $(this).next();
        if (next.length && next.hasClass('ck')) {
            next.remove();
        }
    });
}

function cke5_pastinit(element, sub_options) {
    var next = element.next();
    if (next.length && next.hasClass('ck')) {
        var editable = next.find('.ck-editor__editable');
        if ('min-height' in sub_options && editable.length && sub_options['min-height'] != 'none') {
            next.find('.ck-editor__editable').css('min-height', sub_options['min-height'] + 'px');
        }
        if ('max-height' in sub_options && editable.length && sub_options['max-height'] != 'none') {
            next.find('.ck-editor__editable').css('max-height', sub_options['max-height'] + 'px');
        }
    }
}

function cke5_init_edit() {

    var toolbar = $('#cke5toolbar-input'),
        alignment = $('#cke5alignment-input'),
        heading = $('#cke5heading-input'),
        fontsize = $('#cke5fontsize-input'),
        rexlink = $('#cke5link-input'),
        name = $('#cke5name-input'),
        image = $('#cke5image-input'),
        minheight = $('#cke5minheight-input'),
        maxheight = $('#cke5maxheight-input'),
        height = $('#cke5height-input-default-height'),
        highlight = $('#cke5highlight-input')
        ;

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

    if (toolbar.length) {
        if (toolbar.attr('data-default-tags') === '1') {
            toolbar.val('heading,|')
        }
        var toolbarTags = toolbar.cke5InputTags({
            autocomplete: {
                values: ['|', 'heading', 'fontSize', 'fontFamily', 'alignment', 'bold', 'italic', 'underline', 'strikethrough', 'code', 'link', 'rexImage', 'imageUpload', 'bulletedList', 'numberedList', 'blockQuote', 'Undo', 'Redo', 'highlight', 'emoji']
            },
            create: function (e) {
                cke5_toolbar_create_tag('toolbar',e.tags);
            },
            destroy: function (e) {
                cke5_toolbar_destroy_tag('toolbar',e.tags);
            }
        });
    }

    if (alignment.length) {
        if (alignment.attr('data-default-tags') === '1') {
            alignment.val('left,right,center')
        }
        var alignmentTags = alignment.cke5InputTags({
            autocomplete: {
                values: ['left', 'right', 'center', 'justify']
            },
            max: 4
        });
    }

    if (heading.length) {
        if (heading.attr('data-default-tags') === '1') {
            heading.val('paragraph,h1,h2,h3');
        }
        var headingTags = heading.cke5InputTags({
            autocomplete: {
                values: ['paragraph', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']
            },
            max: 7
        });
    }

    if (fontsize.length) {
        if (fontsize.attr('data-default-tags') === '1') {
            fontsize.val('tiny,small,big,huge');
        }
        var fontsizeTags = fontsize.cke5InputTags({
            autocomplete: {
                values: ['tiny', 'small', 'big', 'huge', '8', '9',
                            '10', '11', '12', '13', '14', '15', '16', '17', '18', '19',
                            '20', '21', '22', '23', '24', '25', '26', '27', '28', '29',
                            '30', '31', '32', '33', '34', '35', '36', '37', '38', '39',
                            '40', '41', '42', '43', '44', '45', '46', '47', '48', '49',
                            '50', '51', '52', '53', '54', '55', '56', '57', '58', '59',
                            '60', '61', '62', '63', '64', '65', '66', '67', '68', '69',
                            '70', '71', '72', '73', '74', '75', '76', '77', '78', '79']
            }
        });
    }

    if (rexlink.length) {
        if (rexlink.attr('data-default-tags') === '1') {
            rexlink.val('internal,media');
        }
        var rexlinkTags = rexlink.cke5InputTags({
            autocomplete: {
                values: ['internal', 'media'],
                max: 2
            }
        });
    }

    if (image.length) {
        if (image.attr('data-default-tags') === '1') {
            image.val('imageTextAlternative,|,full,alignLeft,alignRight');
        }
        var imageTags = image.cke5InputTags({
            autocomplete: {
                values: ['|', 'imageTextAlternative', 'full', 'alignLeft', 'alignCenter', 'alignRight'],
                max: 12
            }
        });
    }

    if (highlight.length) {
        if (highlight.attr('data-default-tags') === '1') {
            var defaults = highlight.attr('data-defaults');
            highlight.val(defaults);
        }

        var highlightTags = highlight.cke5InputTags({
            autocomplete: {
                values: JSON.parse(highlight.attr('data-tags')),
                max: 12
            }
        });
    }

    if (minheight.length) {
        minheight.slider({
            tooltip: 'show',
            min: 0,
            max: 600,
            step: 10,
            ticks: [0, 100, 200, 300, 400, 500, 600],
            ticks_labels: ['none', '100px', '200px', '300px', '400px', '500px', '600px'],
        });
    }

    if (maxheight.length) {
        maxheight.slider({
            tooltip: 'show',
            min: 0,
            max: 600,
            step: 10,
            ticks: [0, 200, 400, 600, 800, 1000, 1200],
            ticks_labels: ['none', '200px', '400px', '600px', '800px', '1000px', '1200px'],
        });
    }

    if (height.length) {
        height.parent().attr('for', '');
        height.bootstrapToggle();

        if (!height.prop('checked')) {
            $('#cke5minmax-collapse').addClass('in');
            window.dispatchEvent(new Event('resize'));
        }

        height.on('change', function() {
            var toogle_it = 'show';
            if ($(this).prop('checked')) { toogle_it = 'hide'; }
            toggle_collapse('minmax', toogle_it);
        });
    }
}

function cke5_toolbar_create_tag(typename, tags) {
    cktypes.forEach(function(type) {
        if ($.inArray(type, tags) != -1 && typename === 'toolbar') {
            toggle_collapse(type, 'show');
        }
    });
}

function cke5_toolbar_destroy_tag(typename, tags) {
    cktypes.forEach(function(type) {
        if ($.inArray(type, tags) != -1) {} else {
            if (typename === 'toolbar') {
                toggle_collapse(type, 'hide');
            }
        }
    });
}

function toggle_collapse(typename, direction) { // direction => [show,hide]
    if ($('#cke5' + typename + '-collapse').length) {
        $('#cke5' + typename + '-collapse').collapse(direction);
        window.dispatchEvent(new Event('resize'));
    }
}