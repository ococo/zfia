<?php

class PlaceController extends Zend_Controller_Action
{
    function init()
    {
        $readActions = array('index', 'details', 'browse', 'reportCorrection');
        $writeActions = array('add', 'edit', 'delete');
        $this->_helper->_acl->allow('member', $readActions);
        $this->_helper->_acl->allow('admin', $writeActions);
    }
    
    public function indexAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id == 0) {
            throw new Exception('Unknown place');
            return;
        }

        $placesFinder = new Places();
        $place = $placesFinder->fetchRow('id='.$id);
        if ($place->id != $id) {
            $this->_redirect('/');
            return;
        }
        $this->view->place = $place;
        $this->view->title = $place->name;

        $reviewsFinder = new Reviews();
        $this->view->reviews = $reviewsFinder->fetchByPlaceId($id);

    }

    public function browseAction()
    {
        $placesFinder = new Places();
        $this->view->places = $placesFinder->fetchAll(null, 'name');
        $this->view->title = 'Browse places';
    }
}