<?php

class Users extends Zend_Db_Table
{
    protected $_name = 'users';
    protected $_rowClass = 'User';
    
    protected $_referenceMap = array(
        'Reviews' => array(
            'columns' => array('id'),
            'refTableClass' => 'Reviews',
            'refColumns' => 'created_by')
            );

}