<?php


namespace Codeception\Lib\WFramework\Explanations\Result;


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class TraverseFromRootExplanationResult extends AbstractExplanationResult
{
    protected $pageObjectChecks = [];

    protected $pageObjectOrder = [];

    protected $problemNotFound = false;

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

        $this->pageObjectChecks[$class]['results'][] = ['checkName' => $checkName, 'checkResult' => $checkResult];
    }

    public function setProblemNotFound(bool $problemNotFound) : void
    {
        $this->problemNotFound = $problemNotFound;
    }

    public function getPageObjectChecks() : array
    {
        return $this->pageObjectChecks;
    }

    public function getPageObjectOrder() : array
    {
        return $this->pageObjectOrder;
    }

    public function isProblemNotFound() : bool
    {
        return $this->problemNotFound;
    }
}