<?php


namespace Codeception\Lib\WFramework\Debug;

use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class DebugInfo
{
    /** @var IPageObject */
    protected $pageObject;

    /**
     * @return IPageObject
     */
    public function getPageObject() : IPageObject
    {
        return $this->pageObject;
    }

    /**
     * @param IPageObject $pageObject
     * @return DebugInfo
     */
    public function setPageObject(IPageObject $pageObject) : DebugInfo
    {
        $this->pageObject = $pageObject;
        return $this;
    }
}
