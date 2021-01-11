<?php


namespace Codeception\Lib\WFramework\WebObjects\Base\Traits;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\Conditions\Disabled;
use Codeception\Lib\WFramework\Conditions\Enabled;
use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Conditions\FullyVisible;
use Codeception\Lib\WFramework\Conditions\Hidden;
use Codeception\Lib\WFramework\Conditions\Not_;
use Codeception\Lib\WFramework\Conditions\Text;
use Codeception\Lib\WFramework\Conditions\TextContains;
use Codeception\Lib\WFramework\Conditions\Value;
use Codeception\Lib\WFramework\Conditions\ValueContains;
use Codeception\Lib\WFramework\Conditions\Visible;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Exceptions\WaitUntilElement;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Wait\WaitUntil;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

/**
 * Trait PageObjectBaseMethods
 *
 * Методы которые можно сделать общими для WPageObject и WCollection
 *
 * @package Codeception\Lib\WFramework\WebObjects\Base\Traits
 */
trait PageObjectBaseMethods
{
    abstract public function __toString() : string;

    abstract public function getTimeout() : int;

    abstract public function traverseDepthFirst() : \Generator;

    abstract public function getParent();

    /**
     * Метод валит тест
     *
     * @param string $description - причина
     * @throws UsageException
     */
    protected function fail(string $description = '')
    {
        $this
            ->returnCodeceptionActor()
            ->fail($description)
            ;
    }

    /**
     * Ждёт выполнение заданного условия для данного PageObject'а.
     *
     * Если условие не было выполнено в течении заданного таймаута (elementTimeout/collectionTimeout) - валит тест.
     *
     * @param AbstractCondition $condition - условие
     * @param bool $deep - вызывает should-метод для всех PageObject'ов, которые объявлены внутри данного PageObject'а
     * @return $this
     * @throws UsageException
     */
    public function should(AbstractCondition $condition, bool $deep = false)
    {
        WLogger::logInfo($this . " -> условие: '$condition' - должно выполниться в течение таймаута");

        /** @var WPageObject $selfOrChild */
        foreach ($this->traverseDepthFirst() as $selfOrChild)
        {
            if ($deep && !$condition->applicable($selfOrChild))
            {
                continue;
            }

            try
            {
                $selfOrChild->accept(new WaitUntil($condition));
            }
            catch (WaitUntilElement $e)
            {
                $this->fail($this . " -> условие: '$condition' - не выполнилось в течение таймаута: " . $this->getTimeout());
            }

            if (!$deep)
            {
                break;
            }
        }

        return $this;
    }

    /**
     * Ждёт выполнение заданного условия для данного PageObject'а.
     *
     * Если условие не было выполнено в течении заданного таймаута (elementTimeout/collectionTimeout) - возвращает false.
     *
     * @param AbstractCondition $condition - условие
     * @param bool $deep - вызывает finally_-метод для всех PageObject'ов, которые объявлены внутри данного PageObject'а
     * @return bool
     */
    public function finally_(AbstractCondition $condition, bool $deep = false) : bool
    {
        WLogger::logInfo($this . " -> условие: '$condition' - может быть выполнится в течение таймаута");

        /** @var WPageObject $selfOrChild */
        foreach ($this->traverseDepthFirst() as $selfOrChild)
        {
            if ($deep && !$condition->applicable($selfOrChild))
            {
                continue;
            }

            try
            {
                $selfOrChild->accept(new WaitUntil($condition));
            }
            catch (WaitUntilElement $e)
            {
                return false;
            }

            if (!$deep)
            {
                break;
            }
        }

        return true;
    }

    /**
     * Проверяет условие для данного PageObject'а.
     *
     * @param AbstractCondition $condition - условие
     * @param bool $deep - вызывает is-метод для всех PageObject'ов, которые объявлены внутри данного PageObject'а
     * @return bool
     * @throws UsageException
     */
    public function is(AbstractCondition $condition, bool $deep = false) : bool
    {
        WLogger::logInfo($this . " -> условие: '$condition'");

        /** @var WPageObject $selfOrChild */
        foreach ($this->traverseDepthFirst() as $selfOrChild)
        {
            if ($deep && !$condition->applicable($selfOrChild))
            {
                continue;
            }

            if (!$selfOrChild->accept($condition))
            {
                return false;
            }

            if (!$deep)
            {
                break;
            }
        }

        return true;
    }


