<?php

class RestController extends Zend_Controller_Action 
{
    protected $_server;
    protected $_application_directory;

    public function init()
    {
        $this->_server = new Zend_Rest_Server();
        $this->_helper->viewRenderer->setNoRender();
        //$this->_application_directory = Zend_Registry::get('application_directory');
        $this->_helper->acl->allow(null);
    }

    public function indexAction()
    {
        $writer = new Zend_Log_Writer_Stream(ROOT_DIR . '/logs/rest.log');
        $logger = new Zend_Log($writer);
        $headers = apache_request_headers();
        $headerString = '---------------------------' . "\n\n";
        foreach ($headers as $header => $value) {
            $headerString .= "$header: $value\n";
        }
        foreach($_REQUEST as $request) {
            $headerString .= 'Request: ' . $request. "\n";
        }
        $logger->info($headerString);
        require_once 'ServicePlaces.php';
        $this->_server->setClass('ServicePlaces');
        $this->_server->handle();
    }

    public function clientAction()
    {
        //$atom = new Places_Service_Atom;
        $server = 'http://places/rest/?method=getPlace';
        $client = new Zend_Rest_Client($server);

        $response = $client->id('6')->get();
        if ($response->isSuccess()) {
            var_dump($response);
            echo 'The name is ' . $response->name;
        } else {
            echo 'Failed';
        }

    }

    public function akismetAction()
    {
        $server = 'http://rest.akismet.com/1.1/verify-key';
        $data = array(
            'key'  => 'f99ad602d555',
            'blog' => 'http://ingredients.com.au/nick'
        );

        $client = new Zend_Http_Client($server);
        $client->setParameterPost($data);
        $response = $client->request(Zend_Http_Client::POST);
        var_dump($response);

        $client = new Zend_Rest_Client('http://rest.akismet.com');
        try {
            $response = $client->restPost('/1.1/verify-key', $data);
            var_dump($response);
        } catch (Zend_Rest_Client_Exception $e) {
            echo $e->getCode() . ': ' . $e->getMessage() . "\n";
        }

    }
}