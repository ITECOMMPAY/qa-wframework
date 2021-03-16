<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Explanations\Result\ImagickExplanationResult;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Get\GetLayoutViewportSize;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Facebook\WebDriver\WebDriverDimension;

class NotLikeBeforeExplanation extends AbstractExplanation
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
    красным - различия между эталоном и текущим видом PageObject'а 
EOF;
        if (!isset($this->condition->diff))
        {
            throw new UsageException('Для использования Explanation: ' . static::class . ' - в Condition: ' .
                                     get_class($this->condition) . ' - необходимо чтобы это Condition имело public свойство "diff"');
        }

        $viewportSize = $pageObject->accept(new GetLayoutViewportSize());

        $diffPic = $this->fitIntoDimensions($this->condition->diff, $viewportSize);

        return new ImagickExplanationResult($message, $diffPic);
    }

    /**
     * Подгоняет картинку под заданное разрешение.
     *
     * Будет создан холст заданного разрешения, залитый чёрным цветом. Картинка будет размещена в центре холста.
     * Если картинка больше чем холст, то она будет отмасштабирована под его размер.
     *
     * @param \Imagick $imagick
     * @param WebDriverDimension $dimensions
     * @return string
     * @throws \ImagickException
     */
    private function fitIntoDimensions(\Imagick $imagick, WebDriverDimension $dimensions) : string
    {
        WLogger::logDebug($this, 'Подгоняем картинку под разрешение, если она в него не вмещается');

        $imageGeometry = $imagick->getImageGeometry();

        if ($imageGeometry['width'] > $dimensions->getWidth() || $imageGeometry['height'] > $dimensions->getHeight())
        {
            $imagick->scaleImage($dimensions->getWidth(), $dimensions->getHeight(), true);
        }

        $canvas = new \Imagick();
        $canvas->newImage($dimensions->getWidth(), $dimensions->getHeight(), 'black', 'PNG32');

        $imageGeometry = $imagick->getImageGeometry();

        $offsetX = (int)($dimensions->getWidth()  / 2) - (int)($imageGeometry['width']  / 2);
        $offsetY = (int)($dimensions->getHeight() / 2) - (int)($imageGeometry['height'] / 2);

        $canvas->compositeImage($imagick, \Imagick::COMPOSITE_OVER, $offsetX, $offsetY);

        return $canvas->getImageBlob();
    }
}