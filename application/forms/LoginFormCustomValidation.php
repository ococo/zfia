<?php

include_once 'Zend/Form.php';

class LoginFormCustomValidation extends Zend_Form
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
                  ->addFilter('StringTrim')
                  ->addPrefixPath('Places_Validate', 'Places/Validate/', 'validate')
                  ->addValidator('Authorise');
        $username->getValidator('alnum')->setMessage('Your username should include letters and numbers only');

        $password = $this->addElement('password', 'password', 
            array('label' => 'Password'));
        $password = $this->getElement('password')
                  ->addValidator('stringLength', true, array(4))
                  ->setRequired(true)
                  ->addFilter('StringTrim');
        $password->getValidator('stringLength')->setMessage('Your password is too short');

        $submit = $this->addElement('submit', 'Login'); 
    }
}