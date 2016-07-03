<?php
class Places_Validate_Authorise extends Zend_Validate_Abstract
{
    const NOT_AUTHORISED = 'notAuthorised';

    protected $_authAdapter;
    protected $_messageTemplates = array(
        self::NOT_AUTHORISED => 'No users with those details exist'
    );

    public function getAuthAdapter()
    {
        return $this->_authAdapter;
    }

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (!isset($context['password'])) {
                return false;
            }
        }

        $dbAdapter = Zend_Registry::get('db');
        $this->_authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $this->_authAdapter->setTableName('users')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password');

        // get "salt" for better security
        $config = Zend_Registry::get('config');
        $salt = $config->auth->salt;
        $password = sha1($salt.$context['password']);
        
        $this->_authAdapter->setIdentity($value);
        $this->_authAdapter->setCredential($password);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($this->_authAdapter);
        if (!$result->isValid()) {
            $this->_error(self::NOT_AUTHORISED);
            return false;
        }
        return true;
    }
}