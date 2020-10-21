<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.04.19
 * Time: 15:46
 */

namespace Common\Module\WFramework\WebObjects\Primitive;


use Common\Module\WFramework\WebObjects\Base\WElement\WElement;

/**
 * To Aru Unknown no WElement
 *
 * @package Common\Module\WFramework\WebObjects\Primitive
 */
class WUnknown extends WElement
{
    public function initTypeName() : string
    {
        return 'Некий веб-элемент';
    }
}
