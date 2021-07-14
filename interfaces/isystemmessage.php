<?php
namespace tradersoft\interfaces;

interface ISystemMessage
{
    const SYSTEM_MESSAGE_SUCCESS = 1;
    const SYSTEM_MESSAGE_WARNING = 2;
    const SYSTEM_MESSAGE_INFO = 3;

    public function addSystemMessage($text, $type = self::SYSTEM_MESSAGE_INFO);
    public function getSystemMessages($type = null);
    public function hasSystemMessage($type = null);
    public function renderSystemMessages($type = null);
}