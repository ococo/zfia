<?php

include_once 'Row.php';

class Support_Table extends Zend_Db_Table_Abstract
{
    protected $_name = 'support';
    protected $_rowClass = 'Support_Row';
    protected $_dependentTables = array('Users');
    protected $_referenceMap = array(
        'Support' => array(
            'columns'           => array('user_id'),
            'refTableClass'     => 'Users',
            'refColumns'        => array('id')
        )
    );
}