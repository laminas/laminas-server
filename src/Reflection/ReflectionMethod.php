<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace Laminas\Server\Reflection;

use function array_map;
use function array_merge;
use function implode;
use function str_contains;
use function str_replace;

use const PHP_EOL;

/**
 * Method Reflection
 *
 * @final This class should not be extended
 */
class ReflectionMethod extends AbstractFunction
{
    /**
     * Doc block inherit tag for search
     */
    public const INHERIT_TAG = '{@inheritdoc}';

    /**
     * Parent class name
     *
     * @var string
     */
    protected $class;

    /**
     * Parent class reflection
     *
     * @var ReflectionClass|\ReflectionClass
     */
    protected $classReflection;

    /**
     * Constructor
     *
     * @param string $namespace
     * @param array $argv
     */
    public function __construct(ReflectionClass $class, \ReflectionMethod $r, $namespace = null, $argv = [])
    {
        $this->classReflection = $class;
        $this->reflection      = $r;

        $classNamespace = $class->getNamespace();

        // Determine namespace
        if (! empty($namespace)) {
            $this->setNamespace($namespace);
        } elseif (! empty($classNamespace)) {
            $this->setNamespace($classNamespace);
        }

        // Determine arguments
        $this->argv = $argv;

        // If method call, need to store some info on the class
        $this->class = $class->getName();
        $this->name  = $r->getName();

        // Perform some introspection
        $this->reflect();
    }

    /**
     * Return the reflection for the class that defines this method
     *
     * @return ReflectionClass|\ReflectionClass
     */
    public function getDeclaringClass()
    {
        return $this->classReflection;
    }

    /**
     * Wakeup from serialization
     *
     * Reflection needs explicit instantiation to work correctly. Re-instantiate
     * reflection object on wakeup.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->classReflection = new ReflectionClass(
            new \ReflectionClass($this->class),
            $this->getNamespace(),
            $this->getInvokeArguments()
        );

        $this->reflection = new \ReflectionMethod($this->classReflection->getName(), $this->name);
    }

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [
            'class'          => $this->class,
            'argv'           => $this->argv,
            'config'         => $this->config,
            'name'           => $this->name,
            'description'    => $this->description,
            'namespace'      => $this->namespace,
            'prototypes'     => $this->prototypes,
            'docComment'     => $this->docComment,
            'return'         => $this->return,
            'returnDesc'     => $this->returnDesc,
            'paramDesc'      => $this->paramDesc,
            'sigParams'      => $this->sigParams,
            'sigParamsDepth' => $this->sigParamsDepth,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        $this->class          = $data['class'];
        $this->argv           = $data['argv'] ?? [];
        $this->config         = $data['config'] ?? [];
        $this->name           = $data['name'];
        $this->description    = $data['description'] ?? '';
        $this->namespace      = $data['namespace'] ?? null;
        $this->prototypes     = $data['prototypes'] ?? [];
        $this->docComment     = $data['docComment'] ?? '';
        $this->return         = $data['return'] ?? [];
        $this->returnDesc     = $data['returnDesc'] ?? null;
        $this->paramDesc      = $data['paramDesc'] ?? [];
        $this->sigParams      = $data['sigParams'] ?? [];
        $this->sigParamsDepth = $data['sigParamsDepth'] ?? 0;

        /** @psalm-var class-string $className */
        $className             = $this->class;
        $this->classReflection = new ReflectionClass(
            new \ReflectionClass($className),
            $this->getNamespace(),
            $this->getInvokeArguments()
        );
        /** @psalm-suppress UndefinedMagicMethod */
        /** @psalm-var class-string $classReflectionName */
        $classReflectionName = $this->classReflection->getName();
        $this->reflection    = new \ReflectionMethod($classReflectionName, $this->name);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function reflect()
    {
        $docComment = $this->reflection->getDocComment();
        if (str_contains($docComment, self::INHERIT_TAG)) {
            $this->docComment = $this->fetchRecursiveDocComment();
        }

        parent::reflect();
    }

    /**
     * Fetch all doc comments for inherit values
     *
     * @return string
     */
    private function fetchRecursiveDocComment()
    {
        $currentMethodName = $this->reflection->getName();
        $docCommentList[]  = $this->reflection->getDocComment();

        // fetch all doc blocks for method from parent classes
        $docCommentFetched = $this->fetchRecursiveDocBlockFromParent($this->classReflection, $currentMethodName);
        if ($docCommentFetched) {
            $docCommentList = array_merge($docCommentList, $docCommentFetched);
        }

        // fetch doc blocks from interfaces
        $interfaceReflectionList = $this->classReflection->getInterfaces();
        foreach ($interfaceReflectionList as $interfaceReflection) {
            if (! $interfaceReflection->hasMethod($currentMethodName)) {
                continue;
            }

            $docCommentList[] = $interfaceReflection->getMethod($currentMethodName)->getDocComment();
        }

        $normalizedDocCommentList = array_map(
            function ($docComment) {
                $docComment = str_replace('/**', '', $docComment);
                $docComment = str_replace('*/', '', $docComment);

                return $docComment;
            },
            $docCommentList
        );

        return '/**' . implode(PHP_EOL, $normalizedDocCommentList) . '*/';
    }

    /**
     * Fetch recursive doc blocks from parent classes
     *
     * @param \ReflectionClass $reflectionClass
     * @param string           $methodName
     * @return array|void
     */
    private function fetchRecursiveDocBlockFromParent($reflectionClass, $methodName)
    {
        $docComment            = [];
        $parentReflectionClass = $reflectionClass->getParentClass();
        if (! $parentReflectionClass) {
            return;
        }

        if (! $parentReflectionClass->hasMethod($methodName)) {
            return;
        }

        $methodReflection = $parentReflectionClass->getMethod($methodName);
        $docCommentLast   = $methodReflection->getDocComment();
        $docComment[]     = $docCommentLast;
        if ($this->isInherit($docCommentLast)) {
            if ($docCommentFetched = $this->fetchRecursiveDocBlockFromParent($parentReflectionClass, $methodName)) {
                $docComment = array_merge($docComment, $docCommentFetched);
            }
        }

        return $docComment;
    }

    /**
     * Return true if doc block inherit from parent or interface
     *
     * @param string $docComment
     * @return bool
     */
    private function isInherit($docComment)
    {
        return str_contains($docComment, self::INHERIT_TAG);
    }
}
