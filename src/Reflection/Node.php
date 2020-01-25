<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Server\Reflection;

/**
 * Node Tree class for Laminas\Server reflection operations
 */
class Node
{
    /**
     * @var mixed Node value
     */
    protected $value = null;

    /**
     * @var self[] Array of child nodes
     */
    protected $children = [];

    /**
     * @var null|self Parent node (if any)
     */
    protected $parent = null;

    /**
     * @param mixed $value
     * @param self $parent Optional
     */
    public function __construct($value, self $parent = null)
    {
        $this->value = $value;
        if (null !== $parent) {
            $this->setParent($parent, true);
        }
    }

    /**
     * Set parent node
     *
     * @param  self $node
     * @param  bool $new Whether or not the child node is newly created
     *                   and should always be attached
     * @return void
     */
    public function setParent(self $node, $new = false)
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
     * @return static New child node
     */
    public function createChild($value)
    {
        return new static($value, $this);
    }

    /**
     * Attach a child node
     *
     * @param  self $node
     * @return void
     */
    public function attachChild(self $node)
    {
        $this->children[] = $node;

        if ($node->getParent() !== $this) {
            $node->setParent($this);
        }
    }

    /**
     * Return an array of all child nodes
     *
     * @return self[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Does this node have children?
     *
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * Return the parent node
     *
     * @return null|self
     */
    public function getParent()
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
    public function setValue($value)
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
     * @return self[]
     */
    public function getEndPoints()
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
