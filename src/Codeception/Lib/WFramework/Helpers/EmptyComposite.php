<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.02.19
 * Time: 15:32
 */

namespace Codeception\Lib\WFramework\Helpers;

use Codeception\Lib\WFramework\Helpers\Composite;

/**
 * Это - пустой узел Composite.
 *
 * Он не имеет детей. Его родителем является он сам.
 *
 * Он имеет единственный экземпляр, который вызывается с помощью метода ::get()
 *
 * Он нужен чтобы не засорять код проверками на null.
 *
 * @package Common\Module\WFramework\WebObjects\Base\EmptyObjects
 */
class EmptyComposite extends Composite
{
    protected function initName() : string
    {
        return 'Пустой узел Composite';
    }

    public function setParent(Composite $parent)
    {

    }

    protected function addChild(Composite $child)
    {

    }

    // Здесь начинается стандартный код синглтона

    private static $instances = array();

    private function __clone(){}

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton!');
    }

    public static function get() : EmptyComposite
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class]))
        {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    // Здесь кончается стандартный код синглтона

    public function __construct()
    {
        //здесь нельзя вызывать родительский конструктор
        $this->setParent($this);
    }
}
