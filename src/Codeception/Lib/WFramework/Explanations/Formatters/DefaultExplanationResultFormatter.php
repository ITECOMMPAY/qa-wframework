<?php


namespace Codeception\Lib\WFramework\Explanations\Formatters;


use Codeception\Lib\WFramework\Explanations\Result\DefaultExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\ExplanationResultAggregate;
use Codeception\Lib\WFramework\Explanations\Result\MissingValue;
use Codeception\Lib\WFramework\Explanations\Result\TraverseFromRootExplanationResult;

class DefaultExplanationResultFormatter extends AbstractExplanationResultVisitor
{
    public const EXPLANATIONS_DELIMITER = '=======================================================================================' . PHP_EOL;

    public const EXPLANATIONS_TAB = '    ';

    protected $message = '';

    protected $header = '';

    protected $traverseFromRootResult = '';

    protected $defaultResult = '';

    public function acceptExplanationResultAggregate(ExplanationResultAggregate $explanationResult) : void
    {
        $this->header = static::EXPLANATIONS_DELIMITER;
        $this->header .= 'ДИАГНОСТИРУЕМ ЭЛЕМЕНТ:' . PHP_EOL;
        $this->header .= static::EXPLANATIONS_TAB . $explanationResult->getPageObject() . PHP_EOL;
        $this->header .= 'ПРОБЛЕМА:' . PHP_EOL;
        $this->header .= static::EXPLANATIONS_TAB .  'почему ' . ($explanationResult->getActualResult() ? '' : 'НЕ ') . $explanationResult->getCondition() . PHP_EOL;
    }

    public function acceptTraverseFromRootExplanationResult(TraverseFromRootExplanationResult $explanationResult) : void
    {
        $this->traverseFromRootResult = static::EXPLANATIONS_DELIMITER;
        $this->traverseFromRootResult .= 'ПРОВЕРЯЕМ ЦЕПОЧКУ:' . PHP_EOL;

        $messages = [];

        $pageObjectChecks = $explanationResult->getPageObjectChecks();

        foreach ($explanationResult->getPageObjectOrder() as $index => $className)
        {
            $pageObjectCheck = $pageObjectChecks[$className];

            $message = '';
            $message .= static::EXPLANATIONS_TAB . $pageObjectCheck['name'] . PHP_EOL;
            $message .= static::EXPLANATIONS_TAB . static::EXPLANATIONS_TAB . 'класс: ' . $pageObjectCheck['class'] . PHP_EOL;
            $message .= static::EXPLANATIONS_TAB . static::EXPLANATIONS_TAB . 'локатор: ' . $pageObjectCheck['locator'] . PHP_EOL;

            $checkResults = [];

            foreach ($pageObjectCheck['results'] as $pageObjectCheckResult)
            {
                $checkName = $pageObjectCheckResult['checkName'];
                $checkResult = $pageObjectCheckResult['checkResult'];

                $checkResults[] = '[' . $checkName . ' ' . ($checkResult ? '✓' : '⦻') . ']';
            }

            $message .= static::EXPLANATIONS_TAB . implode(' ', $checkResults) . PHP_EOL;

            $messages[] = $message;
        }

        $this->traverseFromRootResult .= implode(static::EXPLANATIONS_TAB . '^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^' . PHP_EOL, $messages);

        if ($explanationResult->isProblemNotFound())
        {
            $this->traverseFromRootResult .= static::EXPLANATIONS_DELIMITER;
            $this->traverseFromRootResult .= 'ПРОБЛЕМА НЕ ВЫЯВЛЕНА! ' . PHP_EOL;
            $this->traverseFromRootResult .= '                            возможно в тесте отсутствует умное ожидание перед операцией' . PHP_EOL;
        }
    }

    public function acceptDefaultExplanationResult(DefaultExplanationResult $explanationResult) : void
    {
        $this->defaultResult = static::EXPLANATIONS_DELIMITER;
        $this->defaultResult .= 'АКТУАЛЬНОЕ ЗНАЧЕНИЕ:' . PHP_EOL;

        $expectedValue = $explanationResult->getExpectedValue();
        $actualValue = $explanationResult->getActualValue();

        if ($expectedValue instanceof MissingValue && $actualValue instanceof MissingValue)
        {
            $this->defaultResult .= static::EXPLANATIONS_TAB . ' - для работы дефолтной диагностики у условия должны быть public поля: expected - для ожидаемого значения и actual - для актуального значения' . PHP_EOL;
            return;
        }

        if ($actualValue === '' || $actualValue === [])
        {
            $this->defaultResult .= static::EXPLANATIONS_TAB . '- отсутствует!' . PHP_EOL;
            return;
        }

        if (!is_array($actualValue) && !$actualValue instanceof \Traversable)
        {
            $this->defaultResult .= static::EXPLANATIONS_TAB . json_encode($actualValue) . PHP_EOL;
            return;
        }

        foreach ($actualValue as $key => $value)
        {
            $this->defaultResult .= static::EXPLANATIONS_TAB . json_encode($key) . ': ' . json_encode($value) . PHP_EOL;
        }
    }

    public function getMessage() : string
    {
        if (!empty($this->message))
        {
            return $this->message;
        }

        $this->message = $this->header;

        $this->message .= $this->traverseFromRootResult;

        $this->message .= $this->defaultResult;

        return $this->message;
    }
}