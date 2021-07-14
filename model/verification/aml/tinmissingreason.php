<?php

namespace tradersoft\model\verification\aml;

use tradersoft\helpers\Arr;

class TINMissingReason
{
    public $id;
    public $title;
    public $description;
    public $hasComment;

    public function __construct(array $data)
    {
        $this->id = Arr::get($data, 'id');
        $this->title = Arr::get($data, 'title');
        $this->description = Arr::get($data, 'description');
        $this->hasComment = Arr::get($data, 'hasComment');
    }

    /**
     * @param array $data
     * @return array
     */
    public static function getListFromData(array $data)
    {
        $reasons = [];
        foreach ($data as $row => $reasonData) {
            $reasons[] = new self($reasonData);
        }
        return $reasons;
    }
}