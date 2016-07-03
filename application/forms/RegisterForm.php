<?php

include_once 'Zend/Form.php';

class RegisterForm extends Zend_Form
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
        $required = true;
        
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
            array('label' => 'Username'))->username;
        $username = $this->getElement('username')
                 ->addValidator('usernameUnique', true, 
                    array('id','username','id','Users'))
                 ->addValidator('alnum')
                 ->setRequired($required)
                 ->addFilter('StringToLower');
        $username->getValidator('alnum')->setMessage('Your username must be letters and digits only');
        $username->setDecorators($decorators);

        $password = $this->addElement('password', 'password', 
            array('label' => 'Password'))->password;
        $password->setDescription('Password must be longer than 6 characters and include letters and numbers')
                 ->addValidator('stringLength', true, array(6))
                 ->addValidator('regex', true, array('/^(?=.*\d)(?=.*[a-zA-Z]).{6,}$/'))
                 ->setRequired($required);
        $password->getValidator('stringLength')->setMessage('Password is too short');
        $password->getValidator('regex')->setMessage('Password does not contain letters and numbers');
        $password->setDecorators($decorators);

        $firstName = $this->addElement('text', 'first_name', 
            array('label' => 'First Name', 'size' => '30'))->first_name;
        $firstName->setRequired(true)
                  ->addFilter('stripTags');

        $lastName = $this->addElement('text', 'last_name', 
            array('label' => 'Last Name', 'size' => '30'))->last_name;
        $lastName->setRequired(true)
                 ->addFilter('stripTags');
        $lastName->setDecorators($decorators);

        $preferredName = $this->addElement('text', 'preferred_name', 
            array('label' => 'Preferred Name', 'size' => '30'))->preferred_name;
        $preferredName->setDescription('e.g. Joe Brown rather than Joseph Brown')
                      ->setRequired(true)
                      ->addFilter('stripTags');
        $preferredName->setDecorators($decorators);

        $email = $this->addElement('text', 'email', 
            array('label' => 'Email Address', 'size' => '30'))->email;
        $email->addValidator('emailAddress');
        $email->setDecorators($decorators);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Sign Me Up');
        $submit->clearDecorators();
        $submit->addDecorators(array(
            array('ViewHelper'),    // element's view helper
            array('HtmlTag', array('tag' => 'li', 'class' => 'submit')),
        ));
        $this->addElement($submit);

        $this->addDecorator('FormElements')
             ->addPrefixPath('Places_Form_Decorator', 'Places/Form/Decorator', 'decorator')
             ->addDecorator(array('ListWrapper' => 'HtmlTag'), array('tag' => 'ul'))
             ->addDecorator('Form', array('class' => 'clientForm'));
    }
}