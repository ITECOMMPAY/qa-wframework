<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Explanations\Result\ImagickExplanationResult;
use Codeception\Lib\WFramework\Helpers\Rect;
use Codeception\Lib\WFramework\Operations\Get\GetBoundingClientRectVisible;
use Codeception\Lib\WFramework\Operations\Get\GetElementAtPoint;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WSomeElement;

class ElementClickInterceptedExplanation extends AbstractExplanation
{
    public function acceptWElement($element) : ImagickExplanationResult
    {
        return $this->apply($element);
    }

    public function acceptWBlock($block) : ImagickExplanationResult
    {
        return $this->apply($block);
    }

    protected function apply(IPageObject $pageObject) : ImagickExplanationResult
    {
        $message = <<<EOF
На скриншоте отмечены:
    синим   - элемент по которому производился клик
    жёлтым  - точка клика
    красным - элемент который перекрыл синий элемент и получил его клик 
EOF;

        $screenshot = $pageObject->returnSeleniumServer()->takeScreenshot();

        $background = new \Imagick();
        $background->readImageBlob($screenshot);

        $transparentLayer = $this->getTransparentLayer($background);

        /** @var Rect $expectedRect */
        $expectedRect = $pageObject->accept(new GetBoundingClientRectVisible());
        $clickX = $expectedRect->x + floor($expectedRect->width / 2);
        $clickY = $expectedRect->y + floor($expectedRect->height / 2);

        /** @var WSomeElement $overlappingElement */
        $overlappingElement = $pageObject->accept(new GetElementAtPoint(WSomeElement::class, $clickX, $clickY));

        /** @var Rect $overlappingRect */
        $overlappingRect = $overlappingElement->accept(new GetBoundingClientRectVisible());

        $expectedRectDraw = $this->drawRect($expectedRect, 'blue');
        $transparentLayer->drawImage($expectedRectDraw);
        $expectedRectTextSettings = $this->getTextDrawSettings('blue');
        $transparentLayer->annotateImage($expectedRectTextSettings, $expectedRect->left + 5,$expectedRect->top + 17, 0, 'ЭЛЕМЕНТ');

        if (!$expectedRect->equals($overlappingRect))
        {
            $overlappingRectDraw = $this->drawRect($overlappingRect, 'red');
            $transparentLayer->drawImage($overlappingRectDraw);
            $overlappingRectTextSettings = $this->getTextDrawSettings('red');
            $transparentLayer->annotateImage($overlappingRectTextSettings, $overlappingRect->left + 5, $overlappingRect->top + 17, 0, 'ПЕРЕКРЫТИЕ');
        }

        $clickPointDraw = $this->drawPoint($clickX, $clickY, 'yellow');
        $transparentLayer->drawImage($clickPointDraw);
        $clickPointTextSettings = $this->getTextDrawSettings('yellow');
        $transparentLayer->annotateImage($clickPointTextSettings, $clickX + 5,$clickY + 17, 0, 'КЛИК');

        return new ImagickExplanationResult($message, $screenshot, $transparentLayer);
    }

    protected function getTransparentLayer(\Imagick $image) : \Imagick
    {
        $layer = new \Imagick();
        $layer->newImage($image->getImageWidth(), $image->getImageHeight(), new \ImagickPixel('none'));
        $layer->setImageFormat('png');

        return $layer;
    }

    protected function drawRect(Rect $rect, string $color) : \ImagickDraw
    {
        $drawColor = new \ImagickPixel($color);

        $rectDraw = new \ImagickDraw();
        $rectDraw->setStrokeOpacity(1);
        $rectDraw->setStrokeWidth(2);
        $rectDraw->setFillOpacity(0.3);
        $rectDraw->setStrokeColor($drawColor);
        $rectDraw->setFillColor($drawColor);
        $rectDraw->rectangle($rect->left, $rect->top, $rect->right, $rect->bottom);

        return $rectDraw;
    }

    protected function drawPoint(float $clickX, float $clickY, string $color) : \ImagickDraw
    {
        $drawColor = new \ImagickPixel($color);

        $pointDraw = new \ImagickDraw();
        $pointDraw->setStrokeOpacity(1);
        $pointDraw->setStrokeColor($drawColor);
        $pointDraw->setFillColor($drawColor);
        $pointDraw->setStrokeWidth(2);
        $pointDraw->circle($clickX, $clickY, $clickX+2, $clickY+2);

        return $pointDraw;
    }

    protected function getTextDrawSettings(string $color) : \ImagickDraw
    {
        $drawColor = new \ImagickPixel($color);

        $textDraw = new \ImagickDraw();
        $textDraw->setStrokeOpacity(1);
        $textDraw->setStrokeWidth(1);
        $textDraw->setStrokeColor($drawColor);
        $textDraw->setFillColor($drawColor);
        $textDraw->setFontSize(16);

        return $textDraw;
    }
}