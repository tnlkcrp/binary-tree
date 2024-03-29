<?php

namespace binary;

/**
 * Class Tree
 */
class Tree
{
    /**
     * @var Node
     */
    private $root;

    /**
     * @var array
     */
    private $index;

    /**
     * Tree constructor.
     * @param Node $root
     */
    public function __construct(Node $root)
    {
        $this->root = $root;
    }

    /**
     * @param Node $node
     * @param Node $parent
     * @return Node
     * @throws \Exception
     */
    public function addNode($node, $parent)
    {
        $node->parent = $parent;

        if ($node->position == Node::POS_LEFT) {
            $parent->left = $node;
        } else {
            $parent->right = $node;
        }

        return $node;
    }

    /**
     * @param Node $node
     */
    public function addToIndex($node)
    {
        $this->index[$node->id] = $node;
    }

    /**
     * @param $subtreeIndex
     */
    public function mergeIndexes($subtreeIndex)
    {
        $this->index = array_merge($this->index, $subtreeIndex);
    }

    /**
     * @param int $id
     * @return Node|null
     */
    public function findById($id)
    {
        return $this->index[$id] ?? null;
    }

    /**
     * @return Node
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return array
     */
    public function getIndex()
    {
        return $this->index;
    }
}
