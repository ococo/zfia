<?php

require_once ('Zend/Db/Table/Row/Abstract.php');

class Places_Db_Table_Row_Observable extends Zend_Db_Table_Row_Abstract
{
    /**
     * @var array Array of observers
     */
    protected static $_observers = array();

    /**
     * Attach an observer class
     *
     * Allows observation of pre/post insert/update/delete events.
     *
     * Expects a valid class name; that class must have a public 
     * static method 'observeTableRow' that accepts two arguments: 
     *      * string $eventname
     *      * Places_Db_Table_Row_Observable $row  
     *
     * @param string $class
     * @return boolean
     */
    public static function attachObserver($class)
    {
        if (!is_string($class) || !class_exists($class) || !is_callable(array($class , 'observeTableRow'))) {
            return false;
        }
        
        if (!isset(self::$_observers[$class])) {
            self::$_observers[$class] = true;
        }
        
        return true;
    }

    /**
     * Detach an observer
     *
     * @param string $class
     * @return boolean
     */
    public static function detachObserver($class)
    {
        if (!isset(self::$_observers[$class])) {
            return false;
        }
        
        unset(self::$_observers[$class]);
        return true;
    }

    protected function _notifyObservers($event)
    {
        if (!empty(self::$_observers)) {
            foreach (array_keys(self::$_observers) as $observer) {
                call_user_func(array($observer , 'observeTableRow'), $event, $this);
            }
        }
        parent::_postInsert();
    }

    protected function _insert()
    {
        $this->_notifyObservers('pre-insert');
    }

    protected function _postInsert()
    {
        $this->_notifyObservers('post-insert');
    }

    protected function _update()
    {
        $this->_notifyObservers('pre-update');
    }

    protected function _postUpdate()
    {
        $this->_notifyObservers('post-update');
    }

    protected function _delete()
    {
        $this->_notifyObservers('pre-delete');
    }

    protected function _postDelete()
    {
        $this->_notifyObservers('post-delete');
    }

}

