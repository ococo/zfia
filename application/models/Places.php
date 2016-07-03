<?php

class Places extends Zend_Db_Table
{
    protected $_name = 'places';
    protected $_rowClass = 'Place';
    
    protected $_referenceMap = array(
        'Reviews' => array(
            'columns' => array('id'),
            'refTableClass' => 'Reviews',
            'refColumns' => 'place_id'),
        'User' => array(
            'columns' => array('created_by'),
            'refTableClass' => 'Users',
            'refColumns' => 'id')            
            );

    /**
     * Fetch the latest $count places
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchLatest($count = 10)

    {
        return $this->fetchAll(null, 
            'date_created DESC', $count);
    }
}
