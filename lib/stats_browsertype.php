<?php

/**
 * Handles the "browsertype" data for statistics
 *
 * @author Andreas Lenhardt
 */
class stats_browsertype
{

    private $start_date = '';
    private $end_date = '';

    /**
     * 
     * 
     * @param mixed $start_date 
     * @param mixed $end_date 
     * @return void 
     * @author Andreas Lenhardt
     */
    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
     *
     *
     * @return array
     * @throws InvalidArgumentException
     * @throws rex_sql_exception
     * @author Andreas Lenhardt
     */
    private function get_sql()
    {
        $sql = rex_sql::factory();

        if ($this->start_date != '' && $this->end_date != '') {
            $result = $sql->setQuery('SELECT browsertype, COUNT(browsertype) as "count" FROM ' . rex::getTable('pagestats_dump') . ' where date between :start and :end GROUP BY browsertype ORDER BY count DESC', ['start' => $this->start_date, 'end' => $this->end_date]);
        } else {
            $result = $sql->setQuery('SELECT browsertype, COUNT(browsertype) as "count" FROM ' . rex::getTable('pagestats_dump') . ' GROUP BY browsertype ORDER BY count DESC');
        }

        $data = [];

        foreach ($result as $row) {
            $data[$row->getValue('browsertype')] = $row->getValue('count');
        }

        return $data;
    }


    /**
     *
     *
     * @return (string|false)[]
     * @throws InvalidArgumentException
     * @throws rex_sql_exception
     * @author Andreas Lenhardt
     */
    public function get_data()
    {
        $data = $this->get_sql();

        return [
            'labels' => json_encode(array_keys($data)),
            'values' => json_encode(array_values($data)),
        ];
    }


    /**
     *
     *
     * @return string
     * @throws InvalidArgumentException
     * @throws rex_exception
     * @author Andreas Lenhardt
     */
    public function get_list()
    {
        $addon = rex_addon::get('statistics');

        if ($this->start_date != '' && $this->end_date != '') {
            $list = rex_list::factory('SELECT browsertype, COUNT(browsertype) as "count" FROM ' . rex::getTable('pagestats_dump') . ' where date between "' . $this->start_date . '" and "' . $this->end_date . '" GROUP BY browsertype ORDER BY count DESC');
        } else {
            $list = rex_list::factory('SELECT browsertype, COUNT(browsertype) as "count" FROM ' . rex::getTable('pagestats_dump') . ' GROUP BY browsertype ORDER BY count DESC');
        }

        $list->setColumnLabel('browsertype', $addon->i18n('statistics_name'));
        $list->setColumnLabel('count', $addon->i18n('statistics_count'));
        $list->addTableAttribute('class', 'dt_order_second');

        return $list->get();
    }


    /**
     *
     *
     * @return array
     * @throws InvalidArgumentException
     * @throws rex_sql_exception
     * @author Andreas Lenhardt
     */
    public function get_data_dashboard()
    {
        $data = $this->get_sql();

        return $data;
    }
}
