<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 14:48
 */

namespace Common\Module\WFramework\CollectionCondition;


use Common\Module\WFramework\CollectionCondition\Operator\Conj;
use Common\Module\WFramework\CollectionCondition\Operator\Delegate;
use Common\Module\WFramework\CollectionCondition\Operator\Disj;
use Common\Module\WFramework\CollectionCondition\Operator\EveryElement;
use Common\Module\WFramework\CollectionCondition\Operator\ExactTexts;
use Common\Module\WFramework\CollectionCondition\Operator\ExactTextsInAnyOrder;
use Common\Module\WFramework\CollectionCondition\Operator\Explain;
use Common\Module\WFramework\CollectionCondition\Operator\IsEmpty;
use Common\Module\WFramework\CollectionCondition\Operator\Not;
use Common\Module\WFramework\CollectionCondition\Operator\Size;
use Common\Module\WFramework\CollectionCondition\Operator\SizeGreaterThan;
use Common\Module\WFramework\CollectionCondition\Operator\SizeGreaterThanOrEqual;
use Common\Module\WFramework\CollectionCondition\Operator\SizeLessThan;
use Common\Module\WFramework\CollectionCondition\Operator\SizeLessThanOrEqual;
use Common\Module\WFramework\CollectionCondition\Operator\SizeNotEqual;
use Common\Module\WFramework\CollectionCondition\Operator\SomeElement;
use Common\Module\WFramework\CollectionCondition\Operator\Texts;
use Common\Module\WFramework\CollectionCondition\Operator\TextsInAnyOrder;
use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;
use Common\Module\WFramework\Logger\WLogger;


abstract class CCond
{
    protected $name = 'undefined';

    protected $expectedValue = 'undefined';

    protected $actualValue = 'undefined';

    protected $result = False;

    abstract protected function apply(FacadeWebElements $facadeWebElements);

    public function check(FacadeWebElements $facadeWebElements) : bool
    {
        $this->apply($facadeWebElements);

        WLogger::logDebug($this->toString());

        return $this->result;
    }

    public function printActualValue() : string
    {
        return (string) $this->actualValue;
    }

    public function printExpectedValue() : string
    {
        return (string) $this->expectedValue;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getResult() : bool
    {
        return $this->result;
    }

    public function toString() : string
    {
        return  'Условие (коллекции): ' . $this->name . PHP_EOL . ' -> ' . json_encode($this->getResult()) . ' [ожидаемое: ' . $this->printExpectedValue() . ' | актуальное: ' . $this->printActualValue() . ']';
    }

    public function __construct(string $conditionName)
    {
        $this->name = $conditionName;
    }

    //------------------------------------------------------------------------------------------------------------------

    public static function size(int $size) : CCond
    {
        return new Size('size', $size);
    }

    public static function sizeGreaterThan(int $size) : CCond
    {
        return new SizeGreaterThan('sizeGreaterThan', $size);
    }

    public static function sizeGreaterThanOrEqual(int $size) : CCond
    {
        return new SizeGreaterThanOrEqual('sizeGreaterThanOrEqual', $size);
    }

    public static function sizeLessThan(int $size) : CCond
    {
        return new SizeLessThan('sizeLessThan', $size);
    }

    public static function sizeLessThanOrEqual(int $size) : CCond
    {
        return new SizeLessThanOrEqual('sizeLessThanOrEqual', $size);
    }

    public static function sizeNotEqual(int $size) : CCond
    {
        return new SizeNotEqual('sizeNotEqual', $size);
    }

    public static function texts(string ...$texts) : CCond
    {
        return new Texts('texts', ...$texts);
    }

    public static function textsInAnyOrder(string ...$texts) : CCond
    {
        return new TextsInAnyOrder('textsInAnyOrder', ...$texts);
    }

    public static function exactTexts(string ...$texts) : CCond
    {
        return new ExactTexts('exactTexts', ...$texts);
    }

    public static function exactTextsInAnyOrder(string ...$texts) : CCond
    {
        return new ExactTextsInAnyOrder('exactTextsInAnyOrder', ...$texts);
    }

    public static function everyElement(Cond $condition) : CCond
    {
        return new EveryElement('каждый элемент', $condition);
    }

    public static function someElement(Cond $condition) : CCond
    {
        return new SomeElement('хотя бы один элемент', $condition);
    }

    public static function and(CCond ...$conditions) : CCond
    {
        return new Conj('AND', ...$conditions);
    }

    public static function or(CCond ...$conditions) : CCond
    {
        return new Disj('OR', ...$conditions);
    }

    public static function not(CCond $condition) : CCond
    {
        return new Not($condition);
    }

    public static function empty() : CCond
    {
        return new IsEmpty('пустая');
    }

    public static function be(CCond $condition) : CCond
    {
        return new Delegate('должна быть', $condition);
    }

    public static function has(CCond $condition) : CCond
    {
        return new Delegate('должна иметь', $condition);
    }

    public function because(string $message) : CCond
    {
        return new Explain($this, $message);
    }

}
