<?php


namespace Codeception\Lib\WFramework\Operations\Field;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Mouse\MouseClickWithLeftButton;
use Codeception\Lib\WFramework\Operations\Mouse\MouseScrollTo;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Facebook\WebDriver\WebDriverKeys;

class FieldPrepend extends AbstractOperation
{
    public function getName() : string
    {
        return "в начало поля ввода дописываем: $this->value";
    }

    /**
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $animationTimeout;

    /**
     * Добавляет текст в начало имеющегося текста элемента (Ctrl+Home, send keys).
     *
     * @param string $value - текст, который следует добавить
     * @param int $animationTimeout - если положительный, то перед вводом текста сначала будет осуществлён клик
     *                                чтобы активировать поле, а затем будет выдержан указанный таймаут в микросекундах
     *                                чтобы дождаться окончания анимации исчезновения плейсхолдера
     */
    public function __construct(string $value, int $animationTimeout = 0)
    {
        $this->value = $value;
        $this->animationTimeout = $animationTimeout;
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    protected function apply(WPageObject $pageObject)
    {
        $pageObject->should(new Exist());

        if ($this->animationTimeout > 0)
        {
            $pageObject->accept(new MouseScrollTo());
            $pageObject->accept(new MouseClickWithLeftButton());

            usleep($this->animationTimeout);
        }

        $pageObject
            ->returnSeleniumElement()
            ->sendKeys([WebDriverKeys::CONTROL, WebDriverKeys::HOME])
            ->sendKeys($this->value)
            ;
    }
}
