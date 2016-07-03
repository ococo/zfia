<?php

include_once 'Support.php';

class SupportController extends Zend_Controller_Action
{
    private $_support;

    public function init()
    {
        $this->_support = new Support();
        $this->_helper->_acl->allow('member', null);
    }

    public function indexAction()
    {
        $this->view->issues = $this->_support->getIssues();
    }

    public function addAction()
    {
        $form = new SupportForm('/support/create/');
        $this->view->formResponse = '';
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $id = $this->_support->saveIssue($form->getValues());
                $this->_redirect('/support/edit/id/' . $id . '/');
            } else {
                $this->view->formResponse = 'Sorry, there was a problem with your submission. Please check the following:';
            }
        } 
        $this->view->form = $form;
    }

    public function editAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $issue = $this->view->issue = $this->_support->getIssue($id);

        include_once 'Users.php';
        $this->view->creator = $issue->findParentRow(new Users, 'Support');

        $form = new SupportForm('/support/update/id/' . $id . '/');
        $form->populate($issue->toArray());

        $this->view->formResponse = '';
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $this->_support->saveIssue($form->getValues(), $id);
            } else {
                $this->view->formResponse = 'Sorry, there was a problem with your submission. Please check the following:';
            }
        } 
        $this->view->form = $form;
    }
    
    public function deleteAction()
    {
        die('This action is not implemented');
    }

    public function readAction()
    {
        // This info would likely be stored in config
        $mail = new Zend_Mail_Storage_Pop3(array('host'     => 'mail.example.com',
                                                 'user'     => 'user',
                                                 'password' => 'password'));
        $this->_support->readMail($mail);
        $this->_helper->viewRenderer->setNoRender();
    }
}