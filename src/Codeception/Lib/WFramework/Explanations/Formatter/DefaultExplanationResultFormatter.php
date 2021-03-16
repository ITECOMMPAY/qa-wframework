<?php


namespace Codeception\Lib\WFramework\Explanations\Formatter;


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

    protected $message = '';

    protected $plainTexts = [];

    protected $screenshot = '';

    protected $header = '';

    protected $traverseFromRootResult = '';

    protected $defaultResult = '';

    protected $imagickResultArray = [];

    protected $result = null;

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

    public function acceptTextExplanationResult(TextExplanationResult $explanationResult)
    {
        $this->plainTexts []= $explanationResult->getText();
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

    public function acceptImagickExplanationResult(ImagickExplanationResult $result) : void
    {
        $this->imagickResultArray []= $result;
    }

    protected function constructMessage() : void
    {
        $this->message = $this->header;

        $this->message .= $this->traverseFromRootResult;

        $this->message .= $this->defaultResult;

        $this->message .= implode(static::EXPLANATIONS_DELIMITER, $this->plainTexts);

        if (!empty($this->imagickResultArray))
        {
            /** @var ImagickExplanationResult $imagickResult */
            foreach ($this->imagickResultArray as $imagickResult)
            {
                $text = $imagickResult->getText();

                if (empty($text))
                {
                    continue;
                }

                $this->message .= static::EXPLANATIONS_DELIMITER . $text;
            }
        }

        $this->message .= PHP_EOL;
    }

    protected function constructScreenshot() : void
    {
        $backgroundToLayer = [];

        if (empty($this->imagickResultArray))
        {
            return;
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

        $this->screenshot = $imagick->getImageBlob();
    }

    public function getResult() : Why
    {
        if ($this->result !== null)
        {
            return $this->result;
        }

        $this->constructMessage();
        $this->constructScreenshot();

        $this->result = new Why($this->message, $this->screenshot);

        return $this->result;
    }
}