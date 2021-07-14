/**
 * Created by alexandr.tomenko on 5/18/2017.
 */
window.validation = (function ($) {
    var pub = {

        isEmpty: function (value) {
            return value === null || value === undefined || ($.isArray(value) && value.length === 0) || value === '' || value === 0;
        },

        addMessage: function (messages, message, value) {
            messages.push(message.replace(/\{value\}/g, value));
        },

        required: function (attribute, value, messages, options) {
            if (attribute.type === 'radioCommentList') {
                pub.radioCommentListRequired(attribute, value, messages, options);
                return
            }

            var valid = false;
            if (options.result != undefined) {
                valid = options.result
            } else {
                var isString = typeof value == 'string' || value instanceof String;
                if (!pub.isEmpty(isString ? $.trim(value) : value)) {
                    valid = true;
                }
            }

            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },
        radioCommentListRequired(attribute, value, messages, options) {
            if (!value.hasOwnProperty('radio')) {
                pub.addMessage(messages, options.message, value);
                return;
            }
            if (!value.hasOwnProperty('comment')) {
                pub.addMessage(messages, options.message, value);
                return;
            }
            if (!attribute.hasOwnProperty('inputItems')) {
                pub.addMessage(messages, options.message, value);
                return;
            }


            let _isNeedComment = function (radioValue, items) {
                let commentOptions = _getCommentOptions(radioValue, items);
                if (commentOptions.hasOwnProperty('visible')) {
                    return commentOptions.visible;
                }
                return false
            };

            let _getCommentOptions = function (radioValue, items) {
                for (let i = 0; i < items.length; i++) {
                    if (radioValue == items[i]['radioOptions']['value']) {
                        return  items[i]['commentOptions'];
                    }
                }
                return [];
            };

            let commentValue = value.comment;
            let radioValue = value.radio;
            let isNeedComment = _isNeedComment(radioValue, attribute.inputItems);

            if (pub.isEmpty(radioValue)) {
                pub.addMessage(messages, options.message, value);
                return;
            }
            if (isNeedComment && pub.isEmpty($.trim(commentValue))) {
                pub.addMessage(messages, options.message, value);
            }
        },

        number: function (value, messages, options) {
            var valid = true;
            if (options.result != undefined) {
                valid = options.result;
            }
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            if (!$.isNumeric(value)) {
                valid = false;
            }

            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },

        match: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value) && value != '0') {
                return;
            }

            let pattern = this.getPattern(options.pattern);
            if (pattern && !pattern.test(value)) {
                pub.addMessage(messages, options.message, value);
            }
        },

        getPattern: function(pattern) {
            try {
                if (!pattern || !pattern.trim()) {
                    return null;
                }

                pattern = pattern.trim();
                let modifiers = '';
                let firstSymbol = pattern.charAt(0);
                if (pattern.lastIndexOf(firstSymbol) === 0) {
                    return null;
                }
                let lastSymbol = pattern.charAt(pattern.length - 1);
                if (firstSymbol !== lastSymbol) {
                    let regexpModifiers = new RegExp('\\'+firstSymbol+'(\\w*)$');
                    let matches = pattern.match(regexpModifiers);
                    if (matches) {
                        modifiers = matches[1];
                    }
                    pattern = pattern.replace(firstSymbol + modifiers, firstSymbol);
                }
                pattern = pattern.substring(1, pattern.length - 1);

                return new RegExp(pattern, modifiers);
            } catch(err) {
                console.log(err.message);
                return null;
            }
        },

        email: function (value, messages, options) {
            var valid = true;
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }
            if (options.pattern === undefined) {
                return;
            }

            if (typeof value != 'string') {
                valid = false;
            } else if (value.length > 254) {
                valid = false;
            } else {
                valid = options.pattern.test(value);
            }

            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },

        maxLength: function (value, messages, options) {
            var valid = true;
            if (options.max === undefined) {
                return;
            }

            if (typeof value != 'string') {
                valid = false;
            } else if (value.length > options.max) {
                valid = false;
            }
            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },

        minLength: function (value, messages, options) {
            var valid = true;
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }
            if (options.min === undefined) {
                return;
            }

            if (typeof value != 'string') {
                valid = false;
            } else if (value.length < options.min) {
                valid = false;
            }
            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },

        exactLength: function (value, messages, options) {
            var valid = true;
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }
            if (options.length === undefined) {
                return;
            }

            if (typeof value != 'string') {
                valid = false;
            } else if (value.length != options.length) {
                valid = false;
            }
            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },

        trim: function (attribute,value, messages, options) {
            if ( !pub.isEmpty(value)) {
                value = $.trim(value);
                $(attribute.id).val(value);
            }
            return value;
        },

        stripTags: function (attribute,value, messages, options) {
            if ( !pub.isEmpty(value)) {
                value = value.replace(/<\/?[^>]+(>|$)/g, "");
                value = value.replace(/[<>]/g, "");
                value = $.trim(value);
                $(attribute.id).val(value);
            }
            return value;
        },

        noHtml: function (value, messages, options) {
            let valid = true;

            if (typeof value != 'string') {
                valid = false;
            } else {
                valid = value.replace(/<\/?[^>]*(>|$)/g, "") === value;
            }

            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },

        threeFieldsDate: function(messages, value, options, field) {
            let yearField = options.yearField;
            let monthField = options.monthField;
            let dayField = options.dayField;

            if (!yearField || !monthField || !dayField) {
                return;
            }

            yearField = field.fieldsList.findField(yearField);
            monthField = field.fieldsList.findField(monthField);
            dayField = field.fieldsList.findField(dayField);

            dayField.errorMessages = [];
            yearField.errorMessages = [];
            monthField.errorMessages = [];

            dayField.showErrors();
            yearField.showErrors();
            monthField.showErrors();

            let year, month, day;

            yearField.onChange(function(){}, 'change.threeFieldsDate');
            year = yearField.getValue();

            monthField.onChange(function(){}, 'change.threeFieldsDate');
            month = monthField.getValue();

            dayField.onChange(function(){}, 'change.threeFieldsDate');
            day = dayField.getValue();

            if (options.skipOnEmpty && (!year || !month || !day)) {
                return;
            }

            if (!options.skipOnEmpty && (!year || !month || !day)) {
                yearField.setError('');
                yearField.showErrors();
                monthField.setError('');
                monthField.showErrors();
                dayField.setError(options.requiredFieldMsg);
                dayField.showErrors();

                if (field === dayField) {
                    pub.addMessage(messages, options.requiredFieldMsg, value);
                } else {
                    pub.addMessage(messages, '', value);
                }

                return;
            }

            let date = year + '-' + month + '-' + day;
            let parsedDate = new Date(date);

            if (parsedDate.getDate() != day || parsedDate.getFullYear() < 1900) {
                yearField.setError('');
                yearField.showErrors();
                monthField.setError('');
                monthField.showErrors();
                dayField.setError(options.message);
                dayField.showErrors();

                if (field === dayField) {
                    pub.addMessage(messages, options.message, value);
                } else {
                    pub.addMessage(messages, '', value);
                }
            }
        },

        compare: function (attribute, value, messages, options, field) {
            if (attribute.type !== 'checkbox' && options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            var cValue, valid = true;
            let compareAttribute = options.compareAttribute;
            if (compareAttribute === null) {
                cValue = options.compareValue;
            } else {
                //find field in block
                let compareField = field.fieldsList.findField(compareAttribute);
                if (!compareField) {
                    //find field in form
                    field.fieldsList.parentFormElement.getFormFields().forEach(function(itemField) {
                        if (itemField.getName() === compareAttribute) {
                            compareField = itemField;
                        }
                    });
                }

                if (!compareField) {
                    return;
                }

                compareField.onChange(function(){field.validate()}, 'change.compare');
                cValue = compareField.getValue();
            }

            if (options.type === 'number') {
                value = parseFloat(value);
                cValue = parseFloat(compareValue);
            }

            switch (options.operator) {
                case '==':
                    valid = value == cValue;
                    break;
                case '!=':
                    valid = value != cValue;
                    break;
                case '>':
                    valid = value > cValue;
                    break;
                case '>=':
                    valid = value >= cValue;
                    break;
                case '<':
                    valid = value < cValue;
                    break;
                case '<=':
                    valid = value <= cValue;
                    break;
                default:
                    valid = false;
                    break;
            }

            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },

        uniqueGlobal: function(value, messages, options) {
            let attribute = '.' + options.attribute.toLowerCase();
            let container = '.' + options.formName;
            let $fields = $(container + ' .form-input' + attribute);

            // get invalid values
            let values = [];
            let invalidValues = [];
            for (let i = 0; i < $fields.length; i++) {
                let fieldValue = $($fields[i]).val();
                if (values.includes(fieldValue)) {
                    invalidValues.push(fieldValue);
                }
                values.push(fieldValue);
            }

            // clear errors
            for (let i = 0; i < $fields.length; i++) {
                $($fields[i]).trigger('removeError', [{message: options.message}]);
            }

            // trigger error
            for (let i = 0; i < $fields.length; i++) {
                let fieldValue = $($fields[i]).val();
                if (invalidValues.includes(fieldValue)) {
                    $($fields[i]).trigger('setError', [{message : options.message}]);
                }
            }

            if (invalidValues.includes(value)) {
                pub.addMessage(messages, options.message, value);
            }
        },

        ssntin: function(attribute, value, messages, options) {

            let currentName = attribute.name;
            let currentRealName = attribute.realName;
            let countryFieldName = options.countryField;

            let reg = new RegExp('\\[' + currentName + '\\]', 'i');
            let countryFieldRealName = currentRealName.replace(reg, '\[' + countryFieldName + '\]');

            let countryField = $('[name="' + countryFieldRealName + '"]');
            let countryValue = countryField.val();

            if (countryField.length) {
                countryField.off('change.ssntin');
                countryField.on('change.ssntin', function () {
                    let triggerInput = $('[name="' + currentRealName + '"]');
                    triggerInput.trigger('change');
                    triggerInput.trigger('blur');
                });
            }

            let data = {
                modelData: {
                    [currentName]:value,
                    [countryFieldName]:countryValue
                },
                validatorName: 'ssntin',
                validationFields: [currentName],
                validatorParams: options
            };

            let resultCallback = function(result){
                if (!result.success) {
                    pub.addMessage(messages, options.message, value);
                }
            };

            $.ajaxSetup({async: false});
            $.post(
                '/ajax_validation/validate',
                data,
                resultCallback.bind(this),
                'json'
            );
            $.ajaxSetup({async: true});
        },

        boolean: function (value, messages, options) {
            if (options.value === undefined || options.strict) {
                return;
            }

            var valid = (!options.strict && value == options.value)
                || (options.strict && value === options.value);

            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },

        file: function (value, messages, options) {
            var files = getUploadedFiles(messages, options);
            $.each(files, function (i, file) {
                validateFile(file, messages, options);
            });
        },

        inArray: function (value, messages, options) {
            if (options.list === undefined) {
                return;
            }
            let list = objToArray(JSON.parse(options.list));
            let exist = false;
            for (let i = 0; i < list.length; i++) {
                if (list[i] == value) {
                    exist = true;
                    break;
                }
            }
            if (!exist) {
                pub.addMessage(messages, options.message, value);
            }
        },

        password: function (value, messages, options) {
            var otherSymbols = value.replace(/[a-zA-Z0-9-!"#$%&'()*+,.\/:;<=>?@\[\]^\\_`{|}~]/gi, '');

            if (otherSymbols.length !== 0) {
                pub.addMessage(messages, options.message, value);
            }
        },

        ip: function (value, messages, options) {
            var pattern = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }
            if (!pattern.test(value)) {
                pub.addMessage(messages, options.message, value);
            }
        },

        recaptcha: function (value, messages, options) {
            return;
        },

        personalDataFormat: function(value, messages, options) {
            if (options.patternsList) {
                for (var key in options.patternsList) {
                    let pattern = this.getPattern(options.patternsList[key]);
                    if (pattern && !pattern.test(value)) {
                        pub.addMessage(messages, options.message, value);
                        return;
                    }
                }
            }
        }
    };

    function getUploadedFiles(messages, options) {
        if (typeof File === "undefined") {
            return [];
        }

        var files = $('input[name="' + options.attribute + '"]').get(0).files;
        if (!files) {
            messages.push(options.messages.message);
            return [];
        }

        if (files.length === 0) {
            if (!options.param.skipOnEmpty) {
                messages.push(options.messages.required);
            }
            return [];
        }

        return files;
    }

    function validateFile(file, messages, options) {
        if (options.param.extensions && options.param.extensions.length > 0) {
            var index = file.name.lastIndexOf('.');
            var ext = index === -1 ? '' : file.name.substr(index + 1, file.name.length).toLowerCase();
            var extensions = options.param.extensions;
            if (extensions.indexOf(ext) === -1) {
                messages.push(prepareMsg(options.messages.extensions, {'\\{file\\}' : file.name, '\{extensions\}': extensions.join(', ')}));
            }
        }


        if (options.param.maxSize && options.param.maxSize < file.size) {
            messages.push(prepareMsg(options.messages.tooBig, {'\\{file\\}' : file.name, '\{formatLimit\}': options.param.maxSize}));
        }

        if (options.param.minSize && options.param.minSize > file.size) {
            messages.push(prepareMsg(options.messages.tooSmall, {'\\{file\\}' : file.name, '\{formatLimit\}': options.param.minSize}));
        }
    }

    function prepareMsg(message, param) {
        if(message && typeof param === 'object') {
            for (var i in param) {
                message = message.replace(new RegExp(i, 'g'), param[i]);
            }
        }
        return message;
    }

    function objToArray(obj) {
        if (typeof obj === 'object') {
            return Object.keys(obj).map(function (key) { return obj[key]; });
        }
        return obj;
    }

    return pub;
})(jQuery);