<?php

class Places_View_Helper_BaseUrl
{
    function baseUrl()
    {
        $fc = Zend_Controller_Front::getInstance();
        return $this->_baseUrl =  $fc->getBaseUrl();
    }
}
