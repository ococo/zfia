<?php

class Places_Filter_Word implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns Content with those nasty Word characters stripped
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $final = ''; 
        $search = array(chr(145),chr(146),chr(147),chr(148),chr(150),chr(151)); 
        $replace = array("'","'",'&quot;','&quot;','&ndash;','&ndash;'); 

        $hold = str_replace($search[0],$replace[0],$value); 
        $hold = str_replace($search[1],$replace[1],$hold); 
        $hold = str_replace($search[2],$replace[2],$hold); 
        $hold = str_replace($search[3],$replace[3],$hold); 
        $hold = str_replace($search[4],$replace[4],$hold); 
        $hold = str_replace($search[5],$replace[5],$hold); 

        $holdarr = str_split($hold); 
        foreach ($holdarr as $val) { 
             if (ord($val) < 128) $final .= $val; 
        } 
        return $final; 
    }
}