<?php

class AuthController extends Zend_Controller_Action
{
    protected $_redirectUrl = '/';

    public function init()
    {
        $this->_helper->acl->allow(null);
    }
    
    public function indexAction()
    {
        // If user isn't logged in, show login form
        if (null === Zend_Auth::getInstance()->getIdentity()) {
            $this->_helper->redirector->gotoRouteAndExit(array('action'=>'form'));
        } else {
            $this->_redirect('/');
        }
    }

    public function formAction()
    {
        // This is the new login form used after chapter 8 (Forms)
        // It authorises the user via a validator (Places_Validate_Authorise)
        
        $form = new LoginFormDecorated();
        $form->setAction($this->view->baseUrl() .'/auth/form/');
        $this->view->formResponse = '';
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                // Retrieve the auth adapter from Places_Validate_Authorise
                // via the username form element
                $authAdapter = $form->username->getValidator('Authorise')->getAuthAdapter();

                // success: store database row to auth's storage 
                // (Not the password though!)
                $data = $authAdapter->getResultRowObject(null, 
                            'password');
                $auth = Zend_Auth::getInstance();
                $auth->getStorage()->write($data);
                $this->_redirect($this->_redirectUrl);
            } else {
                // ensure that the Auth adapter is cleared
                $auth = Zend_Auth::getInstance();
                $auth->clearIdentity();
        
                $this->view->formResponse = 'Sorry, there was a problem with your submission. Please check the following:';
                // Cheat for the translated bit
                //$this->view->formResponse = 'Entschuldigung, es gab ein Problem mit Ihrer Eingabe. Überprüfen Sie bitte das folgende:';
                $form->populate($_POST);
            }
        } 
        
        $this->view->form = $form;
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('/');
    }
    
    public function loginAction()
    {
        // This is the login form used for chapter 7 (Authorisation and Access Control)
        // It is used in conjunction with identifyAction()
        $flashMessenger = $this->_helper->FlashMessenger;
        $flashMessenger->setNamespace('actionErrors');
        $this->view->actionErrors = $flashMessenger->getMessages();
    }
    
    public function privilegesAction()
    {
        $this->_forward('login');
    }

    public function identifyAction()
    {
        if ($this->getRequest()->isPost()) {
            // collect the data from the user
            $formData = $this->_getFormData();
        
            if (empty($formData['username']) 
                    || empty($formData['password'])) {
                $this->_flashMessage('Please provide a username and password.');
            } else {
                // do the authentication
                $authAdapter = $this->_getAuthAdapter($formData);
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if (!$result->isValid()) {
                    $this->_flashMessage('Login failed');
                } else {
                    // success: store database row to auth's storage 
                    // (Not the password though!)
                    $data = $authAdapter->getResultRowObject(null, 
                                'password');
                    $auth->getStorage()->write($data);

                    $this->_redirect($this->_redirectUrl);
                    return;
                }
            }
        }
        
        $this->_redirect('/auth/login');
    }
    
    protected function _flashMessage($message) {
        $flashMessenger = $this->_helper->FlashMessenger;
        $flashMessenger->setNamespace('actionErrors');
        $flashMessenger->addMessage($message);
    }
    
    /**
     * Retrieve the login form data from _POST
     *
     * @return array
     */
    protected function _getFormData()
    {
        $data = array();
        $filterChain = new Zend_Filter;
        $filterChain->addFilter(new Zend_Filter_StripTags);
        $filterChain->addFilter(new Zend_Filter_StringTrim);
        
        $data['username'] = $filterChain->filter(
            $this->getRequest()->getPost('username'));
        $data['password'] = $filterChain->filter(
            $this->getRequest()->getPost('password'));

        return $data;
    }
    
    /**
     * Set up the auth adapater for interaction with the database
     *
     * @return Zend_Auth_Adapter_DbTable
     */
    protected function _getAuthAdapter($formData)
    {
        $dbAdapter = Zend_Registry::get('db');
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName('users')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('SHA1(?)');

        // get "salt" for better security
        $config = Zend_Registry::get('config');
        $salt = $config->auth->salt;
        $password = $salt.$formData['password'];
        
        $authAdapter->setIdentity($formData['username']);
        $authAdapter->setCredential($password);

        return $authAdapter;
    }
    
}