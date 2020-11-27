<?php


namespace Common\Module\WFramework\WOperations\Field;


use Common\Module\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Common\Module\WFramework\WOperations\AbstractPageObjectVisitor;

class FieldSet extends AbstractPageObjectVisitor
{
    public function acceptWPageObject(IPageObject $pageObject)
    {
        echo PHP_EOL . 'wop' . PHP_EOL;
    }
}
