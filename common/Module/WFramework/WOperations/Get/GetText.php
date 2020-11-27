<?php


namespace Common\Module\WFramework\WOperations\Get;


use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Common\Module\WFramework\WOperations\AbstractPageObjectVisitor;
use Facebook\WebDriver\WebDriverSelect;

class GetText extends AbstractPageObjectVisitor
{
    /**
     * Возвращает видимый текст элемента.
     *
     * @return string - видимый текст элемента.
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\UnexpectedTagNameException
     */
    public function acceptWPageObject(IPageObject $pageObject) : string
    {
        WLogger::logDebug('Получаем текст');

        $element = $pageObject->getProxyWebElement();

        $tag = $element->getTagName();

        $result = '';

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

        WLogger::logDebug('Получили текст: ' . $result);

        return $result;
    }
}
