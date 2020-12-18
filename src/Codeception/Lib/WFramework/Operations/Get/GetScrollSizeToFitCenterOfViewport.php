<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;
use Facebook\WebDriver\WebDriverDimension;

class GetScrollSizeToFitCenterOfViewport extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем насколько нужно прокрутить элемент чтобы выровнять его по центру viewport'а";
    }

    /**
     * @var WPageObject
     */
    protected $viewport;

    /**
     * Возвращает насколько нужно прокрутить элемент чтобы выровнять его по центру viewport'а
     */
    public function __construct(WPageObject $viewport)
    {
        $this->viewport = $viewport;
    }

    public function acceptWBlock($block) : WebDriverDimension
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : WebDriverDimension
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : WebDriverDimension
    {
        WLogger::logDebug('Получаем насколько нужно прокрутить элемент чтобы выровнять его по центру viewport\'а');

        $elementScrollSize = $pageObject->accept(new GetScrollSize());
        $viewportScrollSize = $this->viewport->accept(new GetClientSize());

        $inViewportHorizontalOffset = ($viewportScrollSize->getWidth() - $elementScrollSize->getWidth()) / 2;
        $inViewportVerticalOffset = ($viewportScrollSize->getHeight() - $elementScrollSize->getHeight()) / 2;

        if ($inViewportHorizontalOffset < 0 || $inViewportVerticalOffset < 0)
        {
            throw new UsageException('Элемент не вмещается в заданный viewport');
        }

        $elementLocation = $pageObject->accept(new GetLocation());
        $viewportLocation = $this->viewport->accept(new GetLocation());

        $incX = $elementLocation->getX() < $viewportLocation->getX();
        $incY = $elementLocation->getY() < $viewportLocation->getY();

        if ($incX)
        {
            $outViewportHorizontalOffset = $viewportLocation->getX() - $elementLocation->getX();
        }
        else
        {
            $outViewportHorizontalOffset = ($elementLocation->getX() + $elementScrollSize->getWidth()) - ($viewportLocation->getX() + $viewportScrollSize->getWidth());
        }

        if ($incY)
        {
            $outViewportVerticalOffset = $viewportLocation->getY() - $elementLocation->getY();
        }
        else
        {
            $outViewportVerticalOffset = ($elementLocation->getY() + $elementScrollSize->getHeight()) - ($viewportLocation->getY() + $viewportScrollSize->getHeight());
        }

        $horizontalOffset = $outViewportHorizontalOffset + $inViewportHorizontalOffset;
        $verticalOffset = $outViewportVerticalOffset + $inViewportVerticalOffset;

        if ($incX)
        {
            $horizontalOffset = -$horizontalOffset;
        }

        if ($incY)
        {
            $verticalOffset = -$verticalOffset;
        }

        return new WebDriverDimension($horizontalOffset, $verticalOffset);
    }
}
