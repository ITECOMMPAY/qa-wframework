<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\WebDriverSelect;

class GetText extends AbstractOperation
{
    /**
     * Возвращает видимый текст элемента.
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
     * @return array - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : array
    {
        return $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject) : string
    {
        WLogger::logDebug('Получаем видимый текст');

        $element = $pageObject->getProxyWebElement();

        $tag = $element->getTagName();

        if (strcasecmp('select', $tag) === 0)
        {
            $select = new WebDriverSelect($element);

            $result = $select
                            ->getFirstSelectedOption()
                            ->getText()
                            ;
        }
        else
        {
            $result = $element->getText();
        }

        WLogger::logDebug('Получили видимый текст: ' . $result);

        return $result;
    }
}
