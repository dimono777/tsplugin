<?php
namespace tradersoft\model\account_details\saasic;

class BeforeVerification extends Base
{
    /**
     * @inheritdoc
     */
    public function attributeOptions()
    {
        return [
            'gender' => [
                'prompt' => \TS_Functions::__('Gender'),
            ],
            'email' => ['disabled'=>'disabled'],
            'country' => ['disabled'=>'disabled'],
            'submit' => ['value' => 'Save'],
        ];
    }

    /**
     * Save account details
     * @return bool
     */
    public function save()
    {
        return $this->_beforeVerificationSaving();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareAccountData()
    {
        return array_merge(
            parent::_prepareAccountData(),
            [
                'firstName'     => $this->fname,
                'nationalId'    => $this->nationalId,
                'lastName'      => $this->lname,
                'gender'        => $this->gender,
                'birthday'      => $this->yearNumber . '-' . $this->monthNumber . '-' . $this->dayNumber,
                'country'       => $this->country,
                'state'         => $this->state,
            ]
        );
    }
}