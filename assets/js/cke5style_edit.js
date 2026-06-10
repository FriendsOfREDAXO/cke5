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
    if (element.data('cke5-style-edit-initialized') === true) {
        return;
    }
    element.data('cke5-style-edit-initialized', true);

    let classesArea = element.find('#cke5-classes-area'),
        elementArea = element.find('#cke5-element-area'),
        toggleElements = element.find('[data-toggle=toggle]'),
        form = element.closest('form');

    // Status-Variablen
    let inactivityTimer = null;
    const INACTIVITY_DELAY = 2000; // 2 Sekunden

    if (classesArea.length) {
        if (classesArea.data('cke5-tag-init-done') !== true) {
            classesArea.data('cke5-tag-init-done', true);
            classesArea.cke5InputTags({
                editable: true
            });
        }
        classesArea.removeAttr('required').removeAttr('aria-required');
    }

    if (elementArea.length) {
        if (elementArea.data('cke5-tag-init-done') !== true) {
            elementArea.data('cke5-tag-init-done', true);
            elementArea.cke5InputTags({
                max: 1,
                editable: true,
                autocomplete: {
                    values: JSON.parse(elementArea.attr('data-tags')),
                }
            });
        }
        elementArea.removeAttr('required').removeAttr('aria-required');
    }

    function clearTagInputErrors(field) {
        let group = field.closest('.form-group');
        if (group.length) {
            group.removeClass('has-error');
            group.find('.cke5-tag-input-error').remove();
        }
    }

    function showTagInputError(field, message) {
        let group = field.closest('.form-group'),
            list = field.next('.cke5InputTags-list'),
            visibleInput = list.find('.cke5InputTags-field').first();

        if (!group.length) {
            return;
        }

        group.addClass('has-error');
        if (!group.find('.cke5-tag-input-error').length) {
            group.append('<p class="help-block cke5-tag-input-error">' + message + '</p>');
        }

        if (visibleInput.length) {
            visibleInput.trigger('focus');
        }
    }

    function validateRequiredTagField(field) {
        if (!field.length) {
            return true;
        }

        clearTagInputErrors(field);

        if (field.val().trim() !== '') {
            return true;
        }

        showTagInputError(field, field.attr('data-empty-error') || 'Bitte Wert eingeben');
        return false;
    }

    if (form.length && form.data('cke5-style-submit-validation') !== true) {
        form.data('cke5-style-submit-validation', true);
        form.on('submit', function (e) {
            let isValid = true;

            if (!validateRequiredTagField(elementArea)) {
                isValid = false;
            }

            if (!validateRequiredTagField(classesArea)) {
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            return true;
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