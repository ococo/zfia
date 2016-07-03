<?php

class ServiceAuth
{
    protected $_database;
    protected $_config;

    public function __construct()
    {
        $this->_database = Zend_Registry::get('db');
        $this->_config = Zend_Registry::get('config');
    }

    /**
    * Returns information about all the blogs a given user is a member of
    * Data is returned as an array of structs containing the blogid, blogName and url of each blog
    *
    * @param string $username Login for the user who's blogs will be retrieved
    * @param string $password a string
    * @return struct
    */
    public function authenticate($username, $password)
    {
        $filterChain = new Zend_Filter;
        $filterChain->addFilter(new Zend_Filter_StripTags)
                    ->addFilter(new Zend_Filter_StringTrim);

        $login = $filterChain->filter($username);
        $password = $filterChain->filter($password);

        $authAdapter = new Zend_Auth_Adapter_DbTable($this->_database);
        $authAdapter->setTableName('users')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('SHA1(?)');

        $authAdapter->setIdentity($login)
                ->setCredential($password);
        $result = $authAdapter->authenticate();
        if (!$result->isValid()) {
           return false; 
        }
        $identity = $authAdapter->getResultRowObject(null, array('password'));
        return $identity;
    }
}