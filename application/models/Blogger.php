<?php

class Blogger implements Places_Service_Blogger_Interface
{
    protected $_database;
    protected $_config;
    protected $_auth;

    public function __construct()
    {
        $this->_database = Zend_Registry::get('db');
        $this->_config = Zend_Registry::get('config');
        $this->_auth = new ServiceAuth;
    }

    /**
    * Returns information about all the blogs a given user is a member of
    * Data is returned as an array of structs containing the blogid, blogName and url of each blog
    *
    * @param string $appkey Unique identifier of the application sending the post
    * @param string $username Login for the Blogger user who's blogs will be retrieved
    * @param string $password a string
    * @return struct
    */
    public function getUsersBlogs($appkey, $username, $password)
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }
        $isAdmin = 'admin' == $identity->role ? true : false;

        $struct[] = array(
            'isAdmin'  => $isAdmin,
            'blogid'   => '1',
            'blogName' => 'Places',
            'url'      => 'http://' . $this->_config->server . '/'
        );

        return $struct;
    }

    /**
    * Returns a struct containing userid, firstname, lastname, nickname, email, and url
    *
    * @param string $appkey Unique identifier of the application sending the post
    * @param string $username Login for the Blogger user who's blogs will be retrieved
    * @param string $password Password for said username
    * @return struct
    */
    public function getUserInfo($appkey, $username, $password)
    {
        $identity = $this->_auth->authenticate($username, $password);
        if(false === $identity) {
            throw new Exception('Authentication Failed');
        }

        $struct = array(
            'nickname'  => $identity->nickname,
            'userid'    => $identity->id,
            'url'       => '',
            'email'     => $identity->email,
            'lastname'  => $identity->last_name,
            'firstname' => $identity->first_name
        );
        return $struct;
    }

    /**
    * Returns an array of structs containing the latest n posts to a given blog, newest first
    *
    * @param string $appkey Unique identifier of the application sending the post
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a Blogger user who has permission to post to the blog
    * @param string $password Password for said username
    * @param int $numposts Number of posts to be retrieved from blog
    * @return struct
    */
    public function getRecentPosts($appkey, $blogid, $username, $password, $numposts) { }

    /**
    * Returns a struct like the structs in getRecentPosts containing the userid, post body, datecreated, and post id
    *
    * @param string $appkey Unique identifier of the application sending the post
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a Blogger user who has permission to post to the blog
    * @param string $password Password for said username
    * @return struct
    */
    public function getPost($appkey, $postid, $username, $password) { }

    /**
    * Makes a new post to a designated blog
    * Optionally, will publish the blog after making the post. On success, it returns the unique ID of the new post
    *
    * @param string $appkey Unique identifier of the application sending the post
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a Blogger user who has permission to post to the blog
    * @param string $password Password for said username
    * @param string $content  Contents of the post
    * @param boolean $publish If true, the blog will be published immediately after the post is made
    * @return string
    */
    public function newPost($appkey, $blogid, $username, $password, $content, $publish) { }

    /**
    * Changes the contents of a given post
    * Optionally, will publish the blog the post belongs to after changing the post
    *
    * @param string $appkey Unique identifier of the application sending the post
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a Blogger user who has permission to edit the given post (either the user who originally created it or an admin of the blog)
    * @param string $password Password for said username
    * @param string $content New content of the post
    * @param boolean $publish If true, the blog will be published immediately after the post is made
    * @return boolean
    */
    public function editPost($appkey, $postid, $username, $password, $content, $publish) { }

    /**
    * Deletes a post
    *
    * @param string $appkey Unique identifier of the application sending the post
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a Blogger user who has permission to edit the given post (either the user who originally created it or an admin of the blog)
    * @param string $password Password for said username
    * @param string $content New content of the post
    * @param boolean $publish If true, the blog will be published immediately after the post is made
    * @return boolean
    */
    public function deletePost($appkey, $postid, $username, $password, $content, $publish) { }
}