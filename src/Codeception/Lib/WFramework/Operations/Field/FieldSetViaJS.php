<?php


namespace Codeception\Lib\WFramework\Operations\Field;


use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class FieldSetViaJS extends AbstractOperation
{
    public function getName() : string
    {
        return "задаём полю ввода текст: $this->value (с помощью JavaScript)";
    }

    /**
     * @var string
     */
    protected $value;

    /**
     * Задаёт текст данного элемента (с помощью JavaScript).
     *
     * Если элемент содержал текст - он будет заменён.
     *
     * @param string $value - новый текст для элемента
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }



    protected function apply(WPageObject $pageObject)
    {
        $pageObject->returnSeleniumElement()->executeScriptOnThis(static::SCRIPT_SET, [$this->value]);
    }

    protected const SCRIPT_SET = <<<EOF
arguments[0].value = arguments[1];
EOF;
}