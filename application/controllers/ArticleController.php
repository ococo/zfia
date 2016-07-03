<?php

class ArticleController extends Zend_Controller_Action
{
    function init()
    {
        $this->_helper->_acl->allow(null, 'about'); // everyone can access the about action
        $this->_helper->_acl->allow('member', null); // members can access every action...
        $this->_helper->_acl->deny('member', 'blacklist'); // ...except blacklistAction
        $this->_helper->_acl->allow('admin', null); // admins can access every action
    }
    
    public function indexAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id == 0) {
            $this->_redirect('/');
            return;
        }

        $articlesTable = new ArticleTable();
        $article = $articlesTable->fetchRow('id='.$id);
        if ($article->id != $id) {
            $this->_redirect('/');
            return;
        }
        $this->view->article = $article;

        $frontendOptions = array(
           'lifetime' => 300,
           'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => ROOT_DIR . '/var/cache'
        );

        // getting a Zend_Cache_Core object
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        // Try loading the cached version
        if(!$results = $cache->load('flickr')) {
            // Cache miss: query Amazon
            $flickr = new Flickr;
            $results = $flickr->search($article->keywords);
            // If we get results cache them
            if(null !== $results) {
                $cache->save($results, 'flickr');
            }
        }
        $this->view->flickr = $results;
    }
    
    public function aboutAction()
    {
        $this->view->title = 'About Places to take the kids!';
        $this->view->headTitle('About');
    }
}