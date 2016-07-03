<?php

include_once 'Support/Table.php';
include_once 'Support/Mailer.php';

class Support
{
    public function __construct()
    {
        $this->_supportTable = new Support_Table;
    }

    public function getIssues()
    {
        return $this->_supportTable->fetchAll();
    }

    public function getIssue($id)
    {
        $where  = $this->_supportTable->getAdapter()
                       ->quoteInto('id = ?', $id);
        return $this->_supportTable->fetchRow($where);
    }

    public function saveIssue(array $data, $id = null)
    {
        print_r($data);
        $filterStripTags = new Zend_Filter_StripTags;
        $filterFormat = new Zend_Filter;
        $filterFormat->addFilter(new Zend_Filter_StripTags)
                     ->addFilter(new ThirdParty_Filter_Markdown);

        if (null === $id) {
            $row = $this->_supportTable->createRow();
            $row->date_created   = date('Y-m-d H:i:s');
        } else {
            $row = $this->getIssue($id);
        }

        $row->user_id        = Zend_Auth::getInstance()->getIdentity()->id;
        $row->type           = $filterStripTags->filter($data['type']);
        $row->priority       = (int) $data['priority'];
        $row->status         = $filterStripTags->filter($data['status']);
        $row->title          = $filterStripTags->filter($data['title']);
        $row->body           = $filterStripTags->filter($data['body']);
        $row->body_formatted = $filterFormat->filter($data['body']);
        
        $id = $row->save();
        $mailer = new Support_Mailer($id);
        $mailer->sendMail();
        return $id;
    }

    public function readMail(Zend_Mail_Storage_Abstract $mail)
    {
        foreach ($mail as $messageNum => $message) {
            $part = $message;

            while ($part->isMultipart()) {
                $part = $message->getPart(1);
            }

            if (strtok($part->contentType, ';') == 'text/plain') {
                $plainTextContent = $part->getContent();
            }

            $data['priority'] = isset($message->xPriority) ? $message->xPriority : 3;
            $data['status']   = 'open';
            $data['title']    = $message->subject;
            $data['body']     = $plainTextContent;
            $id = $this->saveIssue($data);

            $messageString = '';
            foreach ($message->getHeaders() as $name => $value) {
                $messageString .= $name . ': ' . $value . "\n";
            }
            $messageString .= "\n" . $message->getContent();
            file_put_contents(getcwd() . '/support/' . $id . '/email.txt',
                          $messageString
            );
        }
    }
}