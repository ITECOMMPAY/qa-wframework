<?php


namespace Codeception\Lib\WFramework\Explanations\Formatter;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Explanations\Result\AbstractExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\DefaultExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\ExplanationResultAggregate;
use Codeception\Lib\WFramework\Explanations\Result\ImagickExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\TextExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\MissingValue;
use Codeception\Lib\WFramework\Explanations\Result\TraverseFromRootExplanationResult;
use Codeception\Lib\WFramework\Logger\WLogger;

class DefaultExplanationResultFormatter extends AbstractExplanationResultVisitor
{
    public const EXPLANATIONS_DELIMITER = '=======================================================================================' . PHP_EOL;

    public const EXPLANATIONS_TAB = '    ';

    protected $result = null;

    protected $header = '';

    protected $textResultArray = [];

    protected $traverseFromRootResultArray = [];

    protected $defaultResult = '';

    protected $imagickResultArray = [];



    public function acceptExplanationResultAggregate(ExplanationResultAggregate $explanationResult) : void
    {
        if (empty($this->header))
        {
            $this->header = PHP_EOL . PHP_EOL . static::EXPLANATIONS_DELIMITER;
            $this->header .= 'ДИАГНОСТИРУЕМ ЭЛЕМЕНТ:' . PHP_EOL;
            $this->header .= static::EXPLANATIONS_TAB . $explanationResult->getPageObject() . PHP_EOL;
            $this->header .= 'ВОПРОС:' . PHP_EOL;
            $this->header .= static::EXPLANATIONS_TAB .
                             'почему ' .
                             ($explanationResult->getActualResult() ? '' : 'НЕ ') .
                             $explanationResult->getCondition() .
                             PHP_EOL;
        }

        /** @var AbstractExplanationResult $result */
        foreach ($explanationResult->traverseDepthFirst(true) as $result)
        {
            $result->accept($this);
        }
    }

    public function acceptTextExplanationResult(TextExplanationResult $explanationResult) : void
    {
        $this->textResultArray [] = $explanationResult->getText();
    }

    protected function printTextExplanationResults() : string
    {
        $result = static::EXPLANATIONS_DELIMITER;

        return $result . implode(static::EXPLANATIONS_DELIMITER, $this->textResultArray);
    }

    public function acceptTraverseFromRootExplanationResult(TraverseFromRootExplanationResult $explanationResult) : void
    {
        $this->traverseFromRootResultArray []= $explanationResult;
    }

