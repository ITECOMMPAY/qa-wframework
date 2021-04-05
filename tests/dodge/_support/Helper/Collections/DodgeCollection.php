<?php


namespace dodge\Helper\Collections;


use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use dodge\DodgeTester;
use dodge\_generated\Collection\Operations;
use dodge\Helper\Elements\DodgeElement;
use Ds\Sequence;

class DodgeCollection extends WCollection
{
    public function returnOperations() : Operations
    {
        return $this->operations ?? $this->operations = new Operations($this);
    }

    //Ниже переопределяем методы Коллекции, чтобы они возвращали классы нашего проекта

    /**
     * Данный метод возвращает массив элементов коллекции
     *
     * Элементы коллекции будут иметь тот же класс, что и веб-элемент из которого она была создана
     *
     * @return DodgeElement[]|Sequence
     */
    public function getElementsArray() : Sequence
    {
        return parent::getElementsArray();
    }

    /**
     * Данный метод возвращает ассоциативный массив элементов коллекции
     *
     * Ключом в массиве будет значение поля или результат вызова метода указанного в $methodOrProperty,
     * а значением - соответствующий элемент.
     *
     * Если $preserveDuplicates = false и несколько элементов имеют одно и то же значение указанного поля, или одинаковый
     * результат вызова метода - то в массив попадёт первый из этих элементов.
     *
     * Если $preserveDuplicates = true - то все элементы с дублирующимися ключами попадут в результирующий массив. Для этого
     * к ключу будет приписан суффикс _0, _1, _2 и т.д. Это будет сделано посредством неэффективного алгоритма так что без
     * нужды эту опцию использовать не стоит.
     *
     * Элементы коллекции будут иметь тот же класс, что и веб-элемент из которого она была создана
     *
     * @param string $methodOrProperty
     * @param bool $preserveDuplicates
     * @return DodgeElement[]
     */
    public function getElementsMap(string $methodOrProperty, bool $preserveDuplicates = false) : array
    {
        return parent::getElementsMap($methodOrProperty, $preserveDuplicates);
    }

    public function getFirstElement() : DodgeElement
    {
        /** @var DodgeElement $element */
        $element = parent::getFirstElement();
        return $element;
    }

    public function getElement(int $index) : DodgeElement
    {
        /** @var DodgeElement $element */
        $element = parent::getElement($index);
        return $element;
    }

    public function getLastElement() : DodgeElement
    {
        /** @var DodgeElement $element */
        $element = parent::getLastElement();
        return $element;
    }

    public function shouldExist(bool $deep = true) : DodgeCollection
    {
        return parent::shouldExist($deep);
    }

    public function shouldNotExist(bool $deep = true) : DodgeCollection
    {
        return parent::shouldNotExist($deep);
    }

    public function shouldBeDisplayed(bool $deep = true) : DodgeCollection
    {
        return parent::shouldBeDisplayed($deep);
    }

    public function shouldBeHidden(bool $deep = true) : DodgeCollection
    {
        return parent::shouldBeHidden($deep);
    }

    public function shouldBeEnabled(bool $deep = true) : DodgeCollection
    {
        return parent::shouldBeEnabled($deep);
    }

    public function shouldBeDisabled(bool $deep = true) : DodgeCollection
    {
        return parent::shouldBeDisabled($deep);
    }

    public function shouldBeInViewport(bool $deep = true) : DodgeCollection
    {
        return parent::shouldBeInViewport($deep);
    }

    public function shouldBeOutOfViewport(bool $deep = true) : DodgeCollection
    {
        return parent::shouldBeOutOfViewport($deep);
    }
}