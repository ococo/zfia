<?php

require_once 'Zend/Service/Amazon/Query.php';

class Amazon
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = Zend_Registry::get('config')->amazon_api_key;
    }

    public function search($keywords)
    {
        // If we've no api key it's a no go
        if(empty($this->apiKey)) {
            return null;
        }

        try {
            $query = new Zend_Service_Amazon_Query($this->apiKey, 'UK');
            $query->category('Books')
                ->Keywords($keywords)
                ->ResponseGroup('Small,ItemAttributes,Images');
            $results = $query->search();
            if ($results->totalResults() > 0) {
                return $results;
            }
        } catch (Zend_Service_Exception $e) {
            return null;
        }
        return null;
    }
}