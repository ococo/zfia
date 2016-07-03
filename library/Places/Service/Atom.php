<?php

include_once 'admin/models/ContentTable.php';

class Places_Service_Atom
{
    protected $_database;
    protected $_config;
    protected $_auth;
    protected $_contentTable;

    public function __construct()
    {
        $this->_database = Zend_Registry::get('database');
        $this->_config = Zend_Registry::get('config');
        $this->_auth = new Places_Service_Auth;
        $this->_contentTable = new ContentTable();
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
        header('Authorization: Basic ' . base64_encode($username . ':' . $password));
        $links = array();
        $links[] = array(
            'rel' => 'service.feed',
            'href' => 'http://' . $this->_config->server . '/rest/',
            'title' => $this->_config->server,
            'type' => 'application/x.atom+xml',
        );
        $links[] = array(
            'rel' => 'service.post',
            'href' => 'http://' . $this->_config->server . '/rest/',
            'title' => $this->_config->server,
            'type' => 'application/x.atom+xml',
        );

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;

        $r = $doc->createElement('feed');
        $doc->appendChild($r);

        foreach($links as $link) {
            $b = $doc->createElement('link');

            $rel = $doc->createAttribute('rel');
            $rel->appendChild($doc->createTextNode($link['rel']));
            $b->appendChild($rel);

            $href = $doc->createAttribute('href');
            $href->appendChild($doc->createTextNode($link['href']));
            $b->appendChild($href);

            $title = $doc->createAttribute('title');
            $title->appendChild($doc->createTextNode($link['title']));
            $b->appendChild($title);

            $type = $doc->createAttribute('type');
            $type->appendChild($doc->createTextNode($link['type']));
            $b->appendChild($type);

            $r->appendChild($b);
        }
        return $doc->saveXML();
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
            'nickname'  => $identity->alias,
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
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a Blogger user who has permission to post to the blog
    * @param string $password Password for said username
    * @param int $numposts Number of posts to be retrieved from blog
    * @return struct
    */
    public function getRecentPosts($blogid, $username, $password, $numposts) 
    {
        $rowset = $this->_contentTable->fetchAll(null, null, 3);
        //return $rowset->toArray();

        $channel = array(
            'title'       => 'Support Tracker',
            'link'        => 'http://places/admin/support/',
            'description' => 'All the latest problems, bugs and ideas',
            'charset'     => 'UTF-8',
            'entries'     => array()
        );

        foreach ($rowset as $item) {
            $channel['entries'][] = array(
                'title'       => $item->title,
                'link'        => 'http://places/admin/support/edit/id/' . $item->id . '/',
                'description' => $item->body
             );
        }
       $feed = Zend_Feed::importArray($channel, 'atom');
       return $feed->saveXML();
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
        $row = $this->_contentTable->find($filterInt->filter($postid));
        $content = $row->current();

        $channel = array(
            'title'       => 'Support Tracker',
            'link'        => 'http://places/admin/support/',
            'description' => 'All the latest problems, bugs and ideas',
            'charset'     => 'UTF-8',
            'entries'     => array()
        );

        $channel['entries'][] = array(
            'pubDate'     => $content->date_created,
            'postid'      => $content->id,
            'author'      => $content->creator,
            'title'       => $content->title,
            'link'        => 'http://' . $this->_config->server . '/' . $content->type . '/' . $content->ref . '/',
            'description' => $content->body
        );

        $feed = Zend_Feed::importArray($channel, 'atom');
        $entry = $feed->current();

        return $entry->saveXML();
    }

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