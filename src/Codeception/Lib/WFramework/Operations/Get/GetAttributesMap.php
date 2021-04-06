<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Ds\Map;
use Ds\Sequence;

class GetAttributesMap extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем ассоциативный массив атрибутов";
    }

    public function acceptWBlock($block) : Map
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : Map
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return \Ds\Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : Map
    {
        $result = $pageObject->returnSeleniumElement()->executeScriptOnThis(static::SCRIPT_GET_ATTRIBUTES);

        WLogger::logDebug($this, 'Элемент имеет атрибуты: ' . json_encode($result));

        return new Map($result);
    }

    const SCRIPT_GET_ATTRIBUTES = <<<EOF
var items = {}; 

for (index = 0; index < arguments[0].attributes.length; ++index) { 
    items[arguments[0].attributes[index].name] = arguments[0].attributes[index].value 
}; 

return items;
EOF;
}