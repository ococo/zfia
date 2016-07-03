<?php

class Review extends Places_Db_Table_Row_Observable
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

    	if(is_null($this->helpful_yes)) {
    		$this->helpful_yes = 0;
    		$this->helpful_total = 0;
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

    
    /**
     * Return the name of the user who created this record.
     *
     * @return string
     */
    public function author()
    {
        $user = $this->findParentRow('Users');
        return $user->name();
    }

    public function getSearchIndexFields()
    {
    	$place = $this->findParentRow('Places');
    	$user = $this->findParentRow('Users');

    	$result = array();
        $result['class'] = 'Review';
        $result['key'] = $this->id;
    	$result['title'] = 'User review of ' . $place->name;
    	$result['contents'] = $this->body;
    	$result['summary'] = $this->body;

    	if(strlen($result['summary']) > 100) {
    	   $result['summary'] = substr($result['summary'], 0, 100) . '...';
    	}
    	$result['createdBy'] = $user->name();
    	$result['dateCreated'] = $this->date_created;
    	return $result;
    }
}