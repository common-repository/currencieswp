<?php
namespace Dnolbon\CurrenciesWp\Tables;

use Dnolbon\CurrenciesWp\CurrenciesWp;
use Dnolbon\Wordpress\WpListTable;

class Archive extends WpListTable
{

    /**
     * Get a list of columns. The format is:
     * 'internal-name' => 'Title'
     *
     * @since 3.1.0
     * @access public
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = [
            'id' => 'ID',
            'currency' => 'Currency',
            'date' => 'Date',
            'rate' => 'Rate'
        ];
        return $columns;
    }

    public function getId($item)
    {
        return $item->id;
    }

    /**
     * Prepares the list of items for displaying.
     *
     * @since 3.1.0
     * @access public
     */
    public function prepareItems()
    {
        global $wpdb;

        $current_page = $this->getPagenum();

        $sql = 'SELECT count(*) FROM ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES;
        $total = $wpdb->get_var($sql);

        $orderBy = (isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) . ' ' . sanitize_text_field($_GET['order']) : 'date desc, rate asc');

        $preparedSql = 'SELECT 
                    ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES . '.*
                FROM ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES . ' 
                     
                order by '.$orderBy.'
                    
                limit ' . (($current_page - 1) * 20) . ',20';

        $this->items = $wpdb->get_results($preparedSql);

        $this->setPagination(['total_items' => $total, 'per_page' => 20]);

        $this->initTable();
    }

    /**
     * Get a list of sortable columns. The format is:
     * 'internal-name' => 'orderby'
     * or
     * 'internal-name' => array( 'orderby', true )
     *
     * The second format will make the initial sorting order be descending
     *
     * @since 3.1.0
     * @access protected
     *
     * @return array
     */
    protected function getSortableColumns()
    {
        $columns = [
            'id' => ['id', false],
            'currency' => ['currency', false],
            'date' => ['date', false],
            'rate' => ['rate', false]
        ];
        return $columns;
    }
}
