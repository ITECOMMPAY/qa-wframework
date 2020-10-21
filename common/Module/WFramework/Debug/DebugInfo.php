<?php


namespace Common\Module\WFramework\Debug;

use Common\Module\WFramework\WebObjects\Base\WPageObject;

class DebugInfo
{
    /** @var WPageObject */
    protected $pageObject;

    /**
     * @return WPageObject
     */
    public function getPageObject() : WPageObject
    {
        return $this->pageObject;
    }

    /**
     * @param WPageObject $pageObject
     * @return DebugInfo
     */
    public function setPageObject(WPageObject $pageObject) : DebugInfo
    {
        $this->pageObject = $pageObject;
        return $this;
    }
}
