<?php

include_once 'ArticleTable.php';
include_once 'CategoryTable.php';
include_once 'ArticleToCategoryTable.php';

class MovableType implements Places_Service_MovableType_Interface
{
    protected $_database;
    protected $_config;
    protected $_auth;
    protected $_articleTable;
    protected $_categoryTable;
    protected $_articleToCategoryTable;

    public function __construct()
    {
        $this->_database = Zend_Registry::get('db');
        $this->_config = Zend_Registry::get('config');
        $this->_auth = new ServiceAuth;
        $this->_articleTable = new ArticleTable();
        $this->_categoryTable = new CategoryTable();
        $this->_articleToCategoryTable = new ArticleToCategoryTable();
    }

    /**
     * Returns: on success, an array of structs containing 
     * categoryName(string), categoryId(string); on failure, fault. 
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @return struct
    */
    public function getCategoryList($blogid, $username, $password)
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        $rowSet = $this->_categoryTable->fetchAll();

        foreach($rowSet as $key => $row) {
            $struct[$key] = array(
                'categoryId' => $row->id,
                'categoryName' => $row->name,
            );
        }

        return $struct;
    }

    /**
    * Returns struct containing key (string identifying a text formatting available) and label (string description displayed to a user). 
    * key is the value that should be passed in the mt_convert_breaks parameter to metaWeblog.newPost and metaWeblog.editPost. 
    *
    * @return struct
    */
    public function supportedTextFilters()
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        return array();
    }

    /**
    * Returns a struct containing categoryName (string), categoryId (string), and isPrimary (boolean)
    * on failure, fault 
    *
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @return struct
    */
    public function getPostCategories($postid, $username, $password)
    {
        $struct = array();
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        $filterInt = new Zend_Filter_Int;
        $id = $filterInt->filter($postid);

        $where  = $this->_articleTable->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->_articleTable->fetchRow($where);

        if(null === $row) {
            throw new Exception('No categories found for post id: ' . $id);
        }

        $this->view->edit_article = $row->toArray();
        $categories = $row->findManyToManyRowset($this->_categoryTable, $this->_articleToCategoryTable);

        foreach ($categories as $key => $category) {
            $struct[$key]['categoryName'] = $category->name;
            $struct[$key]['categoryId'] = $category->id;
            $struct[$key]['isPrimary'] = false;
        }

        return $struct;
    }

    /**
    * Set categories for a post
    *
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @param array $categories Array for each category containing categoryId (string) and isPrimary (boolean) 
    * @return boolean true on success, false otherwise
    */
    public function setPostCategories($postid, $username, $password, $categories)
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }
        $filterInt = new Zend_Filter_Int;
        $id = $filterInt->filter($postid);
        // Clear previous categories and replace 
        $this->_articleToCategoryTable->replace($id, $categories);

        return false;
    }
}