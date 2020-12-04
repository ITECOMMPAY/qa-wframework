<?php


namespace Codeception\Lib\WFramework\WOperations\Edit;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class EditInnerHTML extends AbstractOperation
{
    /**
     * @var string
     */
    protected $text;

    /**
     * Задаёт innerHTML
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function acceptWBlock($block)
    {
        $this->apply($block);
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    public function acceptWCollection($collection)
    {
        $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Задаём innerHTML равное: ' . $this->text);

        $pageObject->getProxyWebElement()->executeScriptOnThis('arguments[0].innerHTML = arguments[1];', [$this->text]);
    }
}
