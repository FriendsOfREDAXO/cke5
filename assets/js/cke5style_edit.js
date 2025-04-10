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

    // Status-Variablen
    let inactivityTimer = null;
    const INACTIVITY_DELAY = 2000; // 2 Sekunden

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

    // Funktion zum Erstellen eines Tags
    function createTag(inputField) {
        let value = inputField.val().trim();
        if (value !== '') {
            // Leerzeichen entfernen
            inputField.val(value);

            // Enter simulieren
            const enterEvent = $.Event('keyup');
            enterEvent.key = 'Enter';
            inputField.trigger(enterEvent);
        }
    }

    // Funktion zum Entfernen des letzten Tags
    function removeLastTag(inputField) {
        const tagList = inputField.closest('.cke5InputTags-list');
        const lastTag = tagList.find('.cke5InputTags-item').last();

        if (lastTag.length > 0) {
            lastTag.find('.close-item').trigger('click');
            return true;
        }
        return false;
    }

    // Event-Handler für keydown
    element.find('.cke5InputTags-field').on('keydown', function(e) {
        // Enter verhindern
        if (e.keyCode === 13) {
            e.preventDefault();
            return false;
        }

        // Einfache Backspace-Behandlung - Löscht ein Tag, wenn das Feld leer ist
        if (e.keyCode === 8) {
            const value = $(this).val();
            if (value === '') {
                removeLastTag($(this));
                e.preventDefault(); // Verhindert weitere Verarbeitung
                return false;
            }
        }
    });

    // Event-Handler für keyup
    element.find('.cke5InputTags-field').on('keyup', function(e) {
        // Timer zurücksetzen
        if (inactivityTimer) {
            clearTimeout(inactivityTimer);
        }

        // Bei Leertaste
        if (e.keyCode === 32) {
            createTag($(this));
        }
        // Bei anderen Tasten (außer Backspace, das wird durch keydown behandelt)
        else if (e.keyCode !== 8) {
            // Inaktivitäts-Timer setzen
            inactivityTimer = setTimeout(function() {
                createTag($(this));
            }.bind(this), INACTIVITY_DELAY);
        }
    });

    // Bei Fokusverlust Tag erstellen
    element.find('.cke5InputTags-field').on('blur', function() {
        if (inactivityTimer) {
            clearTimeout(inactivityTimer);
        }
        createTag($(this));
    });

    if (toggleElements.length > 0) {
        toggleElements.each(function(){
            cke5_bootstrapToggle_collapse($(this), true);
        });
    }
}

// Helper-Funktion fürs Umschalten der Toggle-Elemente
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