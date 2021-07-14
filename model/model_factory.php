<?php
namespace tradersoft\model;

/**
 * Factory for create model.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
final class Model_Factory
{
    /**
     * @param $class string
     * @param $data array
     * @param $stripSlashes bool
     * @return Model
     * @throws \Exception
     */
    public static function factory($class, array $data, $stripSlashes = true)
    {
        if (class_exists($class)) {
            $model = new $class();
            if ($stripSlashes) {
                self::stripSlashes($data);
            }
            if ($model instanceof Model) {
                self::_loadModel($model, $data);
                return $model;
            }
        }
        throw new \Exception('Unknown class');
    }

    /**
     * @param $model Model
     * @param $data array
     */
    protected static function _loadModel(Model $model, $data)
    {
        if (!empty($data)) {
            $model->load($data);
            if($model->validate()){
                $model->save();
            }
        }
    }

    private static function stripSlashes(array &$data)
    {
        foreach ($data as &$value) {
            $value = stripslashes($value);
        }
    }
}