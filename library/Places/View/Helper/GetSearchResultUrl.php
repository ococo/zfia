<?php

class Places_View_Helper_GetSearchResultUrl
{
    protected $_view;
    
    function setView($view)
    {
        $this->_view = $view;
    }
    
    function getSearchResultUrl($class, $id)
    {
        $baseUrl = $this->_view->baseUrl;
        
        switch($class) {
//            case 'Review':
//            	$reviews = new Reviews;
//            	$review = $reviews->fetchRow('id='.$id);
//            	$placeId = $review->place_id;
//                $url = $baseUrl . '/place/index/id/' . $placeId . '#' . $id;
//                break;

            case 'Place':
            default: 
                $url = $this->_view->url(array('controller'=>$class, 
                        'action'=>'index', 'id'=>$id));
                break;
        }
        return $url;        
        
    }
}
