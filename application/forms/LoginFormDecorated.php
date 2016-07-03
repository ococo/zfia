<?php

include_once 'Zend/Form.php';

class LoginFormDecorated extends Zend_Form
{
    public function init()
    {
        $this->setAction('/auth/index/')
             ->setMethod('post')
             ->setAttrib('id', 'loginForm');

        $this->clearDecorators();
        $this->addElementPrefixPath('Places_Validate', 'Places/Validate/', 'validate');

        $decorators = array(
            array('ViewHelper'), 
            array('Errors'),
            array('Label', array('requiredSuffix' => ' *', 'class' => 'leftalign')),
            array('HtmlTag', array('tag' => 'li')),
        );

        $username = $this->addElement('text', 'username', 
            array('label' => 'Username'));
        $username = $this->getElement('username')
                  ->addValidator('alnum')
                  ->setRequired(true)
                  ->addFilter('StringTrim')
                  ->addValidator('Authorise');
        $username->getValidator('alnum')
                 ->setMessage('Your username should include letters and numbers only');
        $username->setDecorators($decorators);

        $password = $this->addElement('password', 'password', 
            array('label' => 'Password'));
        $password = $this->getElement('password')
                  ->addValidator('stringLength', true, array(3))
                  ->setRequired(true)
                  ->addFilter('StringTrim');
        $password->getValidator('stringLength')
                 ->setMessage('Your password is too short');
        $password->setDecorators($decorators);

        $submit = $this->addElement('submit', 'Login');
        $submit = $this->getElement('Login')
                       ->setDecorators(array(
                        array('ViewHelper'),
                        array('HtmlTag', array('tag' => 'li', 'class' => 'submit')),
        ));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'ul')),
            array(array('DivTag' => 'HtmlTag'), 
                array('tag' => 'div', 'id' => 'loginDiv')),           
            'Form'
        ));
    }
}