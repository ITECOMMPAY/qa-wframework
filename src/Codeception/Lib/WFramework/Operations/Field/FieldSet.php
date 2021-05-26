<?php


namespace Codeception\Lib\WFramework\Operations\Field;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Operations\Mouse\MouseClickWithLeftButton;
use Codeception\Lib\WFramework\Operations\Mouse\MouseScrollTo;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class FieldSet extends AbstractOperation
{
    public function getName() : string
    {
        return "задаём полю ввода текст: $this->value";
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
     * Задаёт текст данного элемента (через sendKeys).
     *
     * Если элемент содержал текст - он будет заменён.
     *
     * @param string $value - новый текст для элемента
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

        $pageObject->accept(new FieldClear($this->animationTimeout));

        $pageObject
            ->returnSeleniumElement()
            ->sendKeys($this->value)
            ;
    }
}
