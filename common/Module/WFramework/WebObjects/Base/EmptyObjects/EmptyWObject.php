<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.02.19
 * Time: 15:32
 */

namespace Common\Module\WFramework\WebObjects\Base\EmptyObjects;

use Common\Module\WFramework\WebObjects\Base\WObject;

/**
 * Это - пустой WObject.
 *
 * Он не имеет детей. Его родителем является он сам.
 *
 * Он имеет единственный экземпляр, который вызывается с помощью метода ::get()
 *
 * Он нужен чтобы не засорять код проверками на null.
 *
 * @package Common\Module\WFramework\WebObjects\Base\EmptyObjects
 */
class EmptyWObject extends WObject
{
    protected function initName() : string
    {
        return 'Пустой WObject';
    }

    public function setParent(WObject $parentWObject)
    {

    }

    protected function addChild(WObject $child)
    {

    }

    protected function registerChildrenWObjects()
    {

    }

    // Здесь начинается стандартный код синглтона

    private static $instances = array();

    private function __clone(){}

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton!');
    }

    public static function get() : EmptyWObject
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
