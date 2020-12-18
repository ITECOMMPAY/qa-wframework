<?php


namespace Codeception\Lib\WFramework\Operations\Edit;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class EditInnerHTML extends AbstractOperation
{
    public function getName() : string
    {
        return "заменяем внутренний HTML на: " . substr($this->text, 0, 64);
    }

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
        $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Задаём innerHTML равное: ' . $this->text);

        $pageObject->returnSeleniumElement()->executeScriptOnThis('arguments[0].innerHTML = arguments[1];', [$this->text]);
    }
}
