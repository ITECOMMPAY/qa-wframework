<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetText;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class TextEmpty extends AbstractCondition
{
    protected const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    public function getName() : string
    {
        return "видимый текст пустой? (без учёта пробелов)";
    }

    public function __construct(){}

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    public function acceptWCollection($collection) : bool
    {
        if ($collection->isEmpty())
        {
            return true;
        }

        return $this->apply($collection->getFirstElement());
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        $actualText = $pageObject->accept(new GetText());

        return '' === trim(preg_replace(static::BLANK, ' ', $actualText));
    }
}