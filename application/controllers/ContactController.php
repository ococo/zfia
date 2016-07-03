<?php

class ContactController extends Zend_Controller_Action
{
    function init()
    {
        $this->_helper->acl->allow(null);
    }
    
    public function indexAction()
    {
        include_once 'ContactForm.php';
        $form = new ContactForm('/contact/');
        $this->view->formResponse = '';
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $this->createTopicAction($form);
            } else {
                $this->view->formResponse = 'Sorry, there was a problem with your submission. Please check the following:';
                $form->populate($_POST);
            }
        }
        $this->view->form = $form;
    }
}