    protected function printTraverseFromRootExplanationResult() : string
    {
        if (empty($this->traverseFromRootResultArray))
        {
            return '';
        }

        $aggregatePageObjectChecks = function ()
        {
            $result = [];

            $existCheckName = (new Exist())->getName();

            $pageObjectChecks = array_map(
                function (TraverseFromRootExplanationResult $v)
                {
                    return $v->getPageObjectChecks();
                },
                $this->traverseFromRootResultArray
            );

            foreach ($pageObjectChecks as $pageObjectCheck)
            {
                foreach ($pageObjectCheck as $class => $check)
                {
                    if (!isset($result[$class]))
                    {
                        $result[$class] = $check;
                        continue;
                    }

                    $checkResults = array_filter(
                        $check['results'],
                        function ($v) use ($existCheckName)
                        {
                            return $v['checkName'] !== $existCheckName;
                        }
                    );

                    array_push($result[$class]['results'], ...$checkResults);
                }
            }

            return $result;
        };

        $aggregatePageObjectOrder = function ()
        {
            $pageObjectOrders = array_map(
                function (TraverseFromRootExplanationResult $v)
                {
                    return $v->getPageObjectOrder();
                },
                $this->traverseFromRootResultArray
            );

            $result = array_pop($pageObjectOrders);

            foreach ($pageObjectOrders as $pageObjectOrder)
            {
                if (count($pageObjectOrder) < count($result))
                {
                    $result = $pageObjectOrder;
                }
            }

            return $result;
        };

        $aggregateProblemNotFound = function ()
        {
            $notFound = array_map(
                function (TraverseFromRootExplanationResult $v)
                {
                    return $v->isProblemNotFound();
                },
                $this->traverseFromRootResultArray
            );

            return !in_array(false, $notFound, true);
        };

        $pageObjectChecks = $aggregatePageObjectChecks();
        $pageObjectOrder  = $aggregatePageObjectOrder();
        $problemNotFound  = $aggregateProblemNotFound();

        $result = static::EXPLANATIONS_DELIMITER;
        $result .= 'ПРОВЕРЯЕМ ЦЕПОЧКУ:' . PHP_EOL;

        $messages = [];

        foreach ($pageObjectOrder as $index => $className)
        {
            $pageObjectCheck = $pageObjectChecks[$className];

            $message = '';
            $message .= static::EXPLANATIONS_TAB . $pageObjectCheck['name'] . PHP_EOL;
            $message .= static::EXPLANATIONS_TAB .
                        static::EXPLANATIONS_TAB .
                        'класс: ' .
                        $pageObjectCheck['class'] .
                        PHP_EOL;
            $message .= static::EXPLANATIONS_TAB .
                        static::EXPLANATIONS_TAB .
                        'локатор: ' .
                        $pageObjectCheck['locator'] .
                        PHP_EOL;

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

        $result .= implode(
            static::EXPLANATIONS_TAB . '^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^' . PHP_EOL,
            $messages
        );

        if ($problemNotFound)
        {
            $result .= static::EXPLANATIONS_DELIMITER;
            $result .= 'ПРОБЛЕМА НЕ ВЫЯВЛЕНА! ' . PHP_EOL;
            $result .= '                            возможно в тесте отсутствует умное ожидание перед операцией' .
                       PHP_EOL;
        }

        return $result;
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

    protected function printDefaultExplanationResult() : string
    {
        return $this->defaultResult;
    }

    public function acceptImagickExplanationResult(ImagickExplanationResult $result) : void
    {
        $this->imagickResultArray []= $result;
    }

    protected function printImagickExplanationResult() : string
    {
        $result = '';

        if (empty($this->imagickResultArray))
        {
            return $result;
        }

        /** @var ImagickExplanationResult $imagickResult */
        foreach ($this->imagickResultArray as $imagickResult)
        {
            $text = $imagickResult->getText();

            if (empty($text))
            {
                continue;
            }

            $result .= static::EXPLANATIONS_DELIMITER . $text;
        }

        return $result;
    }

    protected function constructMessage() : string
    {
        $result = $this->header;

        $result .= $this->printTraverseFromRootExplanationResult();

        $result .= $this->printDefaultExplanationResult();

        $result .= $this->printTextExplanationResults();

        $result .= $this->printImagickExplanationResult();

        $result .= PHP_EOL;

        return $result;
    }

    protected function constructScreenshot() : string
    {
        $backgroundToLayer = [];

        if (empty($this->imagickResultArray))
        {
            return '';
        }

        /** @var ImagickExplanationResult $imagickResult */
        foreach ($this->imagickResultArray as $imagickResult)
        {
            $backgroundToLayer[$imagickResult->getBackground()] [] = $imagickResult->getExplanationLayer();
        }

        if (count($backgroundToLayer) > 1)
        {
            WLogger::logWarning($this, "Вьюпорт сдвинулся при создании Explanations - в лог будет выведен скриншот только для одного из них");
        }

        $explanationLayers = reset($backgroundToLayer);
        $screenshot = key($backgroundToLayer);

        $imagick = new \Imagick();
        $imagick->readImageBlob($screenshot);

        foreach ($explanationLayers as $layer)
        {
            if ($layer === null)
            {
                continue;
            }

            $imagick->addImage($layer);
        }

        $imagick = $imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);

        return $imagick->getImageBlob();
    }

    public function getResult() : Why
    {
        if ($this->result !== null)
        {
            return $this->result;
        }

        $this->result = new Why($this->constructMessage(), $this->constructScreenshot());

        return $this->result;
    }
}