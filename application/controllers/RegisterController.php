<?php

class RegisterController extends Zend_Controller_Action
{
    function init()
    {
        $this->_helper->acl->allow(null);
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/yahoo.js');
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/connection.js');
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/checkUsername.js');
    }
     
    public function preDispatch()
    {
        $this->getRequest()->setParam('format', 'json');
        $this->_helper->ajaxContext()->initContext();
    }

    public function indexAction()
    {
        include_once 'RegisterForm.php';
        $form = new RegisterForm('/register/');
        $this->view->formResponse = '';
        if ($this->getRequest()->isPost()) {
           // AJAX Validation
           if ($this->getRequest()->isXmlHttpRequest()) {
               // XmlHttpRequest detected. Generate AJAX response
               $this->_helper->viewRenderer->setNoRender();
               $this->_helper->layout->disableLayout();
               $response = $form->processAjax($this->getRequest()->getPost());
               $this->getResponse()->setHeader('Content-Type', 'application/json')
                                   ->setBody($response);
               return;
           }

           // Normal Validation
            if ($form->isValid($_POST)) {
                $user->registerUser($form);
            } else {
                $this->view->formResponse = 'Sorry, there was a problem with your submission. Please check the following:';
                $form->populate($_POST);
            }
        }
        $this->view->form = $form;
    }

    public function checkUsernameAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $username = $this->getRequest()->getParam('username');
        if (empty($username)) {
            $return = Zend_Json::encode(array('result' => false));
            $this->_response->appendBody($return);
            return;
        }
        $usersTable = new Users;
        $where = $usersTable->getAdapter()->quoteInto('username = ?', $username);
        $row = $usersTable->fetchRow($where);
        $return = null === $row ? false : true;
        $return = Zend_Json::encode(array('result' => $return));
        $this->_response->appendBody($return);
        return;
    }
}