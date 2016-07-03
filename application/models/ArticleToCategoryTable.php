<?php

class ArticleToCategoryTable extends Zend_Db_Table
{
    protected $_name = 'article_to_category';
    protected $_referenceMap = array(
        'Article' => array(
            'columns' => array('article_id'),
            'refTableClass' => 'ArticleTable',
            'refColumns' => 'id'),
        'Category' => array(
            'columns' => array('category_id'),
            'refTableClass' => 'CategoryTable',
            'refColumns' => 'id')
    );

    public function replace($id=null, $values=array())
    {
        $where = $this->_db->quoteInto('article_id = ?', $id);
        $this->delete($where);
        $this->multipleInsert($id, $values);
    }

    public function multipleInsert($id, $values=array())
    {
        if(empty($values)) {
            return TRUE;
        }

        foreach ($values as $key => $value) {
            extract($value);
            $values[$key] = '(' . intval($id) . ', ' . $categoryId . ')';
        }
        $sql = 'INSERT INTO ' . $this->_name . ' (';
        $sql .= 'article_id, category_id) ';
        $sql .= 'VALUES ';
        $sql .= implode(',', $values);
        //echo $sql;
        $result = $this->_db->query($sql);
    }
}