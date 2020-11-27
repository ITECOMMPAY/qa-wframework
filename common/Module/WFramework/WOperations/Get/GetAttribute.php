<?php


namespace Common\Module\WFramework\WOperations\Get;


use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Common\Module\WFramework\WOperations\AbstractPageObjectVisitor;

class GetAttribute extends AbstractPageObjectVisitor
{
    /** @var string */
    protected $attribute;

    /**
     * Возвращает значение атрибута данного элемента.
     *
     * @param string $attribute - атрибут
     */
    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @param IPageObject $pageObject
     * @return null|string - значение атрибута, или null - если атрибут не найден
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\StaleElementReferenceException
     */
    public function acceptWPageObject(IPageObject $pageObject) : string
    {
        WLogger::logDebug('Получаем значение атрибута: ' . $this->attribute);

        $result = $pageObject
                        ->getProxyWebElement()
                        ->getAttribute($this->attribute)
                        ;

        WLogger::logDebug('Атрибут имеет значение: ' . json_encode($result));

        return $result;
    }
}
