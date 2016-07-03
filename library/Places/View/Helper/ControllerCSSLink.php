<?php

class Places_View_Helper_ControllerCSSLink
{
    protected $_view;
    
    function setView($view)
    {
        $this->_view = $view;
    }
    
    function controllerCSSLink()
    {
        $result = '';
        $controller = $this->_view->originalController;
        
        // screen
        $file = ROOT_DIR . '/web_root/css/' . $controller . '.css';
        if (file_exists($file)) {
            $url = $this->_view->baseUrl . '/css/' . $controller . '.css';
            $result .= '<link rel="stylesheet" type="text/css" media="screen,projection" href="'.$url.'" />' . PHP_EOL; 
        }

        // print
        $file = ROOT_DIR . '/web_root/css/' . $controller . '.print.css';
        if (file_exists($file)) {
            $url = $this->_view->baseUrl . '/css/' . $controller . '.print.css';
            $result .= '<link rel="stylesheet" type="text/css" media="print" href="'.$url.'" />' . PHP_EOL;
        }
        return $result;
    }
}
