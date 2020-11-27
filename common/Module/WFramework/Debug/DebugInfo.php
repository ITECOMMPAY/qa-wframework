<?php


namespace Common\Module\WFramework\Debug;

use Common\Module\WFramework\WebObjects\Base\Interfaces\IPageObject;

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
