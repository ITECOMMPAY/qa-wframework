<?php


namespace Codeception\Lib\WFramework\Debug;


use Codeception\Lib\WFramework\Helpers\Dindent\Exception\DindentException;
use Codeception\Lib\WFramework\Helpers\Dindent\Indenter;
use Codeception\Lib\WFramework\Helpers\EmptyComposite;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use function array_slice;
use function array_unshift;
use function explode;
use function implode;
use function in_array;

class DebugHelper
{
    const EXIST           = 0;
    const NOT_EXIST       = 1;
    const VISIBLE         = 2;
    const HIDDEN          = 3;
    const ENABLED         = 4;
    const DISABLED        = 5;
    const IN_VIEWPORT     = 6;
    const OUT_OF_VIEWPORT = 7;

    protected function getObjectChain(IPageObject $pageObject) : array
    {
        $objectChain = [$pageObject];

        while (!$pageObject->getParent() instanceof EmptyComposite)
        {
            $pageObject = $pageObject->getParent();
            array_unshift($objectChain, $pageObject);
        }

        return $objectChain;
    }

    protected function diagnoseProperties(IPageObject $pageObject, array $properties = []) : array
    {
        $description = '';
        $stopAfter = false;
        $overallResult = true;

        if (in_array(static::EXIST, $properties, true))
        {
            $checkResult = $pageObject->isExist(false);
            $description .= $checkResult ? 'Найден: 🗸 ' : 'Найден: ⦻ ';
            $stopAfter = $checkResult ? $stopAfter : true;
            $overallResult = $overallResult && $checkResult;
        }

        if (in_array(static::NOT_EXIST, $properties, true))
        {
            $checkResult = $pageObject->isNotExist(false);
            $description .= $checkResult ? 'Не найден: 🗸 ' : 'Не найден: ⦻ ';
            $overallResult = $overallResult && $checkResult;
        }

        if (in_array(static::VISIBLE, $properties, true))
        {
            $checkResult = $pageObject->isDisplayed(false);
            $description .= $checkResult ? 'Видимый: 🗸 ' : 'Видимый: ⦻ ';
            $stopAfter = $checkResult ? $stopAfter : true;
            $overallResult = $overallResult && $checkResult;
        }

        if (in_array(static::HIDDEN, $properties, true))
        {
            $checkResult = $pageObject->isHidden(false);
            $description .= $checkResult ? 'Невидимый: 🗸 ' : 'Невидимый: ⦻ ';
            $overallResult = $overallResult && $checkResult;
        }

        if (in_array(static::ENABLED, $properties, true))
        {
            $checkResult = $pageObject->isEnabled(false);
            $description .= $checkResult ? 'Enabled: 🗸 ' : 'Enabled: ⦻ ';
            $stopAfter = $checkResult ? $stopAfter : true;
            $overallResult = $overallResult && $checkResult;
        }

        if (in_array(static::DISABLED, $properties, true))
        {
            $checkResult = $pageObject->isDisabled(false);
            $description .= $checkResult ? 'Disabled: 🗸 ' : 'Disabled: ⦻ ';
            $overallResult = $overallResult && $checkResult;
        }

        return [$description, $stopAfter, $overallResult];
    }

    public function diagnoseLocator(DebugInfo $debugInfo, ...$properties) : string
    {
        WLogger::logWarning('Начинаем дебаг');

        $result = PHP_EOL . '=======================================================================================' . PHP_EOL;
        $result .= 'ДИАГНОСТИРУЕМ ЭЛЕМЕНТ: ' . PHP_EOL;

        $pageObject = $debugInfo->getPageObject();

        $result .= $pageObject . PHP_EOL;

        $result .= 'ПРОБЛЕМА: ' . PHP_EOL;

        $result .= $this->getPropertiesDescription($properties). PHP_EOL;

        $result .= '=======================================================================================' . PHP_EOL;
        $result .= 'ПРОВЕРЯЕМ ЛОКАТОРЫ: ' . PHP_EOL;

        $objectChain = $this->getObjectChain($pageObject);

        $previousObject = null;

        $possibleMissingTimeout = true;

        /** @var IPageObject $object */
        foreach ($objectChain as $object)
        {
            $result .= $object . PHP_EOL;
            $result .= $object->getClass() . PHP_EOL;
            $result .= $object->getLocator()->getValue() . PHP_EOL;

            if ($object->getLocator()->getValue() !== '/html')
            {
                [$diagnoseResult, $stopAfter, $checkSuccessful] = $this->diagnoseProperties($object, $properties);

                $result .= $diagnoseResult . PHP_EOL;
                $possibleMissingTimeout = $possibleMissingTimeout && $checkSuccessful;

                if ($stopAfter)
                {
                    break;
                }
            }

            $previousObject = $object;


            $result .= '^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^' . PHP_EOL;
        }

        if ($previousObject !== null)
        {
            $result .= '=======================================================================================' . PHP_EOL;
            $result .= 'ДЛЯ СПРАВКИ: ' . PHP_EOL;
            $result .= $previousObject . PHP_EOL . 'содержит HTML:' . PHP_EOL . PHP_EOL;
            $innerHtml = $previousObject->returnSeleniumElement()->get()->attribute('innerHTML');

            try
            {
                $innerHtmlIndented = (new Indenter())->indent($innerHtml);
                $lines = explode(PHP_EOL, $innerHtmlIndented);
                $innerHtml = implode(PHP_EOL, array_slice($lines,0,20));
            }
            catch (DindentException $e)
            {

            }

            $result .= $innerHtml . PHP_EOL;
        }

        if ($possibleMissingTimeout)
        {
            $result .= '=======================================================================================' . PHP_EOL;
            $result .= 'ПРОБЛЕМА НЕ ВЫЯВЛЕНА! ' . PHP_EOL;
            $result .= '                            возможно в тесте отсутствует умное ожидание перед операцией' . PHP_EOL;
        }

        return $result;
    }

    protected function getPropertiesDescription($properties) : string
    {
        $result = [];

        foreach ($properties as $property)
        {
            switch ($property)
            {
                case 0:
                    $result[] = 'не существует';
                    break;
                case 1:
                    $result[] = 'существует';
                    break;
                case 2:
                    $result[] = 'невидимый';
                    break;
                case 3:
                    $result[] = 'видимый';
                    break;
                case 4:
                    $result[] = 'disabled';
                    break;
                case 5:
                    $result[] = 'enabled';
                    break;
                case 6:
                    $result[] = 'за пределами вьюпорта';
                    break;
                case 7:
                    $result[] = 'внутри вьюпорта';
                    break;
                default:
                    $result[] = 'иное';
            }
        }

        return implode(', ', $result);
    }
}
