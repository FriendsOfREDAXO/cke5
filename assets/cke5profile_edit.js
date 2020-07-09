/*!
	autosize 4.0.2
	license: MIT
	http://www.jacklmoore.com/autosize
*/
!function(e,t){if("function"==typeof define&&define.amd)define(["module","exports"],t);else if("undefined"!=typeof exports)t(module,exports);else{var n={exports:{}};t(n,n.exports),e.autosize=n.exports}}(this,function(e,t){"use strict";var n,o,p="function"==typeof Map?new Map:(n=[],o=[],{has:function(e){return-1<n.indexOf(e)},get:function(e){return o[n.indexOf(e)]},set:function(e,t){-1===n.indexOf(e)&&(n.push(e),o.push(t))},delete:function(e){var t=n.indexOf(e);-1<t&&(n.splice(t,1),o.splice(t,1))}}),c=function(e){return new Event(e,{bubbles:!0})};try{new Event("test")}catch(e){c=function(e){var t=document.createEvent("Event");return t.initEvent(e,!0,!1),t}}function r(r){if(r&&r.nodeName&&"TEXTAREA"===r.nodeName&&!p.has(r)){var e,n=null,o=null,i=null,d=function(){r.clientWidth!==o&&a()},l=function(t){window.removeEventListener("resize",d,!1),r.removeEventListener("input",a,!1),r.removeEventListener("keyup",a,!1),r.removeEventListener("autosize:destroy",l,!1),r.removeEventListener("autosize:update",a,!1),Object.keys(t).forEach(function(e){r.style[e]=t[e]}),p.delete(r)}.bind(r,{height:r.style.height,resize:r.style.resize,overflowY:r.style.overflowY,overflowX:r.style.overflowX,wordWrap:r.style.wordWrap});r.addEventListener("autosize:destroy",l,!1),"onpropertychange"in r&&"oninput"in r&&r.addEventListener("keyup",a,!1),window.addEventListener("resize",d,!1),r.addEventListener("input",a,!1),r.addEventListener("autosize:update",a,!1),r.style.overflowX="hidden",r.style.wordWrap="break-word",p.set(r,{destroy:l,update:a}),"vertical"===(e=window.getComputedStyle(r,null)).resize?r.style.resize="none":"both"===e.resize&&(r.style.resize="horizontal"),n="content-box"===e.boxSizing?-(parseFloat(e.paddingTop)+parseFloat(e.paddingBottom)):parseFloat(e.borderTopWidth)+parseFloat(e.borderBottomWidth),isNaN(n)&&(n=0),a()}function s(e){var t=r.style.width;r.style.width="0px",r.offsetWidth,r.style.width=t,r.style.overflowY=e}function u(){if(0!==r.scrollHeight){var e=function(e){for(var t=[];e&&e.parentNode&&e.parentNode instanceof Element;)e.parentNode.scrollTop&&t.push({node:e.parentNode,scrollTop:e.parentNode.scrollTop}),e=e.parentNode;return t}(r),t=document.documentElement&&document.documentElement.scrollTop;r.style.height="",r.style.height=r.scrollHeight+n+"px",o=r.clientWidth,e.forEach(function(e){e.node.scrollTop=e.scrollTop}),t&&(document.documentElement.scrollTop=t)}}function a(){u();var e=Math.round(parseFloat(r.style.height)),t=window.getComputedStyle(r,null),n="content-box"===t.boxSizing?Math.round(parseFloat(t.height)):r.offsetHeight;if(n<e?"hidden"===t.overflowY&&(s("scroll"),u(),n="content-box"===t.boxSizing?Math.round(parseFloat(window.getComputedStyle(r,null).height)):r.offsetHeight):"hidden"!==t.overflowY&&(s("hidden"),u(),n="content-box"===t.boxSizing?Math.round(parseFloat(window.getComputedStyle(r,null).height)):r.offsetHeight),i!==n){i=n;var o=c("autosize:resized");try{r.dispatchEvent(o)}catch(e){}}}}function i(e){var t=p.get(e);t&&t.destroy()}function d(e){var t=p.get(e);t&&t.update()}var l=null;"undefined"==typeof window||"function"!=typeof window.getComputedStyle?((l=function(e){return e}).destroy=function(e){return e},l.update=function(e){return e}):((l=function(e,t){return e&&Array.prototype.forEach.call(e.length?e:[e],function(e){return r(e)}),e}).destroy=function(e){return e&&Array.prototype.forEach.call(e.length?e:[e],i),e},l.update=function(e){return e&&Array.prototype.forEach.call(e.length?e:[e],d),e}),t.default=l,e.exports=t.default});

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
        expert = element.find('#cke5-expert-toggle-expert-definition'),
        extra = element.find('#cke5extra-definition-input-extra-definition'),
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

    autosize($('#cke5expertDefinition-collapse textarea'));
    autosize($('#cke5extraDefinition-collapse textarea'));

    cke5_addColorFields(fontcolor_area);
    cke5_addColorFields(fontbgcolor_area);
    cke5_addFontFamiliesFields(fontfamily_area);
    cke5_bootstrapToggle_collapse(fontcolor_default);
    cke5_bootstrapToggle_collapse(fontbgcolor_default);
    cke5_bootstrapToggle_collapse(fontfamily_default);
    cke5_bootstrapToggle_collapse(height);
    cke5_bootstrapToggle_collapse(extra, true);

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

function toggle_collapse(typename, direction) { // direction => [show,hide]
    let element = $('#cke5' + typename + '-collapse');
    if (element.length) {
        element.collapse(direction);
        window.dispatchEvent(new Event('resize'));
    }
}
