<?php

interface Places_Service_Metaweblog_Interface
{
    /**
    * Returns an orray containing one struct for each category, each with
    * the following elements: description, htmlUrl and rssUrl 
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @return struct
    */
    public function getCategories($blogid, $username, $password);

    /**
    * Returns an array of structs containing the latest n posts to a given blog, newest first
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @param int $numposts Number of posts to be retrieved from blog
    * @return struct
    */
    public function getRecentPosts($blogid, $username, $password, $numposts);

    /**
    * Returns a struct like the structs in getRecentPosts containing the userid, post body, datecreated, and post id
    *
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @return struct
    */
    public function getPost($postid, $username, $password);

    /**
    * Makes a new post to a designated blog
    * Optionally, will publish the blog after making the post. On success, it returns the unique ID of the new post
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @param string $content  Contents of the post
    * @param boolean $publish If true, the blog will be published immediately after the post is made
    * @return string
    */
    public function newPost($blogid, $username, $password, $content, $publish);

    /**
    * Changes the contents of a given post
    * Optionally, will publish the blog the post belongs to after changing the post
    *
    * @param string $postid Unique identifier of the post to be changed
    * @param string $username Login for a user who has permission to edit the given post (either the user who originally created it or an admin of the blog)
    * @param string $password Password for said username
    * @param string $content New content of the post
    * @param boolean $publish If true, the blog will be published immediately after the post is made
    * @return boolean
    */
    public function editPost($postid, $username, $password, $content, $publish);

    /**
    * Returns an orray containing one struct for each category, each with
    * the following elements: description, htmlUrl and rssUrl 
    *
    * @param string $blogid Unique identifier of the blog the post will be added to
    * @param string $username Login for a user who has permission to post to the blog
    * @param string $password Password for said username
    * @param struct $struct Must contain at least three elements, name (string, may be used to name the file), type (string, standard MIME type, like image/jpeg) and bits (base64, containing content of the object)
    * @return struct
    */
    public function newMediaObject ($blogid, $username, $password, $struct);
}