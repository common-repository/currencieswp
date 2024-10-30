<?php
namespace Dnolbon\CurrenciesWp\Pages;

use Dnolbon\CurrenciesWp\Tables\Archive;
use Dnolbon\Twig\Twig;
use Dnolbon\Wordpress\Table\Table;

class ArchivePage
{
    /**
     * @var Table $table
     */
    private $table;

    public function render()
    {
        $template = Twig::getInstance()->getTwig()->load('archive.html');
        echo $template->render(['table' => $this->getTable()]);
    }


    /**
     * @return Table
     */
    public function getTable()
    {
        if ($this->table === null) {
            $this->table = new Archive();
            $this->table->prepareItems();
        }
        return $this->table;
    }
}
