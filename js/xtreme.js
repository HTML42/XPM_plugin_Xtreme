var XtremeObject = function () {
    var _this = this;
    setTimeout(function () {
        _this.init();
    }, 50);
};
XtremeObject.prototype.init = function () {
    var _this = this;
    if (isset(window.jQuery) && isset(window.$)) {
        _this.startup_calls();
    } else {
        setTimeout(function () {
            _this.init();
        }, 20);
    }
};
XtremeObject.prototype.startup_calls = function () {
    var _this = this;
    $$.foreach(Xtreme_startup_calls, function (callable, index) {
        execute(callable);
        delete Xtreme_startup_calls[index];
    });
    setTimeout(function () {
        _this.startup_calls();
    }, 700);
};
var Xtreme_startup_calls = Xtreme_startup_calls || [];
var Xtreme = new XtremeObject();


//Xtreme-Functions
function is_object(input) {
    return typeof input == 'object' && input != null && input.__proto__.constructor.name.toLowerCase() == 'object';
}
function isset(input) {
    return typeof input != 'undefined' && input != null;
}
function is_string(input) {
    return typeof input == 'string';
}
function is_array(input) {
    return typeof input == 'object' && input.__proto__.constructor.name.toLowerCase() == 'array';
}
function is_function(input) {
    return typeof input == 'function';
}
function execute(input) {
    if (is_function(input)) {
        return input.call(window);
    } else if (is_string(input) && is_function(window[input])) {
        return window[input].call(window);
    }
    return false;
}
function has_attr(object, attr) {
    if (typeof (object) != 'undefined' && object != null && typeof (object[attr]) != 'undefined' && object[attr] != null) {
        return object[attr];
    } else {
        return null;
    }
}

