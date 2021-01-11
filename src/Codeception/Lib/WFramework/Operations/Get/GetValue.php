<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;
use Facebook\WebDriver\WebDriverSelect;

class GetValue extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем значение атрибута 'value'";
    }

    /**
     * Возвращает значение атрибута 'value' данного элемента.
     */
    public function __construct() { }

    public function acceptWBlock($block) : string
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : string
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : string
    {
        $element = $pageObject->returnSeleniumElement();

        $tag = $element->getTagName();

        if (strcasecmp('select', $tag) === 0)
        {
            $select = new WebDriverSelect($element);

            $result = $select
                            ->getFirstSelectedOption()
                            ->getAttribute('value')
                            ;
        }
        else
        {
            $result = $element->getAttribute('value');
        }

        $result = $result ?? '';

        WLogger::logDebug('Получили значение: ' . $result);

        return $result;
    }
}