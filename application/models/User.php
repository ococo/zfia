<?php

class User extends Places_Db_Table_Row_Observable
{

	/**
	 * Pre-insert logic
	 * Automatically insert created_by, date_created and date_updated
	 *
	 */
	protected function _insert()
    {
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
    
    /**
     * Return the name of the user who created this record.
     *
     * @return string
     */
    public function name()
    {
        $name = trim($this->first_name . ' ' . $this->last_name); 
        if (empty($name)) { 
           $name = $this->username;  
        } 
        return $name; 
    }
    
}