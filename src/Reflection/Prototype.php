<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server\Reflection;

class Prototype
{
    /** @var ReflectionParameter[] */
    protected $params;

    /** @var ReflectionReturnValue */
    private $return;

    /**
     * @param ReflectionParameter[] $params
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(ReflectionReturnValue $return, array $params = [])
    {
        $this->return = $return;

        foreach ($params as $param) {
            if (! $param instanceof ReflectionParameter) {
                throw new Exception\InvalidArgumentException('One or more params are invalid');
            }
        }

        $this->params = $params;
    }

    public function getReturnType(): string
    {
        return $this->return->getType();
    }

    public function getReturnValue(): ReflectionReturnValue
    {
        return $this->return;
    }

    public function getParameters(): array
    {
        return $this->params;
    }
}
