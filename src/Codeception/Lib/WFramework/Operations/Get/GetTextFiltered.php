<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetTextFiltered extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем видимый текст, отфильтрованный по регулярке: " . $this->regex;
    }

    /**
     * @var string
     */
    protected $regex;

    /**
     * @var string
     */
    protected $groupName;

    /**
     * Возвращает видимый текст элемента, отфильтрованный по регулярке.
     *
     * @param string $regex - регулярка для фильтрации текста, если имя группы не указано, то результат регулярки должен быть в группе 1
     * @param string $groupName - опциональное имя группы
     */
    public function __construct(string $regex, string $groupName = '')
    {
        $this->regex = $regex;
        $this->groupName = $groupName;
    }

    public function acceptWBlock($block) : string
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : string
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return \Ds\Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : string
    {
        $text = $pageObject->accept(new GetText());

        if (!preg_match($this->regex, $text, $matches))
        {
            WLogger::logWarning($this, "Не найдено ни одного совпадения в тексте '$text' по заданной регулярке '$this->regex'!");
            return '';
        }

        if (!empty($this->groupName) && !isset($matches[$this->groupName]))
        {
            throw new UsageException($this, "В результатах заданной регулярки '$this->regex' нет группы '$this->groupName'");
        }

        $index = empty($this->groupName) ? 1 : $this->groupName;

        return $matches[$index];
    }
}
