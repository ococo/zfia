<?php

class Reviews extends Zend_Db_Table
{
    protected $_name = 'reviews';
    protected $_rowClass = 'Review';

    protected $_referenceMap = array(
        'Place' => array(
            'columns' => array('place_id'),
            'refTableClass' => 'Places',
            'refColumns' => 'id'),
        'User' => array(
            'columns' => array('created_by'),
            'refTableClass' => 'Users',
            'refColumns' => 'id')
            );
           
    public function fetchByPlaceId($place_id, 
       $order = 'date_created DESC', $count=null)
   {

        $where = 'place_id = ' . (int)$place_id;

        $order = 'date_created DESC';

        return $this->fetchAll($where, $order, $count);

    }

}