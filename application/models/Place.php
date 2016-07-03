<?php

class Place extends Places_Db_Table_Row_Observable
{

	/**
	 * Pre-insert logic
	 * Automatically insert created_by, date_created and date_updated
	 *
	 */
	protected function _insert()
    {
    	if(is_null($this->created_by)) {
    		$user = Zend_Auth::getInstance()->getIdentity();
    		$this->created_by = (int)$user->id;
    	}
    	
    	if(is_null($this->date_created)) {
    		$this->date_created = date('Y-m-d H:i:s');
    	}
    	
    	if(is_null($this->date_updated)) {
    		$this->date_updated = date('Y-m-d H:i:s');
    	}
    	
    	parent::_insert();
    }
    
    /**
     * Pre-update logic
     * Automatically update date_updated
     * 
     */
    protected function _update()
    {
    	$this->date_updated = date('Y-m-d H:i:s');
    	
    	parent::_update();
    }
    
    public function getSearchIndexFields()
    {
    	$user = $this->findParentRow('Users');
    	
    	$reviews = $this->findReviews();
    	
    	$result = array();
    	$result['class'] = 'Place';
    	$result['key'] = $this->id;
    	$result['title'] = $this->name;
    	$result['contents'] = $this->address();
    	foreach ($reviews as $review) {
    		$result['contents'] .= "\n" . $review->body;
    	}
    	$result['summary'] = $this->address() . "\n" . substr($this->information, 0, 100);
    	$result['createdBy'] = $user->name() . "\n" . $this->information;
    	$result['dateCreated'] = $this->date_created;
    	
    	return $result;
    }
    
    public function address()
    {
    	$address = '';
    	if(!empty($this->address1)) {
    		$address .= $this->address1 . "\n";
    	}
    	if(!empty($this->address2)) {
    		$address .= $this->address2 . "\n";
    	}
    	if(!empty($this->town)) {
    		$address .= $this->town . "\n";
    	}
    	if(!empty($this->county)) {
    		$address .= $this->county . "\n";
    	}
    	if(!empty($this->postcode)) {
    		$address .= $this->postcode . "\n";
    	}
    	if(!empty($this->country)) {
    		$address .= $this->country . "\n";
    	}
    	
    	return $address;
    }
}