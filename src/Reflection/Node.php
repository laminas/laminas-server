<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Server\Reflection;

/**
 * Node Tree class for Laminas\Server reflection operations
 */
class Node
{
    /**
     * Node value
     * @var mixed
     */
    protected $value = null;

    /**
     * Array of child nodes (if any)
     * @var array
     */
    protected $children = [];

    /**
     * Parent node (if any)
     * @var \Laminas\Server\Reflection\Node
     */
    protected $parent = null;

    /**
     * Constructor
     *
     * @param mixed $value
     * @param \Laminas\Server\Reflection\Node $parent Optional
     * @return $this
     */
    public function __construct($value, Node $parent = null)
    {
        $this->value = $value;
        if (null !== $parent) {
            $this->setParent($parent, true);
        }

        return $this;
    }

    /**
     * Set parent node
     *
     * @param \Laminas\Server\Reflection\Node $node
     * @param  bool $new Whether or not the child node is newly created
     * and should always be attached
     * @return void
     */
    public function setParent(Node $node, $new = false): void
    {
        $this->parent = $node;

        if ($new) {
            $node->attachChild($this);
        }
    }

    /**
     * Create and attach a new child node
     *
     * @param mixed $value
     * @access public
     * @return $this New child node
     */
    public function createChild($value): self
    {
        $child = new static($value, $this);

        return $child;
    }

    /**
     * Attach a child node
     *
     * @param \Laminas\Server\Reflection\Node $node
     * @return void
     */
    public function attachChild(Node $node): void
    {
        $this->children[] = $node;

        if ($node->getParent() !== $this) {
            $node->setParent($this);
        }
    }

    /**
     * Return an array of all child nodes
     *
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Does this node have children?
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    /**
     * Return the parent node
     *
     * @return null|$this
     */
    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * Return the node's current value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the node value
     *
     * @param mixed $value
     * @return void
     */
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
     *
     * @return array
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
