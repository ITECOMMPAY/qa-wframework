<?php


namespace Codeception\Lib\WFramework\WebObjects\Base\Traits;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\Conditions\Disabled;
use Codeception\Lib\WFramework\Conditions\Enabled;
use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Conditions\FullyVisible;
use Codeception\Lib\WFramework\Conditions\Hidden;
use Codeception\Lib\WFramework\Conditions\Not_;
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
     * Метод мягко валит тест
     *
     * @param string $description - причина
     * @throws UsageException
     */
    protected function failSoft(string $description = '')
    {
        $this
            ->returnCodeceptionActor()
            ->failSoft($description)
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
        WLogger::logAction($this, "условие: '$condition' - должно выполниться в течение таймаута");

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
                $explanation = $condition->why($selfOrChild, false);

                WLogger::logError($this, $explanation->getMessage(), ['screenshot_blob' => $explanation->getScreenshot()]);

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
        WLogger::logAction($this, "условие: '$condition' - может быть выполнится в течение таймаута");

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
        WLogger::logAction($this, "условие: '$condition'");

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


    public function shouldExist(bool $deep = false)
    {
        return $this->should(new Exist(), $deep);
    }

    public function shouldNotExist(bool $deep = false)
    {
        return $this->should(new Not_(new Exist()), $deep);
    }

    public function shouldBeDisplayed(bool $deep = false)
    {
        return $this->should(new Visible(), $deep);
    }

    public function shouldBeHidden(bool $deep = false)
    {
        return $this->should(new Hidden(), $deep);
    }

    public function shouldBeEnabled(bool $deep = false)
    {
        return $this->should(new Enabled(), $deep);
    }

    public function shouldBeDisabled(bool $deep = false)
    {
        return $this->should(new Disabled(), $deep);
    }

    public function shouldBeInViewport(bool $deep = false)
    {
        return $this->should(new FullyVisible(), $deep);
    }

    public function shouldBeOutOfViewport(bool $deep = false)
    {
        return $this->should(new Not_(new FullyVisible()), $deep);
    }




    public function finallyExist(bool $deep = false) : bool
    {
        return $this->finally_(new Exist(), $deep);
    }

    public function finallyNotExist(bool $deep = false) : bool
    {
        return $this->finally_(new Not_(new Exist()), $deep);
    }

    public function finallyDisplayed(bool $deep = false) : bool
    {
        return $this->finally_(new Visible(), $deep);
    }

    public function finallyHidden(bool $deep = false) : bool
    {
        return $this->finally_(new Hidden(), $deep);
    }

    public function finallyEnabled(bool $deep = false) : bool
    {
        return $this->finally_(new Enabled(), $deep);
    }

    public function finallyDisabled(bool $deep = false) : bool
    {
        return $this->finally_(new Disabled(), $deep);
    }

    public function finallyInViewport(bool $deep = false) : bool
    {
        return $this->finally_(new FullyVisible(), $deep);
    }

    public function finallyOutOfViewport(bool $deep = false) : bool
    {
        return $this->finally_(new Not_(new FullyVisible()), $deep);
    }




    public function isExist(bool $deep = false) : bool
    {
        return $this->is(new Exist(), $deep);
    }

    public function isNotExist(bool $deep = false) : bool
    {
        return $this->is(new Not_(new Exist()), $deep);
    }

    public function isDisplayed(bool $deep = false) : bool
    {
        return $this->is(new Visible(), $deep);
    }

    public function isHidden(bool $deep = false) : bool
    {
        return $this->is(new Hidden(), $deep);
    }

    public function isEnabled(bool $deep = false) : bool
    {
        return $this->is(new Enabled(), $deep);
    }

    public function isDisabled(bool $deep = false) : bool
    {
        return $this->is(new Disabled(), $deep);
    }

    public function isInViewport(bool $deep = false) : bool
    {
        return $this->is(new FullyVisible(), $deep);
    }

    public function isOutOfViewport(bool $deep = false) : bool
    {
        return $this->is(new Not_(new FullyVisible()), $deep);
    }
}
