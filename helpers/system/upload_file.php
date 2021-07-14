<?php
namespace tradersoft\helpers\system;

/**
 * Upload_File
 *
 * @property string $baseName
 * @property string $extension
 * @property bool $hasError
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Upload_File
{
    public $name;
    public $tempName;
    public $type;
    public $size;
    public $error;

    private static $_files;

    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param string $attribute Attribute name
     * @return Upload_File
     */
    public static function getInstance($attribute)
    {
        return static::_getInstanceByName($attribute);
    }

    /**
     * @param string $attribute Attribute name
     * @return Upload_File[]
     */
    public static function getInstances($attribute)
    {
        return static::_getInstancesByName($attribute);
    }

    /**
     * Get if has error
     * @return bool
     */
    public function getHasError()
    {
        return $this->error != UPLOAD_ERR_OK;
    }

    /**
     * Get original file name
     * @return string
     */
    public function getBaseName()
    {
        $pathInfo = pathinfo($this->name, PATHINFO_FILENAME);
        return mb_substr($pathInfo, 0, mb_strlen($pathInfo, 'UTF-8'), 'UTF-8');
    }

    /**
     * Get file extension
     * @return string
     */
    public function getExtension()
    {
        return strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'name'      => $this->name,
            'type'      => $this->type,
            'tmp_name'  => $this->tempName,
            'error'     => $this->error,
            'size'      => $this->size
        ];
    }

    /**
     * Saves the uploaded file.
     * @param string $file
     * @param bool $deleteTempFile
     * @return bool
     */
    public function saveAs($file, $deleteTempFile = true)
    {
        if ($this->error == UPLOAD_ERR_OK) {
            if ($deleteTempFile) {
                return move_uploaded_file($this->tempName, $file);
            } elseif (is_uploaded_file($this->tempName)) {
                return copy($this->tempName, $file);
            }
        }

        return false;
    }

    /**
     * @param string $name Attribute name
     * @return null|Upload_File
     */
    protected static function _getInstanceByName($name)
    {
        $files = self::_loadFiles();
        return isset($files[$name]) ? new static($files[$name]) : null;
    }

    /**
     * @param string $name Attribute name
     * @return Upload_File[]
     */
    protected static function _getInstancesByName($name)
    {
        $files = self::_loadFiles();
        if (isset($files[$name])) {
            return [new static($files[$name])];
        }
        $results = [];
        foreach ($files as $key => $file) {
            if (strpos($key, "{$name}[") === 0) {
                $results[] = new static($file);
            }
        }
        return $results;
    }

    /**
     * Creates Upload_File from $_FILE.
     * @return array the Upload_File instances
     */
    private static function _loadFiles()
    {
        if (self::$_files === null) {
            self::$_files = [];
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $key => $info) {
                    self::_loadFilesRecursive($key, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error']);
                }
            }
        }
        return self::$_files;
    }

    /**
     * @param string $key
     * @param mixed $names file name
     * @param mixed $tempNames temporary file name
     * @param mixed $types file type
     * @param mixed $sizes file size
     * @param mixed $errors
     */
    private static function _loadFilesRecursive($key, $names, $tempNames, $types, $sizes, $errors)
    {
        if (is_array($names)) {
            foreach ($names as $i => $name) {
                self::_loadFilesRecursive($key . '[' . $i . ']', esc_html($name), $tempNames[$i], $types[$i], $sizes[$i], $errors[$i]);
            }
        } elseif ((int)$errors !== UPLOAD_ERR_NO_FILE) {
            self::$_files[$key] = [
                'name' => esc_html($names),
                'tempName' => $tempNames,
                'type' => $types,
                'size' => $sizes,
                'error' => $errors,
            ];
        }
    }

    /**
     * @param $properties array
     */
    private function __construct(array $properties = [])
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }

}