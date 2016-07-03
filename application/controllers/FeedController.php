<?php

class FeedController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->acl->allow(null);
    }

    public function indexAction()
    {
        $format = $this->getRequest()->getParam('format');
        $format = in_array($format, array('rss', 'atom')) ? $format : 'rss';

        $articlesTable = new ArticleTable();
        $rowset = $articlesTable->fetchAll();

        $channel = array(
            'title'       => 'Places',
            'link'        => 'http://places/',
            'description' => 'All the latest articles',
            'charset'     => 'UTF-8',
            'entries'     => array()
        );

        foreach ($rowset as $item) {
            $channel['entries'][] = array(
                'title'       => $item->title,
                'link'        => 'http://places/article/index/id/' . $item->id . '/',
                'description' => $item->body
                );
        }
        $feed = Zend_Feed::importArray($channel, $format);
        header('Content-Type: text/xml; charset=UTF-8');
        echo $feed->saveXML();
        //$feed->send;
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();
    }
}