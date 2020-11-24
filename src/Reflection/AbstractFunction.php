<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server\Reflection;

use Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use Laminas\Code\Generator\DocBlock\Tag\ReturnTag;
use Laminas\Code\Reflection\DocBlockReflection;
use ReflectionClass as PhpReflectionClass;
use ReflectionException;
use ReflectionFunction as PhpReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod as PhpReflectionMethod;

use function array_merge;
use function array_shift;
use function array_unshift;
use function call_user_func_array;
use function count;
use function method_exists;
use function preg_match;

/**
 * Function/Method Reflection
 *
 * Decorates a ReflectionFunction. Allows setting and retrieving an alternate
 * 'service' name (i.e., the name to be used when calling via a service),
 * setting and retrieving the description (originally set using the docblock
 * contents), retrieving the callback and callback type, retrieving additional
 * method invocation arguments, and retrieving the
 * method {@link \Laminas\Server\Reflection\Prototype prototypes}.
 */
abstract class AbstractFunction
{
    /** @var ReflectionFunctionAbstract */
    protected $reflection;

    /**
     * Additional arguments to pass to method on invocation
     *
     * @var array
     */
    protected $argv = [];

    /**
     * Used to store extra configuration for the method (typically done by the
     * server class, e.g., to indicate whether or not to instantiate a class).
     * Associative array; access is as properties via {@link __get()} and
     * {@link __set()}
     *
     * @var array
     */
    protected $config = [];

    /**
     * Declaring class (needed for when serialization occurs)
     *
     * @var string
     */
    protected $class;

    /**
     * Function name (needed for serialization)
     *
     * @var string
     */
    protected $name;

    /**
     * Function/method description
     *
     * @var string
     */
    protected $description = '';

    /**
     * Namespace with which to prefix function/method name
     *
     * @var null|string
     */
    protected $namespace;

    /** @var Prototype[] */
    protected $prototypes = [];

    /** @var string */
    protected $docComment = '';

    protected $return;
    protected $returnDesc;

    /**
     * @var null|string[]
     * @psalm-var null|array<array-key, string>
     */
    protected $paramDesc;
    protected $sigParams;
    protected $sigParamsDepth;

    /**
     * Constructor
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function __construct(ReflectionFunctionAbstract $r, ?string $namespace = null, array $argv = [])
    {
        $this->reflection = $r;

        // Determine namespace
        if (null !== $namespace) {
            $this->setNamespace($namespace);
        }

        // Determine arguments
        $this->argv = $argv;

        // If method call, need to store some info on the class
        if ($r instanceof PhpReflectionMethod) {
            $this->class = $r->getDeclaringClass()->getName();
        }

        $this->name = $r->getName();

        // Perform some introspection
        $this->reflect();
    }

    /**
     * Create signature node tree
     *
     * Recursive method to build the signature node tree. Increments through
     * each array in {@link $sigParams}, adding every value of the next level
     * to the current value (unless the current value is null).
     */
    protected function addTree(Node $parent, int $level = 0): void
    {
        if ($level >= $this->sigParamsDepth) {
            return;
        }

        foreach ($this->sigParams[$level] as $value) {
            $node = new Node($value, $parent);
            if ((null !== $value) && ($this->sigParamsDepth > $level + 1)) {
                $this->addTree($node, $level + 1);
            }
        }
    }

    /**
     * Build the signature tree
     *
     * Builds a signature tree starting at the return values and descending
     * through each method argument. Returns an array of
     * {@link \Laminas\Server\Reflection\Node}s.
     */
    protected function buildTree(): array
    {
        $returnTree = [];
        foreach ($this->return as $value) {
            $node = new Node($value);
            $this->addTree($node);
            $returnTree[] = $node;
        }

        return $returnTree;
    }

    /**
     * Build method signatures
     *
     * Builds method signatures using the array of return types and the array of
     * parameters types
     *
     * @param array  $return Array of return types
     * @param string $returnDesc Return value description
     * @param array  $paramTypes Array of arguments (each an array of types)
     * @param array  $paramDesc Array of parameter descriptions
     * @psalm-param array<array-key, string> $paramDesc
     */
    protected function buildSignatures(array $return, string $returnDesc, array $paramTypes, array $paramDesc): void
    {
        $this->return         = $return;
        $this->returnDesc     = $returnDesc;
        $this->paramDesc      = $paramDesc;
        $this->sigParams      = $paramTypes;
        $this->sigParamsDepth = count($paramTypes);
        $signatureTrees       = $this->buildTree();
        $signatures           = [];

        $endPoints = [];
        foreach ($signatureTrees as $root) {
            $tmp = $root->getEndPoints();
            if (empty($tmp)) {
                $endPoints = array_merge($endPoints, [$root]);
            } else {
                $endPoints = array_merge($endPoints, $tmp);
            }
        }

        foreach ($endPoints as $node) {
            if (! $node instanceof Node) {
                continue;
            }

            $signature = [];
            do {
                array_unshift($signature, $node->getValue());
                $node = $node->getParent();
            } while ($node instanceof Node);

            $signatures[] = $signature;
        }

        // Build prototypes
        $params = $this->reflection->getParameters();
        foreach ($signatures as $signature) {
            $return = new ReflectionReturnValue(array_shift($signature), $this->returnDesc);
            $tmp    = [];
            foreach ($signature as $key => $type) {
                $param = new ReflectionParameter(
                    $params[$key],
                    $type,
                    $this->paramDesc[$key] ?? null
                );
                $param->setPosition($key);
                $tmp[] = $param;
            }

            $this->prototypes[] = new Prototype($return, $tmp);
        }
    }

