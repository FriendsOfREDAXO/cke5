/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

let ckstyleedit = '.cke5_style_edit';


$(document).on('rex:ready', function (event, container) {
    if (container.find(ckstyleedit).length) {
        cke5_init_style_edit($(ckstyleedit));
    }
});

function cke5_init_style_edit(element) {
    let classesArea = element.find('#cke5-classes-area'),
        elementArea = element.find('#cke5-element-area'),
        toggleElements = element.find('[data-toggle=toggle]');

    if (classesArea.length) {
        classesArea.cke5InputTags({
            editable: true
        });
    }

    if (elementArea.length) {
        elementArea.cke5InputTags({
            max: 1,
            editable: true,
            autocomplete: {
                values: JSON.parse(elementArea.attr('data-tags')),
            }
        });
    }

    element.find('.cke5InputTags-field').keydown(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

    if (toggleElements.length > 0) {
        toggleElements.each(function(){
            cke5_bootstrapToggle_collapse($(this), true);
        });
    }
}

