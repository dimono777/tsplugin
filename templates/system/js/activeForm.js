(function($) {

  $.fn.activeForm = function(initData) {
    if ($(this).length) {
      return methods.initActiveForm.apply(this, arguments);
    }
  };
  $.fn.activeMultiForm = function(initData) {
    if ($(this).length) {
      return methods.initActiveMultiForm.apply(this, arguments);
    }
  };

  var attributeDefaults = {
    id: undefined, // input id selector
    container: undefined, // field container selector
    error: '.error', // field error selector
    errorTag: 'small', // field error tag
    validateOnChange: true, // validation when a change is detected
    validateOnBlur: false, // validation when the input loses focus
    validate: undefined, //client validating function
    status: 0,
    cancelled: false,
    value: undefined,
    name: null,
    realName: null,
    events: [],
  };

  var settings = {
    validateOnSubmit: true,
    errorCssClass: 'has-error',
    successCssClass: 'has-success',
    ajaxEnable: false,
    ajaxCallbackDefault: 'ajaxCallbackActiveForm',
    /**
     * Form submission status
     * true - sending form data(with this status, data will not be re-sent to the server)
     * There already exists a similar attribute "submitting",
     * but it does not blocks double send of form. Looks like it is related with validation only
     */
    submitInProgress: false,
    preLoaderEnable: false,
    preLoaderClass: '',
    tooltip: {
      toggleClass: 'tooltip-toggle',
      targetClass: 'tooltip-target',
    },
  };
  var defaultBlockSettings = {
    name: null,
    index: null,
    container: null,
    repeatCnt: 0,
    repeatTemplate: null,
    repeatBtnClass: 'block-duplicate',
    repeatBtnTitle: 'Add',
  };

  var methods = {
    initActiveForm: function(attributes, formSettings) {
      let $element = $(this);
      let form = new Form($element, formSettings);
      $.each(attributes, function(i) {
        attributes[i] = $.extend({}, attributeDefaults, this);
        form.addField(attributes[i]);
      });
      form.initEvents();
      form.afterCreate();

      $element.data('activeForm', {
        form: form,
        attributes: attributes,
        settings: $.extend({}, settings, formSettings),
      });
    },

    initActiveMultiForm: function(attributes, formSettings) {
      let $element = $(this);
      let form = new MultiForm($element, formSettings);
      $.each(attributes, function(indexBlock) {
        let block = new Block(form, attributes[indexBlock].options, formSettings);
        $.each(attributes[indexBlock].fields, function(indexField) {
          attributes[indexBlock]['fields'][indexField] = $.extend({}, attributeDefaults, this);
          block.addField(attributes[indexBlock]['fields'][indexField]);
        });
        form.addBlock(block);
      });
      form.initEvents();
      form.afterCreate();

      $element.data('activeForm', {
        form: form,
        attributes: attributes,
        settings: $.extend({}, settings, formSettings),
      });
    },

    data: function() {
      return this.data('activeForm');
    },
  };

  window.ajaxCallbackActiveForm = function(result, $form) {
    let $systemMessageBlock = $('.system-message-block');
    let data = $form.data('activeForm');

    if (result.systemMessages && result.systemMessages.length) {
      $systemMessageBlock.text('');
      $.each(result.systemMessages, function() {
        $systemMessageBlock.append('<p class="system-message">' + this.text +
            '</p>');
      });
    }

    if (Object.keys(result.validationErrors).length) {
      data.form.setValidationErrors(result.validationErrors);
    }

    let ajaxTrigger = data.settings.ajaxTrigger;

    if (ajaxTrigger !== 'undefined' && typeof window[ajaxTrigger] ===
        'function') {
      window[ajaxTrigger](result, $form);
    }
  };

  let BaseForm = function($form, formSettings) {
    this.$form = $form;
    this.settings = $.extend({}, settings, formSettings);
    this.preLoader = new PreLoader(this.$form, this.settings.preLoaderEnable,
        this.settings.preLoaderClass);
    this.tooltip = tooltip;
    this.submitInProgress = false;
    this.withCaptcha = false;
    this.captchaId = null;
    this.captchaSuccess = false;

    this._init();
  };
  BaseForm.prototype = {
    findElementByQuerySelector: function(querySelector) {
      return this.$form.find(querySelector);
    },
    processValidation: function() {
      return true;
    },
    formButtonLock: function() {
    },
    formButtonUnlock: function() {
    },
    initEvents: function() {
    },
    setValidationErrors(errors) {
    },
    setWithCaptcha: function() {
      this.withCaptcha = true;
    },
    setCaptchaStatus: function(status) {
      this.captchaSuccess = status;
    },
    setCaptchaId: function(captchaId) {
      this.captchaId = captchaId;
    },
    getCaptchaId: function() {
      return this.captchaId;
    },
    submitForm: function() {
      this.$form.submit();
    },
    setupTooltip:function() {
      this.tooltip.init(this.$form, this.settings.tooltip);
    },
    getFormFields: function() {
      throw new FormException('Method should be overridden');
    },
    afterCreate: function() {
    },
    _init: function() {
      this.$form.on('submit', this._processSubmittingForm.bind(this));
      this.setupTooltip();
    },
    _processSubmittingForm: function() {
      if (this.submitInProgress) {
        return false;
      }

      this.submitInProgress = true;
      this.formButtonLock();
      this.preLoader.showLoader();
      let canSend = true;

      if (canSend && this.settings.validateOnSubmit) {
        canSend = this.processValidation();
      }

      if (canSend && this.withCaptcha && !this.captchaSuccess) {
        this.submitInProgress = false;
        this.formButtonUnlock();
        this.preLoader.hideLoader();
        grecaptcha.execute(this.captchaId);
        return false;
      }

      if (canSend && this.settings.ajaxEnable) {
        this._ajaxCall();
        return false;
      }

      this.submitInProgress = false;
      this.formButtonUnlock();
      this.preLoader.hideLoader();

      return canSend;
    },
    _ajaxCall: function() {
      let callBackFunc = this.settings.ajaxCallback;
      let callBackDefaultFunc = this.settings.ajaxCallbackDefault;
      let formData = this._getFormData();

      if (
          this.settings.ajaxEnable
          && typeof this.settings.ajaxUrl !== 'undefined'
          && (typeof window[callBackFunc] === 'function' || callBackFunc ===
          callBackDefaultFunc)
      ) {

        let tradersoftKey = 'tradersoft_submit';
        let tradersoftValue = (new FormData(this.$form[0])).get(tradersoftKey);
        if (tradersoftValue) {
          formData[tradersoftKey] = tradersoftValue;
        }

        $.post(
            this.settings.ajaxUrl,
            formData,
            this._ajaxResponse.bind(this),
            'json',
        ).always(this._afterAjaxCall.bind(this));
      } else {
        this._afterAjaxCall();
      }
    },
    _ajaxResponse: function(result) {
      let callBackFunc = this.settings.ajaxCallback ||
          this.settings.ajaxCallbackDefault;
      window[callBackFunc](result, this.$form);
    },
    _afterAjaxCall: function() {
      this.submitInProgress = false;
      this.formButtonUnlock();
      this.preLoader.hideLoader();
    },
    _getFormData: function() {
      return {};
    },
  };

  let Form = function() {
    BaseForm.apply(this, arguments);
    this.fieldsList = new FieldsList(this, this.$form, this.settings);
  };
  Form.prototype = Object.create(BaseForm.prototype);
  Form.prototype.constructor = Form;
  Form.prototype.addField = function(fieldData) {
    this.fieldsList.addField(fieldData);
  };
  Form.prototype.initEvents = function() {
    this.fieldsList.initFieldsEvents();
  };
  Form.prototype.formButtonLock = function() {
    let fields = this.fieldsList.findFields('type', 'submit');
    for (let i = 0; i < fields.length; i++) {
      fields[i].disable();
    }
  };
  Form.prototype.formButtonUnlock = function() {
    let fields = this.fieldsList.findFields('type', 'submit');
    for (let i = 0; i < fields.length; i++) {
      fields[i].unDisable();
    }
  };
  Form.prototype.processValidation = function() {
    return this.fieldsList.validate();
  };
  Form.prototype.setValidationErrors = function(errors) {
    for (let fieldName in errors) {
      if (!errors.hasOwnProperty(fieldName)) {
        continue;
      }
      for (let key in errors[fieldName]) {
        this.fieldsList.setError(fieldName, errors[fieldName][key]);
      }
    }
  };
  Form.prototype._getFormData = function() {
    return this.fieldsList.getFormData();
  };
  Form.prototype.getFormFields = function() {
    return this.fieldsList.fields.getElements();
  };
  Form.prototype.afterCreate = function() {
    this.fieldsList.manageUniqueOptions();
  };

  let MultiForm = function() {
    BaseForm.apply(this, arguments);
    this.blocks = new Collection();
  };
  MultiForm.prototype = Object.create(BaseForm.prototype);
  MultiForm.prototype.constructor = MultiForm;
  MultiForm.prototype.addBlock = function(block) {
    if (!(block instanceof Block)) {
      throw new FormException('Incorrect data');
    }
    this.blocks.addElement(block);
    this.setupTooltip();
  };
  MultiForm.prototype.initEvents = function() {
    this.blocks.forEach(function(block) {
      block.initEvents();
    });
  };
  MultiForm.prototype.processValidation = function() {
    let blocks = this.blocks.getElements();
    let isValid = true;
    for (let i = 0; i < blocks.length; i++) {
      if (!blocks[i].validate()) {
        isValid = false;
      }
    }

    return isValid;
  };
  //TODO
  MultiForm.prototype.setValidationErrors = function(errors) {
  };
  //TODO
  MultiForm.prototype._getFormData = function() {
    return {};
  };
  MultiForm.prototype.getFormFields = function() {
    let fields = [];
    this.blocks.forEach(function(block) {
      fields = fields.concat(block.fieldsList.fields.getElements())
    });
    return fields;
  };
  MultiForm.prototype.removeBlock = function(block) {
    this.blocks.removeElement(block);
  }
  MultiForm.prototype.afterCreate = function() {
    this.blocks.forEach(function(block) {
      block.fieldsList.manageUniqueOptions();
    });
  };

  let Block = function(parent, blockSettings, formSettings) {
    this.parentFormElement = parent;
    this.settings = $.extend({}, defaultBlockSettings, blockSettings);
    this.formSettings = formSettings;
    this.$element = $(this.settings.container);
    this.fieldsList = new FieldsList(this, this.$element, $.extend({}, settings, formSettings));
    this.createdRemoveButton = false;
    this.repeatingManager = GetRepeatingManager(this.settings.name);
    if (this.settings.repeatCnt > 1) {
      this.repeatingManager.tryInit(this);
    }
  };
  Block.prototype = {
    addField: function(fieldData) {
      this.fieldsList.addField(fieldData);
    },
    initEvents: function() {
      this.fieldsList.initFieldsEvents();
    },
    validate: function() {
      return this.fieldsList.validate();
    },
    setWithCaptcha: function() {
      this.parentFormElement.setWithCaptcha();
    },
    setCaptchaStatus: function(status) {
      this.parentFormElement.setCaptchaStatus(status);
    },
    setCaptchaId: function(captchaId) {
      this.parentFormElement.setCaptchaId(captchaId);
    },
    getCaptchaId: function() {
      return this.parentFormElement.getCaptchaId();
    },
    submitForm: function() {
      this.parentFormElement.submitForm();
    },
    createRemoveButton: function() {
      if (this.createdRemoveButton) {
        return;
      }
      this.createdRemoveButton = true;
      let $removeButton = $('<span/>', {
        class: 'remove-block',
        text: 'x',
        click: this.removeBlock.bind(this),
      });
      this.$element.prepend($removeButton);

    },
    removeRemoveButton: function() {
      this.$element.find('.remove-block').remove();
      this.createdRemoveButton = false;
    },
    removeBlock: function() {
      if (!this.repeatingManager) {
        return;
      }
      this.parentFormElement.removeBlock(this);
      this.repeatingManager.removeBlock(this);
      $(this.settings.container).remove();
      this.afterRemove();
    },
    afterAdd: function() {
      this.fieldsList.manageUniqueOptions();
    },
    afterRemove: function() {
      this.fieldsList.manageUniqueOptions();
    },
    getFormFields: function() {
      return this.parentFormElement.getFormFields();
    },
    isRepeatable: function() {
      return this.settings.repeatCnt > 0;
    }
  };

  /**
   * @param {string} blockName
   */
  function GetRepeatingManager(blockName) {
    if (!GetRepeatingManager.collection.hasOwnProperty(blockName)) {
      GetRepeatingManager.collection[blockName] = new RepeatingManager();
    }

    return GetRepeatingManager.collection[blockName];
  }

  GetRepeatingManager.collection = {};

  let globalCountRepeating = 0
  /**
   * @constructor
   */
  let RepeatingManager = function() {
    this.blocks = [];
    this.blockSettings = $.extend({}, defaultBlockSettings);
    this.blockFormSettings = {};
    this.blockParentElement = null;
    this.blockFields = {};
    this.countRepeating = 0;
    this.duplicateFieldInitied = false;
    this.settingInitied = false;
    this.$duplicateField = null;
  };
  RepeatingManager.prototype = {
    incCountRepeating: function() {
       this.countRepeating++;
       globalCountRepeating++;
       if (this.countRepeating >= this.blockSettings.repeatCnt) {
         this._hideDuplicateField();
       }
    },
    decCountRepeating: function() {
      this.countRepeating--;
      if (this.countRepeating < this.blockSettings.repeatCnt) {
        this._showDuplicateField();
      }
    },
    tryInit: function(block) {
      this._resetData(block);
      this.incCountRepeating();
      this._initRemoveButton();
      this._initDuplicateField();
    },
    _resetData: function(block) {
      this.blocks[block.settings.container] = block;
      if (this.settingInitied) {
        return;
      }
      let copyObjSettings = JSON.parse(JSON.stringify(block.settings));
      this.blockSettings = $.extend(this.blockSettings, copyObjSettings);
      this.blockFormSettings = block.formSettings;
      this.blockParentElement = block.parentFormElement;
      this.blockFields = block.fieldsList.fields.getElements();
      this.settingInitied = true;
    },
    _initDuplicateField: function() {
      if (this.duplicateFieldInitied) {
        return;
      }
      let $duplicateField = $('<span/>', {
        class: this.blockSettings.repeatBtnClass,
        text: this.blockSettings.repeatBtnTitle,
        click: this._duplicateBlock.bind(this),
      });
      $(this.blockSettings.container).parent().append($('<div/>').append($duplicateField));
      this.$duplicateField = $duplicateField;
      this.duplicateFieldInitied = true;
    },
    _hideDuplicateField: function() {
      if (this.$duplicateField) {
        this.$duplicateField.hide();
      }
    },
    _showDuplicateField: function() {
      if (this.$duplicateField) {
        this.$duplicateField.show();
      }
    },
    _duplicateBlock: function() {
      if (this.countRepeating >= this.blockSettings.repeatCnt) {
        return;
      }
      if (!this.$duplicateField) {
        return;
      }
      $(this._getRepeatTemplate()).insertBefore(this.$duplicateField.parent());
      this._createBlock();
      this.incCountRepeating();
      this._addRemoveButton();
    },
    _createBlock: function() {
      let blockSettings = JSON.parse(JSON.stringify(this.blockSettings));
      let formSettings = JSON.parse(JSON.stringify(this.blockFormSettings));

      blockSettings.repeatCnt = 0;
      blockSettings.container = blockSettings.container.replace(
          new RegExp('form-block-item-\\\d','gi'),
          'form-block-item-' + this.getNewIndex()
      );

      let block = new Block(this.blockParentElement, blockSettings,
          formSettings);
      let fields = this.blockFields;
      for (let i = 0; i < fields.length; i++) {
        block.addField(this._duplicateFieldAttributes(fields[i].realAttributes));
      }
      block.fieldsList.fields.forEach(function(field){
        if (field instanceof FieldCheckbox) {
          field.unCheck();
        }
      });
      block.initEvents();
      this.blockParentElement.addBlock(block);
      this.blocks[block.settings.container] = block;
      block.afterAdd();
    },
    _duplicateFieldAttributes(data) {
      let attributes = JSON.parse(JSON.stringify(data));
      let nameReg = this._getFieldNameReg();
      let namePart = this._getNewFieldNamePart();
      let classReg = this._getFieldClassReg();
      let classPart = this._getNewFieldClassPart();
      let realName = attributes.realName.replace(nameReg, namePart);

      attributes.validate = data.validate;
      attributes.realName = realName;
      attributes.inputOptions.name = realName;
      attributes.id = attributes.id.replace(classReg, classPart);
      attributes.container = attributes.container.replace(classReg, classPart);
      attributes.inputOptions.id = attributes.inputOptions.id.replace(
          classReg,
          classPart
      );
      switch (attributes.type) {
        case FieldText.TYPE:
        case FieldHidden.TYPE:
        case FieldPassword.TYPE:
        case FieldTextarea.TYPE:
        case FieldDropDownList.TYPE:
          attributes.value = '';
          break;
        case FieldRadioCommentList.TYPE:
          attributes.value = {radio:null, comment:''};
          break;
        case FieldDoublePhone.TYPE:
          attributes.value = {phoneCode:null, phoneNumber:''};
          break;
      }

      return attributes;
    },
    _getRepeatTemplate: function() {
      let template = this.blockSettings.repeatTemplate;
      template = template.replace(this._getFieldClassReg(),
          this._getNewFieldClassPart());
      template = template.replace(this._getFieldNameReg(),
          this._getNewFieldNamePart());
       template = template.replace(new RegExp('form-block-item-\\\d','gi'),
           'form-block-item-' +  this.getNewIndex());
      return template;
    },
    getNewIndex: function() {
      return globalCountRepeating + 1;
    },
    _getNewFieldNamePart: function() {
      return '[' + this.blockSettings.name + '][' + this.getNewIndex() + ']';
    },
    _getNewFieldClassPart: function() {
      return this.blockSettings.name + '-' + this.getNewIndex();
    },
    _getFieldNameReg: function() {
      return new RegExp('\\[' + this.blockSettings.name + '\\]\\[' +
          this.blockSettings.index + '\\]', 'gi');
    },
    _getFieldClassReg: function() {
      return new RegExp(
          this.blockSettings.name + '-' + this.blockSettings.index, 'gi');
    },
    _initRemoveButton: function() {
      if (Object.keys(this.blocks).length > 1) {
        this._addRemoveButton();
      }
    },
    _addRemoveButton: function() {
      for (let index in this.blocks) {
        this.blocks[index].createRemoveButton();
      }
    },
    _removeRemoveButton: function() {
      if (Object.keys(this.blocks).length > 1) {
        return;
      }
      for (let index in this.blocks) {
        this.blocks[index].removeRemoveButton();
      }
    },
    removeBlock: function(block) {
      delete this.blocks[block.settings.container];
      this.decCountRepeating();
      this._removeRemoveButton();
    }
  };

  let FieldsList = function(parent, $element, formSettings) {
    this.parentFormElement = parent;
    this.$element = $element;
    this.settings = $.extend({errorCssClass: '', successCssClass: ''},
        formSettings);
    this.fields = new Collection();
    this._init();
  };
  FieldsList.prototype = {
    getErrorCssClass: function() {
      return this.settings.errorCssClass;
    },
    getSuccessCssClass: function() {
      return this.settings.successCssClass;
    },
    addField: function(fieldData) {
      let field = CreateField(this, fieldData);
      this.fields.addElement(field);
      this._watchField(field);
    },
    initFieldsEvents: function() {
      this.fields.forEach(function(field) {
        field.initFieldEvents();
      });
    },
    findElementByQuerySelector: function(querySelector) {
      return this.$element.find(querySelector);
    },
    /**
     * @param {String} propertyName
     * @param {String} propertyValue
     * @returns {Fields[]}
     */
    findFields: function(propertyName, propertyValue) {
      return this.fields.findBy(propertyName, propertyValue);
    },
    findField: function(fieldName) {
      let fields = this.findFields('name', fieldName);
      if (typeof fields[0] == 'undefined') {
        return null;
      }

      return fields[0];
    },
    validate: function() {
      let fields = this.fields.getElements();
      let isValid = true;
      for (let i = 0; i < fields.length; i++) {
        if (!this.validateField(fields[i])) {
          isValid = false;
        }
      }

      return isValid;
    },
    /**
     * @param {Field} field
     * @returns {Boolean}
     */
    validateField: function(field) {
      if (!(field instanceof Field)) {
        throw new FormException('Incorrect argument type');
      }
      return field.validate();
    },
    getFormData: function() {
      let fields = this.fields.getElements();
      let formData = {};
      for (let i = 0; i < fields.length; i++) {
        let field = fields[i];
        if (field.type === FieldSubmit.TYPE) {
          continue;
        }
        formData[field.getName()] = field.getValue();

      }

      return formData;
    },
    setWithCaptcha: function() {
      this.parentFormElement.setWithCaptcha();
    },
    setCaptchaStatus: function(status) {
      this.parentFormElement.setCaptchaStatus(status);
    },
    setCaptchaId: function(captchaId) {
      this.parentFormElement.setCaptchaId(captchaId);
    },
    getCaptchaId: function() {
      return this.parentFormElement.getCaptchaId();
    },
    submitForm: function() {
      this.parentFormElement.submitForm();
    },
    setError: function(fieldName, errorMsg) {
      let field = this.findField(fieldName);
      if (field instanceof Field) {
        field.setError(errorMsg);
        field.showErrors();
      }
    },
    manageUniqueOptions: function() {
      let list = this._getUniqueOptions();
      for (let fieldName in list) {
        list[fieldName].fields.forEach(function(field) {
          field.manageOptions(list[fieldName].values);
        });
      }
    },
    _getUniqueOptions: function() {
      let list = {};
      this.parentFormElement.getFormFields().forEach(function(field) {
        if (!field.isUniqueGlobal() || !field.canManageOptions()) {
          return;
        }
        let selectedValues = [];
        let selectedFields = [];
        if (list[field.name]) {
          selectedValues = list[field.name].values;
          selectedFields = list[field.name].fields;
        }
        if (field.getValue() !== '') {
          selectedValues.push(field.getValue());
        }
        selectedFields.push(field);
        list[field.name] = {
          values: selectedValues,
          fields: selectedFields,
        };
      });
      return list;
    },
    _init: function() {
      this._watchFields();
    },
    /**
     * @private
     */
    _watchFields: function() {
      let fields = this.fields.getElements();
      for (let i = 0; i < fields.length; i++) {
        this._watchField(fields[i]);
      }
    },
    /**
     * @param {Field} field
     * @private
     */
    _watchField: function(field) {
      field.onChange(this._fieldOnChange.bind(this, field));
      field.onBlur(this._fieldOnBlur.bind(this, field));
      field.onSetError(this._fieldOnOnSetError.bind(this, field));
      field.onRemoveError(this._fieldOnRemoveError.bind(this, field));
    },
    /**
     * @param {Field} field
     * @private
     */
    _fieldOnChange: function(field) {
      if (field.isValidateOnChange()) {
        this.validateField(field);
      }
      if (field.isUniqueGlobal() && field.canManageOptions()) {
        this.manageUniqueOptions();
      }
    },
    /**
     * @param {Field} field
     * @private
     */
    _fieldOnBlur: function(field) {
      if (field.isValidateOnBlur()) {
        this.validateField(field);
      }
    },
    _fieldOnOnSetError: function(field, args) {
      if ((typeof args === 'object') && (args.hasOwnProperty('message'))) {
        field.errorMessages.push(args.message);
        field.showErrors();
      }
    },
    _fieldOnRemoveError: function(field, args) {
      if ((typeof args === 'object') && (args.hasOwnProperty('message'))) {
        let index = field.errorMessages.indexOf(args.message);
        if (index !== -1) {
          field.errorMessages.splice(index, 1);
        }
        field.showErrors();
      }
    },
  };

  /**
   * @param {FieldsList} fieldsList
   * @param {Object} attributes
   * @constructor
   */
  let Field = function(fieldsList, attributes) {
    this.observer = new Observer();
    this.fieldsList = fieldsList;
    this.preValidator = new PreValidation(this);
    this.realAttributes = JSON.parse(JSON.stringify(attributes));
    this.realAttributes.validate = attributes.validate;
    this.attributes = JSON.parse(JSON.stringify(attributes));
    this.attributes.validate = attributes.validate;
    this.type = this.attributes.type;
    this.name = this.attributes.name;
    this.errorMessages = [];
    this.events = [];
    if (this.realAttributes.hasOwnProperty('value')) {
      this.setValue(this.realAttributes.value);
    }
    this._initEvents();
  };
  Field.prototype = {
    /**
     * @returns {Object|Undefined}
     */
    getAdditionalData: function() {
      return this.attributes.additionalData;
    },
    /**
     * @returns {string}
     */
    getName: function() {
      return this.attributes.name;
    },
    /**
     * @returns {*}
     */
    getValue: function() {
      let $input = this._findInput();
      if ($input.length) {
        return $input.val();
      }
      return null;
    },
    /**
     * @param {*} value
     */
    setValue: function(value) {
      let $input = this._findInput();
      if ($input.length) {
        $input.val(value);
      }
    },
    /**
     * @param {string} attrName
     * @param {*} attrValue
     */
    setInputAttr: function(attrName, attrValue) {
      let $input = this._findInput();
      if ($input.length) {
        $input.attr(attrName, attrValue);
      }
    },
    disable: function() {
      this.setInputAttr('disabled', true);
      this.disabled = true;
    },
    unDisable: function() {
      this.setInputAttr('disabled', false);
    },
    /**
     * @returns {boolean}
     */
    isDisabled: function() {
      let $input = this._findInput();
      let attr = $input.attr('disabled');
      return typeof attr !== 'undefined' && attr !== false;
    },
    clear: function() {
      let $input = this._findInput();
      if ($input.length) {
        $input.val('');
      }
    },
    /**
     * @returns {boolean}
     */
    validate: function() {
      if (this.isDisabled()) {
        return true;
      }

      let messages = [];
      if (typeof this.attributes.validate === 'function') {
        this.attributes.validate(
            this.attributes,
            this.getValue(),
            messages,
            this.checkValidationCondition.bind(this),
            this
        );
      }

      this.errorMessages = messages;
      this.showErrors();

      return !this.hasError();
    },
    isValidateOnChange() {
      return this.attributes.validateOnChange;
    },
    isValidateOnBlur() {
      return this.attributes.validateOnBlur;
    },
    checkValidationCondition(conditions) {
      let res = this.preValidator.check(conditions);

      return res;
    },
    /**
     * @returns {boolean}
     */
    hasError: function() {
      return this.errorMessages.length > 0;
    },
    setError: function(msg) {
      this.errorMessages.push(msg);
    },
    showErrors: function() {
      let $container = this._getContainer();
      let $error = $container.find(this.attributes.error);
      let addClass = '';
      let removeClass = '';
      if (this.hasError()) {
        $error.html(this.errorMessages[0]);
        addClass = this.fieldsList.getErrorCssClass();
        removeClass = this.fieldsList.getSuccessCssClass();
      } else {
        $error.html('');
        removeClass = this.fieldsList.getErrorCssClass();
        addClass = this.fieldsList.getSuccessCssClass();
      }

      $container.removeClass(removeClass).addClass(addClass);
    },
    /**
     * @param {string} eventName
     * @param {function} callback
     */
    on: function(eventName, callback) {
      let event = 'on' + ucFirst(eventName);
      if (typeof this[event] !== 'function') {
        throw new FormFieldException('Unknown event name');
      }
      this[event](callback);
    },
    /**
     * @param {function} callback
     * @param {string} callbackName
     */
    onChange: function(callback, callbackName) {
      this.observer.subscribe('onChange', callback, callbackName);
    },
    /**
     * @param {function} callback
     * @param {string} callbackName
     */
    onBlur: function(callback, callbackName) {
      this.observer.subscribe('onBlur', callback, callbackName);
    },
    onSetError: function(callback) {
      this.observer.subscribe('onSetError', callback);
    },
    onRemoveError: function(callback) {
      this.observer.subscribe('onRemoveError', callback);
    },
    initFieldEvents: function() {
      let eventData = this._getFieldEventsData();
      for (let i = 0; i < eventData.length; i++) {
        this.events = new ActiveFormEvent(this, eventData[i]);
      }
    },
    hide: function() {
      this._getContainer().hide();
    },
    show: function() {
      this._getContainer().show();
    },
    isRequired: function() {
      return this.realAttributes.isRequired;
    },
    isUniqueGlobal: function() {
      return this.realAttributes.isUniqueGlobal;
    },
    canManageOptions: function() {
      return false;
    },
    _initEvents: function() {
      this._initOnChange();
      this._initOnBlur();
      this._initOnSetError();
      this._initOnRemoveError();
    },
    _callChange: function() {
      this.observer.broadcast('onChange');
    },
    _callBlur: function() {
      this.observer.broadcast('onBlur');
    },
    _callSetError: function(event, args) {
      this.observer.broadcast('onSetError', args);
    },
    _callRemoveError: function(event, args) {
      this.observer.broadcast('onRemoveError', args);
    },
    _findInput: function() {
      let $input = this.fieldsList.findElementByQuerySelector(
          this.attributes.id);
      if ($input.length && $input[0].tagName.toLowerCase() === 'div') {
        return $input.find('input');
      } else {
        return $input;
      }
    },
    _initOnChange: function() {
      let $input = this._findInput();
      if ($input.length) {
        $input.on('change', this._callChange.bind(this));
      }
    },
    _initOnBlur: function() {
      let $input = this._findInput();
      if ($input.length) {
        $input.on('blur', this._callBlur.bind(this));
      }
    },
    _initOnSetError: function() {
      let $input = this._findInput();
      if ($input.length) {
        $input.on('setError', this._callSetError.bind(this));
      }
    },
    _initOnRemoveError: function() {
      let $input = this._findInput();
      if ($input.length) {
        $input.on('removeError', this._callRemoveError.bind(this));
      }
    },
    _getContainer: function() {
      return this.fieldsList.findElementByQuerySelector(
          this.attributes.container);
    },
    /**
     * @returns {Array}
     * @private
     */
    _getFieldEventsData: function() {
      return this.attributes.events;
    },
  };

  let FieldText = function() {
    Field.apply(this, arguments);
  };
  FieldText.TYPE = 'text';
  FieldText.prototype = Object.create(Field.prototype);
  FieldText.prototype.constructor = FieldText;

  let FieldHidden = function() {
    Field.apply(this, arguments);
  };
  FieldHidden.prototype = Object.create(Field.prototype);
  FieldHidden.prototype.constructor = FieldHidden;
  FieldHidden.TYPE = 'hidden';

  let FieldPassword = function() {
    Field.apply(this, arguments);
  };
  FieldPassword.prototype = Object.create(Field.prototype);
  FieldPassword.prototype.constructor = FieldPassword;
  FieldPassword.TYPE = 'password';

  let FieldFile = function() {
    Field.apply(this, arguments);
  };
  FieldFile.prototype = Object.create(Field.prototype);
  FieldFile.prototype.constructor = FieldFile;
  FieldFile.TYPE = 'file';

  let FieldTextarea = function() {
    Field.apply(this, arguments);
  };
  FieldTextarea.prototype = Object.create(Field.prototype);
  FieldTextarea.prototype.constructor = FieldTextarea;
  FieldTextarea.TYPE = 'textarea';

  let FieldCheckbox = function() {
    Field.apply(this, arguments);
    this._initValidation();
  };
  FieldCheckbox.prototype = Object.create(Field.prototype);
  FieldCheckbox.prototype.constructor = FieldCheckbox;
  FieldCheckbox.TYPE = 'checkbox';
  FieldCheckbox.prototype.getValue = function() {
    let $container = this._getContainer();
    let $realInput = $container.find('input').filter(':checked');
    if (!$realInput.length) {
      $realInput = $container.find(
          'input[type=hidden][name="' + this.attributes.realName + '"]');
    }
    if (!$realInput.length) {
      return null;
    }
    return parseInt($realInput.val(), 10);
  };
  FieldCheckbox.prototype.check = function() {
    let $input = this._findInput();
    $input.prop('checked', true);
  };
  FieldCheckbox.prototype.unCheck = function() {
    this.clear();
  };
  FieldCheckbox.prototype.clear = function() {
    let $input = this._findInput();
    if ($input.length) {
      $input.prop('checked', false);
    }
  };
  FieldCheckbox.prototype._initValidation = function () {
    this._findInput().on('click', this._callValidate.bind(this));
  };
  FieldCheckbox.prototype._callValidate = function () {
    this.validate();
  };

  let FieldRadio = function() {
    FieldCheckbox.apply(this, arguments);
  };
  FieldRadio.prototype = Object.create(FieldCheckbox.prototype);
  FieldRadio.prototype.constructor = FieldRadio;
  FieldRadio.TYPE = 'radio';

  let FieldRadioList = function() {
    FieldRadio.apply(this, arguments);
  };
  FieldRadioList.prototype = Object.create(FieldRadio.prototype);
  FieldRadioList.prototype.constructor = FieldRadioList;
  FieldRadioList.TYPE = 'radioList';

  let FieldRadioCommentList = function() {
    FieldRadioList.apply(this, arguments);
    this._initShowComment();
  };
  FieldRadioCommentList.prototype = Object.create(FieldRadioList.prototype);
  FieldRadioCommentList.prototype.constructor = FieldRadioCommentList;
  FieldRadioCommentList.TYPE = 'radioCommentList';
  FieldRadioCommentList.prototype.isValidateOnChange = function() {
    return true;
  };
  FieldRadioCommentList.prototype.isValidateOnBlur = function() {
    return false;
  };
  FieldRadioCommentList.prototype.getValue = function() {
    let $container = this._getContainer();
    let realRadioName = this.attributes.realName + '[radio]';
    let realCommentName = this.attributes.realName + '[comment]';
    let $realCommentInput = $container.find(
        'input[type=hidden][name="' + realCommentName + '"]');
    let $realRadioInput = $container.find('input').filter(':checked');
    if (!$realRadioInput.length) {
      $realRadioInput = $container.find(
          'input[type=hidden][name="' + realRadioName + '"]');
    }
    if (!$realRadioInput.length) {
      return null;
    }

    return {radio: $realRadioInput.val(), comment: $realCommentInput.val()};
  };
  FieldRadioCommentList.prototype.setValue = function(value) {
    if (!(value instanceof Object)) {
      throw new FormFieldException(
          'Incorrect type of incoming param. Value must have type as Object'
      );
    }
    if (!value.hasOwnProperty('radio')) {
      return;
    }

    let radioValue = value.radio;
    let commentValue = '';
    if (value.hasOwnProperty('comment')) {
      commentValue = value.comment;
    }
    let $inputs = this._getRadioInputs();

    for (let i=0; i < $inputs.length; i++) {
      let $input = $($inputs[i]);
      let $inputComment = this._getCommentInput($input);
      if ($input.val() == radioValue) {
        $input.prop('checked', true);
        if (commentValue && $inputComment.length) {
          $inputComment.val(commentValue);
          $inputComment.trigger('change');
        }
      } else {
        $input.prop('checked', false);
        if (radioValue === null && $inputComment.length) {
          $inputComment.val('');
        }
      }
    }
  };
  FieldRadioCommentList.prototype.getSelectedElement = function(value) {
    if (!(value instanceof Object)) {
      throw new FormFieldException(
          'Incorrect type of incoming param. Value must have type as Object'
      );
    }
    if (!value.hasOwnProperty('radio')) {
      return null;
    }

    let $inputs = this._getRadioInputs();

    for (let i=0; i < $inputs.length; i++) {
      let $input = $($inputs[i]);
      if ($input.val() == value.radio) {
        let $element = $input.parent('.element-radio').parent('.radio-item');
        if ($element.length) {
          return $element;
        }
        return null;
      }
    }
    return null;
  };
  FieldRadioCommentList.prototype.showErrors = function() {
    let value = this.getValue();
    let $element = this.getSelectedElement(value);
    let errorClass = this.attributes.error.replace(new RegExp('\\.','gi'), ' ');
    let errorClassSelector = this.attributes.error;
    let $container = this._getContainer();
    let addClass = '';
    let removeClass = '';

    $container.find(errorClassSelector).remove();
    if (this.hasError()) {
      let $errorElement = $(
          '<' + this.attributes.errorTag + '/>',
          {class: errorClass, text: this.errorMessages[0]}
      );
      if ($element) {
        $element.append($errorElement);
      } else {
        $container.append($errorElement);
      }
      addClass = this.fieldsList.getErrorCssClass();
      removeClass = this.fieldsList.getSuccessCssClass();
    } else {
      removeClass = this.fieldsList.getErrorCssClass();
      addClass = this.fieldsList.getSuccessCssClass();
    }

    $container.removeClass(removeClass).addClass(addClass);
  };
  FieldRadioCommentList.prototype._initShowComment = function() {
    this._getContainer().find('input[type=radio]').on('click',this._showComment.bind(this));
    this._hideAllComments();
    this._showComment();
  };
  FieldRadioCommentList.prototype._hideAllComments = function() {
    this._getContainer().find('.element-comment').hide();
  };
  FieldRadioCommentList.prototype._showComment = function() {
    let currentValue = this.getValue();
    let commentOptions = this._getCommentOptionsByValue(currentValue);
    let $selectedElement = this.getSelectedElement(currentValue);
    this._hideAllComments();
    if (!$selectedElement || !commentOptions) {
      return;
    }
    if (commentOptions.hasOwnProperty('visible') && commentOptions.visible) {
      $selectedElement.find('.element-comment').show();
    }
  };
  FieldRadioCommentList.prototype._getCommentOptionsByValue = function(value) {
    if (!(value instanceof Object)) {
      throw new FormFieldException(
          'Incorrect type of incoming param. Value must have type as Object'
      );
    }
    if (!value.hasOwnProperty('radio')) {
      return null;
    }
    for (let i = 0; i < this.attributes.inputItems.length; i++) {
      let item = this.attributes.inputItems[i];
      if (!item.hasOwnProperty('commentOptions') || !item.hasOwnProperty('radioOptions')) {
        throw new FormFieldException('Incorrect Input Items options');
      }
      if (item.radioOptions.value == this.getValue().radio) {
        return item.commentOptions;
      }
    }
    return null;
  };
  FieldRadioCommentList.prototype._getRadioInputs = function() {
    return this._getContainer().find('input[type=radio]');
  };
  FieldRadioCommentList.prototype._getCommentInput = function($selectedRadio) {
    let radioId =  $selectedRadio.attr('id');
    let comId = radioId.replace(new RegExp('-radio', 'gi'), '-comment');
    return  this._getContainer().find('#' + comId);
  };

  let FieldDropDownList = function() {
    Field.apply(this, arguments);
  };
  FieldDropDownList.prototype = Object.create(Field.prototype);
  FieldDropDownList.prototype.constructor = FieldDropDownList;
  FieldDropDownList.TYPE = 'dropDownList';
  FieldDropDownList.prototype.setValue = function(value) {
    if (!value) {
      this.unSelect();
    }
    let $input = this.fieldsList.findElementByQuerySelector(
        this.attributes.id + ' [value="' + value + '"]');
    if ($input.length) {
      $input.attr('selected', 'selected');
    }
  };
  FieldDropDownList.prototype.unSelect = function() {
    $('option:selected', this._findInput()).each(function() {
      this.selected = false;
    });
  };
  FieldDropDownList.prototype.getOptions = function(excludeSelectedOption) {
    if (excludeSelectedOption) {
      return $('option:not(:selected)', this._findInput());
    }
    return $('option', this._findInput());
  };
  FieldDropDownList.prototype.canManageOptions = function() {
    return true;
  };
  FieldDropDownList.prototype.manageOptions = function(list) {
    this.getOptions(true).each(function(i, option) {
      let disabled = $.inArray($(option).attr('value'), list) > -1;
      $(option).prop('disabled', disabled);
    });
  };

  let FieldDoublePhone = function() {
    Field.apply(this, arguments);
  };
  FieldDoublePhone.prototype = Object.create(Field.prototype);
  FieldDoublePhone.prototype.constructor = FieldDoublePhone;
  FieldDoublePhone.TYPE = 'doublePhone';
  FieldDoublePhone.prototype._findInput = function() {
    return {
      phoneCode: this._findCodeInput(),
      phoneNumber: this._findNumberInput(),
    };
  };
  FieldDoublePhone.prototype._findCodeInput = function() {
    let $container = this._getContainer();
    let secondName = this.attributes.inputOptions.phoneCode.secondName;
    let element = $container.find(
        'input[name="' + this.attributes.realName + '[' + secondName + ']' +
        '"]');
    if (!element.length) {
      throw new FormFieldException('Input `phoneCode` not found');
    }
    return element;
  };
  FieldDoublePhone.prototype._findNumberInput = function() {
    let $container = this._getContainer();
    let secondName = this.attributes.inputOptions.phoneNumber.secondName;
    let element = $container.find(
        'input[name="' + this.attributes.realName + '[' + secondName + ']' +
        '"]');
    if (!element.length) {
      throw new FormFieldException('Input `phoneNumber` not found');
    }
    return element;
  };
  FieldDoublePhone.prototype.getValue = function() {
    let input = this._findInput();
    return input.phoneCode.val() + input.phoneNumber.val();
  };
  FieldDoublePhone.prototype.setValue = function(value) {
    let input = this._findInput();
    if (!(value instanceof Object)) {
      throw new FormFieldException(
          'Incorrect type of incoming param. Value must have type as Object');
    }
    if (value.hasOwnProperty('phoneCode')) {
      input.phoneCode.val(value.phoneCode);
    }
    if (value.hasOwnProperty('phoneNumber')) {
      input.phoneNumber.val(value.phoneNumber);
    }
  };
  FieldDoublePhone.prototype.setInputAttr = function(attrName, attrValue) {
    let input = this._findInput();
    input.phoneCode.attr(attrName, attrValue) &&
    input.phoneNumber.attr(attrName, attrValue);
  };
  FieldDoublePhone.prototype._initOnChange = function() {
    let input = this._findInput();
    input.phoneCode.on('change', this._callChange.bind(this));
    input.phoneNumber.on('change', this._callChange.bind(this));
  };
  FieldDoublePhone.prototype._initOnBlur = function() {
    let input = this._findInput();
    input.phoneCode.on('blur', this._callBlur.bind(this));
    input.phoneNumber.on('blur', this._callBlur.bind(this));
  };
  FieldDoublePhone.prototype.isDisabled = function() {
    return false;
    let input = this._findInput();
    let attrCode = input.phoneCode.attr('disabled');
    let attrNumber = input.phoneNumber.attr('disabled');
    return typeof attrCode !== typeof undefined && attrCode !== false
        && typeof attrNumber !== typeof undefined && attrNumber !== false;
  };
  FieldDoublePhone.prototype.clear = function() {
    let input = this._findInput();
    input.phoneCode.val('');
    input.phoneNumber.val('');
  };

  let FieldSubmit = function() {
    Field.apply(this, arguments);
  };
  FieldSubmit.prototype = Object.create(Field.prototype);
  FieldSubmit.prototype.constructor = FieldSubmit;
  FieldSubmit.TYPE = 'submit';

  let FieldReCaptcha = function() {
    Field.apply(this, arguments);
  };
  FieldReCaptcha.prototype = Object.create(Field.prototype);
  FieldReCaptcha.prototype.constructor = FieldReCaptcha;
  FieldReCaptcha.TYPE = 'reCaptcha';
  FieldReCaptcha.prototype.getName = function() {
    return 'g-recaptcha-response';
  };
  FieldReCaptcha.prototype.setValue = function() {
    return false;
  };
  FieldReCaptcha.prototype.getValue = function() {
    if (typeof grecaptcha === 'undefined') {
      return null;
    }

    return grecaptcha.getResponse();
  };

  let FieldInvisibleCaptcha = function() {
    Field.apply(this, arguments);
    this.fieldsList.setWithCaptcha();
    grecaptcha.ready(this.registerCaptcha.bind(this));
  };
  FieldInvisibleCaptcha.prototype = Object.create(Field.prototype);
  FieldInvisibleCaptcha.prototype.constructor = FieldInvisibleCaptcha;
  FieldInvisibleCaptcha.TYPE = 'invisibleCaptcha';
  FieldInvisibleCaptcha.prototype.registerCaptcha = function() {
    let captchaId = grecaptcha.render(this.attributes.captchaContainerId, {
      sitekey: this.attributes.captchaSiteKey,
      size: 'invisible',
      badge: 'bottomright',
      callback: this.onCaptchaSuccess.bind(this),
      'expired-callback': this.onCaptchaError.bind(this),
      'error-callback': this.onCaptchaError.bind(this),
    });
    this.fieldsList.setCaptchaId(captchaId);
  };
  FieldInvisibleCaptcha.prototype.onCaptchaSuccess = function(token) {
    this.setValue(token);
    this.fieldsList.setCaptchaStatus(true);
    this.fieldsList.submitForm();
  };
  FieldInvisibleCaptcha.prototype.onCaptchaError = function() {
    this.fieldsList.setCaptchaStatus(false);
    grecaptcha.reset(this.fieldsList.getCaptchaId());
  };

  let CreateField = function(parent, attributes) {
    switch (attributes.type) {
      case FieldText.TYPE : {
        return new FieldText(parent, attributes);
      }
      case FieldHidden.TYPE : {
        return new FieldHidden(parent, attributes);
      }
      case FieldPassword.TYPE : {
        return new FieldPassword(parent, attributes);
      }
      case FieldFile.TYPE : {
        return new FieldFile(parent, attributes);
      }
      case FieldTextarea.TYPE : {
        return new FieldTextarea(parent, attributes);
      }
      case FieldRadio.TYPE : {
        return new FieldRadio(parent, attributes);
      }
      case FieldRadioList.TYPE : {
        return new FieldRadioList(parent, attributes);
      }
      case FieldRadioCommentList.TYPE : {
        return new FieldRadioCommentList(parent, attributes);
      }
      case FieldCheckbox.TYPE : {
        return new FieldCheckbox(parent, attributes);
      }
      case FieldDropDownList.TYPE : {
        return new FieldDropDownList(parent, attributes);
      }
      case FieldDoublePhone.TYPE : {
        return new FieldDoublePhone(parent, attributes);
      }
      case FieldSubmit.TYPE : {
        return new FieldSubmit(parent, attributes);
      }
      case FieldReCaptcha.TYPE : {
        return new FieldReCaptcha(parent, attributes);
      }
      case FieldInvisibleCaptcha.TYPE : {
        return new FieldInvisibleCaptcha(parent, attributes);
      }
    }
    throw new FormFieldException('Unknown field type. type=' + attributes.type);
  };

  /**
   * @param {Field} field
   * @constructor
   */
  let PreValidation = function(field) {
    this.field = field;
  };
  PreValidation.prototype = {
    check: function(conditions) {
      for (let i = 0; i < conditions.length; i++) {
        if (typeof conditions[i]['name'] === 'undefined') {
          throw new FormFieldValidationException('Incorrect conditions data');
        }
        if (typeof conditions[i]['params'] === 'undefined') {
          throw new FormFieldValidationException('Incorrect conditions data');
        }
        if (!this._checkCondition(conditions[i]['name'],
            conditions[i]['params'])) {
          return false;
        }
      }
      return true;
    },
    _checkCondition: function(conditionName, params) {
      return this._getCondition(conditionName).check(params);
    },
    _getCondition: function(conditionName) {
      switch (conditionName) {
        case FieldHasValue.NAME: {
          return new FieldHasValue(this.field);
        }
      }
      throw new FormFieldValidationException(
          'Unknown condition name. name=' + conditionName);
    },
  };

  let Condition = function(field) {
    this.field = field;
  };
  Condition.prototype = {
    check: function(params) {
      return true;
    },
  };

  let FieldHasValue = function(field) {
    Condition.apply(this, arguments);
  };
  FieldHasValue.NAME = 'fieldHasValue';
  FieldHasValue.prototype = Object.create(Condition.prototype);
  FieldHasValue.prototype.constructor = FieldHasValue;
  FieldHasValue.prototype.check = function(params) {
    let fieldName = params['field'];
    let fieldValue = params['fieldValue'];
    let field = this.field.fieldsList.findField(fieldName);
    if (!(field instanceof Field)) {
      return false;
    }

    field.onChange(this.field.validate.bind(this.field), FieldHasValue.NAME);

    return fieldValue == field.getValue();
  };

  let Collection = function(elements) {
    this.elements = [];

    if (elements) {
      this.setElements(elements);
    }
  };
  Collection.prototype = {
    getElements: function() {
      return this.elements;
    },

    setElements: function(elements) {
      this.clear();
      this.addAllElements(elements);
      return this;
    },

    addElement: function(element) {
      this.elements.push(element);
      return this;
    },

    addAllElements: function(elements) {
      if (!elements) {
        return this;
      }
      if (elements instanceof Array || elements instanceof Collection) {
        elements.forEach(this.addElement.bind(this));
      } else if (elements instanceof Object) {
        let key;
        for (key in elements) {
          if (elements.hasOwnProperty(key)) {
            this.addElement(elements[key]);
          }
        }
      }
      return this;
    },

    removeElement: function(element) {
      let newElements = [];
      this.elements.forEach(function(currentElement) {
        if (element === currentElement) {
          return;
        }
        newElements.push(currentElement)
      });
      this.elements = newElements;
    },

    clear: function() {
      this.elements = [];
      return this;
    },

    forEach: function(callback) {
      this.elements.forEach(callback);
      return this;
    },

    /**
     * Find element by name
     * @param propertyName
     * @param value
     * @returns Array
     */
    findBy: function(propertyName, value) {
      let i, len, result = [];
      len = this.elements.length;
      for (i = 0; i < len; ++i) {
        if (typeof this.elements[i][propertyName] !== 'undefined' &&
            this.elements[i][propertyName] === value) {
          result.push(this.elements[i]);
        }
      }
      return result;
    },
  };

  let Observer = function() {
    this._listenerList = {};
    this._listenersNames = {};
  };
  Observer.prototype = {
    subscribe: function(group, callback, callbackName) {
      this._initGroup(group);
      if (callbackName && this._listenersNames[group].includes(callbackName)) {
        return;
      }
      if (callbackName) {
        this._listenersNames[group].push(callbackName);
      }
      this._listenerList[group].push(callback);
    },
    unsubscribe: function(group, callback) {
      this._initGroup(group);
      let index = this._listenerList[group].indexOf(callback);

      if (index !== -1) {
        this._listenerList[group].splice(index, 1);
      }
    },
    broadcast: function(group, args) {
      this._initGroup(group);
      for (let i = 0; i < this._listenerList[group].length; i++) {
        this._listenerList[group][i](args);
      }
    },
    _initGroup: function(group) {
      if (!this._listenerList.hasOwnProperty(group)) {
        this._listenerList[group] = [];
        this._listenersNames[group] = [];
      }
    },
  };

  let PreLoader = function($form, isEnabled, preLoaderClass) {
    this.$form = $form;
    this.isEnabled = isEnabled;
    this.preLoaderClass = preLoaderClass;
  };
  PreLoader.prototype = {
    showLoader: function() {
      if (this.isEnable()) {
        this.getLoader().show();
      }
    },

    hideLoader: function() {
      if (this.isEnable()) {
        this.getLoader().hide();
      }
    },

    getLoader: function() {
      return this.$form.find('.' + this.preLoaderClass);
    },

    isEnable: function() {
      return this.isEnabled && this.preLoaderClass;
    },
  };

  let tooltip = {
    init: function($form, setting) {
      this._showingOnMouseOver(
          $form.find('.' + setting.toggleClass + ' input:checkbox').
              parent().
              find('label'),
          setting,
      );
      this._showingOnMouseOver(
          $form.find('.' + setting.toggleClass + ' select'),
          setting,
      );
      this._showingOnFocus(
          $form.find('.' + setting.toggleClass + ' input, .' + setting.toggleClass + ' textarea'),
          setting,
      );
    },
    _showingOnMouseOver: function($elem, setting) {
      $elem.on('mouseover', function() {
        $(this).siblings('.' + setting.targetClass).fadeIn(300);
      }).on('mouseout', function() {
        $(this).siblings('.' + setting.targetClass).fadeOut(300);
      });
    },
    _showingOnFocus: function($elem, setting) {
      $elem.on('focus', function() {
        $(this).siblings('.' + setting.targetClass).fadeIn(300);
      }).on('blur', function() {
        $(this).siblings('.' + setting.targetClass).fadeOut(300);
      });
    },
  };

  var OPTION_TYPE_TEXT = 1;
  var OPTION_TYPE_DATA_KEY = 2;
  var OPTION_TYPE_TARGET = 3;

  /**
   * @param {Object} data
   * @param {Field} data.field
   * @param {Object} data.optionData
   * @param {string} data.name
   */
  var Option = function(data) {
    this.data = data;
  };
  Option.prototype = {
    getTypeId: function() {
      return undefined;
    },
    getValue: function() {
      return undefined;
    },
    getName: function() {
      return this.data.name;
    },
  };

  var OptionText = function() {
    Option.apply(this, arguments);
  };
  OptionText.prototype = Object.create(Option.prototype);
  OptionText.prototype.constructor = OptionText;
  OptionText.prototype.getTypeId = function() {
    return OPTION_TYPE_TEXT;
  };
  OptionText.prototype.getValue = function() {
    return this.data.optionData.value;
  };

  var OptionDataKey = function() {
    Option.apply(this, arguments);
  };
  OptionDataKey.prototype = Object.create(Option.prototype);
  OptionDataKey.prototype.constructor = OptionDataKey;
  OptionDataKey.prototype.getTypeId = function() {
    return OPTION_TYPE_DATA_KEY;
  };
  OptionDataKey.prototype.getValue = function() {
    let additionalData = this.data.field.getAdditionalData();
    let optionValue = this.data.optionData.value;
    if (optionValue && additionalData[optionValue]) {
      return additionalData[optionValue];
    }
    return additionalData;
  };

  var OptionTarget = function() {
    Option.apply(this, arguments);
  };
  OptionTarget.prototype = Object.create(Option.prototype);
  OptionTarget.prototype.constructor = OptionTarget;
  OptionTarget.prototype.getTypeId = function() {
    return OPTION_TYPE_TARGET;
  };
  OptionTarget.prototype.getValue = function() {
    let fieldName = this.data.optionData.value;
    let fieldsList = this.data.field.fieldsList;
    let field = fieldsList.findField(fieldName);
    if (!field) {
      if (fieldsList.parentFormElement instanceof Block && !fieldsList.parentFormElement.isRepeatable()) {
        fieldsList.parentFormElement.getFormFields().forEach(function (itemField) {
          if (itemField.getName() === fieldName) {
            field = itemField;
          }
        });
      }
    }
    return field;
  };

  /**
   * @param {Field} field
   * @param {string} optionName
   * @param {Object} optionData
   * @param {string} optionData.typeId
   * @param {string} optionData.value
   * @return {OptionText}
   */
  var createOption = function(field, optionName, optionData) {
    let data = {
      field: field,
      name: optionName,
      optionData: optionData,
    };
    switch (parseInt(optionData.typeId)) {
      case OPTION_TYPE_TEXT: {
        return new OptionText(data);
      }
      case OPTION_TYPE_DATA_KEY: {
        return new OptionDataKey(data);
      }
      case OPTION_TYPE_TARGET: {
        return new OptionTarget(data);
      }
    }
  };

  /**
   * @param {ActiveFormEvent} activeFormEvent
   * @constructor
   */
  ActiveFormAction = function(activeFormEvent) {
    this.activeFormEvent = activeFormEvent;
  };
  ActiveFormAction.prototype = {
    /**
     * Set Phone Code
     * @param {Object} options
     * @param {OptionDataKey} options.ExtraData
     * @param {OptionTarget} options.TargetField
     */
    actionSetPhoneCode: function(options) {
      if (!options.ExtraData) {
        throw new FormEventException(
            'Invalid option, `ExtraData` must be set.',
        );
      }
      if (!options.TargetField) {
        throw new FormEventException(
            'Invalid option, `TargetField` must be set.',
        );
      }

      let extraData = options.ExtraData.getValue();
      let currentValue = this._getCurrentValue();

      if (typeof extraData.phoneCode === 'undefined') {
        throw new FormEventException(
            'Invalid option, `ExtraData` must have `phoneCode`.',
        );
      }
      let phoneCodes = extraData.phoneCode;
      let targetField = options.TargetField.getValue();
      let value = '';

      if (typeof phoneCodes[currentValue] !== 'undefined') {
        value = phoneCodes[currentValue];
      }
      if (targetField instanceof FieldDoublePhone) {
        value = {phoneCode: value};
      }

      targetField.setValue(value);
    },

    /**
     * Show Popup For Invalid Country
     * @param {Object} options
     * @param {OptionDataKey} options.ExtraData
     * @param {OptionText} options.TriggerName
     */
    actionShowPopupForInvalidCountry: function(options) {
      if (!options.TriggerName) {
        throw new FormEventException(
            'Invalid option, `TriggerName` must be set.');
      }
      if (!options.ExtraData) {
        throw new FormEventException(
            'Invalid option, `ExtraData` must be set.');
      }

      let currentField = this._getCurrentField();
      let extraData = options.ExtraData.getValue();
      let currentValue = currentField.getValue();

      if (
          !extraData.hasOwnProperty('invalidCountries') ||
          !extraData.invalidCountries.includes(currentValue)
      ) {
        return;
      }

      if (currentField instanceof FieldDropDownList) {
        currentField.unSelect();
      }
      $(document).trigger(options.TriggerName.getValue());
    },

    /**
     * Custom Trigger
     * @param {Object} options
     * @param {Option} options.TriggerName
     */
    actionCustomTrigger: function(options) {
      if (!options.TriggerName) {
        throw new FormEventException(
            'Invalid option, `TriggerName` must be set.');
      }

      let params = {};
      params.field = this._getCurrentField();
      for (let key in options) {
        if (key === 'TriggerName') {
          continue;
        }
        params[key] = options[key].getValue();
      }

      $(document).trigger(options.TriggerName.getValue(), params);
    },

    /**
     * TODO: Removed! These actions will no longer be used.
     * Trigger Event
     * @param {Object} options
     * @param {Option} options.TriggerName
     * @deprecated
     */
    actionTriggerEvent: function(options) {
      if (!options.TriggerName) {
        throw new FormEventException(
            'Invalid option, `TriggerName` must be set.',
        );
      }

      $(document).trigger(options.TriggerName.getValue());
    },

    /**
     * TODO: Removed! These actions will no longer be used.
     * Disable
     * @param {Object} options
     * @deprecated
     */
    actionDisable: function(options) {
      this._getCurrentField().disable();
    },

    /**
     * TODO: Removed! These actions will no longer be used.
     * UnSelect
     * @param {Object} options
     * @deprecated
     */
    actionUnSelect: function(options) {
      let currentField = this._getCurrentField();
      if (currentField instanceof FieldDropDownList) {
        currentField.unSelect();
      }
    },

    actionTargetHide: function(options) {
      if (!options.TargetField) {
        throw new FormEventException(
            'Invalid option, `TargetField` must be set.',
        );
      }

      let targetField = options.TargetField.getValue();
      targetField.hide();
    },
    actionTargetShow: function(options) {
      if (!options.TargetField) {
        throw new FormEventException(
            'Invalid option, `TargetField` must be set.',
        );
      }

      let targetField = options.TargetField.getValue();
      targetField.show();
    },

    /**
     * @return {Field}
     * @private
     */
    _getCurrentField: function() {
      return this.activeFormEvent.field;
    },
    _getCurrentValue: function() {
      return this._getCurrentField().getValue();
    },
  };

  /**
   * @param {Field} field
   * @param setting
   * @constructor
   */
  ActiveFormEvent = function(field, setting) {
    this.field = field;
    this.activeFormAction = new ActiveFormAction(this);
    this.setting = {
      action: {
        name: '',
        options: {},
      },
      event: undefined,
      condition: undefined,
      target: undefined, //TODO: REMOVE!!!
      weight: 0,
    };
    this._autostartedActions = ['targetShow', 'targetHide'];

    for (let key in setting.action.options) {
      if (setting.action.options.hasOwnProperty(key)) {
        setting.action.options[key] = createOption(
            this.field,
            key,
            setting.action.options[key],
        );
      }
    }

    $.extend(this.setting, setting);
    this._init();
  };
  ActiveFormEvent.prototype = {
    _init: function() {
      this.field.on(this._getEventName(), this._action.bind(this));
      this._autoStart();
    },
    _autoStart: function() {
      if (this._autostartedActions.includes(this.setting.action.name)) {
        this._action();
      }
    },

    _action: function() {
      try {
        if (this._checkCondition()) {
          this.activeFormAction[this._getActionName()](
              this.setting.action.options);
        }
      } catch (e) {
        if (e instanceof FormEventException) {
          errorLog(e.stack);
        } else {
          throw e;
        }
      }
    },

    _checkCondition: function() {
      if (!this.setting.condition) {
        return true;
      }
      if (!Array.isArray(this.setting.condition)) {
        return false;
      }
      let condition = this.setting.condition;
      for (let i = 0; i < condition.length; i++) {
        let field, operand, value;
        try {
          field = this._getConditionValue(condition[i][0]);
          operand = condition[i][1];
          value = this._getConditionValue(condition[i][2]);
        } catch (e) {
          if (e instanceof FormEventException) {
            errorLog(e.stack);
          } else {
            throw e;
          }
          return false;
        }

        if (!this._checkRule(field, operand, value)) {
          return false;
        }
      }
      return true;
    },

    _checkRule: function(checkingVal, operand, ruleVal) {
      var result = false;
      switch (operand.toUpperCase()) {
        case '=':
          result = (checkingVal == ruleVal);
          break;

        case '!=':
          result = (checkingVal != ruleVal);
          break;

        case '<':
          result = (checkingVal < ruleVal);
          break;

        case '<=':
          result = (checkingVal <= ruleVal);
          break;

        case '>':
          result = (checkingVal > ruleVal);
          break;

        case '>=':
          result = (checkingVal >= ruleVal);
          break;

        case 'IN':
          result = (Array.isArray(ruleVal) &&
              (ruleVal.indexOf(checkingVal) !== -1));
          break;

        case 'NOT IN':
          result = (Array.isArray(ruleVal) &&
              (ruleVal.indexOf(checkingVal) === -1));
          break;

        default:
          throw new FormEventException('Unknown operand.');

      }

      return result;
    },

    _getConditionValue: function(field) {
      if (field === 'value') {
        return this._getAttributeValue();
      }

      var prefix = /data-/;
      if (field.search(prefix) === 0) {
        var key = field.replace(prefix, '');
        return this._getAttributeAdditionalData(key);
      }

      return field;
    },

    _getAttributeAdditionalData: function(key) {
      let attributeData = this.field.getAdditionalData();
      if (typeof attributeData[key] === 'undefined') {
        throw new FormEventException('Invalid attributeData or key');
      }

      return attributeData[key];
    },

    _getAttributeValue: function() {
      return this.field.getValue();
    },

    _getEventName: function() {
      return this.setting.event;
    },

    _getActionName: function() {
      return actionName = 'action' + ucFirst(this.setting.action.name);
    },
  };

  var ucFirst = function(str) {
    if (!str) {
      return str;
    }
    return str[0].toUpperCase() + str.slice(1);
  };

  var FormException = function(message) {
    this.message = message;
    if (Error.captureStackTrace) {
      Error.captureStackTrace(this, FormException);
    } else {
      this.stack = (new Error()).stack;
    }
  };
  FormException.prototype = Object.create(Error.prototype);
  FormException.prototype.constructor = FormException;

  var FormFieldException = function() {
    FormException.apply(this, arguments);
  };
  FormFieldException.prototype = Object.create(FormException.prototype);
  FormFieldException.prototype.constructor = FormFieldException;

  var FormEventException = function() {
    FormException.apply(this, arguments);
  };
  FormEventException.prototype = Object.create(FormException.prototype);
  FormEventException.prototype.constructor = FormEventException;

  var FormFieldValidationException = function() {
    FormException.apply(this, arguments);
  };
  FormFieldValidationException.prototype = Object.create(
      FormException.prototype);
  FormFieldValidationException.prototype.constructor = FormFieldValidationException;

  var errorLog = function(msg) {
    console.error(msg);
  };

})(jQuery);