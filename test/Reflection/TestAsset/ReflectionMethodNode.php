<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

namespace LaminasTest\Server\Reflection\TestAsset;

use Laminas\Server\Reflection\Node;

class ReflectionMethodNode extends Node
{
    /**
     * {@inheritdoc}
     */
    public function setParent(Node $node, $new = false)
    {
        // it doesn`t matter
    }
}