//Xtreme-Utilities
var $$ = Utilities = {
    hash: function (key, value) {
        if (typeof value == 'undefined') {
            return Utilities.hash_get(key, 'object');
        } else {
            return Utilities.hash_set(key, value);
        }
    },
    hash_get: function (key, type) {
        var lh = $$.hash_plain_get();
        var lh_json = lh[0] == '!' ? lh.substring(1) : '{}', lh_object;
        try {
            lh_object = JSON.parse(lh_json);
        } catch (err) {
            lh_object = {};
        }
        if (typeof key == 'undefined') {
            //Fetch all
        } else {
            //Fetch only specific
            lh_object = typeof lh_object[key] != 'undefined' ? lh_object[key] : {};
        }
        if (type != 'undefined') {
            switch (type) {
                case 'int':
                    lh_object = $$.intval(lh_object);
                    if (typeof lh_object != 'number') {
                        lh_object = 0;
                    }
                    break;
                case 'bool':
                    lh_object = !!lh_object;
                    break;
            }
        }
        return lh_object;
    },
    hash_plain_get: function () {
        return location.hash[0] == '#' ? location.hash.substring(1) : location.hash;
    },
    hash_set: function (key, value) {
        var current = Utilities.hash_get();
        current[key] = value;
        location.hash = '!' + JSON.stringify(current);
    },
    intval: function (value) {
        var val = 0, _i;
        switch (typeof value) {
            case 'string':
                val = value.match(/\d+/);
                if (val && typeof val[1] != 'undefined') {
                    val = parseInt(val[1]);
                } else if (val && typeof val[0] != 'undefined') {
                    val = parseInt(val[0]);
                } else {
                    val = 0;
                }
                break;
            case 'number':
                val = parseInt(value);
                break;
            case 'object':
                $$.foreach(value, function (inner_value, value_key) {
                    if (typeof inner_value == 'string') {
                        value[value_key] = parseInt(inner_value);
                    } else if (typeof inner_value == 'number') {
                        value[value_key] = parseInt(inner_value);
                    } else if (typeof inner_value == 'object') {
                        value[value_key] = $$.intval(inner_value);
                    } else {
                        value[value_key] = 0;
                    }
                });
                val = value;
                break;
            case 'function':
                val = value.call();
                if (typeof val == 'string') {
                    val = val.match(/\d+/);
                    if (val && typeof val[1] != 'undefined') {
                        val = parseInt(val[1]);
                    } else {
                        val = 0;
                    }
                } else if (typeof val == 'number') {
                    val = parseInt(val);
                } else {
                    val = 0;
                }
                break;
        }
        return val;
    },
    validate_to_int: function (value) {
        if (typeof value == 'string') {
            value = $$.intval(value.replace('px', ''));
        } else {
            value = $$.intval(value);
        }
        return value;
    },
    /**
     * 
     * @param {type} items
     * @param {type} function(item, key)
     * @returns {Boolean}
     */
    foreach: function (items, callback) {
        if (typeof items == 'object' && typeof callback == 'function') {
            var key;
            if (is_array(items)) {
                for (key = 0; key < items.length; key++) {
                    __exec(key);
                }
            } else {
                for (key in items) {
                    __exec(key);
                }
            }
            function __exec(key) {
                var item = items[key];
                callback.call(items, item, key);
            }
            return true;
        } else {
            return false;
        }
    },
    /**
     * Loops throught the Object and creates a now object
     */
    array_group: function (object, group_key, keep_keys) {
        keep_keys = (typeof keep_keys != 'undefined');
        var return_object = {}, group_value;
        $$.foreach(object, function (item, key) {
            if (isset(item[group_key])) {
                group_value = item[group_key].toString();
            } else {
                group_value = 'undefined';
            }
            //
            if (!isset(return_object[group_value])) {
                return_object[group_value] = (keep_keys ? {} : []);
            }
            //
            if (keep_keys) {
                return_object[group_value][key] = item;
            } else {
                return_object[group_value].push(item);
            }
        });
        return return_object;
    },
    /**
     * in_array() like in PHP, but this also works with arrays as first parameter
     */
    in_array: function (needle, haystack, type_force) {
        var includes_needle = false;
        if (typeof needle == 'object') {
            $$.foreach(needle, function (item) {
                if (!includes_needle && $$.in_array(item, haystack)) {
                    includes_needle = true;
                }
            });
        } else if (typeof needle == 'string' || typeof needle == 'number') {
            type_force = typeof type_force == 'string' ? type_force : false;
            if (type_force == 'string') {
                needle = needle.toString();
            } else if (type_force == 'int' || type_force == 'integer') {
                needle = $$.intval(needle);
            }
            includes_needle = $.inArray(needle, haystack) >= 0;
        }
        return includes_needle;
    },
    /**
     * fetches the length of objects, arrays, strings
     */
    length: function (item) {
        var l = null;
        if (typeof item == 'string') {
            l = item.length;
        } else if (typeof item == 'number') {
            l = item.toString().length;
        } else if (typeof item == 'object') {
            l = 0;
            if (isset(item.length)) {
                l = item.length;
            } else {
                $$.foreach(item, function () {
                    l++;
                });
            }
        }
        return l;
    },
    /**
     * 
     */
    rand: function (length) {
        if (typeof length != 'number') {
            length = 16;
        }
        var symbols = '0123456789ABCDEF', i, random = '';
        for (i = 0; i < length; i++) {
            random += symbols.charAt(Math.floor(Math.random() * symbols.length));
        }
        return random;
    },
    /**
     * 
     */
    transition_duration: function (element) {
        if ($$.msie_low) {
            var duration = '0s';
        } else {
            var duration = $(element).css('transition-duration');
        }
        var duration_match = duration.match(/(\d+)\.*(\d*)s/);
        if (typeof duration != 'string') {
            return 0;
        } else if (duration_match && duration_match.length && duration_match.length >= 3) {
            if (duration_match[2].length < 1) {
                duration_match[2] = '000';
            } else if (duration_match[2].length < 2) {
                duration_match[2] += '00';
            } else if (duration_match[2].length < 3) {
                duration_match[2] += '0';
            } else if (duration_match[2].length > 3) {
                duration_match[2] = duration_match[2].substring(0, 3);
            }
            return parseInt(duration_match[1] + duration_match[2]);
        } else {
            return 0;
        }
    },
    /**
     * 
     */
    scroll_to: function (element, offset, time) {
        var target_top = null;
        if (typeof element == 'string' || typeof element == 'number') {
            var top_try = $.trim(element);
            if (top_try == parseInt(top_try)) {
                //Element is a Number
                target_top = top_try;
            } else if ($(element).length) {
                //Element is a CSS-Path
                target_top = $(element).offset().top;
            }
        } else if ($(element).length) {
            //Element is an Element
            target_top = $(element).offset().top;
        }
        if (!isset(offset)) {
            offset = 10;
        }
        if (!isset(time)) {
            time = 500;
        }
        $$.foreach($$.global_scroll_offset_extras, function (additional_offset) {
            if (typeof additional_offset == 'number') {
                offset += additional_offset;
            } else if ($(additional_offset).length) {
                offset += $(additional_offset).outerHeight();
            }
        });

        if (typeof target_top == 'number') {
            $('html, body').animate({
                scrollTop: target_top - offset - $$.global_scroll_offset
            }, $$.intval(time));
        }
    },
    global_scroll_offset: 0,
    global_scroll_offset_extras: [],
    global_scroll_offset_max: function (value) {
        value = $$.validate_to_int(value);
        $$.global_scroll_offset = Math.max($$.global_scroll_offset, value);
    },
    global_scroll_offset_set: function (value) {
        $$.global_scroll_offset = $$.validate_to_int(value);
    },
    array_shuffle: function shuffle(a) {
        var j, x, i;
        for (i = a.length; i; i--) {
            j = Math.floor(Math.random() * i);
            x = a[i - 1];
            a[i - 1] = a[j];
            a[j] = x;
        }
        return a;
    },
    window_top: function () {
        return $$.msie ? document.documentElement.scrollTop : window.scrollY;
    }
};

//Enhancement of jQuery
if (typeof $ == 'object') {
    $.postJSON = function (url, data, callback) {
        $.ajax({
            url: url,
            type: "POST",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: callback
        });
    };
}
