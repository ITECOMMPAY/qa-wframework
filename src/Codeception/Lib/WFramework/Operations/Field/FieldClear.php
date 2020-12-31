<?php


namespace Codeception\Lib\WFramework\Operations\Field;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Mouse\MouseClickWithLeftButton;
use Codeception\Lib\WFramework\Operations\Mouse\MouseScrollTo;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Facebook\WebDriver\WebDriverKeys;

class FieldClear extends AbstractOperation
{
    public function getName() : string
    {
        return "очищаем поле ввода";
    }

    /**
     * @var int
     */
    protected $animationTimeout;

    /**
     * Очищает текст элемента (Ctrl+A, Backspace).
     *
     * @param int $animationTimeout - если положительный, то перед вводом текста сначала будет осуществлён клик
     *                                чтобы активировать поле, а затем будет выдержан указанный таймаут в микросекундах
     *                                чтобы дождаться окончания анимации исчезновения плейсхолдера
     */
    public function __construct(int $animationTimeout = 0)
    {
        $this->animationTimeout = $animationTimeout;
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    protected function apply(WPageObject $pageObject)
    {
        if ($this->animationTimeout > 0)
        {
            $pageObject->accept(new MouseScrollTo());
            $pageObject->accept(new MouseClickWithLeftButton());

            usleep($this->animationTimeout);
        }

        $pageObject
            ->returnSeleniumElement()
            ->sendKeys([WebDriverKeys::CONTROL, 'a'])
            ->sendKeys(WebDriverKeys::BACKSPACE)
            ->clear()
            ;
    }
}
