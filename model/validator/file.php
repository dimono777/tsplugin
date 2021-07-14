<?php
namespace tradersoft\model\validator;

use tradersoft\helpers\system\Upload_File;
use tradersoft\model\ModelWithFieldInterface;

/**
 * Compare validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class File implements Validator
{
    private static $_msg = '';
    private static $_skipOnEmpty = true;
    private static $_messages = [];

    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value mixed
     * @param $param mixed
     * @throws \Exception
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        self::_init($param);
        $validVal = self::_checkValue($value);
        if (!$validVal) {
            if (!$param['skipOnEmpty']) {
                $model->addError($attribute, self::$_messages['required']);
            }
            return;
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $file) {
            $result = self::_validateValue($file, $param);
            if (!empty($result) && !$result[0]) {
                $model->addError($attribute, $result[1]);
            }
        }
    }

    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $param mixed
     * @return string
     * @throws \Exception
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        self::_init($param);
        $options = [
            'attribute' => $attribute,
            'message' => self::$_msg,
            'param' => $param,
            'messages' => self::$_messages,
        ];
        return 'validation.file(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }

    /**
     * @param $value
     * @return bool
     */
    private static function _checkValue(&$value)
    {
        if (empty($value)) {
            return false;
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        foreach ($value as $file) {
            if(!$file instanceof Upload_File) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $param mixed
     * @throws \Exception
     */
    protected static function _prepareParam(&$param)
    {
        if (empty($param)) {
            throw new \Exception('File validator property must be set.');
        }
        if (!is_array($param)) {
            throw new \Exception('File validator property must be array.');
        }
        if (isset($param['extensions']) && !is_array($param['extensions'])) {
            throw new \Exception('File validator extensions must be array.');
        }

        if (!isset($param['msg'])) {
            $param['msg'] = self::$_msg;
        }

        if (isset($param['skipOnEmpty'])) {
            $param['skipOnEmpty'] = (bool)$param['skipOnEmpty'];
        } else {
            $param['skipOnEmpty'] = self::$_skipOnEmpty;
        }

        if (isset($param['messages'])) {
            self::$_messages = array_replace(self::$_messages, $param['messages']);
        }

        self::$_messages['message'] = $param['msg'];
    }

    /**
     * @param Upload_File $value mixed
     * @param $param mixed
     * @return array
     */
    protected static function _validateValue(Upload_File $value, $param)
    {
        if (empty($value)) {
            if (!$param['skipOnEmpty']) {
                return [false, self::$_messages['required']];
            }
            return [true];
        }

        if ($value->error != UPLOAD_ERR_OK) {
            return [false, self::$_messages['message']];
        }

        if (isset($param['maxSize']) && $value->size > $param['maxSize']) {
            list($limitInUnit, $unit) = self::_formatBitesInUnits($param['maxSize']);

            return [
                false,
                self::_prepareMsg(
                    self::$_messages['tooBig'],
                    [
                        '{file}' => $value->name,
                        '{formatLimitInBites}' => $param['maxSize'],
                        '{formatLimitInUnits}' => $limitInUnit,
                        '{formatUnit}' => $unit,
                    ]
                )
            ];
        }

        if (isset($param['minSize']) && $value->size < $param['minSize']) {
            list($limitInUnit, $unit) = self::_formatBitesInUnits($param['minSize']);

            return [
                false,
                self::_prepareMsg(
                    ($param['minSize']) ? self::$_messages['tooSmall'] :  self::$_messages['notEmpty'],
                    [
                        '{file}' => $value->name,
                        '{formatLimitInBites}' => $param['minSize'],
                        '{formatLimitInUnits}' => $limitInUnit,
                        '{formatUnit}' => $unit,
                    ]
                )
            ];
        }

        if (isset($param['extensions']) && !self::_validateExtension($value, $param['extensions'])) {
            return [
                false,
                self::_prepareMsg(
                    self::$_messages['extensions'],
                    ['{file}' => $value->name, '{extensions}' => implode(', ',$param['extensions'])]
                )
            ];
        }
        return [true];
    }

    /**
     * @param $param array
     *
     * @throws \Exception
     */
    protected static function _init(&$param)
    {
        self::_prepareDefaultMessages();
        self::_prepareParam($param);
    }

    /**
     * @param Upload_File $file
     * @param array $extensions
     * @return bool
     */
    protected static function _validateExtension(Upload_File $file, $extensions)
    {
        $ext = mb_strtolower($file->extension, 'UTF-8');
        if (!in_array($ext, $extensions)) {
            return false;
        }
        return true;
    }

    protected static function _prepareMsg($msg, $param = [])
    {
        return strtr($msg, $param);
    }

    protected static function _prepareDefaultMessages()
    {
        self::$_msg = \TS_Functions::__('Some error. Please, try again later.');
        self::$_messages = [
            'required'  => \TS_Functions::__('Please, choose file for upload'),
            'tooBig'    => \TS_Functions::__('Maximum file size is {formatLimitInBites} bytes.'),
            'tooSmall'  => \TS_Functions::__('The file "{file}" is too small. Its size cannot be smaller than {formatLimitInBites} bytes.'),
            'notEmpty'  => \TS_Functions::__('You can\'t upload an empty file.'),
            'extensions' => \TS_Functions::__('Wrong file extensions. Available extensions: {extensions}'),
        ];
    }

    /**
     * @param $bites
     *
     * @return array
     */
    protected static function _formatBitesInUnits($bites)
    {
        $base = log($bites) / log(1024);
        $suffix = ['bytes', 'Kb', 'Mb', 'Gb', 'Tb'];

        $unit = $suffix[floor($base)];
        $limitInUnit = round(pow(1024, $base - floor($base)), 1);

        return [
            $unit,
            $limitInUnit,
        ];
    }
}