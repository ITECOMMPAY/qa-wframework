<?php


namespace Codeception\Lib\WFramework\Operations\Field;


use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
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
        $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_SET, [$this->value]));
    }

    //https://github.com/facebook/react/issues/10135#issuecomment-314441175
    protected const SCRIPT_SET = <<<EOF
function setNativeValue(element, value) {
  const valueSetter = Object.getOwnPropertyDescriptor(element, 'value').set;
  const prototype = Object.getPrototypeOf(element);
  const prototypeValueSetter = Object.getOwnPropertyDescriptor(prototype, 'value').set;
  
  if (valueSetter && valueSetter !== prototypeValueSetter) {
  	prototypeValueSetter.call(element, value);
  } else {
    valueSetter.call(element, value);
  }
}

setNativeValue(arguments[0], arguments[1]);

arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
EOF;
}