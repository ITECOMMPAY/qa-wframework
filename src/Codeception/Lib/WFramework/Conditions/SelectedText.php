<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class SelectedText extends AbstractCondition
{
    protected const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    /**
     * @var string
     */
    protected $expectedValue;

    public function getName() : string
    {
        return "выделенный текст соответствует '" . $this->expectedValue . "'? (без учёта регистра и пробелов)";
    }

    public function __construct(string $expectedText)
    {
        $this->expectedValue = strtolower(
                                        trim(
                                            preg_replace(static::BLANK, ' ', $expectedText)));
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        $selectedText = $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_SELECTED_TEXT));

        return $this->expectedValue === strtolower(
                                                trim(
                                                    preg_replace(static::BLANK, ' ', $selectedText)));
    }

    protected const SCRIPT_SELECTED_TEXT = "return arguments[0].value.substring(arguments[0].selectionStart, arguments[0].selectionEnd);";
}
