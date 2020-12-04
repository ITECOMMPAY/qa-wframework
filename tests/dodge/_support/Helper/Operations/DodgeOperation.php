<?php


namespace dodge\Helper\Operations;


use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use dodge\Helper\Blocks\DodgeBlock;

class DodgeOperation extends AbstractOperation
{
    /**
     * @param DodgeBlock $block
     * @return mixed|void
     */
    public function acceptWBlock($block)
    {
        return parent::acceptWBlock($block);
    }

    /**
     * @param DodgeElement $element
     * @return mixed|void
     */
    public function acceptWElement($element)
    {
        return parent::acceptWElement($element);
    }

    /**
     * @param DodgeCollection $collection
     * @return mixed|void
     */
    public function acceptWCollection($collection)
    {
        return parent::acceptWCollection($collection);
    }
}
