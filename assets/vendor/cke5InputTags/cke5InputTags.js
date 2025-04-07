/**
 * https://github.com/betaWeb/inputTags-jQuery-plugin
 */
(function ($) {

    $.fn.cke5InputTags = function (options) {
        if (!('cke5InputTags' in window)) {
            window.cke5InputTags = {
                instances: []
            };
        }

        window.cke5InputTags.methods = {
            tags: function (element, callback) {
                if (element) {
                    switch (typeof element) {
                        case 'string':
                            switch (element) {
                                case '_toString':
                                    var str = _instance.tags.toString();

                                    if (callback) {
                                        return callback(str);
                                    }
                                    return str;
                                case '_toObject':
                                    var obj = _instance._toObject(_instance.tags);

                                    if (callback) {
                                        return callback(obj);
                                    }
                                    return obj;
                                case '_toJSON':
                                    var obj = _instance._toObject(_instance.tags);
                                    var json = JSON.stringify(obj);

                                    if (callback) {
                                        return callback(json);
                                    }
                                    return json;
                                case '_toArray':
                                    if (callback) {
                                        return callback(_instance.tags);
                                    }
                                    return _instance.tags;
                            }

                            var partials = element.split(',');

                            if (partials.length > 1) {
                                var current = _instance.tags;
                                _instance.tags = current.concat(partials);
                            } else {
                                _instance.tags.push(partials[0]);
                            }
                            break;
                        case 'object':
                            var current = _instance.tags;

                            if ('[object Object]' === Object.prototype.toString.call(element)) {
                                element = Object.keys(element).map(function (k) {
                                    return element[k];
                                });
                            }

                            _instance.tags = current.concat(element);
                            break;
                        case 'function':
                            return element(_instance.tags);
                    }

                    _instance._clean();
                    _instance._fill();
                    _instance._updateValue();

                    _instance.destroy();

                    _instance._setInstance(_instance);

                    if (callback) {
                        return callback(_instance.tags);
                    }
                }

                return _instance.tags;
            },
            event: function (method, callback) {
                _instance.options[method] = callback;
                _instance._setInstance(_instance);
            },
            options: function (key, value) {
                if (!key && !value) {
                    return _instance.options;
                }

                if (value) {
                    _instance.options[key] = value;
                    _instance._setInstance(_instance);
                } else {
                    return _instance.options[key];
                }
            },
            destroy: function () {
                var id = $(this).attr('data-uniqid');
                delete window.cke5InputTags.instances[id];
            }
        };

        if ('object' === typeof options || !options) {
            var options = $.extend(true, {}, $.fn.cke5InputTags.defaults, options);

            this.each(function () {
                var self = $(this);

                /* Constants */
                self.KEY_ENTER = 'Enter';
                self.KEY_COMMA = ',';
                self.KEY_ESCAPE = 'Escape';
                self.UNIQID = Math.round(Date.now() / (Math.random() * (548 - 54) - 54));
                self.DEFAULT_CLASS = 'cke5InputTags';
                self.ELEMENT_CLASS = self.DEFAULT_CLASS + '-' + self.UNIQID;
                self.LIST_CLASS = self.DEFAULT_CLASS + '-list';
                self.ITEM_CLASS = self.DEFAULT_CLASS + '-item';
                self.ITEM_CONTENT = '<span class="value">%s</span><i class="close-item">&times</i>';
                self.FIELD_CLASS = self.DEFAULT_CLASS + '-field';
                self.ERROR_CLASS = self.DEFAULT_CLASS + '-error';
                self.ERROR_CONTENT = '<p class="' + self.ERROR_CLASS + '">%s</p>';

                self.AUTOCOMPLETE_LIST_CLASS = self.DEFAULT_CLASS + '-autocomplete-list';
                self.AUTOCOMPLETE_ITEM_CLASS = self.DEFAULT_CLASS + '-autocomplete-item';
                self.AUTOCOMPLETE_ITEM_CONTENT = '<li class="' + self.AUTOCOMPLETE_ITEM_CLASS + '">%s</li>';

                /* Variables */
                self.options = options;
                self.keys = [self.KEY_ENTER, self.KEY_COMMA, self.KEY_ESCAPE];
                self.tags = [];

                if (self.options.keys.length > 0) {
                    self.keys = self.keys.concat(self.options.keys);
                }

                self.init = function () {
                    self.addClass(self.ELEMENT_CLASS).attr('data-uniqid', self.UNIQID);

                    self.$element = $('.' + self.ELEMENT_CLASS);
                    self.$element.hide();

                    /* initialization */
                    self.build();
                    self.fill();
                    self.save();
                    self.edit();
                    self.destroy();
                    self._autocomplete._init();
                    self._focus();
                };

                /**
                 * Build plugin's HTML skeleton
                 *
                 * @returns {void}
                 */
                self.build = function () {
                    self.$html = $('<div>').addClass(self.LIST_CLASS);
                    self.$input = $('<input>').attr({
                        'type': 'text',
                        'class': self.FIELD_CLASS
                    });

                    self.$html.insertAfter(self.$element).prepend(self.$input);

                    self.$list = self.$element.next('.' + self.LIST_CLASS);

                    self.$list.on('click', function (e) {
                        if ($(e.target).hasClass('cke5InputTags-field')) {
                            return false;
                        }
                        self.$input.focus();
                    });
                };

                /**
                 * Init tags list if present in options, otherwise returns false
                 *
                 * @returns {void | boolean}
                 */
                self.fill = function () {
                    self._getDefaultValues();

                    if (0 === self.options.tags) {
                        return false;
                    }

                    self._concatenate();
                    self._updateValue();

                    self._fill();
                };

                /**
                 * Fills tag list
                 *
                 * @returns {void}
                 */
                self._fill = function () {
                    self.tags.forEach(function (value, i) {
                        var validate = self._validate(value, false);

                        if (true === validate || ('max' === validate && i + 1 <= self.options.max)) {
                            self._buildItem(value);
                        }
                    });
                };

                /**
                 * Clear HTML tags list
                 *
                 * @returns {void}
                 */
                self._clean = function () {
                    $('.' + self.ITEM_CLASS, self.$list).remove();
                };

                /**
                 * Add or edit tag depends on key pressed
                 *
                 * @returns {void}
                 */
                self.save = function () {
                    self.$input.on('keyup', function (e) {
                        e.preventDefault();

                        var key = e.key;
                        var value = self.$input.val().trim();

                        if ($.inArray(key, self.keys) < 0) {
                            // Input changed, update autocomplete
                            self._autocomplete._init(true);
                            self._autocomplete._show();

                            return false;
                        }

                        if (self.KEY_ESCAPE === key) {
                            self._cancel();
                            self._autocomplete._hide();

                            return false;
                        }

                        value = self.KEY_COMMA === key ? value.slice(0, -1) : value;

                        if (!self._validate(value, true)) {
                            return false;
                        }

                        if (self.options.only && self._exists(value)) {
                            self._errors('exists');

                            return false;
                        }

                        if (self.$input.hasClass('is-edit')) {
                            var old_value = self.$input.attr('data-old-value');

                            if (old_value === value) {
                                self._cancel();
                                return true;
                            }

                            self._update(old_value, value);
                            self._clean();
                            self._fill();
                        } else {
                            if (self._autocomplete._isSet() && self._autocomplete._get('only')) {
                                if ($.inArray(value, self._autocomplete._get('values')) < 0) {
                                    self._autocomplete._hide();
                                    self._errors('autocomplete_only');
                                    return false;
                                }
                            }

                            if (self._exists(value)) {
                                self.$input.removeClass('is-autocomplete');
                                self._errors('exists');

                                var $tag = $('[data-tag="' + value + '"]', self.$list);

                                $tag.addClass('is-exists');

                                setTimeout(function () {
                                    $tag.removeClass('is-exists');
                                }, 300);
                                return false;
                            }

                            self._buildItem(value);
                            self._insert(value);
                        }

                        self._cancel();
                        self._updateValue();
                        self.destroy();
                        self._autocomplete._build();

                        self._setInstance(self);

                        self.$input.focus();

                        return false;
                    });

                    // Add input event to filter on each keystroke
                    self.$input.on('input', function(e) {
                        var value = self.$input.val().trim();
                        self._autocomplete._init(true);
                        if (value.length > 0) {
                            self._autocomplete._show();
                        } else {
                            self._autocomplete._show(); // Show all when empty
                        }
                    });
                };

                /**
                 * Init edit input when a tag is focused
                 *
                 * @returns {void}
                 */
                self.edit = function () {
                    self.$list.on('click', '.' + self.ITEM_CLASS, function (e) {
                        if ($(e.target).hasClass('close-item') || false === self.options.editable || (self._autocomplete._isSet() && self._autocomplete._get('only'))) {
                            self._cancel();
                            return true;
                        }

                        var $item = $(this).addClass('is-edit');
                        var value = $('.value', $item).text();

                        self.$input.width($item.outerWidth()).insertAfter($item).addClass('is-edit').attr('data-old-value', value).val(value).focus();

                        self._bindEvent('selected');

                        self.$input.on('blur', function () {
                            self._cancel();
                            self._bindEvent('unselected');
                        });
                    });
                };

                /**
                 * Delete tag
                 *
                 * @returns {void}
                 */
                self.destroy = function () {
                    $('.' + self.ITEM_CLASS, self.$list).off('click').on('click', '.close-item', function () {

                        var $item = $(this).parent('.' + self.ITEM_CLASS);
                        var value = $('.value', $item).text();

                        $item.addClass('is-closed');

                        setTimeout(function () {
                            self._pop(value);
                            self._updateValue();
                            $item.remove();

                            self._autocomplete._build();

                            self.$input.focus();

                            self._setInstance(self);
                        }, 200);
                    });
                };

                /**
                 * Build and inject tag into HTML list
                 *
                 * @returns {void}
                 */
                self._buildItem = function (value) {
                    var $content = $(self.ITEM_CONTENT.replace('%s', value));
                    var $item = $('<span>').addClass(self.ITEM_CLASS + ' is-closed').attr('data-tag', value).html($content);

                    $item.insertBefore(self.$input).delay(100).queue(function () {
                        $(this).removeClass('is-closed');
                    });
                };

                /**
                 * Returns tag index
                 *
                 * @returns {number}
                 */
                self._getIndex = function (value) {
                    return self.tags.indexOf(value);
                };

                /**
                 * Remove extra tags only if > max option and concat user tags
                 *
                 * @returns {void}
                 */
                self._concatenate = function () {
                    if (self.options.max > 0) {
                        if (self.options.tags.length > self.options.max) {
                            self.options.tags.splice(-Math.abs(self.options.tags.length - self.options.max));
                        }
                    }

                    self.tags = self.tags.concat(self.options.tags);
                };


                /**
                 * Get default values
                 *
                 * @returns {void}
                 */
                self._getDefaultValues = function () {
                    if (self.$element.val().length > 0) {
                        self.tags = self.tags.concat(self.$element.val().split(self.KEY_COMMA));
                    } else {
                        self.$element.prop('value', '');
                    }
                };

                /**
                 * Insert item
                 *
                 * @param {string} item
                 * @returns {void}
                 */
                self._insert = function (item) {
                    self.tags.push(item);

                    self._bindEvent(['change', 'create']);
                };

                /**
                 * Swap tag value
                 *
                 * @param {string} old_value
                 * @param {string} new_value
                 * @returns {void}
                 */
                self._update = function (old_value, new_value) {
                    var index = self._getIndex(old_value);
                    self.tags[index] = new_value;

                    self._bindEvent(['change', 'update']);
                };

                /**
                 * Delete item based on value parameter
                 *
                 * @param {string} value
                 * @returns {void}
                 */
                self._pop = function (value) {
                    var index = self._getIndex(value);

                    if (index < 0) {
                        return false;
                    }

                    self.tags.splice(index, 1);

                    self._bindEvent(['change', 'destroy']);
                };

                /**
                 * Reset input field
                 *
                 * @returns {void}
                 */
                self._cancel = function () {
                    $('.' + self.ITEM_CLASS).removeClass('is-edit');

                    self.$input
                        .removeClass('is-edit is-autocomplete')
                        .removeAttr('data-old-value style')
                        .val('')
                        .appendTo(self.$list);
                };

                /**
                 * Autocomplete object
                 *
                 * @returns {object}
                 */
                self._autocomplete = {

                    /**
                     * Is autocomplete list have values
                     *
                     * @returns {boolean}
                     */
                    _isSet: function () {
                        return self.options.autocomplete.values.length > 0;
                    },

                    /**
                     * Init autocomplete
                     *
                     * @param {boolean | undefined} filter
                     * @returns {boolean | void}
                     */
                    _init: function (filter) {
                        if (!self._autocomplete._isSet()) {
                            return false;
                        }

                        self._autocomplete._build(filter);
                    },

                    /**
                     * Build autocomplete HTML list
                     *
                     * @param {boolean | undefined} filter
                     * @returns {void}
                     */
                    _build: function (filter) {
                        var value = self.$input.val().trim().toLowerCase();

                        if (self._autocomplete._exists()) {
                            self.$autocomplete.remove();
                        }

                        self.$autocomplete = $('<ul>').addClass(self.AUTOCOMPLETE_LIST_CLASS);

                        // Filter für Autocomplete-Werte
                        var filteredValues = self._autocomplete._get('values').filter(function(v) {
                            // Bei leerer Eingabe alle anzeigen
                            if (value.length === 0) {
                                return true;
                            }

                            // Nur Werte anzeigen, die mit dem Eingabetext beginnen
                            if (v.toLowerCase().indexOf(value) === 0) {
                                return true;
                            }

                            return false;
                        });

                        // Keine Ergebnisse - keine Liste anzeigen
                        if (filteredValues.length === 0 && value.length > 0) {
                            return;
                        }

                        filteredValues.forEach(function (v) {
                            var li = self.AUTOCOMPLETE_ITEM_CONTENT.replace('%s', v);
                            if (v === '|' || v === '-') {
                                var $item = $.inArray(v, self.tags) >= 0 ? $(li) : $(li);
                            } else {
                                var $item = $.inArray(v, self.tags) >= 0 ? $(li).addClass('is-disabled') : $(li);
                            }
                            $item.appendTo(self.$autocomplete);
                        });

                        self._autocomplete._bindClick();

                        $(document)
                            .not(self.$autocomplete)
                            .on('click', function () {
                                self._autocomplete._hide();
                            });
                    },

                    /**
                     * Bind click event on list item
                     *
                     * @returns {void}
                     */
                    _bindClick: function () {
                        $(self.$autocomplete).off('click').on('click', '.' + self.AUTOCOMPLETE_ITEM_CLASS, function (e) {
                            if ($(e.target).hasClass('is-disabled')) {
                                return false;
                            }

                            self.$input.addClass('is-autocomplete').val($(this).text());
                            self._autocomplete._hide();
                            self._bindEvent('autocompleteTagSelect');

                            var e = $.Event("keyup");
                            e.key = self.KEY_ENTER;
                            self.$input.trigger(e);
                        });
                    },

                    /**
                     * Show autocomplete list
                     *
                     * @returns {boolean | void}
                     */
                    _show: function () {
                        if (!self._autocomplete._isSet()) {
                            return false;
                        }

                        // Wenn die Liste leer ist (kein Match), nicht anzeigen
                        if (self.$autocomplete && self.$autocomplete.children().length === 0) {
                            self._autocomplete._hide();
                            return false;
                        }

                        self.$autocomplete
                            .css({
                                'left': self.$input[0].offsetLeft,
                                'minWidth': self.$input.width()
                            })
                            .insertAfter(self.$input);

                        setTimeout(function () {
                            self._autocomplete._bindClick();
                            self.$autocomplete.addClass('is-active');
                        }, 100);
                    },

                    /**
                     * Hide autocomplete list
                     *
                     * @returns {void}
                     */
                    _hide: function () {
                        if (self.$autocomplete) {
                            self.$autocomplete.removeClass('is-active');
                        }
                    },

                    /**
                     * Get autocomplete item based on key parameter
                     *
                     * @param {string} key
                     * @returns {boolean | void}
                     */
                    _get: function (key) {
                        return self.options.autocomplete[key];
                    },

                    /**
                     * Returns true if autocomplete is defined, false otherwise
                     *
                     * @returns {boolean}
                     */
                    _exists: function () {
                        return undefined !== self.$autocomplete;
                    }
                };

                /**
                 * Update plugin binded input value
                 *
                 * @returns {void}
                 */
                self._updateValue = function () {
                    self.$element.prop('value', self.tags.join(self.KEY_COMMA));
                };

                /**
                 * Define focus events on input
                 *
                 * @returns {void}
                 */
                self._focus = function () {
                    self.$input.on('focus', function () {
                        self._bindEvent('focus');

                        if (self._autocomplete._isSet() && !self.$input.hasClass('is-autocomplete') && !self.$input.hasClass('is-edit')) {
                            self._autocomplete._build();
                            self._autocomplete._show();
                        }
                    });
                };

                /**
                 * Convert array into object
                 *
                 * @param {string[]} arr
                 * @returns {object}
                 */
                self._toObject = function (arr) {
                    return arr.reduce(function (o, v, i) {
                        o[i] = v;
                        return o;
                    }, {});
                };

                /**
                 * Input validation
                 *
                 * @param {string} value
                 * @param {boolean} alert
                 * @returns {boolean}
                 */
                self._validate = function (value, alert) {
                    var type = '', re;

                    switch (true) {
                        case !value:
                        case undefined === value:
                        case 0 === value.length:
                            self._cancel();
                            type = 'empty';
                            break;
                        case value.length > 0 && value.length < self.options.minLength:
                            type = 'minLength';
                            break;
                        case value.length > self.options.maxLength:
                            type = 'maxLength';
                            break;
                        case self.options.max > 0 && self.tags.length >= self.options.max:
                            if (!self.$input.hasClass('is-edit')) {
                                type = 'max';
                            }
                            break;
                        case self.options.email:
                            re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                            if (!re.test(value)) {
                                type = 'email';
                            }
                            break;
                    }

                    if (type.length > 0) {
                        return alert ? self._errors(type) : type;
                    }

                    return true;
                };

                /**
                 * Returns true if value is in tags list, false otherwise
                 *
                 * @param {string} value
                 * @returns {boolean}
                 */
                self._exists = function (value) {
                    if (value !== '|' && value !== '-') {
                        return $.inArray(value, self.tags) >= 0;
                    }
                }

                /**
                 * Get error message
                 *
                 * @param {string} type
                 * @returns {boolean}
                 */
                self._errors = function (type) {
                    if (0 === type.length) {
                        return false;
                    }

                    if (self._autocomplete._exists()) {
                        self.$autocomplete.remove();
                    }

                    self._displayErrors(self.options.errors[type].replace('%s', self.options[type]), type);

                    return false;
                };

                /**
                 * Display errors(s) if any
                 *
                 * @param {string} error
                 * @param {string} type
                 * @returns {void}
                 */
                self._displayErrors = function (error, type) {
                    var $error = $(self.ERROR_CONTENT.replace('%s', error)).attr('data-error', type);
                    var timeout = self.options.errors.timeout;

                    if ($('.' + self.ERROR_CLASS + '[data-error="' + type + '"]').length) {
                        return false;
                    }

                    $error.hide().insertAfter(self.$list).slideDown();

                    if (!timeout || timeout <= 0) {
                        return false;
                    }

                    $('.' + self.ERROR_CLASS).on('click', function () {
                        self._collapseErrors($(this));
                    });

                    setTimeout(function () {
                        self._collapseErrors();
                    }, timeout);
                };

                /**
                 * Clears error(s) if any
                 *
                 * @param {object} $elem
                 * @returns {void}
                 */
                self._collapseErrors = function ($elem) {

                    var $obj = $elem ? $elem : $('.' + self.ERROR_CLASS);

                    $obj.slideUp(300, function () {
                        $obj.remove();
                    });
                };

                /**
                 * Retrieve inputTags instance based on uniqid
                 *
                 * @returns {object}
                 */
                self._getInstance = function () {
                    return window.cke5InputTags.instances[self.UNIQID];
                };

                /**
                 * Store instance based on uniqid
                 *
                 * @returns {void}
                 */
                self._setInstance = function () {
                    window.cke5InputTags.instances[self.UNIQID] = self;
                };

                /**
                 * Returns true if elem exists on options object, false otherwise
                 *
                 * @returns {boolean}
                 */
                self._isSet = function (elem) {
                    return !(undefined === self.options[elem] || false === self.options[elem] || self.options[elem].length);
                };

                /**
                 * Call method based on method_name parameter if exists on options, returns false otherwise
                 *
                 * @param {string} method_name
                 * @param {object} self
                 * @returns {void}
                 */
                self._callMethod = function (method_name, self) {
                    if (undefined === self.options[method_name] || 'function' !== typeof self.options[method_name]) {
                        return false;
                    }

                    self.options[method_name].apply(this, Array.prototype.slice.call(arguments, 1));
                }

                /**
                 * Init an event
                 *
                 * @param {string | object} method
                 * @param {function} callback
                 * @returns {boolean}
                 */
                self._initEvent = function (method, callback) {
                    if (!method) {
                        return false;
                    }

                    switch (typeof method) {
                        case 'string':
                            callback(method, self);
                            break;
                        case 'object':
                            method.forEach(function (m, i) {
                                callback(m, self);
                            });
                            break;
                    }

                    return true;
                };

                /**
                 * Bind an event
                 *
                 * @param {string | object} method
                 * @returns {boolean}
                 */
                self._bindEvent = function (method) {
                    return self._initEvent(method, function (m, s) {
                        self._callMethod(m, s);
                    });
                };

                /**
                 * Unbind an event
                 *
                 * @param {string | object} method
                 * @returns {boolean}
                 */
                self._unbindEvent = function (method) {
                    return self._initEvent(method, function (m) {
                        self.options[m] = false;
                    });
                };

                self.init();

                self._bindEvent('init');

                self._setInstance(self);
            });

            return {
                on: function (method, callback) {
                    window.cke5InputTags.methods.event(method, callback);
                }
            };

        } else if (window.cke5InputTags.methods[options]) {
            var id = $(this).attr('data-uniqid');
            var _instance = window.cke5InputTags.instances[id];

            if (undefined === _instance) {
                return $.error("[undefined instance] No inputTags instance found.");
            }

            return window.cke5InputTags.methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            $.error("[undefined method] The method [" + options + "] does not exists.");
        }
    };

    $.fn.cke5InputTags.defaults = {
        tags: [],
        keys: [],
        minLength: 1,
        maxLength: 300,
        max: 60,
        email: false,
        only: true,
        init: false,
        create: false,
        update: false,
        destroy: false,
        focus: false,
        selected: false,
        unselected: false,
        change: false,
        autocompleteTagSelect: false,
        editable: false,
        autocomplete: {
            values: [],
            only: false
        },
        errors: {
            empty: 'Warning, you can not add an empty tag.',
            minLength: 'Warning, your tag must have at least %s characters.',
            maxLength: 'Warning, your tag must not exceed %s characters.',
            max: 'Attention, the number of tags must not exceed %s.',
            email: 'Warning, the email address you entered is not valid.',
            exists: 'Warning, this tag already exists!',
            autocomplete_only: 'Warning, you must select a value from the list.',
            timeout: 8000
        }
    };

})(jQuery);