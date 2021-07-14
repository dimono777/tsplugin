<?php
namespace tradersoft\model\account_details\saasic;

class AfterVerification extends Base
{
    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['phone'], 'required'],
            [['phone', 'cellphone'], 'stripTags'],
            ['phone', 'phone'],
            ['cellphone', 'phone', ['skipOnEmpty' => true]],
        ];

        $rules = $this->_addAgreedReceiveNewslettersRule($rules);

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeOptions()
    {
        return [
            'fname'     => ['disabled'=>'disabled'],
            'lname'     => ['disabled'=>'disabled'],
            'gender'    => [
                'prompt' => \TS_Functions::__('Gender'),
                'disabled' => 'disabled',
            ],
            'nationalId' => ['disabled'=>'disabled'],
            'town'       => ['disabled'=>'disabled'],
            'dayNumber'  => ['disabled'=>'disabled'],
            'monthNumber' => ['disabled'=>'disabled'],
            'yearNumber' => ['disabled'=>'disabled'],
            'email'     => ['disabled'=>'disabled'],
            'country'   => ['disabled'=>'disabled'],
            'state'     => ['disabled'=>'disabled'],
            'submit'    => ['value' => 'Save'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_setStatusMessage();
    }

    /**
     * Save account details
     * @return bool
     */
    public function save()
    {
       return $this->_afterVerificationSaving();
    }
}