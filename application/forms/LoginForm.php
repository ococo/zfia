<?php

include_once 'Zend/Form.php';

class LoginForm extends Zend_Form
{
    protected $_actionUrl;

    public function __construct($actionUrl = null, $options=null)
    {
        parent::__construct($options);
        $this->setActionUrl($actionUrl);
        $this->init();
    }

    public function setActionUrl($actionUrl) {
        $this->_actionUrl = $actionUrl;
        return $this;
    }

    public function init()
    {
        $this->setAction($this->_actionUrl)
             ->setMethod('post')
             ->setAttrib('id', 'registerform');

        $this->clearDecorators();
        $this->addElementPrefixPath('Places_Validate', 'Places/Validate/', 'validate');
        $decorators = array(
            array('ViewHelper'), 
            array('Description'),
            array('Errors'),
            array('Label', array('separator'=>' ', 'requiredSuffix' => ' *', 'class' => 'lefty')),
            array('HtmlTag', array('tag' => 'li')),
        );

        $username = $this->addElement('text', 'username', 
            array('label' => 'Username'));
        $username = $this->getElement('username')
                 ->addValidator('usernameUnique', true, 
                    array('id','username','id','Users'))
                 ->addValidator('alnum')
                 ->setRequired($required)
                 ->addFilter('StringToLower');
        $username->getValidator('alnum')->setMessage('Your username must be letters and digits only');
        $username->setDecorators($decorators);

        $password = new Zend_Form_Element_Password('password', 
            array('label' => 'Password'));
        $password->setDescription('Password must be longer than 6 characters and include letters and numbers')
                 ->addValidator('stringLength', true, array(6))
                 ->addValidator('regex', true, array('/^(?=.*\d)(?=.*[a-zA-Z]).{6,}$/'))
                 ->setRequired($required);
        $password->getValidator('stringLength')->setMessage('Password is too short');
        $password->getValidator('regex')->setMessage('Password does not contain letters and numbers');
        $password->setDecorators($decorators);
        $this->addElement($password);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Login');
        $submit->clearDecorators();
        $submit->addDecorators(array(
            array('ViewHelper'),    // element's view helper
            array('HtmlTag', array('tag' => 'li', 'class' => 'submit')),
        ));

        $this->addElement($submit);

        $this->addDecorator('FormElements')
             ->addPrefixPath('Places_Form_Decorator', 'Places/Form/Decorator', 'decorator')
             ->addDecorator(array('ListWrapper' => 'HtmlTag'), array('tag' => 'ul'))
             ->addDecorator('Form', array('class' => 'form'));
    }
}
