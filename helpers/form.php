<?php

namespace tradersoft\helpers;

/**
 * Active form
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Form extends SimpleForm
{
    /**
     * Init form
     */
    public function init()
    {
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * Function run
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        parent::run();

        $this->_buildForm();
    }

    protected function _buildForm()
    {
        $content = ob_get_clean();
        echo $this->formStart();
        echo $this->getPreLoaderBlock();
        echo $content;
        echo $this->formEnd();
    }
}