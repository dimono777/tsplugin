<?php
namespace tradersoft\traits;

use tradersoft\interfaces\ISystemMessage;
use tradersoft\helpers\Html;
use Exception;

trait SystemMessage
{
    protected $_systemMessages = [];

    protected $_systemMessagesHtmlClasses = [
        ISystemMessage::SYSTEM_MESSAGE_INFO => 'system-message system-message-info',
        ISystemMessage::SYSTEM_MESSAGE_SUCCESS => 'system-message system-message-success',
        ISystemMessage::SYSTEM_MESSAGE_WARNING => 'system-message system-message-warning',
    ];

    /**
     * @param $text
     * @param int $type
     * @throws Exception
     */
    public function addSystemMessage($text, $type = ISystemMessage::SYSTEM_MESSAGE_INFO)
    {
        if (!in_array($type, [
            ISystemMessage::SYSTEM_MESSAGE_INFO,
            ISystemMessage::SYSTEM_MESSAGE_SUCCESS,
            ISystemMessage::SYSTEM_MESSAGE_WARNING
        ])) {
            throw new Exception('Incorect system message type');
        }

        $this->_systemMessages[$type][] = [
            'text' => $text,
            'type' => $type,
        ];
    }

    /**
     * @param null $type
     * @return array
     */
    public function getSystemMessages($type = null)
    {
        if (empty($this->_systemMessages)) {
            return [];
        }
        if ($type === null) {
            $messages = [];
            foreach ($this->_systemMessages as $msg) {
                $messages = array_merge($messages, $msg);
            }
            return $messages;
        }
        return isset($this->_systemMessages[$type]) ? $this->_systemMessages[$type] : [];
    }

    /**
     * @param int $type
     * @return bool
     */
    public function hasSystemMessage($type = null)
    {
        return $type === null ? !empty($this->_systemMessages) : !empty($this->_systemMessages[$type]);
    }

    /**
     * @param int $type
     * @return string
     */
    public function renderSystemMessages($type = null)
    {
        $str = '';
        foreach ($this->getSystemMessages($type) as $message) {
            $str .= Html::tag(
                'p',
                $message['text'],
                ['class' => $this->_systemMessagesHtmlClasses[$message['type']]]);
        }

        return $str;
    }
}