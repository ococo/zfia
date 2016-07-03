<?php

class ArticleTable extends Zend_Db_Table
{
    protected $_name = 'articles';
    
    function fetchLatest($count = 10)
    {
        return $this->fetchAll(null, 
            'date_created DESC', $count);
    }
}