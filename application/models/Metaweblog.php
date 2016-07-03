<?php

include_once 'ArticleTable.php';
include_once 'CategoryTable.php';
include_once 'ArticleToCategoryTable.php';

class Metaweblog implements Places_Service_Metaweblog_Interface
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
    * Returns an orray containing one struct for each category, each with
    * the following elements: description, htmlUrl and rssUrl 
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @return struct
    */
    public function getCategories($blogid, $username, $password)
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        $rowSet = $this->_categoryTable->fetchAll();

        foreach($rowSet as $key => $row) {
            $struct[$key] = array(
                'categoryId' => $row->id,
                'description' => $row->name,
                'categoryName' => $row->name,
                'htmlUrl' => 'http://' . $this->_config->server . '/category/' . $row->reference . '/',
                'rssUrl' => 'http://' . $this->_config->server . '/category/' . $row->reference . '/feed/'
            );
        }

        return $struct;
    }

    /**
    * Returns an array of structs containing the latest n posts to a given blog, newest first
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @param int $numposts Number of posts to be retrieved from blog
    * @return struct
    */
    public function getRecentPosts($blogid, $username, $password, $numposts)
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        $filterWord = new Places_Filter_Word;
        $rowSet = $this->_articleTable->fetchAll();
        foreach($rowSet as $key => $row) {
            $struct[$key] = array(
                'dateCreated' => new Zend_XmlRpc_Value_DateTime($row->date_created, Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME),
                'userid' => $row->creator,
                'postid' => $row->id,
                'description' => $filterWord->filter($row->body),
                //'description' => new Zend_XmlRpc_Value_Base64($row->body),
                'title' => $row->title,
                'link' => 'http://' . $this->_config->server . '/article/index/id/' . $row->id . '/',
                'permaLink' => 'http://' . $this->_config->server . '/article/index/id/' . $row->id . '/',
                'categories' => array('Zend Framework'),
                'mt_excerpt' => '',
                'mt_text_more' => '',
                'mt_allow_comments' => 1,
                'mt_allow_pings' => 1
            );
        }

        return $struct;
    }

    /**
    * Makes a new post to a designated blog
    * Optionally, will publish the blog after making the post. On success, it returns the unique ID of the new post
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @param struct $struct  Contents of the post
    * @param boolean $publish If true, the blog will be published immediately after the post is made
    * @return string
    */
    public function newPost($blogid, $username, $password, $struct, $publish)
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        // Set up filters
        $filterInt = new Zend_Filter_Int;
        $filterStripTags = new Zend_Filter_StripTags;
        $filterChainDirify = new Zend_Filter;
        $filterChainHTMLEntities = new Zend_Filter;
        $filterChainHTMLEntities->addFilter(new Zend_Filter_StripTags)
                                ->addFilter(new Zend_Filter_StringTrim)
                                ->addFilter(new Zend_Filter_HtmlEntities);

        $data = array(
            //'ref'           => $ref,
            'creator'       => $identity->id,
            //'type'          => 'article',
            //'publish'       => $filterInt->filter($struct['publish']),
            'title'         => $filterStripTags->filter($struct['title']),
            //'keywords'      => $filterStripTags->filter($struct['keywords']),
            //'description'   => $filterStripTags->filter($struct['description']),
            //'template'      => 'index',
            'date_created'  => date('Y-m-d H:i:s'),
            //'ranking'       => $filterInt->filter($struct['ranking']),
            'body'          => $struct['description']
       );

        $id = $this->_articleTable->insert($data);

        if(null === $id) {
            throw new Exception('For some reason your post failed to be inserted');
        }

        // Insert Categories 
        //$this->_articleToCategoryTable->multipleInsert($id, $struct['categories']);

        // Insert Tags
        //$tags = preg_split("/[\s,]+/", $filterStripTags->filter($struct['keywords']));
        //$this->_tagTable->insertUpdated($id, $tags);

        // Upload attachments
        //$uploader = new Places_Upload(realpath(getcwd()) . '/article/' . $id . '/');
        //$uploader->uploadMultiple('files');

        return $id;
    }

    /**
    * Changes the articles of a given post
    * Optionally, will publish the blog the post belongs to after changing the post
    *
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a user who has permission to edit the given post (either the user who originally created it or an admin of the blog)
    * @param string $password Password for said username
    * @param struct $struct New content of the post
    * @param boolean $publish If true, the blog will be published immediately after the post is made
    * @return boolean
    */
    public function editPost($postid, $username, $password, $struct, $publish)
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        // Set up filters
        $filterInt = new Zend_Filter_Int;
        $filterStripTags = new Zend_Filter_StripTags;

        $id = $filterInt->filter($postid);

        //$ref = $this->getUnique('ref', $this->getRequest()->getParam('ref'), $id);
        $data = array(
            //'ref'           => $ref,
            //'type'          => 'article',
            //'publish'       => $filterInt->filter($struct['publish']),
            'title'           => $filterStripTags->filter($struct['title']),
            //'keywords'      => $filterStripTags->filter($struct['mt_keywords']),
            //'description'   => $filterStripTags->filter($struct['description']),
            //'template'      => $filterStripTags->filter($struct['template']),
            //'ranking'       => $filterInt->filter($struct['ranking']),
            'body'          => $struct['description']
        );

        // Clear previous categories and replace 
        //$this->_articleToCategoryTable->replace($id, $struct['categories']);

        $where = $this->_database->quoteInto('id = ?', $id);
        $rows_affected = $this->_articleTable->update($data, $where);

        if(0 == $rows_affected) {
            throw new Exception('For some reason your post failed to be updated');
        }

        // Get the submitted tags and make them an array
        //$tags = preg_split("/[\s,]+/", $filterStripTags->filter($struct['keywords']));
        //$this->_tagTable->deleteAndInsert($id, $tags);
        // Upload attachments
        //$uploader = new Places_Upload(realpath(getcwd()) . '/article/' . $id . '/');
        //$uploader->uploadMultiple('files');

        return true;
    }

    /**
    * Returns a struct like the structs in getRecentPosts containing the userid, post body, datecreated, and post id
    *
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @return struct
    */
    public function getPost($postid, $username, $password)
    { 
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        $filterInt = new Zend_Filter_Int;
        $row = $this->_articleTable->find($filterInt->filter($postid));
        $article = $row->current();

        $struct = array(
            'pubDate'     => $article->date_created,
            'postid'      => $article->id,
            'author'      => $article->creator,
            'description' => $article->body,
            'title'       => $article->title,
            'link'        => 'http://' . $this->_config->server . '/article/' . $article->id . '/'
        );
        return $struct;
    }

    public function getUnique($name, $value, $id=null)
    {
        $filterChainDirify = new Zend_Filter;
        $filterChainDirify->addFilter(new Zend_Filter_StripTags)
                          ->addFilter(new Places_Filter_Dirify);
        $value = $filterChainDirify->filter($value);

        $where[] = $this->_articleTable->getAdapter()->quoteInto($name . ' = ?', $value);
        if (null !== $id) {
            $where[] = $this->_articleTable->getAdapter()->quoteInto('id != ?', $id);
        }
        $row = $this->_articleTable->fetchRow($where);
        if (null !== $row) {
            $value .= '-' . date('ymdGis');
        }
        return $value;
    }

    /**
    * Returns an orray containing one struct for each category, each with
    * the following elements: description, htmlUrl and rssUrl 
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @param struct $struct Must contain at least three elements, name (string, may be used to name the file), type (string, standard MIME type, like image/jpeg) and bits (base64, containing article of the object)
    * @return struct
    */
    public function newMediaObject ($blogid, $username, $password, $struct)
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        $struct = array();
        return $struct;
    }
}