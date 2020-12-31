<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class GetTagName extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем имя тега";
    }

    /**
     * Возвращает имя тега элемента (в нижнем регистре)
     */
    public function __construct() {}

    public function acceptWBlock($block) : string
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : string
    {
        return $this->apply($element);
    }

    public function acceptWCollection($collection) : string
    {
        return $this->apply($collection->getFirstElement());
    }

    protected function apply(WPageObject $pageObject) : string
    {
        $result = $pageObject
                        ->returnSeleniumElement()
                        ->getTagName()
                        ;

        WLogger::logDebug('Имя тега элемента: ' . $result);

        return $result;
    }
}
