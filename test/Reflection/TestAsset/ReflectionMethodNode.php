<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection\TestAsset;

use Laminas\Server\Reflection\Node;

class ReflectionMethodNode extends Node
{
    /**
     * {@inheritdoc}
     */
    public function setParent(Node $node, $new = false): void
    {
        // it doesn`t matter
    }
}
