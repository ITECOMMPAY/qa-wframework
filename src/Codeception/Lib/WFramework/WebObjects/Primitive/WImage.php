<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 22.04.19
 * Time: 13:19
 */

namespace Codeception\Lib\WFramework\WebObjects\Primitive;


use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

class WImage extends WElement
{
    protected function initTypeName() : string
    {
        return 'Картинка';
    }
}
