<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.04.19
 * Time: 15:46
 */

namespace Codeception\Lib\WFramework\WebObjects\Primitive;


use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

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
