/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

let ckeditors = {},
    ckareas = '.cke5-editor';

$(document).on('rex:ready', function(e, container) {
    container.find(ckareas).each(function() {
        cke5_init($(this));
    });
})

$(document).on('ready', function () {
    if (typeof mblock_module === 'object') {
        mblock_module.registerCallback('reindex_end', function () {
            if ($(ckareas).length) {
                if (mblock_module.lastAction === 'add_item') {
                    cke5_destroy(mblock_module.affectedItem.find(ckareas));
                    cke5_init_all(mblock_module.affectedItem.find(ckareas));
                }
            }
        });
    }
});

function cke5_init_all(elements) {
    elements.each(function () {
        cke5_init($(this));
    });
}

function cke5_init(element) {
    let unique_id = 'ck' + Math.random().toString(16).slice(2),
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
        options['language'] = lang;
    }

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
        let next = $(this).next();
        if (next.length && (next.hasClass('ck-editor') || next.hasClass('ck'))) {
            next.remove();
        }
    });
}

function cke5_pastinit(element, sub_options) {
    let next = element.next();
    if (next.length && next.hasClass('ck')) {
        let editable = next.find('.ck-editor__editable');
        if (sub_options[0] != undefined) {
            sub_options = sub_options[0];
        }
        if ('min-height' in sub_options && editable.length && sub_options['min-height'] !== 'none') {
            next.find('.ck-editor__editable').css('min-height', sub_options['min-height'] + 'px');
        }
        if ('max-height' in sub_options && editable.length && sub_options['max-height'] !== 'none') {
            next.find('.ck-editor__editable').css('max-height', sub_options['max-height'] + 'px');
        }
    }
}
