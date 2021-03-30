<?php


namespace Codeception\Lib\WFramework\Helpers;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Exceptions\VisitorNotImplementedException;

abstract class CompositeVisitor
{
    public function __call(string $name, array $arguments)
    {
        $composite = reset($arguments);

        if (!$composite instanceof Composite)
        {
            throw new UsageException('Первым аргументов визитора должен быть Composite');
        }

        foreach ($this->getParentAcceptMethods($composite) as $methodToCall)
        {
            if (method_exists($this, $methodToCall))
            {
                return $this->$methodToCall($composite);
            }
        }

        $visitingClassFull = $composite->getClass();
        $visitingClassShort = $composite->getClassShort();

        throw new VisitorNotImplementedException( "Визитор: " . static::class . " - не умеет работать с $visitingClassFull. Если это необходимо, реализуйте в визиторе метод 'accept$visitingClassShort' или более общий - '$methodToCall'");
    }

    public function applicable(Composite $composite) : bool
    {
        if (method_exists($this, 'accept' . $composite->getClassShort()))
        {
            return true;
        }

        foreach ($this->getParentAcceptMethods($composite) as $methodToCall)
        {
            if (method_exists($this, $methodToCall))
            {
                return true;
            }
        }

        return false;
    }

    private function getParentAcceptMethods(Composite $composite) : array
    {
        $result = [];

        $parentClasses = class_parents($composite);

        if ($parentClasses === false)
        {
            return [];
        }

        foreach ($parentClasses as $parentClass)
        {
            $result[] = 'accept' . ClassHelper::getShortName($parentClass);

            if ($this->shouldStopAfterClass($parentClass))
            {
                break;
            }
        }

        return $result;
    }

    protected function shouldStopAfterClass(string $fullClassName) : bool
    {
        return Composite::class === $fullClassName;
    }
}