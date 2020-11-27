<?php


namespace Common\Module\WFramework\WOperations;


use Common\Module\WFramework\Exceptions\Common\UsageException;
use Common\Module\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Common\Module\WFramework\WebObjects\Base\WBlock\WBlock;
use Common\Module\WFramework\WebObjects\Base\WElement\WElement;

abstract class AbstractPageObjectVisitor
{
    /**
     * @param WElement $element
     * @throws UsageException
     * @return mixed
     */
    public function acceptWElement(WElement $element)
    {
        return $this->acceptWPageObject($element);
    }

    /**
     * @param WBlock $block
     * @throws UsageException
     * @return mixed
     */
    public function acceptWBlock(WBlock $block)
    {
        return $this->acceptWPageObject($block);
    }

    /**
     * @param IPageObject $pageObject
     * @throws UsageException
     * @return mixed
     */
    public function acceptWPageObject(IPageObject $pageObject)
    {
        throw new UsageException('Визитор должен содержать хотя бы один из методов: accept[Класс PageObject\'а], acceptWElement, acceptWBlock или acceptWPageObject');
    }

    public function __call(string $name, array $arguments)
    {
        $pageObject = reset($arguments);

        if (!$pageObject instanceof IPageObject)
        {
            throw new UsageException('Первым аргументов визитора должен быть IPageObject');
        }

        if ($pageObject instanceof WElement)
        {
            $methodToCall = 'acceptWElement';
        }
        else
        {
            $methodToCall = 'acceptWBlock';
        }

        return $this->$methodToCall($pageObject);
    }
}
