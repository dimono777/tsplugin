<?php

namespace tradersoft\model;

use tradersoft\components\VerificationUpload;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Config;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\system\Upload_File;

/**
 * Verification_Upload model
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Verification_Upload extends Model
{

    /** @var Upload_File */
    public $file;

    /** @var integer */
    public $categoryTypeId;

    /** @var string */
    public $comment = '';

    /** @var int */
    private $_traderId;

    /** @var array */
    private $_availableTypes = [];

    /** @var array */
    private $_requiredComments = [];

    public function __construct($traderId, $availableTypes = [], $requiredComments = [])
    {
        $this->_traderId = $traderId;
        $this->_availableTypes = $availableTypes;
        $this->_requiredComments = $requiredComments;

        parent::__construct();
    }

    /**
     * @param $id
     *
     * @return void
     */
    public function setCategoryTypeId($id)
    {
        $this->categoryTypeId = $id;
    }

    /**
     * @return array
     */
    public function rules()
    {
        $config = Config::get('verification_upload.img-validation');

        $rules = [
            [
                'file',
                'file',
                [
                    'skipOnEmpty' => false,
                    'maxSize' => $config['size']['max'],
                    'minSize' => $config['size']['min'],
                    'extensions' => $config['name']['extension'],
                    'messages' => [
                        'required' => \TS_Functions::__('Please, choose file for upload'),
                        'tooBig' => \TS_Functions::__('Maximum file size is {formatLimitInUnits} {formatUnit}.'),
                        'tooSmall' => \TS_Functions::__('The file "{file}" is too small. Its size cannot be smaller than {formatLimitInUnits} {formatUnit}.'),
                        'notEmpty' => \TS_Functions::__('You can\'t upload an empty file.'),
                        'extensions' => \TS_Functions::__('Wrong file extensions. Available extensions: {extensions}'),
                    ],
                ],
            ],
        ];

        if ($this->categoryTypeId !== 0) {
            $rules[] = [
                'categoryTypeId',
                'inarray',
                [
                    'array' => array_keys($this->_availableTypes),
                    'msg' => 'Wrong category type',
                ],
            ];
            if (in_array($this->categoryTypeId, $this->_requiredComments)) {
                $rules[] = ['comment', 'required', ['msg' => 'Comment is required for selected document type']];
            }
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ['file' => \TS_Functions::__('File')];
    }

    public function save()
    {
        $fileDetails = $this->getUploadFileDetails();

        if (!Arr::get($fileDetails, 'canUploadDocuments', true)) {
            $result['success'] = false;
            $result['errors'] = [\TS_Functions::__(Arr::get($fileDetails, 'forbidUploadDocumentsReason', ''))];
        } else {
            $uploader = new VerificationUpload($this);
            $uploader->processing();
            $result['success'] = $uploader->getUploadResult();
            $result['errors'] = $uploader->getErrors();
        }

        return $result;
    }

    /**
     * Returns additional information about uploaded files.
     *
     * @return array
     * @author Igor Popravka <igor.popravka@tstechpro.com>
     */
    public function getUploadFileDetails()
    {
        return Arr::get(Interlayer_Crm::getUploadFileDetails($this->_traderId), 'uploadFileDetails', []);
    }

    /**
     * return categories types list for verification file
     *
     * @return array|mixed
     */
    public static function getCategoriesTypesList()
    {
        return Interlayer_Crm::getVerificationFileCategoriesTypesList();
    }

    /**
     * @return int
     * @author Igor Popravka <igor.popravka@tstechpro.com>
     */
    public function getTraderId()
    {
        return $this->_traderId;
    }
}
