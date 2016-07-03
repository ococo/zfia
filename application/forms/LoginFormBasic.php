<?php

include_once 'Zend/Form.php';

class LoginFormBasic extends Zend_Form
{
    public function init()
    {
        $this->setAction('/auth/index/')
             ->setMethod('post')
             ->setAttrib('id', 'loginForm');

        $username = $this->addElement('text', 'username', 
            array('label' => 'Username'));

        $password = $this->addElement('password', 'password', 
            array('label' => 'Password'));

        $submit = $this->addElement('submit', 'Login'); 
    }
}