    public function shouldExist(bool $deep = true)
    {
        return $this->should(new Exist(), $deep);
    }

    public function shouldNotExist(bool $deep = true)
    {
        return $this->should(new Not_(new Exist()), $deep);
    }

    public function shouldBeDisplayed(bool $deep = true)
    {
        return $this->should(new Visible(), $deep);
    }

    public function shouldBeHidden(bool $deep = true)
    {
        return $this->should(new Hidden(), $deep);
    }

    public function shouldBeEnabled(bool $deep = true)
    {
        return $this->should(new Enabled(), $deep);
    }

    public function shouldBeDisabled(bool $deep = true)
    {
        return $this->should(new Disabled(), $deep);
    }

    public function shouldBeInViewport(bool $deep = true)
    {
        return $this->should(new FullyVisible(), $deep);
    }

    public function shouldBeOutOfViewport(bool $deep = true)
    {
        return $this->should(new Not_(new FullyVisible()), $deep);
    }

    public function shouldHaveText(string $text)
    {
        return $this->should(new Text($text), false);
    }

    public function shouldContainText(string $text)
    {
        return $this->should(new TextContains($text), false);
    }

    public function shouldHaveValue(string $value)
    {
        return $this->should(new Value($value), false);
    }

    public function shouldContainValue(string $value)
    {
        return $this->should(new ValueContains($value), false);
    }


    public function finallyExist(bool $deep = true) : bool
    {
        return $this->finally_(new Exist(), $deep);
    }

    public function finallyNotExist(bool $deep = true) : bool
    {
        return $this->finally_(new Not_(new Exist()), $deep);
    }

    public function finallyDisplayed(bool $deep = true) : bool
    {
        return $this->finally_(new Visible(), $deep);
    }

    public function finallyHidden(bool $deep = true) : bool
    {
        return $this->finally_(new Hidden(), $deep);
    }

    public function finallyEnabled(bool $deep = true) : bool
    {
        return $this->finally_(new Enabled(), $deep);
    }

    public function finallyDisabled(bool $deep = true) : bool
    {
        return $this->finally_(new Disabled(), $deep);
    }

    public function finallyInViewport(bool $deep = true) : bool
    {
        return $this->finally_(new FullyVisible(), $deep);
    }

    public function finallyOutOfViewport(bool $deep = true) : bool
    {
        return $this->finally_(new Not_(new FullyVisible()), $deep);
    }

    public function finallyHaveText(string $text) : bool
    {
        return $this->finally_(new Text($text), false);
    }

    public function finallyContainText(string $text) : bool
    {
        return $this->finally_(new TextContains($text), false);
    }

    public function finallyHaveValue(string $value) : bool
    {
        return $this->finally_(new Value($value), false);
    }

    public function finallyContainValue(string $value) : bool
    {
        return $this->finally_(new ValueContains($value), false);
    }


    public function isExist(bool $deep = true) : bool
    {
        return $this->is(new Exist(), $deep);
    }

    public function isNotExist(bool $deep = true) : bool
    {
        return $this->is(new Not_(new Exist()), $deep);
    }

    public function isDisplayed(bool $deep = true) : bool
    {
        return $this->is(new Visible(), $deep);
    }

    public function isHidden(bool $deep = true) : bool
    {
        return $this->is(new Hidden(), $deep);
    }

    public function isEnabled(bool $deep = true) : bool
    {
        return $this->is(new Enabled(), $deep);
    }

    public function isDisabled(bool $deep = true) : bool
    {
        return $this->is(new Disabled(), $deep);
    }

    public function isInViewport(bool $deep = true) : bool
    {
        return $this->is(new FullyVisible(), $deep);
    }

    public function isOutOfViewport(bool $deep = true) : bool
    {
        return $this->is(new Not_(new FullyVisible()), $deep);
    }

    public function isHaveText(string $text) : bool
    {
        return $this->is(new Text($text), false);
    }

    public function isContainText(string $text) : bool
    {
        return $this->is(new TextContains($text), false);
    }

    public function isHaveValue(string $value) : bool
    {
        return $this->is(new Value($value), false);
    }

    public function isContainValue(string $value) : bool
    {
        return $this->is(new ValueContains($value), false);
    }
}
