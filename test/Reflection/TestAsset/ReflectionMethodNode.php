<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection\TestAsset;

use Laminas\Server\Reflection\Node;
use Override;

final class ReflectionMethodNode extends Node
{
    /**
     * {@inheritdoc}
     */
    #[Override]
    public function setParent(parent $node, bool $new = false): void
    {
        // it doesn`t matter
    }
}
