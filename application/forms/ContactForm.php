<?php

include_once 'Zend/Form.php';

class ContactForm extends Zend_Form
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
             ->setAttrib('id', 'contactform');

        $this->clearDecorators();
        $this->addElementPrefixPath('Places_Validate', 'Places/Validate/', 'validate');

        $decorators = array(
            array('ViewHelper'),    // element's view helper
            array('Errors'),
            array('Description'),   // description field
            array('Label', array('separator'=>' ', 'requiredSuffix' => ' *', 'class' => 'lefty')),
            array('HtmlTag', array('tag' => 'li')),
        );

        $email = $this->addElement('text', 'email', 
            array('label' => 'Email Address', 'size' => '30'));
        $email->addValidator('emailAddress')
              ->setRequired(true);
        $email->setDecorators($decorators);
        $this->addElement($email);

        $subject = $this->addElement('text', 'subject', 
            array('label' => 'Subject', 'size' => '50', 'maxlength' => 255));
        $subject->addFilter('stripTags');
        $subject->setDescription('')
                ->addValidator('notEmpty')
                ->setRequired(true);
        $subject->getValidator('notEmpty')->setMessage('You are missing a subject');
        $subject->setDecorators($decorators);
        $this->addElement($subject);

        $body = $this->addElement('textarea','body', 
            array('label' => 'Message', 'cols' => 60, 'rows' => 15))->body;
        $body->addFilter('stripTags')
             ->addValidator('notEmpty')
             ->setRequired(true);
        $body->getValidator('notEmpty')->setMessage('You need to add a message');
        $subject->setDecorators($decorators);
        $this->addElement($body);

        $idHidden = $this->addElement('hidden','id')->id; 
        $idHidden->addFilter('int');
        $idHidden->clearDecorators();
        $idHidden->addDecorators(array(
            array('ViewHelper'),    // element's view helper
        ));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Send Message');
        $submit->clearDecorators();
        $submit->addDecorators(array(
            array('ViewHelper'),    // element's view helper
            array('HtmlTag', array('tag' => 'li', 'class' => 'submit')),
        ));

        $this->addElement($idHidden);
        $this->addElement($submit);

        $this->addDecorator('FormElements')
             ->addPrefixPath('Places_Form_Decorator', 'Places/Form/Decorator', 'decorator')
             ->addDecorator(array('ListWrapper' => 'HtmlTag'), array('tag' => 'ul'))
             ->addDecorator('Form', array('class' => 'clientForm'));
    }
}