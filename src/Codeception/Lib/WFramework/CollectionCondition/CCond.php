<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 14:48
 */

namespace Codeception\Lib\WFramework\CollectionCondition;


use Codeception\Lib\WFramework\CollectionCondition\Operator\Conj;
use Codeception\Lib\WFramework\CollectionCondition\Operator\Delegate;
use Codeception\Lib\WFramework\CollectionCondition\Operator\Disj;
use Codeception\Lib\WFramework\CollectionCondition\Operator\EveryElement;
use Codeception\Lib\WFramework\CollectionCondition\Operator\ExactTexts;
use Codeception\Lib\WFramework\CollectionCondition\Operator\ExactTextsInAnyOrder;
use Codeception\Lib\WFramework\CollectionCondition\Operator\Explain;
use Codeception\Lib\WFramework\CollectionCondition\Operator\IsEmpty;
use Codeception\Lib\WFramework\CollectionCondition\Operator\Not;
use Codeception\Lib\WFramework\CollectionCondition\Operator\Size;
use Codeception\Lib\WFramework\CollectionCondition\Operator\SizeGreaterThan;
use Codeception\Lib\WFramework\CollectionCondition\Operator\SizeGreaterThanOrEqual;
use Codeception\Lib\WFramework\CollectionCondition\Operator\SizeLessThan;
use Codeception\Lib\WFramework\CollectionCondition\Operator\SizeLessThanOrEqual;
use Codeception\Lib\WFramework\CollectionCondition\Operator\SizeNotEqual;
use Codeception\Lib\WFramework\CollectionCondition\Operator\SomeElement;
use Codeception\Lib\WFramework\CollectionCondition\Operator\Texts;
use Codeception\Lib\WFramework\CollectionCondition\Operator\TextsInAnyOrder;
use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElements\FacadeWebElements;
use Codeception\Lib\WFramework\Logger\WLogger;


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
