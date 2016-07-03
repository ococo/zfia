<?php

class ArticlesController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->acl->allow(null);
    }
    
    public function indexAction()
    {
        $this->view->title = '';
        $articlesTable = new ArticleTable();
        $this->view->articles = $articlesTable->fetchLatest();
        
        //$this->renderView();
    }
    
    public function testAction()
    {
        $this->view->title = 'Controllers2';
        $articlesTable = new ArticleTable();
        $this->view->articles = $articlesTable->fetchLatest();
        //$this->renderView();
    }    
}