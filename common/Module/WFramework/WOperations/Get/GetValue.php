<?php


namespace Common\Module\WFramework\WOperations\Get;


use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Common\Module\WFramework\WOperations\AbstractPageObjectVisitor;
use Facebook\WebDriver\WebDriverSelect;

class GetValue extends AbstractPageObjectVisitor
{
    /**
     * Возвращает значение атрибута 'value' данного элемента.
     *
     * @return string - значение атрибута 'value'
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\UnexpectedTagNameException
     */
    public function acceptWPageObject(IPageObject $pageObject) : string
    {
        WLogger::logDebug('Получаем значение');

        $element = $pageObject->getProxyWebElement();

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
