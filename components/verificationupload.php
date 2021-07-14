<?php

namespace tradersoft\components;

use tradersoft\helpers\Config;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\model\Verification_Upload;
use \tradersoft\helpers\Arr;
use \tradersoft\helpers\Session;

/**
 * Class Kohana_VerificationUpload
 *
 * Check and process Verification images upload
 *
 * @author Andrey Fomov <andrey.fomov@bonline.co>
 *
 */
class VerificationUpload
{
    protected $_messages = [];
    protected $_config = [];
    protected $_file = [];
    protected $_errors = [];
    protected $_uploadResult = false;
    protected $_model;
    /** @var Session  */
    protected $_session;

    /**
     * Kohana_Verification constructor.
     *
     * @param Verification_Upload $model
     */
    public function __construct(Verification_Upload $model)
    {
        $this->_config = Config::get('verification_upload');
        $this->_model = $model;

        $this->_messages = Arr::get($this->_config, 'messages');
        $this->_session = new Session();

        $this->_initFile();
    }

    /**
    * Function getErrors
    *
    * @author Andrey Fomov <andrey.fomov@bonline.co>
    *
    * @return array
    */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
    * Function getUpload
    *
    * @author Andrey Fomov <andrey.fomov@bonline.co>
    *
    * @return mixed
    */
    public function getUploadResult()
    {
        return $this->_uploadResult;
    }

    /**
    * Function processing
    * If file data exist and no errors found - do upload file and set result to $_upload
    *
    * @author Andrey Fomov <andrey.fomov@bonline.co>
    *
    * @return VerificationUpload
    */
    public function processing()
    {
        if ($this->_file && !$this->_errors) {
            $this->_uploadResult = $this->_attachLeadFile($this->_file);
        }

        return $this;
    }

    /**
     * @param $fileData array
     */
    protected static function _prepareFileData(array &$fileData)
    {
        foreach ($fileData as &$field) {
            if (!is_array($field)) {
                $field = [$field];
            }
        }
    }

    /**
    * Validate file data, parse it if all valid and set $_file. Set errors to $_errors
    *
    * @return VerificationUpload
    * @author Andrey Fomov <andrey.fomov@bonline.co>
    */
    protected function _initFile()
    {
        $fileData =  $this->_model->file->asArray();

        VerificationUpload::_prepareFileData($fileData);

        foreach ($fileData as $fieldname => $values) {

            $validationRules = Arr::get($this->_config['img-validation'], $fieldname);

            if (!$validationRules) {
                continue;
            }

            if (!$values || !is_array($values) || !($value = current($values))) {
                $this->_errors[] = Arr::get($this->_messages, "no-$fieldname");
                continue;
            }

            switch ($fieldname) {

                case 'name' :
                    $extension = pathinfo($value, PATHINFO_EXTENSION);
                    if (!in_array(strtolower($extension), $validationRules['extension'])) {
                        $this->_errors[] = Arr::get($this->_messages, 'wrong-name-extension');
                        break;
                    }
                    $this->_file[$fieldname] = $value;
                    break;

                case 'type' :
                    if (!in_array($value, $validationRules)) {
                        $this->_errors[] = Arr::get($this->_messages, "wrong-type");
                        break;
                    }
                    $this->_file[$fieldname] = $value;
                    break;

                case 'size' :
                    if ($value <= $validationRules['min']) {
                        $this->_errors[] = Arr::get($this->_messages, "wrong-size-min");
                        break;
                    } elseif ($value > $validationRules['max']) {
                        $this->_errors[] = Arr::get($this->_messages, "wrong-size-max");
                        break;
                    }

                    $this->_file[$fieldname] = $value;

                    break;

                case 'tmp_name' :
                    if (!$value && !$this->_errors) {
                        $this->_errors[] = Arr::get($this->_messages, "system");
                        break;
                    }
                    $this->_file[$fieldname] = $value;
                    break;
            }

        }

        return $this;
    }

    /**
     * Attach file to current trader via Binopt API
     *
     * @param array $fileData
     * @return bool
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    protected function _attachLeadFile($fileData)
    {
        if (!$fileData) {
            return false;
        }

        $response = Interlayer_Crm::attachLeadFile(
            $this->_model->getTraderId(),
            Arr::get($fileData, 'tmp_name'),
            Arr::get($fileData, 'name'),
            $this->_model->categoryTypeId,
            $this->_model->comment,
            Arr::get($this->_config, 'sourceKey')
        );

        return $this->_processingResponse($response);
    }

    /**
     * Checks response error and prepares error message
     *
     * @param array $response
     * @return bool
     * @author Igor Popravka <igor.popravka@tstechpro.com>
     */
    protected function _processingResponse(array $response)
    {
        if (($code = Arr::get($response, 'returnCode', 0)) === 0) {
            return true;
        }

        if ($code == Interlayer_Crm::RESPONSE_CODE_DOCUMENTS_UPLOAD_FORBIDDEN) {
            $this->_errors[] = Arr::get($response, 'description');
        } else {
            $this->_errors[] = Arr::get($this->_messages, 'system');
        }

        return false;
    }
}