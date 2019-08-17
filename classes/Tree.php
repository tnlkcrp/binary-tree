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

        if ($node->position === Node::POS_LEFT) {
            $parent->left = $node;
        } else {
            $parent->right = $node;
        }

        return $node;
    }
}
