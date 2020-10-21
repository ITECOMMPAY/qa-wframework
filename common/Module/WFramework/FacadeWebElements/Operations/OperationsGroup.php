<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 28.02.19
 * Time: 17:28
 */

namespace Common\Module\WFramework\FacadeWebElements\Operations;

use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;


abstract class OperationsGroup
{
    protected $facadeWebElements;

    public function __construct(FacadeWebElements $facadeWebElements)
    {
        $this->facadeWebElements = $facadeWebElements;
    }

    public function and() : FacadeWebElements
    {
        return $this->facadeWebElements;
    }

    public function then() : FacadeWebElements
    {
        return $this->facadeWebElements;
    }

}
