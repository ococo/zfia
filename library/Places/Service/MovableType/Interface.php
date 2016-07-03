<?php

interface Places_Service_MovableType_Interface
{
    /**
    * Returns: on success, an array of structs containing 
    * categoryName(string), categoryId(string); on failure, fault. 
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @return struct
    */
    public function getCategoryList($blogid, $username, $password);

    /**
    * Returns struct containing key (string identifying a text formatting available) and label (string description displayed to a user). 
    * key is the value that should be passed in the mt_convert_breaks parameter to metaWeblog.newPost and metaWeblog.editPost. 
    *
    * @return struct
    */
    public function supportedTextFilters();

    /**
    * Returns a struct containing categoryName (string), categoryId (string), and isPrimary (boolean)
    * on failure, fault 
    *
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @return struct
    */
    public function getPostCategories($postid, $username, $password);

    /**
    * Set categories for a post
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @param struct $categories Array for each category containing categoryId (string) and isPrimary (boolean) 
    * @return boolean true on success, false otherwise
    */
    public function setPostCategories($postid, $username, $password, $categories);
}