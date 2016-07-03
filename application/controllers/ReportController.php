<?php

include_once 'Report/Document.php';
include_once 'Report/Page.php';

class ReportController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->acl->allow(null);
    }
    
    /**
     * This action builds and outputs a PDF report
     */
    public function indexAction()
    {

        $report = new Report_Document;

        $page1 = new Report_Page;
        $page1->setYear(2008);
        $page1->setHeadTitle('Places reviews');
        $page1->setIntroText('This is the report for reviews. Reviews are very important to Places to take the kids because they are an indication not only of how many people are reading the places information but of how confident users are in the community element of the site.');

        // Fictitious data
        $page1->setGraphData(array(30, 25, 60, 90, 10, 45, 80, 30, 80, 20, 0, 0));
        $report->addPage($page1);

        $page2 = new Report_Page;
        $page2->setYear(2008);
        $page2->setHeadTitle('Submitted Articles');
        $page2->setIntroText("This is the report for user submitted articles.  Articles are currently submitted via email from the member's pages but are accredited to the submitter when published.");

        // Fictitious data
        $page2->setGraphData(array(3, 5, 6, 9, 10, 15, 10, 13, 20, 20, 0, 0));
        $report->addPage($page2);

        header('Content-Type: application/pdf; charset=UTF-8');
        echo $report->getDocument()->render();

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }
}