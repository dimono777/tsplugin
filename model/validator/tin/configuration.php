<?php

namespace tradersoft\model\validator\tin;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;

class Configuration
{
    /** @var array */
    public $formatter = [];

    /** @var array */
    public $structure = [];

    /**
     * @param string|null $countryIso
     * @return Configuration
     * @throws \Exception
     */
    public static function getInstance($countryIso)
    {
        $config = Interlayer_Crm::getTinValidationConfigData();

        return new static(Arr::path($config, "TIN.$countryIso", []));
    }

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->formatter = Arr::get($config, 'formatter', []);
        $this->structure = Arr::get($config, 'structure', []);
    }
}