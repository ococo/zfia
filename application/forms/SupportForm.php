<?php

include_once 'Zend/Form.php';

class SupportForm extends Zend_Form
{
    protected $_actionUrl;
    protected $_priorities = array(
        1 =>'Highest',
        2 =>'High',
        3 =>'Normal',
        4 =>'Low',
        5 =>'Lowest'
    );

    protected $_types = array(
        'bug'     => 'Bug',
        'feature' => 'Feature'
    );

    protected $_status = array(
        'open'     => 'Open',
        'resolved' => 'Resolved',
        'on hold'  => 'On Hold'
    );

    public function __construct($actionUrl, $options=null)
    {
        parent::__construct($options);
        $this->_actionUrl = $actionUrl;
    }

    public function init()
    {
        $this->setAction($this->_actionUrl)
             ->setMethod('post')
             ->setAttrib('id', 'loginForm');

        $this->clearDecorators();
        $decorators = array(
            array('ViewHelper'), 
            array('Errors'),
            array('Label', array('requiredSuffix' => ' *', 'class' => 'leftalign')),
            array('HtmlTag', array('tag' => 'li')),
        );

        $type = $this->addElement('select', 'type', 
            array('label' => 'Type'));
        $type = $type->getElement('type')
                     ->setMultiOptions($this->_types);
        $type->setDecorators($decorators);

        $priority = $this->addElement('select', 'priority', 
            array('label' => 'Priority'));
        $priority = $priority->getElement('priority')
                             ->setMultiOptions($this->_priorities);
        $priority->setDecorators($decorators);

        $status = $this->addElement('select', 'status', 
            array('label' => 'Status'));
        $status = $status->getElement('status')
                         ->setMultiOptions($this->_status);
        $status->setDecorators($decorators);

        $title = $this->addElement('text', 'title', 
            array('label' => 'Title', 'size' => 25 ));
        $title->getElement('title')
              ->setDecorators($decorators);

        $body = $this->addElement('textarea', 'body', 
            array(
                'label' => 'Body',
                'rows' => 13,
                'cols' => 40,
        ));
        $body->getElement('body')
             ->setDecorators($decorators);

        $submit = $this->addElement('submit', 'Submit');
        $submit = $this->getElement('Submit')
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