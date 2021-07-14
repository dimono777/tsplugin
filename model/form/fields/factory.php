<?php

namespace tradersoft\model\form\fields;

use Exception;

class Factory
{
    /**
     * @param $view
     *
     * @return FieldInterface
     * @throws Exception
     */
    public static function createField($view)
    {
        switch ($view) {
            case FieldInterface::VIEW_TEXT_INPUT:
                return new TextInput();
            case FieldInterface::VIEW_HIDDEN_INPUT:
                return new HiddenInput();
            case FieldInterface::VIEW_PASSWORD_INPUT:
                return new PasswordInput();
            case FieldInterface::VIEW_FILE_INPUT:
                return new FileInput();
            case FieldInterface::VIEW_TEXTAREA:
                return new Textarea();
            case FieldInterface::VIEW_RADIO:
                return new Radio();
            case FieldInterface::VIEW_RADIO_LIST:
                return new RadioList();
            case FieldInterface::VIEW_CHECKBOX:
                return new Checkbox();
            case FieldInterface::VIEW_DROP_DOWN:
                return new DropDownList();
            case FieldInterface::VIEW_SUBMIT_INPUT:
                return new SubmitInput();
            case FieldInterface::VIEW_DOUBLER_PHONE_INPUT:
                return new DoublePhoneInput();
            case FieldInterface::VIEW_RADIO_COMMENT_LIST:
                return new RadioCommentList();
            case FieldInterface::VIEW_INVISIBLE_CAPTCHA:
                return new InvisibleCaptcha();
        }

        throw new Exception("Unknown view.[view=$view]");
    }
}