<?php

namespace binary;

/**
 * Class Node
 */
class Node
{
    const POS_LEFT = 1;
    const POS_RIGHT = 2;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $parentId;

    /**
     * @var int
     */
    public $position = null;

    /**
     * @var string
     */
    public $path;

    /**
     * @var int
     */
    public $level;

    /**
     * @var Node
     */
    public $left = null;

    /**
     * @var Node
     */
    public $right = null;

    /**
     * @var Node
     */
    public $parent = null;

    /**
     * @param string|null $parentPath
     * @return string
     */
    public function generatePath($parentPath = null)
    {
        $this->path = is_null($parentPath)
            ? strval($this->id)
            : "{$parentPath}.{$this->id}";
    }
}
