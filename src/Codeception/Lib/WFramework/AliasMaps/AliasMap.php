<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 16.04.19
 * Time: 12:07
 */

namespace Codeception\Lib\WFramework\AliasMaps;

use function array_flip;
use Codeception\Lib\WFramework\Exceptions\AliasMap\UsageException;
use function get_called_class;

/**
 * Этот класс описывает маппинг псевдонимов.
 *
 * UI-тестам часто приходится манипулировать строками, которые отображаются на странице.
 *
 * Это могут быть тексты сообщений об ошибках. Пункты выпадающих меню. Определённые значения полей.
 *
 * К сожалению, все эти надписи могут легко измениться по мере развития тестируемой системы. Из-за этого придётся править
 * все тесты, которые их используют. Чтобы избежать данную проблему и существует маппинг псевдонимов.
 * Он содержит внутри себя ассоциативный массив: "псевдоним" => "реальный текст". Псевдоним гуляет по тестам, а там где
 * необходимо проверить реальный текст - он извлекается из маппинга по этому псевдониму.
 *
 * @package Common\Module\WFramework\AliasMap
 */
abstract class AliasMap
{
    protected $map = null;

    protected $reversedMap = null;

    public function __construct()
    {
        $this->map = $this->getMap();
    }

    abstract protected function getMap() : array;

    /**
     * Получает реальное имя по псевдониму
     *
     * @param string $alias
     * @return string
     * @throws UsageException
     */
    public function getValue(string $alias) : string
    {
        if (isset($this->map[$alias]))
        {
            return $this->map[$alias];
        }

        $className = get_called_class();

        throw new UsageException("Словарь псевдонимов '$className' не содержит псевдонима '$alias'");
    }

    /**
     * Получает псевдоним по реальному имени
     *
     * @param string $value
     * @return string
     * @throws UsageException
     */
    public function getAlias(string $value) : string
    {
        $reversedMap = $this->getReversedMap();

        if (isset($reversedMap[$value]))
        {
            return $reversedMap[$value];
        }

        $className = get_called_class();

        throw new UsageException("Словарь псевдонимов '$className' не содержит значения '$value'");
    }

    protected function getReversedMap() : array
    {
        if ($this->reversedMap === null)
        {
            $this->reversedMap = array_flip($this->map);
        }

        return $this->reversedMap;
    }

    /**
     * Содержит ли данный маппинг заданное имя?
     *
     * @param string $value
     * @return bool
     */
    public function hasValue(string $value) : bool
    {
        $reversedMap = $this->getReversedMap();

        return isset($reversedMap[$value]);
    }

    /**
     * Содержит ли данный маппинг заданный псевдоним?
     *
     * @param string $alias
     * @return bool
     */
    public function hasAlias(string $alias) : bool
    {
        return isset($this->map[$alias]);
    }

    public function getAliasesList() : array
    {
        return array_keys($this->map);
    }

    public function getValuesList() : array
    {
        return array_values($this->map);
    }

    public function __toString()
    {
        return (string) json_encode($this->getMap(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
