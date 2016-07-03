<?php

class ReviewController extends Zend_Controller_Action
{
    public $ajaxable = array('feedback'=> array('json'));
    function init()
    {
        $readActions = array('index' , 'feedback' , 'add');
        $writeActions = array();
        $this->_helper->_acl->allow('member', $readActions);
        $this->_helper->_acl->allow('admin', $writeActions);
        
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        //$ajaxContext->addActionContext('feedback', 'json');
        $ajaxContext->initContext();
    }
    
    public function addAction()
    {
    	$userId = (int)Zend_Auth::getInstance()->getIdentity()->id;
    	$users = new Users();
    	$user = $users->fetchRow('id='.$userId);
    	
        $this->view->messages = array();
        $redirector = $this->_helper->getHelper('Redirector'); /* @var $redirector Zend_Controller_Action_Helper_Redirector */
        
        $placeId = (int) $this->getRequest()->getParam('placeId');
        $this->view->placeId = $placeId;
        
        if ($this->view->placeId > 0) {
            $places = new Places();
            $place = $places->fetchRow("id = $placeId");
            $this->view->place = $place;
        }
        
        if ($this->getRequest()->isPost()) {
            $submitAction = $this->getRequest()->getPost('submitAction', 'cancel');
            
            switch (strtolower($submitAction)) {
                case 'save':
                    
                    $filters = array('*' => array('StringTrim' , 'StripTags'));
                    $validators = array(
                            'review' => array('NotEmpty', 
                                    'messages'=>array(Zend_Validate_NotEmpty::IS_EMPTY => 'Please provide a review!')),
                            'rating' => array('Digits', 
                                    'messages'=>array(Zend_Validate_Digits::STRING_EMPTY => 'Please provide a rating!',
                                        Zend_Validate_Digits::NOT_DIGITS => 'Please provide a rating!'))
                    );
                    $input = new Zend_Filter_Input($filters, $validators, $_POST);
                    
                    if ($input->isValid()) {
                    	
                    	$reviews = new Reviews();
                    	$newReview = $reviews->createRow();
                    	
                    	$newReview->place_id = $placeId;
                    	$newReview->body = $input->getUnescaped('review');
                    	$newReview->rating = $input->getUnescaped('rating');
                    	
                    	$newReview->save();
                    	
                        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
                        $flashMessenger->addMessage('Thank you for your review!');
                        
                        $redirector->goto('index', 'place', null, array('id' => $placeId));
                        
                    } else {
                        // failed - fall through and redisplay theform
                        $this->view->messages = $input->getMessages();
                        
                        $this->view->review = $input->getUnescaped('review');
                        $this->view->rating = $input->getUnescaped('rating');
                    }
                    break;
                    
                default:
                    // cancel
                    $redirector->goto('index', 'place', null, array('id' => $placeId));
                            }
        } else {
        	// set up default values for form fields
        	$this->view->rating = null;
        	$this->view->review = null;
        }
        
        $this->view->username = $user->name();
        
        
    }

    public function feedbackAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id == 0) {
            $this->view->result = false;
            return;
        }
        
        $helpful = (int)$this->getRequest()->getParam('helpful');
        $helpful = $helpful == 0 ? 0 : 1; //ensure is only 0 or 1
        
        $reviewsFinder = new Reviews();
        $review = $reviewsFinder->fetchRow('id='.$id);
        if ($review->id != $id) {
            $this->view->result = false;
            return;
        }

        if ($helpful) {
            $sql = "Update reviews SET helpful_yes = (helpful_yes+1), 
                    helpful_total = (helpful_total+1) 
                    WHERE id = $id";
        } else {
            $sql = "Update reviews SET helpful_total = (helpful_total+1) 
                    WHERE id = $id";
        }
        $reviewsFinder->getAdapter()->query($sql);
        
        $review = $reviewsFinder->fetchRow('id='.$id);
        
        $this->view->result = true;
        $this->view->id = $id;
        $this->view->helpful_yes = $review->helpful_yes;
        $this->view->helpful_total = $review->helpful_total;
    }
    
}