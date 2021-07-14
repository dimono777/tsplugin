<?php
namespace tradersoft\traits;

/**
 * Trait IteratorTrait
 * @package tradersoft\traits
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
trait IteratorTrait
{
    public function reset()
    {
        if(empty($this->_attributesData)) {
            $this->_attributesData = [];
        }

        reset($this->_attributesData);
    }

    public function rewind()
    {
        $this->reset();
    }

    public function current()
    {
        return current($this->_attributesData);
    }

    public function key()
    {
        return key($this->_attributesData);
    }

    public function next()
    {
        next($this->_attributesData);
    }

    public function valid()
    {
        return isset($this->_attributesData[$this->key()]);
    }
}