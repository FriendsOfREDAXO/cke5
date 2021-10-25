/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

let ckeditors = {},
    ckareas = '.cke5-editor';

$(document).on('rex:ready', function (e, container) {
    container.find(ckareas).each(function () {
        cke5_init($(this));
        if (rex.cke5theme != 'notheme') {
            if (rex.cke5theme == 'dark') {
                $('head').append('<link id="ckedark" rel="stylesheet" type="text/css" href="' + rex.cke5darkcss + '">');
            }

            if (rex.cke5theme == 'auto') {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    $('head').append('<link id="ckedark" rel="stylesheet" type="text/css" href="' + rex.cke5darkcss + '">');
                }
                window.matchMedia('(prefers-color-scheme: dark)')
                    .addEventListener('change', event => {
                        if (event.matches) {
                            //   $('head').append('<link id="ckedark" rel="stylesheet" type="text/css" href="/assets/addons/cke5/dark.css">');
                            $('head').append('<link id="ckedark" rel="stylesheet" type="text/css" href="' + rex.cke5darkcss + '">');
                        } else {
                            $('head').find("#ckedark").remove();
                        }
                    })
            }
        }
    });

});

$(document).on('ready', function () {
    if (typeof mblock_module === 'object') {
        // add callback for mblock
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
    if (!element.next().hasClass('ck')) {
        let unique_id = 'ck' + Math.random().toString(16).slice(2),
            options = {},
            sub_options = {},
            profile_set = element.attr('data-profile'),
            min_height = element.attr('data-min-height'),
            max_height = element.attr('data-max-height'),
            lang = {},
            ui_lang = element.attr('data-lang'),
            content_lang = element.attr('data-content-lang');

        element.attr('id', unique_id);

        if (typeof profile_set === undefined || !profile_set) {
        } else {
            if (profile_set in cke5profiles) {
                options = cke5profiles[profile_set];
            }
            if (profile_set in cke5suboptions) {
                if (cke5suboptions[profile_set].length > 0) {
                    cke5suboptions[profile_set].forEach(function (value, key) {
                        if (value.hasOwnProperty('min-height')) {
                            sub_options['min-height'] = value['min-height'];
                        }
                        if (value.hasOwnProperty('max-height')) {
                            sub_options['max-height'] = value['max-height'];
                        }
                    });
                }
            }
        }

        if (typeof min_height === undefined || !min_height) {
        } else {
            sub_options['min-height'] = min_height;
        }
        if (typeof max_height === undefined || !max_height) {
        } else {
            sub_options['max-height'] = max_height;
        }
        if (typeof ui_lang === undefined || !ui_lang) {
        } else {
            lang['ui'] = ui_lang;
        }
        if (typeof content_lang === undefined || !content_lang) {
        } else {
            lang['content'] = content_lang;
        }
        if (lang['ui'] !== undefined || lang['content'] !== undefined) {
            options['language'] = lang;

            if (lang['ui'] !== undefined && options['placeholder_' + lang['ui']] !== undefined) {
                options['placeholder'] = options['placeholder_' + lang['ui']];
            }
        }

        // init editor
        ClassicEditor.create(document.querySelector('#' + unique_id), options)
            .then(editor => {
                ckeditors[unique_id] = editor; // Save for later use.
                cke5_pastinit(editor, sub_options);
            })
            .catch(error => {
                console.error(error);
            });
    }
}

function cke5_destroy(elements) {
    elements.each(function () {
        let next = $(this).next();
        if (next.length && (next.hasClass('ck-editor') || next.hasClass('ck'))) {
            next.remove();
        }
    });
}

function cke5_pastinit(editor, sub_options) {

    editor.editing.view.change(writer => {
        if ('min-height' in sub_options && sub_options['min-height'] !== 'none') {
            writer.setStyle('min-height', sub_options['min-height'] + 'px', editor.editing.view.document.getRoot());
        }
        if ('max-height' in sub_options && sub_options['max-height'] !== 'none') {
            writer.setStyle('max-height', sub_options['max-height'] + 'px', editor.editing.view.document.getRoot());
        }
    });

    // editor.plugins.get( 'SpecialCharacters' ).addItems( 'a', [
    //     { title: 'D simple arrow left', character: '←' },
    //     { title: 'simple arrow up', character: '↑' },
    //     { title: 'simple arrow right', character: '→' },
    //     { title: 'simple arrow down', character: '↓' }
    // ] );
}

