<?php

include_once 'Table.php';

class Support_Mailer
{
    public $supportId;
    
    public function __construct($supportId)
    {
        $this->supportId = intval($supportId);
    }

    function sendMail($html=false)
    {
        $supportTable = new Support_Table;
        $supportIssue = $supportTable->find($this->supportId);
        $mail = new Zend_Mail();

        if ($html) {
            $mail->setBodyText($supportIssue->current()->body_formatted);
        }
        $mail->setBodyText($supportIssue->current()->body);
        

        $mail->setFrom(
            Zend_Registry::get('config')->support->email,
            Zend_Registry::get('config')->support->name
        );
        // This is where we'd set who to notify
        $mail->addTo('somewhere@example.com', '?');
        $mail->setSubject(strtoupper($supportIssue->current()->type) . ': ' . $supportIssue->current()->title);
        $mail->addHeader('X-Priority', $supportIssue->current()->priority, true);
        $mail->send();
    }
}