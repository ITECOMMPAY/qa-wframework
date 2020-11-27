<?php


namespace Common\Module\WFramework\WOperations\Get;


use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Common\Module\WFramework\WOperations\AbstractPageObjectVisitor;

class GetRawText extends AbstractPageObjectVisitor
{
    /**
     * Получает сырой текст элемента (включая невидимый)
     *
     * @return string
     */
    public function acceptWPageObject(IPageObject $pageObject) : string
    {
        WLogger::logDebug('Получаем сырой текст элемента (включая невидимый)');

        $element = $pageObject->getProxyWebElement();

        $result = $element->executeScriptOnThis(static::SCRIPT_GET_TEXT);

        WLogger::logDebug('Получили сырой текст: ' . $result);

        return $result;
    }

    const SCRIPT_GET_TEXT = <<<EOF
let content = arguments[0].textContent;

if (content === "")
{
    content = arguments[0].getAttribute('value') ?? '';
}

return content;
EOF;
}
