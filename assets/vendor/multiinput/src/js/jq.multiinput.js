// Multi Input Plugin
// Author: Thomas Jakobi <thomas.jakobi@partout.info>
;
(function ($, window, document, undefined) {

    var pluginName = 'multiInput',
        defaults = {
            input: false,
            clearInputs: 1,
            limit: 3,
            separator: '\n',
            inputSeparator: '||',
            trimSeparator: false,
            onElementAdd: null,
            onElementRemove: null,
            json: false,
            i18n: {
                limitMessage: 'Limit reached',
                addText: 'Add',
                removeText: 'Remove'
            }
        };

    // multiInput
    function Plugin(el, options) {
        // Extending options
        this.options = $.extend({}, defaults, options);

        // Private
        this.$el = $(el);
        this.element = (this.options.element) ? this.options.element : $(el);
        this.elementId = this.element.attr('id');
        this.elementInput = (this.options.input) ? (this.options.input) : $('<input>').attr({
            name: this.elementId + 'Input',
            id: this.elementId + 'Input',
            type: 'text'
        });
        this.elementInputs = null;
        this.elementCount = 0;
        this.addLink = $('<a>').addClass('add').css('cursor', 'pointer').html('<i class="fa fa-lg fa-plus-circle"></i><span class="sr-only">' + this.options.i18n.addText + '</span>');
        this.removeLink = $('<a></i>').addClass('remove').css('cursor', 'pointer').html('<i class="fa fa-lg fa-minus-circle"></i><span class="sr-only">' + this.options.i18n.removeText + '</span>');
        this.escSeparator = this.options.separator.replace(/[\-\[\]\/{}()*+?.\\^$|]/g, '\\$&');
        this.escInputSeparator = this.options.inputSeparator.replace(/[\-\[\]\/{}()*+?.\\^$|]/g, '\\$&');
        this.trimEx = new RegExp('^(' + this.escSeparator + ')+|(' + this.escSeparator + ')+$', 'gm');
        this.trimExInput = new RegExp('^(' + this.escInputSeparator + ')+|(' + this.escInputSeparator + ')+$', 'gm');

        this.init();
    }

    // Separate functionality from object creation
    Plugin.prototype = {
        /**
         * Initialize multiInput plugin
         * @return {Object} this The plugin object
         */
        init: function () {
            var _this = this;
            if (_this.element.length) {
                _this.elementInputs = _this.fillElementsValues(_this.element);
                _this.element.hide().before(_this.elementInputs);
            }
            return this;
        },
        /**
         * Clear all inputs of an element
         * @param {jQuery} el The element to clear
         */
        clearInputs: function (el) {
            var _this = this;
            $(':input', el).each(function () {
                switch ($(this).attr('type')) {
                    case 'button':
                        break;
                    case 'reset':
                        break;
                    case 'submit':
                        break;
                    case 'checkbox':
                    case 'radio':
                        $(this).attr('checked', false);
                        break;
                    default:
                        $(this).val('');
                }
            });
            _this.saveElementsValues();
        },
        /**
         * Create one element
         * @param {jQuery} el The element to create
         * @param {Number} suffix The new id suffix
         * @param {String/JSON} data The value for the element to create
         * @return {jQuery} The new created element
         */
        createElement: function (el, suffix, data) {
            var _this = this;
            var clone = el.clone(false);
            var cloneWrap = $('<div>').addClass('inputWrapper').hide().append(clone);
            $('[id]', cloneWrap).each(function () {
                $(this).attr('id', $(this).attr('id') + (suffix));
            });
            if (!this.options.json) {
                $('[name]', cloneWrap).each(function () {
                    $(this).attr('name', $(this).attr('name') + (suffix));
                });
            }
            $('.number', clone).text(suffix + 1);

            _this.clearInputs(cloneWrap);
            if (!_this.options.json) {
                var values = data.split(_this.options.inputSeparator);
            } else {
                values = data;
            }
            var inputs = $(':input', cloneWrap);
            if (!_this.options.json) {
                if (values.length) {
                    for (var k = 0; k < values.length; k++) {
                        $(inputs[k]).val(values[k]);
                    }
                }
            } else {
                $.each(values, function (key, value) {
                    $('[name=' + key + ']', cloneWrap).val(value);
                });
            }
            var add = _this.addLink.clone(false).click(function (e) {
                e.preventDefault();
                if (_this.elementCount < _this.options.limit) {
                    var clone = _this.createElement(_this.elementInput, _this.elementCount, '');
                    var number = $(this).parents('.inputWrapper').index();
                    $('.number', clone).text(number + 2);
                    $(this).parents('.inputWrapper').after(clone);
                    $(this).parents('.inputWrapper').nextAll('.inputWrapper').each(function (index) {
                        $(this).find('.number').text(number + index + 2);
                    });

                    clone.show(0, function () {
                        $(this).removeAttr('style');
                    });
                    _this.elementCount++;
                    _this.addElementEvents(clone);
                    _this.saveElementsValues();
                } else {
                    alert(_this.options.i18n.limitMessage);
                }
            });
            var remove = _this.removeLink.clone(false).click(function (e) {
                e.preventDefault();
                if ($('.inputWrapper', _this.elementInputs).length > 1) {
                    var number = $(this).parents('.inputWrapper').index();
                    $(this).parents('.inputWrapper').nextAll('.inputWrapper').each(function (index) {
                        $(this).find('.number').text(number + index + 1);
                    });
                    $(this).parents('.inputWrapper').hide(0, function () {
                        $(this).remove();
                        _this.saveElementsValues();
                        _this.elementCount--;
                    });
                } else {
                    _this.clearInputs($(this).parent());
                }
                if (typeof _this.options.onElementRemove === 'function') {
                    _this.options.onElementRemove(el, _this);
                }
            });
            cloneWrap.append(add).append(remove);
            return cloneWrap;
        },
        /**
         * Fill elements with values
         * @param {jQuery} el The element containing the values for the elements
         * @return {jQuery} The elements
         */
        fillElementsValues: function (el) {
            var _this = this,
                id = el.attr('id'),
                values;

            if (_this.options.json) {
                values = (el.html()) ? JSON.parse(el.html()) : [];
            } else {
                values = el.html().replace(/[\s\r\n]+$/, '').split(_this.options.separator);
            }
            var required = (el.hasClass('required')) ? 'required' : '',
                inputs = $('<div>').attr('id', id + 'Inputs').addClass('multiInput'),
                input;
            if (values.length) {
                for (var k = 0; k < values.length; k++) {
                    input = _this.createElement(_this.elementInput, k, values[k]).addClass(id + 'Input').addClass(required).show();
                    inputs.append(input);
                    _this.elementCount++;
                    _this.addElementEvents(input);
                }
            } else {
                input = _this.createElement(_this.elementInput, 0, '').addClass(id + 'Input').show();
                inputs.append(input);
                _this.elementCount++;
                _this.addElementEvents(input);
            }
            return (inputs);
        },
        /**
         * Add events to an element
         * @param {object} el The element to add events to
         */
        addElementEvents: function (el) {
            var _this = this;
            $('[name]', el).bind('change keyup mouseup', function () {
                _this.saveElementsValues();
                return false;
            });
            if (typeof _this.options.onElementAdd === 'function') {
                _this.options.onElementAdd(el, _this);
            }
        },
        /**
         * Save elements values
         */
        saveElementsValues: function () {
            var _this = this;
            if (_this.elementInputs) {
                var elements = _this.elementInputs.children('.inputWrapper');
                var data = [];
                elements.each(function () {
                    var inputs = $(':input', $(this));
                    var values = (_this.options.json) ? {} : [];
                    $.each(inputs, function () {
                        var name = $(this).attr('name');
                        if (name && _this.options.json) {
                            values[name] = $(this).val();
                        } else {
                            values.push($(this).val());
                        }
                    });
                    if (_this.options.json) {
                        data.push(values);
                    } else {
                        values = values.join(_this.options.inputSeparator);
                        if (_this.options.trimSeparator) {
                            values = values.replace(_this.trimExInput, '');
                        }
                        data.push(values);
                    }
                });
                if (_this.options.json) {
                    _this.element.text(JSON.stringify(data).replace(/\[\[/, '[ [').replace(/]]/, '] ]'));
                } else {
                    var value = data.join(_this.options.separator);
                    if (_this.options.trimSeparator) {
                        value = value.replace(_this.trimEx, '');
                    }
                    _this.element.text(value);
                }
            }
        }
    };

    // The actual plugin
    $.fn[pluginName] = function (options) {
        var args = arguments;
        if (options === undefined || typeof options === 'object') {
            return this.each(function () {
                if (!$.data(this, 'plugin_' + pluginName)) {
                    $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
                }
            });
        } else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {
            var returns;
            this.each(function () {
                var instance = $.data(this, 'plugin_' + pluginName);
                if (instance instanceof Plugin && typeof instance[options] === 'function') {
                    returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
                }
                if (options === 'destroy') {
                    $.data(this, 'plugin_' + pluginName, null);
                }
            });
            return returns !== undefined ? returns : this;
        }
    };
}(jQuery, window, document));