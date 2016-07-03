<?php

//require_once 'Amazon.php';

class Zend_View_Helper_AmazonAds
{
    protected $cache;
    protected $cacheDir;

    public function __construct()
    {
        $this->cacheDir = ROOT_DIR . Zend_Registry::get('config')->cacheDir;

        $frontendOptions = array(
           'lifetime' => 300,
           'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => $this->cacheDir
        );

        // getting a Zend_Cache_Core object
        $this->cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    }

    public function amazonAds($keywords, $amount=3, $elementId='amazonads')
    {
        // Try loading the cached version
        if(!$xhtml = $this->cache->load('amazon')) {
            // Cache miss: query Amazon
            $amazon = new Amazon;
            $results = $amazon->search($keywords);
            // If we get no results stop here
            if(null === $results) {
                return null;
            }

            $xhtml = '<ul id=' . $elementId . '>';
            foreach ($results as $key => $result) {
                $xhtml .= '<li>';
                $xhtml .= '<a href="' . $result->DetailPageURL . '">';
                $xhtml .= '<img src="' . $result->SmallImage->Url 
                    . '" width="' . $result->SmallImage->Width
                    . '" height="' . $result->SmallImage->Height
                    . '" alt="Cover for ' . $result->Title . '" />';
                $xhtml .= '</a>';
                $xhtml .= '<a href="' . $result->DetailPageURL
                    . '" title="Amazon page for ' . $result->Title . '">'
                    . $result->Title . '</a>';
                $xhtml .= '</li>';
                if(intval($amount) == $key) {
                    break;
                }
            }
            $xhtml .= '</ul>';
            $this->cache->save($xhtml, 'amazon');
        }
        return $xhtml;
    }
}