<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.09.18
 * Time: 13:21
 */

namespace Codeception\Lib\WFramework\Helpers;

use Ds\Hashable;
use stdClass;

class ModernObject extends stdClass implements Hashable
{
    public function __toString() : string
    {
        return $this->getClass();
    }

    public function getClass() : string
    {
        return static::class;
    }

    public function getClassShort() : string
    {
        return substr(static::class, strrpos(static::class, '\\') + 1);
    }

    /**
     * Produces a scalar value to be used as the object's hash, which determines
     * where it goes in the hash table. While this value does not have to be
     * unique, objects which are equal must have the same hash value.
     *
     * @return mixed
     */
    public function hash()
    {
        return spl_object_hash($this);
    }

    /**
     * Determines if two objects should be considered equal. Both objects will
     * be instances of the same class but may not be the same instance.
     *
     * @param $obj - An instance of the same class to compare to.
     *
     * @return bool
     */
    public function equals($obj) : bool
    {
        return $this === $obj;
    }
}
