<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator;


use Codeception\Util\Template;
use Codeception\Lib\WFramework\Generator\IGenerator;

class OperationSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'

    /**
     {{doc}}
     */
    public function {{operation_name}}({{params}}){{return_type}}
    {
        {{return}}$this->getFacade()->getPageObject()->accept(new \{{operation_class_full}}({{args}}));{{return_this}}
    }
EOF;

    protected const PARAM_TEMPLATE = '{{type}}${{name}}{{default_value}}';

    /** @var string */
    protected $operationClassFull;

    /** @var string */
    protected $operationName;

    /** @var \ReflectionClass */
    protected $reflectionClass;

    /** @var \ReflectionMethod */
    protected $reflectionMethod;

    public function __construct(string $operationName, string $operationClassFull, \ReflectionClass $reflectionClass, \ReflectionMethod $reflectionMethod)
    {
        $this->operationName = $operationName;
        $this->operationClassFull = $operationClassFull;
        $this->reflectionClass = $reflectionClass;
        $this->reflectionMethod = $reflectionMethod;
    }

    public function produce() : string
    {
        $params = $this->getParams();
        $args = $this->getArgs();
        $returnTypeHint = $this->getReturnTypeHint();
        $doc = $this->getDoc();

        $hasReturnType = $this->hasReturnTypeAnnotation($doc) || $returnTypeHint !== '';

        if (!$hasReturnType)
        {
            $doc = empty($doc) ? $doc : $doc . PHP_EOL . '     ';
            $doc .= '* @return $this';
        }

        return (new Template(static::TEMPLATE))
                        ->place('doc', empty($doc) ? '*' : $doc)
                        ->place('operation_name', $this->operationName)
                        ->place('params', $params)
                        ->place('args', $args)
                        ->place('return_type', empty($returnTypeHint) ? '' : ": $returnTypeHint")
                        ->place('return', $hasReturnType ? 'return ' : '')
                        ->place('operation_class_full', $this->operationClassFull)
                        ->place('return_this', $hasReturnType ? '' : PHP_EOL . '        return $this;')
                        ->produce();
    }

    protected function getParams() : string
    {
        $constructor = $this->reflectionClass->getConstructor();

        if ($constructor === null)
        {
            return '';
        }

        $result = [];

        foreach ($constructor->getParameters() as $param)
        {
            $type = $param->getType() === null ? '' : $this->getTypeName($param->getType()) . ' ';

            $name = $param->getName();

            $defaultValue = '';

            if ($param->isDefaultValueAvailable())
            {
                if (is_array($param->getDefaultValue()))
                {
                    echo PHP_EOL;
                    echo $this->operationClassFull;

                    echo PHP_EOL;
                    echo var_dump($param->getDefaultValue());
                }

                $defaultValue = ' = ' . $param->getDefaultValue();
            }

            $result[] = (new Template(static::PARAM_TEMPLATE))
                                        ->place('type', $type)
                                        ->place('name', $name)
                                        ->place('default_value', $defaultValue)
                                        ->produce();
        }

        return implode(', ', $result);
    }

    protected function getArgs() : string
    {
        $constructor = $this->reflectionClass->getConstructor();

        if ($constructor === null)
        {
            return '';
        }

        $result = [];

        foreach ($constructor->getParameters() as $param)
        {
            $name = $param->getName();

            $result[] = "$$name";
        }

        return implode(', ', $result);
    }

    protected function getReturnTypeHint() : string
    {
        $returnType = $this->reflectionMethod->getReturnType();

        if ($returnType === null)
        {
            return '';
        }

        return $this->getTypeName($returnType);
    }

    protected function getTypeName(\ReflectionType $type) : string
    {
        $typeName = $type->getName();

        return sprintf(
            '%s%s%s',
            $type->allowsNull() ? '?' : '',
            $type->isBuiltin() ? '' : '\\',
            $typeName
        );
    }

    protected function getDoc()
    {
        $constructorDoc = $this->trimDoc($this->getConstructorDoc($this->reflectionClass));

        $methodDoc = $this->trimDoc($this->getMethodDoc($this->reflectionClass, $this->reflectionMethod));

        $methodDocLines = explode(PHP_EOL, $methodDoc);

        $methodDocFilteredLines = [];

        foreach ($methodDocLines as $line)
        {
            if (strpos($line, '@param ') !== false)
            {
                continue;
            }

            $methodDocFilteredLines[] = $line;
        }

        $methodDoc = implode(PHP_EOL, $methodDocFilteredLines);

        $result = [];

        if (!empty($constructorDoc))
        {
            $result[] = $constructorDoc;
        }

        if (!empty($methodDoc))
        {
            $result[] = $methodDoc;
        }

        return implode(PHP_EOL, $result);
    }

    protected function trimDoc(string $doc) : string
    {
        $doc = str_replace('/**', '', $doc);
        $doc = trim(str_replace('*/', '', $doc));

        return $doc;
    }

    protected function getConstructorDoc(\ReflectionClass $class) : string
    {
        $result = '';

        $constructor = $class->getConstructor();

        if ($constructor !== null)
        {
            $result = $constructor->getDocComment() !== false ? $constructor->getDocComment() : '';
        }

        if ($result === '' &&
            $class->getParentClass() !== false &&
            !$class->getParentClass()->isAbstract())
        {
            return $this->getConstructorDoc($class->getParentClass());
        }

        return $result;
    }

    protected function getMethodDoc(\ReflectionClass $class, \ReflectionMethod $refMethod) : string
    {
        $result = $refMethod->getDocComment() !== false ? $refMethod->getDocComment() : '';

        if ($result === '' &&
            $class->getParentClass() !== false &&
            !$class->getParentClass()->isAbstract() &&
            $class->getParentClass()->hasMethod($refMethod->name))
        {
            return $this->getMethodDoc($class->getParentClass(), $class->getParentClass()->getMethod($refMethod->name));
        }

        return $result;
    }

    protected function hasReturnTypeAnnotation(string $doc) : bool
    {
        return strpos($doc, '@return ') !== false;
    }
}
