<?php
namespace Dnolbon\WooCommerce\Products;

abstract class ProductColumnAbstract
{
    public function __construct()
    {
        add_filter('manage_product_posts_columns', [$this, 'manageColumns'], $this->getPriority());
        add_action('manage_product_posts_custom_column', [$this, 'render'], 10, 2);
    }

    protected function getPriority()
    {
        return 11;
    }

    abstract public function render($column, $postId);

    public function manageColumns($columns)
    {
        if ($this->getInsertAfter() !== '') {
            $columns = $this->insertIntoArrayAfter(
                $columns,
                [$this->getColumnName() => $this->getColumnTitle()],
                $this->getInsertAfter()
            );
        } else {
            $columns = $this->insertIntoArrayBefore(
                $columns,
                [$this->getColumnName() => $this->getColumnTitle()],
                $this->getInsertBefore()
            );
        }
        return $columns;
    }

    abstract protected function getInsertAfter();

    private function insertIntoArrayAfter($array, $insert, $positionKey)
    {
        $position = array_search($positionKey, array_keys($array)) + 1;

        $result = array_slice($array, 0, $position, true) +
            $insert +
            array_slice($array, $position, count($array) - $position, true);
        return $result;
    }

    abstract protected function getColumnName();

    abstract protected function getColumnTitle();

    protected function insertIntoArrayBefore($array, $insert, $positionKey)
    {
        $position = array_search($positionKey, array_keys($array));

        $result = array_slice($array, 0, $position, true) +
            $insert +
            array_slice($array, $position, count($array) - $position, true);
        return $result;
    }

    abstract protected function getInsertBefore();
}
