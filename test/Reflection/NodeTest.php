<?php

/**
 * @see       https://github.com/laminas/laminas-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Server\Reflection;

use Laminas\Server\Reflection\Node;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testConstructor(): void
    {
        $node = new Node('string');
        $this->assertInstanceOf(Node::class, $node);
        $this->assertEquals('string', $node->getValue());
        $this->assertNull($node->getParent());
        $children = $node->getChildren();
        $this->assertEmpty($children);

        $child = new Node('array', $node);
        $this->assertInstanceOf(Node::class, $child);
        $this->assertEquals('array', $child->getValue());
        $this->assertEquals($node, $child->getParent());
        $children = $child->getChildren();
        $this->assertEmpty($children);

        $children = $node->getChildren();
        $this->assertEquals($child, $children[0]);
    }

    public function testSetParent(): void
    {
        $parent = new Node('string');
        $child  = new Node('array');

        $child->setParent($parent);

        $this->assertEquals($parent, $child->getParent());
    }

    public function testCreateChild(): void
    {
        $parent = new Node('string');
        $child  = $parent->createChild('array');

        $this->assertEquals($parent, $child->getParent());
        $children = $parent->getChildren();
        $this->assertEquals($child, $children[0]);
    }

    public function testAttachChild(): void
    {
        $parent = new Node('string');
        $child  = new Node('array');

        $parent->attachChild($child);
        $this->assertEquals($parent, $child->getParent());
        $children = $parent->getChildren();
        $this->assertEquals($child, $children[0]);
    }

    public function testGetChildren(): void
    {
        $parent = new Node('string');
        $child  = $parent->createChild('array');

        $children = $parent->getChildren();
        $types    = [];
        foreach ($children as $c) {
            $types[] = $c->getValue();
        }
        $this->assertIsArray($children);
        $this->assertCount(1, $children, var_export($types, true));
        $this->assertEquals($child, $children[0]);
    }

    public function testHasChildren(): void
    {
        $parent = new Node('string');

        $this->assertFalse($parent->hasChildren());
        $parent->createChild('array');
        $this->assertTrue($parent->hasChildren());
    }

    public function testGetParent(): void
    {
        $parent = new Node('string');
        $child  = $parent->createChild('array');

        $this->assertNull($parent->getParent());
        $this->assertEquals($parent, $child->getParent());
    }

    public function testGetValue(): void
    {
        $parent = new Node('string');
        $this->assertEquals('string', $parent->getValue());
    }

    public function testSetValue(): void
    {
        $parent = new Node('string');
        $this->assertEquals('string', $parent->getValue());
        $parent->setValue('array');
        $this->assertEquals('array', $parent->getValue());
    }

    public function testGetEndPoints(): void
    {
        $root               = new Node('root');
        $child1             = $root->createChild('child1');
        $child2             = $root->createChild('child2');
        $child1grand1       = $child1->createChild(null);
        $child1grand2       = $child1->createChild('child1grand2');
        $child2grand1       = $child2->createChild('child2grand1');
        $child2grand2       = $child2->createChild('child2grand2');
        $child2grand2great1 = $child2grand2->createChild(null);
        $child2grand2great2 = $child2grand2->createChild('child2grand2great2');

        $endPoints      = $root->getEndPoints();
        $endPointsArray = [];
        foreach ($endPoints as $endPoint) {
            $endPointsArray[] = $endPoint->getValue();
        }

        $test = [
            'child1',
            'child1grand2',
            'child2grand1',
            'child2grand2',
            'child2grand2great2'
        ];

        $this->assertEquals(
            $test,
            $endPointsArray,
            'Test was [' . var_export($test, true) . ']; endPoints were [' . var_export($endPointsArray, true) . ']'
        );
    }
}
