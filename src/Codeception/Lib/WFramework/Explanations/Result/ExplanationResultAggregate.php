<?php


namespace Codeception\Lib\WFramework\Explanations\Result;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class ExplanationResultAggregate extends AbstractExplanationResult
{
    /**
     * @var IPageObject
     */
    protected $pageObject;
    /**
     * @var AbstractCondition
     */
    protected $condition;
    /**
     * @var bool
     */
    protected $actualResult;

    public function __construct(IPageObject $pageObject, AbstractCondition $condition, bool $actualResult)
    {
        parent::__construct();

        $this->pageObject = $pageObject;
        $this->condition = $condition;
        $this->actualResult = $actualResult;
    }


    public function getPageObject() : IPageObject
    {
        return $this->pageObject;
    }

    public function getCondition() : AbstractCondition
    {
        return $this->condition;
    }

    public function getActualResult() : bool
    {
        return $this->actualResult;
    }
}