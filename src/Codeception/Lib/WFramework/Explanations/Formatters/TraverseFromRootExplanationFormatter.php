<?php


namespace Codeception\Lib\WFramework\Explanations\Formatters;


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class TraverseFromRootExplanationFormatter extends AbstractExplanationFormatter
{
    protected $pageObjectChecks = [];

    protected $pageObjectOrder = [];

    public function addNext(IPageObject $pageObject, string $checkName, bool $checkResult)
    {
        $name = (string) $pageObject;
        $class = $pageObject->getClass();
        $locator = $pageObject->getLocator()->getValue();

        if (!in_array($class, $this->pageObjectOrder, true))
        {
            $this->pageObjectOrder[] = $class;
        }

        if (!isset($this->pageObjectChecks[$class]))
        {
            $this->pageObjectChecks[$class] = [
                'name' => $name,
                'class' => $class,
                'locator' => $locator,
                'results' => []
            ];
        }

        $this->pageObjectChecks[$class]['results'][] = '[' . $checkName . ': ' . ($checkResult ? '🗸' : '⦻') . ']';
    }

    protected function format() : string
    {
        $messages = [];

        foreach ($this->pageObjectOrder as $className)
        {
            $pageObjectCheck = $this->pageObjectChecks[$className];

            $message = '';
            $message .= $pageObjectCheck['name'] . PHP_EOL;
            $message .= $pageObjectCheck['class'] . PHP_EOL;
            $message .= $pageObjectCheck['locator'] . PHP_EOL;
            $message .= implode(' ', $pageObjectCheck['results']) . PHP_EOL;

            $messages[] = $message;
        }

        return implode('^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^' . PHP_EOL, $messages);
    }
}