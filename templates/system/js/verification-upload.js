/**
 *
 * @param settings
 * @constructor
 */

$ = jQuery;

function Uploader(settings) {
    /** Merge exist and new settings */
    this.settings = $.extend({
      canUploadDocuments: true,
      forbidUploadDocumentsReason: ''
    }, settings || {});
}

Uploader.prototype = {
    /**
     * @param {string} str
     * @param {Array} vars
     * @returns {string}
     */
    strtr: function (str, vars) {
        return vars.reduce(function(accumulator, currentValue) {
            return accumulator.replace(currentValue[0], currentValue[1]);
        }, str);
    },

    bytesToUnit: function(bytes) {
        var sizes = ['bytes', 'Kb', 'Mb', 'Gb', 'Tb'];
        if (bytes === 0) return [0, 'Byte'];
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return [Math.round(bytes / Math.pow(1024, i), 2), sizes[i]];
    },

    init: function () {
        if (this.settings.canUploadDocuments === false) {
            $(this.settings.buttonUpload).addClass('disabled');
            $('.form-verification__comment-box', this.settings.formSelector).remove();
            $('.form-verification__select-box', this.settings.formSelector).remove();
            $('.form-verification__notice', this.settings.notificationBox).html(this.settings.forbidUploadDocumentsReason);
            return false;
        }

        //delete default class
        $(this.settings.formSelector).find('.form-row').removeClass('form-row');
        $(this.settings.formSelector).find('.form-input').removeClass('form-input');
        $(this.settings.formSelector).find('.file').removeClass('file');

        this.form = $(this.settings.formSelector);
        this.selected();

        this.initUploadBtn();
        $(this.settings.select).change();
    },

    /** Form object */
    form: {},

    selected: function () {
        var that = this;
        $(this.settings.select).on('change', function () {
            for (var i = 0; i < that.settings.commentsArr.length; i++) {
                if(that.settings.commentsArr[i] == $(this).val()){
                    $(that.settings.commentBox).show()
                        .find('.form-verification__comment').attr('disabled', false);
                    that.scrollSubmitted();
                    break;
                } else {
                    $(that.settings.commentBox).hide()
                        .find('.form-verification__comment').attr('disabled', true);
                }
            }
            if (
                $(that.settings.comment).length > 0 &&
                $(that.settings.comment)[0].hasAttribute('disabled') == false &&
                $(that.settings.comment)[0].value == ''
            ) {
                $(that.settings.buttonUpload).addClass('disabled');
            } else {
                $(that.settings.buttonUpload).removeClass('disabled');
            }
        });


    },

    /** Data object */
    data: {},

    /** Init choose file button to upload on change if value not empty */
    initUploadBtn: function () {
        var that = this;
        this.form.find(this.settings.inputSelector).change(function () {
            var file = $(this).val();

            if (file.length === 0) {
                $(this).val('');
                return
            }

            that.data[0] = {
                name: this.files[0].name,
                text: ''
            };

            if(this.files[0].size > that.settings.maxUploadFileSize) {
                var numberAndUnits = that.bytesToUnit(that.settings.maxUploadFileSize);
                that.errorSubmit(that.strtr(that.settings.maxSizeErrorText, [
                    ['{formatLimitInUnits}', numberAndUnits[0]],
                    ['{formatUnit}', numberAndUnits[1]]
                ]));

                $(this).val('');
                return
            }

            if (that.settings.haveCategoryTypes) {
                var text = $(that.settings.select).find('option:selected').text();
                that.data[0].text = text;

                that.submitForm();
                $(this).val('');
            } else {
                that.submitForm();
                $(this).val('');
            }
        });

        if ($(that.settings.comment).length > 0) {
            if($(that.settings.comment)[0].value == '') {
                $(that.settings.buttonUpload).removeClass('disabled');
            } else {
                $(that.settings.buttonUpload).addClass('disabled');
            }

            $(that.settings.comment).on('input', function () {
                if($(that.settings.comment)[0].value == '') {
                    $(that.settings.buttonUpload).addClass('disabled');
                } else {
                    $(that.settings.buttonUpload).removeClass('disabled');
                }
            });
        };
    },

    setDefaultForm: function () {
        $(this.settings.inputSelector).show();
        $(this.settings.buttonUpload).removeClass('loading');
        $(this.settings.buttonUpload).addClass('used');
    },

    setListFiles: function () {
        var name = this.data[0].name ? this.data[0].name : this.data.name;
        var text = this.data[0].text ? this.data[0].text : '';
        var html = "\n<li class=\"form-verification__list-item\">\n\t" +
            "<i>" + this.settings.iconSvg + "</i> \n" +
            "<p class=\"form-verification__list-title\" title=\"" + name + "\">" + name + "</p>\n" +
            "<span class=\"form-verification__list-type\">\n" +
            "" + text + "\n" +
            "</span>\n" +
            "</li>\n";

        $(this.settings.fileList).append(html);
    },

    successNotification: function () {
        $(this.settings.notificationError).hide();
        $(this.settings.notificationSuccess).show();
        this.scrollSubmitted();
    },

    successSubmit: function () {
        this.setDefaultForm();
        this.setListFiles();
        this.successNotification();
        if ($(this.settings.comment).length > 0 && $(this.settings.comment).is(':visible')){
            $(this.settings.comment).val('');
            $(this.settings.buttonUpload).addClass('disabled');
        }
    },

    errorNotification: function (err) {
        $(this.settings.errorTitle).html(this.data[0].name);
        $(this.settings.errorDescription).html(err);
        $(this.settings.notificationSuccess).hide();
        $(this.settings.notificationError).show();

        this.scrollSubmitted();
    },

    errorSubmit: function (err) {
        this.setDefaultForm();
        this.errorNotification(err);
    },

    scrollSubmitted: function() {
        //hard code for old browsers and iframe
        $('html').css({'height': '100%' });
        var heightDocument = $('html').height();
        $('html').css({'height': 'auto' });

        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + heightDocument;
        var elemTop = $(this.settings.notificationBox).offset().top;
        var elemBottom = elemTop + $(this.settings.notificationBox).height();

        if(!((elemBottom <= docViewBottom) && (elemTop >= docViewTop))) {
            $('html,body').animate({
                scrollTop:  $(this.settings.scrollUpload).offset().top - heightDocument
            }, 750);
        }
    },

    /** do form submit with loader */
    submitForm: function () {
        if (!this.settings.canUploadDocuments) {
           return false;
        }

        var that = this;
        $(this.settings.inputSelector).hide();
        $(this.settings.buttonUpload).addClass('loading');

        $(this.form).submit(
            $.ajax({
                url: that.settings.formUrl,
                type: 'POST',
                data: new FormData(this.form[0]),
                processData: false,
                contentType: false,
                beforeSend:  function(xhr){
                    if ($(that.settings.comment).length > 0){
                        $(that.settings.commentBox).removeClass('error');
                        if($(that.settings.comment)[0].value == '' && $(that.settings.comment)[0].hasAttribute('disabled') == false) {
                            xhr.abort();
                            $(that.settings.commentBox).addClass('error');
                            $(that.settings.inputSelector).show();
                            $(that.settings.buttonUpload).removeClass('loading');
                        }
                    }
                },
                success: function (e) {
                    var success = JSON.parse(e);
                    if(success.isSuccess === true) {
                        that.successSubmit();
                    } else {
                        that.errorSubmit(success.validationErrors);
                    }
                },
                error: function (e) {
                    if(e.readyState === 0) {
                        that.errorSubmit('Network error');
                        return
                    }
                    var err = JSON.parse(e.responseText);
                    that.errorSubmit(err.validationErrors);
                }
            })
        );
    }
};