<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * The controller's init() function is called before 
     * the action. Usually we use it to set up the ACL
     * restrictions for the actions within the controller.
     *
     */
    public function init()
    {
        // allow everyone access to all actions
        $this->_helper->acl->allow('guest', 'menu');
        $this->_helper->acl->allow('guest', 'advert');
        $this->_helper->acl->allow(null);
    }
    
    /**
     * This action is the home page of the website
     *
     */
    public function indexAction()
    {
        $this->view->title = 'Welcome to Places to take the kids!';
        $this->view->headTitle('Welcome');

        $placesFinder = new Places();

        $this->view->places = $placesFinder->fetchLatest();
    }

    /**
     * This action creates the main menu and is called
     * via the action stack
     * 
     * Note that as we are using multiple routes in the
     * bootstrap, we have to specify which route we want
     * to use to generate each url, otherwise the route
     * that was used for this request is used. If you do
     * not specify, then when you are on the about page
     * (which uses the about route), all the urls in the
     * menu will point to /about!
     *
     */
    public function menuAction()
    {
        $mainMenu = array(
            array('title'=>'Home', 'url'=>$this->view->url(array(), 'default', true)),
            array('title'=>'Browse Places', 'url'=>$this->view->url(array('controller'=>'place', 'action'=>'browse'), 'default', true)),
            array('title'=>'Articles', 'url'=>$this->view->url(array('controller'=>'articles'), 'default', true)),
            array('title'=>'About', 'url'=>$this->view->url(array(), 'about', true)),
        );

        $this->view->menu = $mainMenu;
        $this->_helper->viewRenderer->setResponseSegment('menu');
    }
    
    /**
     * This action creates the right hand advert and is
     * called via the action stack.
     *
     */
    public function advertAction()
    {
        $this->_helper->viewRenderer->setResponseSegment('advert');
    }
}