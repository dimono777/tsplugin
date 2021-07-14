<?php

namespace tradersoft\model\form;

use tradersoft\interfaces\ISystemMessage;
use tradersoft\model\ModelWithBlockInterface;

interface FormBuilderModelInterface extends ModelWithBlockInterface, ISystemMessage
{
    /**
     * @return bool
     */
    public function save();

    /**
     * @return bool
     */
    public function isNeedRedirect();

    /**
     * @return string|null
     */
    public function getRedirectUrl();

    /**
     * @param string $url
     */
    public function setRedirectUrl($url);

    /**
     * @return \tradersoft\model\form\decorators\StructureInterface
     */
    public function getStructureData();
}