<?php

include_once 'Zend/Form.php';

class LoginFormValidatedFiltered extends Zend_Form
{
    public function init()
    {
        $this->setAction('/auth/index/')
             ->setMethod('post')
             ->setAttrib('id', 'loginForm');

        $username = $this->addElement('text', 'username', 
            array('label' => 'Username'));
        $username = $this->getElement('username')
                  ->addValidator('alnum')
                  ->setRequired(true)
                  ->addFilter('StringTrim');

        $password = $this->addElement('password', 'password', 
            array('label' => 'Password'));
        $password = $this->getElement('password')
                  ->addValidator('stringLength', true, array(6))
                  ->setRequired(true)
                  ->addFilter('StringTrim');

        $submit = $this->addElement('submit', 'Login'); 
    }
}