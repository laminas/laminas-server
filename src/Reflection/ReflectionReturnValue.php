<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Server\Reflection;

/**
 * Return value reflection
 *
 * Stores the return value type and description
 */
class ReflectionReturnValue
{
    /**
     * @var null|string Return value type
     */
    protected $type;

    /**
     * @var null|string Return value description
     */
    protected $description;

    /**
     * @param null|string $type Return value type
     * @param null|string $description Return value type
     */
    public function __construct($type = 'mixed', $description = '')
    {
        $this->setType($type);
        $this->setDescription($description);
    }

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param  null|string $type
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setType($type)
    {
        if (! is_string($type) && (null !== $type)) {
            throw new Exception\InvalidArgumentException('Invalid parameter type');
        }

        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param  null|string $description
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setDescription($description)
    {
        if (! is_string($description) && (null !== $description)) {
            throw new Exception\InvalidArgumentException('Invalid parameter description');
        }

        $this->description = $description;
    }
}
