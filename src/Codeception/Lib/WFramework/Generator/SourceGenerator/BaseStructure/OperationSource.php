<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\OperationNode;
use Codeception\Util\Template;

class OperationSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'

    /**
     {{doc}}
     */
    public function {{operation_name}}({{params}}){{return_type}}
    {
        {{return}}$this->getFacade()->accept(new \{{operation_class_full}}({{args}}));{{return_this}}
    }
EOF;

    protected const PARAM_TEMPLATE = '{{type}}{{variadic}}${{name}}{{default_value}}';

    protected OperationNode $node;

    public function __construct(OperationNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
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

        $source = (new Template(static::TEMPLATE))
                        ->place('doc',                  empty($doc) ? '*' : $doc)
                        ->place('operation_name',           $this->node->getMethodName())
                        ->place('params',                   $params)
                        ->place('args',                     $args)
                        ->place('return_type',          empty($returnTypeHint) ? '' : ": $returnTypeHint")
                        ->place('return',               $hasReturnType ? 'return ' : '')
                        ->place('operation_class_full',     $this->node->getEntityClassFull())
                        ->place('return_this',          $hasReturnType ? '' : PHP_EOL . '        return $this;')
                        ->produce();

        $this->node->setSource($source);
    }

    protected function getParams() : string
    {
        $constructor = $this->node->getReflectionClass()->getConstructor();

        if ($constructor === null)
        {
            return '';
        }

        $result = [];

        foreach ($constructor->getParameters() as $param)
        {
            $type = $param->getType() === null ? '' : $this->getTypeName($param->getType()) . ' ';

            $name = $param->getName();

            $variadic = $param->isVariadic() ? '...' : '';

            $defaultValue = '';

            if ($param->isDefaultValueAvailable())
            {
                $defaultValue = ' = ' . json_encode($param->getDefaultValue(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            $result[] = (new Template(static::PARAM_TEMPLATE))
                                        ->place('type', $type)
                                        ->place('variadic', $variadic)
                                        ->place('name', $name)
                                        ->place('default_value', $defaultValue)
                                        ->produce();
        }

        return implode(', ', $result);
    }

    protected function getArgs() : string
    {
        $constructor = $this->node->getReflectionClass()->getConstructor();

        if ($constructor === null)
        {
            return '';
        }

        $result = [];

        foreach ($constructor->getParameters() as $param)
        {
            $name = $param->getName();

            $variadic = $param->isVariadic() ? '...' : '';

            $result[] = "$variadic$$name";
        }

        return implode(', ', $result);
    }

    protected function getReturnTypeHint() : string
    {
        $returnType = $this->node->getReflectionMethod()->getReturnType();

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
        $constructorDoc = $this->trimDoc($this->getConstructorDoc($this->node->getReflectionClass()));

        $methodDoc = $this->trimDoc($this->getMethodDoc($this->node->getReflectionClass(), $this->node->getReflectionMethod()));

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