    /**
     * Use code reflection to create method signatures
     *
     * Determines the method help/description text from the function DocBlock
     * comment. Determines method signatures using a combination of
     * ReflectionFunction and parsing of DocBlock @param and @return values.
     *
     * @throws Exception\RuntimeException
     */
    protected function reflect(): void
    {
        $function   = $this->reflection;
        $paramCount = $function->getNumberOfParameters();
        $parameters = $function->getParameters();

        if (empty($this->docComment)) {
            $this->docComment = $function->getDocComment();
        }

        $scanner  = new DocBlockReflection($this->docComment ?: '/***/');
        $helpText = $scanner->getLongDescription();
        /** @var ParamTag[] $paramTags */
        $paramTags = $scanner->getTags('param');
        /** @var ReturnTag|bool $returnTag */
        /** @psalm-var ReturnTag|false $returnTag */
        $returnTag = $scanner->getTag('return');

        if (empty($helpText)) {
            $helpText = $scanner->getShortDescription();
            if (empty($helpText)) {
                $helpText = $function->getName();
            }
        }
        $this->setDescription($helpText);

        if ($returnTag) {
            $return     = [];
            $returnDesc = (string) $returnTag->getDescription();
            foreach ($returnTag->getTypes() as $type) {
                $return[] = $type;
            }
        } else {
            $return     = ['void'];
            $returnDesc = '';
        }

        $paramTypesTmp = [];
        $paramDesc     = [];
        if (empty($paramTags)) {
            foreach ($parameters as $param) {
                $paramTypesTmp[] = [$param->isArray() ? 'array' : 'mixed'];
                $paramDesc[]     = '';
            }
        } else {
            $paramDesc = [];
            foreach ($paramTags as $paramTag) {
                $paramTypesTmp[] = $paramTag->getTypes();
                $paramDesc[]     = $paramTag->getDescription() ? : '';
            }
        }

        // Get all param types as arrays
        $nParamTypesTmp = count($paramTypesTmp);
        if ($nParamTypesTmp < $paramCount) {
            $start = $paramCount - $nParamTypesTmp;
            for ($i = $start; $i < $paramCount; ++$i) {
                $paramTypesTmp[$i] = ['mixed'];
                $paramDesc[$i]     = '';
            }
        } elseif ($nParamTypesTmp !== $paramCount) {
            throw new Exception\RuntimeException(
                'Variable number of arguments is not supported for services (except optional parameters). '
                . 'Number of function arguments must correspond to actual number of arguments described in a docblock.'
            );
        }

        $paramTypes = [];
        foreach ($paramTypesTmp as $i => $param) {
            if ($parameters[$i]->isOptional()) {
                array_unshift($param, null);
            }
            $paramTypes[] = $param;
        }

        $this->buildSignatures($return, $returnDesc, $paramTypes, $paramDesc);
    }

    /**
     * Proxy reflection calls
     *
     * @throws Exception\BadMethodCallException
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (method_exists($this->reflection, $method)) {
            return call_user_func_array([$this->reflection, $method], $args);
        }

        throw new Exception\BadMethodCallException('Invalid reflection method ("' . $method . '")');
    }

    /**
     * Retrieve configuration parameters
     *
     * Values are retrieved by key from {@link $config}. Returns null if no
     * value found.
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
    }

    /**
     * Set configuration parameters
     *
     * Values are stored by $key in {@link $config}.
     *
     * @param mixed $value
     */
    public function __set(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * Set method's namespace
     *
     * @throws Exception\InvalidArgumentException
     */
    public function setNamespace(?string $namespace): void
    {
        if (empty($namespace)) {
            $this->namespace = null;
            return;
        }

        if (! preg_match('/[a-z0-9_\.]+/i', $namespace)) {
            throw new Exception\InvalidArgumentException('Invalid namespace');
        }

        $this->namespace = $namespace;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * Set the description
     *
     * @throws Exception\InvalidArgumentException
     */
    public function setDescription(string $string): void
    {
        $this->description = $string;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Retrieve all prototypes as array of
     * {@link \Laminas\Server\Reflection\Prototype}s
     *
     * @return Prototype[]
     */
    public function getPrototypes(): array
    {
        return $this->prototypes;
    }

    public function getInvokeArguments(): array
    {
        return $this->argv;
    }

    /**
     * @return string[]
     */
    public function __sleep(): array
    {
        $serializable = [];
        foreach ($this as $name => $value) {
            if (
                $value instanceof PhpReflectionFunction
                || $value instanceof PhpReflectionMethod
            ) {
                continue;
            }

            $serializable[] = $name;
        }

        return $serializable;
    }

    /**
     * Wakeup from serialization
     *
     * Reflection needs explicit instantiation to work correctly. Re-instantiate
     * reflection object on wakeup.
     *
     * @throws ReflectionException
     */
    public function __wakeup(): void
    {
        if ($this->reflection instanceof PhpReflectionMethod) {
            $class            = new PhpReflectionClass($this->class);
            $this->reflection = new PhpReflectionMethod($class->newInstance(), $this->name);
        } else {
            $this->reflection = new PhpReflectionFunction($this->name);
        }
    }
}
