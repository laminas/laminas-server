<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Server\Reflection;

/**
 * Method Reflection
 */
class ReflectionMethod extends AbstractFunction
{
    /**
     * Doc block inherit tag for search
     */
    const INHERIT_TAG = '{@inheritdoc}';

    /**
     * @var string Parent class name
     */
    protected $class;

    /**
     * @var ReflectionClass|\ReflectionClass Parent class reflection
     */
    protected $classReflection;

    /**
     * Constructor
     *
     * @param ReflectionClass $class
     * @param \ReflectionMethod $r
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
        if (is_array($argv)) {
            $this->argv = $argv;
        }

        // If method call, need to store some info on the class
        $this->class = $class->getName();
        $this->name = $r->getName();

        // Perform some introspection
        $this->reflect();
    }

    /**
     * Return the reflection for the class that defines this method
     *
     * @return ReflectionClass
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
     * {@inheritdoc}
     */
    protected function reflect()
    {
        $docComment = $this->reflection->getDocComment();
        if (strpos($docComment, self::INHERIT_TAG) !== false) {
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
        $docCommentList[] = $this->reflection->getDocComment();

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
     *
     * @return array|null
     */
    private function fetchRecursiveDocBlockFromParent($reflectionClass, $methodName)
    {
        $docComment = [];
        $parentReflectionClass = $reflectionClass->getParentClass();
        if (! $parentReflectionClass) {
            return null;
        }

        if (! $parentReflectionClass->hasMethod($methodName)) {
            return null;
        }

        $methodReflection = $parentReflectionClass->getMethod($methodName);
        $docCommentLast = $methodReflection->getDocComment();
        $docComment[] = $docCommentLast;
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
     * @param  string $docComment
     * @return bool
     */
    private function isInherit($docComment)
    {
        return strpos($docComment, self::INHERIT_TAG) !== false;
    }
}
