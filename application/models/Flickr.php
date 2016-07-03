<?php

require_once 'Zend/Service/Flickr.php';

class Flickr
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = Zend_Registry::get('config')->flickr_api_key;
    }

    public function search($keywords, $amount=6)
    {
        // If we've no api key it's a no go
        if(empty($this->apiKey)) {
            return null;
        }

        try {
            $flickr = new Zend_Service_Flickr($this->apiKey);
            $results = $flickr->tagSearch($keywords,
                array(
                    'per_page' => $amount,
                    'tag_mode' => 'all',
                    'license'  => 3
                ));

            if ($results->totalResults() > 0) {
                return $results;
            }
        } catch (Zend_Service_Exception $e) {
            return null;
        }
        return null;
    }
}