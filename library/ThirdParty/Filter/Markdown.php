<?php

/**
 * Class Markdown
 * @author Nick Lo
 */

require_once 'Zend/Filter/Interface.php';
include 'MarkdownExtra.php';

class ThirdParty_Filter_Markdown implements Zend_Filter_Interface
{   
    public function filter($value)
    {
        $parser = new MarkdownExtra_Parser; 
        $valueFiltered = $parser->transform($value);
        return $valueFiltered;    
    }
}