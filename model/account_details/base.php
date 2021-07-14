<?php
namespace tradersoft\model\account_details;

use tradersoft\model\Model;
use TSInit;

abstract class Base extends Model
{
    public $fname;
    public $lname;
    public $phone;
    public $cellphone;
    public $town;
    public $postalCode;
    public $dayNumber;
    public $monthNumber;
    public $yearNumber;
    public $email;
    public $agreedReceiveNewsletters;

    /**
     * @var bool
     */
    protected $_withReceiveEmailNewslettersAgreement = false;

    protected $_view;

    public function init()
    {
        $this->_loadModelAttributes();

        parent::init();
    }

    /**
     * @return array
     */
    public function getDays()
    {
        return array_combine(range(1,31), range(1,31));
    }

    /**
     * @return array
     */
    public function getMonths()
    {
        return array_combine(range(1,12), range(1,12));
    }

    /**
     * @return array
     */
    public function getYears()
    {
        $current_year = date('Y')-18;
        return array_combine(range(1900, $current_year), range(1900, $current_year));
    }

    /**
     * @return bool
     */
    public function hasView()
    {
        return !empty($this->_view);
    }

    /**
     * @return bool
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Setting receiveEmailNewslettersAgreement attribute
     *
     * @param bool $receiveEmailNewslettersAgreement
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function setWithReceiveEmailNewslettersAgreement($receiveEmailNewslettersAgreement)
    {
        $this->_withReceiveEmailNewslettersAgreement = (bool) $receiveEmailNewslettersAgreement;
    }

    /**
     * Getting receiveEmailNewslettersAgreement attribute value
     *
     * @return bool
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function withReceiveEmailNewslettersAgreement()
    {
        return $this->_withReceiveEmailNewslettersAgreement;
    }

    /**
     * Load model
     */
    protected function _loadModelAttributes()
    {
        if (!TSInit::$app->trader->isGuest) {
            $born = TSInit::$app->trader->get('born');
            if($born){
                $born = explode('-', $born);
            }
            $this->yearNumber   = isset($born[0]) ? (int)$born[0] : '';
            $this->monthNumber  = isset($born[1]) ? (int)$born[1] : '';
            $this->dayNumber    = isset($born[2]) ? (int)$born[2] : '';
            $this->fname        = TSInit::$app->trader->get('fname');
            $this->lname        = TSInit::$app->trader->get('lname');
            $this->phone        = TSInit::$app->trader->get('phone');
            $this->cellphone    = TSInit::$app->trader->get('cellphone');
            $this->town         = TSInit::$app->trader->get('town');
            $this->postalCode   = TSInit::$app->trader->get('postalCode');
            $this->email        = TSInit::$app->trader->get('email');

            if ($this->withReceiveEmailNewslettersAgreement()) {
                $this->agreedReceiveNewsletters = (int) TSInit::$app->trader->get('agreedReceiveNewsletters');
            }
        }
    }

    /**
     * Adding agreedReceiveNewsletters rule validation
     *
     * @param array $rules
     *
     * @return array
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    protected function _addAgreedReceiveNewslettersRule(array $rules)
    {
        if ($this->withReceiveEmailNewslettersAgreement()) {
            $rules[] = [
                'agreedReceiveNewsletters',
                'inArray',
                ['array' => ['0', '1']]
            ];
        }

        return $rules;
    }
}