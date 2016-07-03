<?php

include_once 'Zend/Gdata/YouTube.php';

class VideosController extends Zend_Controller_Action
{
    protected $_youTube;

    function init()
    {
        $this->_helper->_acl->allow('member', null); // members can access every action...
        $this->_helper->_acl->deny('member', 'blacklist'); // ...except blacklistAction
        $this->_helper->_acl->allow('admin', null); // admins can access every action
        $this->_youTube = new Zend_Gdata_YouTube;
    }
    
    public function indexAction()
    {
        $this->view->playlistListFeed = $this->_youTube->getPlaylistListFeed('ZFinAction'); 
    }

    public function listAction()
    {
        $playlistId = $this->getRequest()->getParam('id');
        $query = $this->_youTube->newVideoQuery('http://gdata.youtube.com/feeds/playlists/' . $playlistId);
        $this->view->videoFeed = $this->_youTube->getVideoFeed($query);
    }

    public function viewAction() 
    {
        $videoId = $this->getRequest()->getParam('id');
        $this->view->videoId = $videoId;
        $this->view->videoEntry = $this->_youTube->getVideoEntry($videoId);
    }
}