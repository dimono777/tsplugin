<?php
namespace tradersoft\model\account_details\saasic;

class ProcessVerification extends Base
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
            'submit' => ['value' => 'Verify'],
        ];
    }

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
        return $this->_processVerificationSaving();
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
                'state'         => $this->state,
            ]
        );
    }
}