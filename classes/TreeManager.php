<?php

namespace binary;

/**
 * Class TreeManager
 */
class TreeManager
{
    const DEFAULT_LEVELS = 5;

    /**
     * @var int
     */
    private $levels = self::DEFAULT_LEVELS;

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var Tree
     */
    private $tree;

    /**
     * TreeManager constructor.
     * @param \PDO $connection
     */
    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
        $this->initTable();
    }

    /**
     * Automatically generate tree.
     * @return Tree
     */
    public function generate()
    {
        $root = $this->createRoot();
        $root->generatePath();
        $this->updateNodePath($root);

        $this->tree = new Tree($root);

        $level = 2;
        if ($level <= $this->levels) {
            $this->makeChildren([$root], $level);
        }

        return $this->tree;
    }

    /**
     * @param array $data
     * @return Tree
     */
    private function mapDataToTree($data)
    {
        $root = $this->mapDataToNode($data[0]);
        $tree = new Tree($root);
        $tree->addToIndex($root);
        unset($data[0]);

        foreach ($data as $nodeData) {
            $node = $this->mapDataToNode($nodeData);
            $parent = $tree->findById($node->parentId);
            $tree->addNode($node, $parent);
            $tree->addToIndex($node);
        }

        return $tree;
    }

    /**
     * @param array $data
     * @return Node
     */
    private function mapDataToNode($data)
    {
        $node = new Node();
        $node->id = $data['id'];
        $node->parentId = $data['parent_id'];
        $node->position = $data['position'];
        $node->path = $data['path'];
        $node->level = $data['level'];
        return $node;
    }

    /**
     * @param int $id
     * @return Tree
     */
    public function getChildrenById($id)
    {
        $path = $this->getPathById($id);

        $sql = "SELECT * FROM nodes WHERE path LIKE :path ORDER BY path";
        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':path', "{$path}%", \PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $this->mapDataToTree($result);
    }

    /**
     * @param int $id
     * @return Tree
     */
    public function getAncestorsById($id)
    {
        $path = $this->getPathById($id);
        $ancestorsIds = explode('.', $path);
        unset($ancestorsIds[count($ancestorsIds) - 1]);
        $ancestorsIds = implode(',', $ancestorsIds);

        $sql = "SELECT * FROM nodes WHERE id IN ({$ancestorsIds})
                ORDER BY path";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $this->mapDataToTree($result);
    }

    /**
     * @param int $id
     * @return string
     */
    private function getPathById($id)
    {
        $sql = "SELECT path FROM nodes WHERE id = :id";
        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * @param int $levels
     */
    public function setLevels($levels)
    {
        $this->levels = $levels;
    }

    /**
     * @return Node
     */
    private function createRoot()
    {
        $root = new Node();
        $root->level = 1;
        $this->insertNode($root);
        return $root;
    }

    /**
     * @param array $parents
     * @param int $currentLevel
     */
    private function makeChildren($parents, $currentLevel)
    {
        $children = [];
        foreach ($parents as $parent) {
            $child = $this->makeChild($parent, Node::POS_LEFT, $currentLevel);
            $children[] = $child;
            $child = $this->makeChild($parent, Node::POS_RIGHT, $currentLevel);
            $children[] = $child;
        }

        $currentLevel++;

        if ($currentLevel <= $this->levels) {
            $this->makeChildren($children, $currentLevel);
        }
    }

    /**
     * @param Node $parentNode
     * @param int $position
     * @param int $level
     * @return Node
     */
    private function makeChild($parentNode, $position, $level)
    {
        $child = new Node();
        $child->level = $level;
        $child->position = $position;
        $child->parentId = $parentNode->id;
        $this->insertNode($child);
        $child->generatePath($parentNode->path);
        $this->updateNodePath($child);
        $this->tree->addNode($child, $parentNode);

        return $child;
    }

    /**
     * Insert node into db table.
     * @param Node $node
     */
    private function insertNode(Node $node)
    {
        $sql = "INSERT INTO nodes (parent_id, position, path, level)
                VALUES (:parent_id, :position, :path, :level)";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':parent_id', $node->parentId, \PDO::PARAM_INT);
        $statement->bindValue(':position', $node->position, \PDO::PARAM_INT);
        $statement->bindValue(':level', $node->level, \PDO::PARAM_INT);
        $statement->bindValue(':path', $node->path, \PDO::PARAM_STR);
        $statement->execute();

        $node->id = $this->connection->lastInsertId();
    }

    /**
     * @param Node $node
     */
    private function updateNodePath(Node $node)
    {
        $sql = "UPDATE nodes SET path = :path WHERE id = :id";
        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':path', $node->path, \PDO::PARAM_STR);
        $statement->bindValue(':id', $node->id, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Create table if not exists.
     */
    private function initTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS nodes (
            id INT UNSIGNED AUTO_INCREMENT,
            parent_id INT UNSIGNED,
            position TINYINT UNSIGNED,
            path TEXT,
            level TINYINT UNSIGNED,
            PRIMARY KEY (id),
            FOREIGN KEY fk_parent_id(parent_id) REFERENCES nodes(id)
            ON UPDATE CASCADE ON DELETE CASCADE
        )";

        $this->connection->exec($sql);
    }

    /**
     * @param Tree $tree
     */
    public function printTree(Tree $tree)
    {
        $node = $tree->getRoot();
        $this->printNode($node, '');
    }

    /**
     * @param Node $node
     * @param string $margin
     */
    private function printNode(Node $node, $margin)
    {
        echo $margin . $node->id . "\n";
        if ($node->left) {
            $this->printNode($node->left, "  " . $margin);
        }
        if ($node->right) {
            $this->printNode($node->right, "  " . $margin);
        }
    }
}
