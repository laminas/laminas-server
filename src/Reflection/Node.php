<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server\Reflection;

class Node
{
    /** @var mixed */
    protected $value;

    /** @var Node[] */
    protected $children = [];

    /** @var null|Node */
    protected $parent;

    public function __construct($value, ?self $parent = null)
    {
        $this->value = $value;
        if (null !== $parent) {
            $this->setParent($parent, true);
        }
    }

    /**
     * Set parent node
     *
     * @param \Laminas\Server\Reflection\Node $node
     * @param  bool $new Whether or not the child node is newly created
     * and should always be attached
     */
    public function setParent(Node $node, bool $new = false): void
    {
        $this->parent = $node;

        if ($new) {
            $node->attachChild($this);
        }
    }

    public function createChild($value): self
    {
        return new static($value, $this);
    }

    public function attachChild(Node $node): void
    {
        $this->children[] = $node;

        if ($node->getParent() !== $this) {
            $node->setParent($this);
        }
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * Retrieve the bottommost nodes of this node's tree
     *
     * Retrieves the bottommost nodes of the tree by recursively calling
     * getEndPoints() on all children. If a child is null, it returns the parent
     * as an end point.
     */
    public function getEndPoints(): array
    {
        $endPoints = [];
        if (! $this->hasChildren()) {
            return $endPoints;
        }

        foreach ($this->children as $child) {
            $value = $child->getValue();

            if (null === $value) {
                $endPoints[] = $this;
            } elseif ((null !== $value) && $child->hasChildren()) {
                $childEndPoints = $child->getEndPoints();
                if (! empty($childEndPoints)) {
                    $endPoints = array_merge($endPoints, $childEndPoints);
                }
            } elseif ((null !== $value) && ! $child->hasChildren()) {
                $endPoints[] = $child;
            }
        }

        return $endPoints;
    }
}
