<?php

require_once 'Zend/Date.php';

class Report_Page
{
    protected $_page;
    protected $_pageWidth;
    protected $_pageHeight;
    protected $_yPosition;
    protected $_leftMargin;
    protected $_normalFont;
    protected $_boldFont;
    protected $_year;
    protected $_headTitle;
    protected $_introText;
    protected $_graphData;

    public function __construct()
    {
        $this->_page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $this->_pageWidth = $this->_page->getWidth(); 
        $this->_pageHeight = $this->_page->getHeight();
        $this->_normalFont = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $this->_boldFont = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $this->_yPosition = 60;
        $this->_leftMargin = 50;
    }

    public function setStyle()
    {
        $style = new Zend_Pdf_Style();
        $style->setFillColor(new Zend_Pdf_Color_Html('#333333'));
        $style->setLineColor(new Zend_Pdf_Color_Html('#990033'));
        $style->setLineWidth(1);
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
        $this->_page->setStyle($style);
    }

    public function setYear($year = null)
    {
        if (null == $year) {
            $year = date('Y');
        }
        $this->_year = $year;
    }

    public function setHeadTitle($title)
    {
        $this->_headTitle = $title;
    }

    public function setIntroText($text)
    {
        $this->_introText = $text;
    }

    public function setGraphData(array $data)
    {
        $this->_graphData = $data;
    }


    public function setHeader()
    {
        $this->_page->saveGS();
        $this->_page->setFont($this->_boldFont, 20);
        $this->_page->drawText($this->_headTitle, $this->_leftMargin, $this->_pageHeight - 50);
        $this->_page->drawLine($this->_leftMargin, $this->_pageHeight - 60, $this->_pageWidth -$this->_leftMargin, $this->_pageHeight - 60);
        $this->_page->restoreGS();
    }

    public function wrapText($text)
    {
        $wrappedText = wordwrap($text, 110, "\n", false);
        $token = strtok($wrappedText, "\n");
        $this->_yPosition = $this->_pageHeight - 80;

        while ($token !== false) {
            $this->_page->drawText($token, $this->_leftMargin, $this->_yPosition);
            $token = strtok("\n");
            $this->_yPosition -= 15;
        }
    }

    public function getGraphSection()
    {
        $this->_page->setFont($this->_boldFont, 16);
        $this->_yPosition -= 20;
        $this->_page->drawText('Monthly statistics for ' . $this->_year, $this->_leftMargin, $this->_yPosition);
        $this->_yPosition -= 10;
        $this->_page->drawLine($this->_leftMargin, $this->_yPosition, $this->_pageWidth - $this->_leftMargin, $this->_yPosition);

        $graphX = 50;
        $this->_yPosition -= 40;
        $graphY = $this->_yPosition - max($this->_graphData);
        $columnWidth = 40;

        $date = new Zend_Date();

        $this->_page->saveGS();
        foreach ($this->_graphData as $key => $value ) {
            $graphFill = $key % 2 == 1 ? '#FA9300' : '#990033';
            $this->_page->setFillColor(new Zend_Pdf_Color_Html($graphFill));
            $this->_page->setLineColor(new Zend_Pdf_Color_Html($graphFill));
            $this->_page->drawText($value, $graphX + ($columnWidth/3), $graphY + $value);
            $this->_page->drawRectangle($graphX, $graphY, $graphX + $columnWidth, $graphY + $value);
            $date->set($key + 1 ,Zend_Date::MONTH_SHORT);
            $yPosition = $graphY - 20;
            $this->_page->drawText($date->get(Zend_Date::MONTH_NAME_SHORT), $graphX + ($columnWidth/8), $yPosition);
            $graphX += $columnWidth;
        }
        $this->_page->restoreGS();
        $this->_yPosition = $yPosition - 20;
    }

    public function getNotesSection()
    {
        $this->_yPosition -= 20;
        $this->_page->drawText('Meeting Notes', $this->_leftMargin, $this->_yPosition);
        $this->_yPosition -= 10;
        $this->_page->drawLine($this->_leftMargin, $this->_yPosition, $this->_pageWidth - $this->_leftMargin, $this->_yPosition);

        $noteLineHeight = 30;
        $this->_yPosition -= $noteLineHeight;

        $this->_page->saveGS();
        $this->_page->setLineColor(new Zend_Pdf_Color_Html('#999999'));
        $this->_page->setLineWidth(0.5);
        $this->_page->setLineDashingPattern(array(2, 2, 2, 2));
        while($this->_yPosition > 70) {
            $this->_page->drawLine($this->_leftMargin, $this->_yPosition, $this->_pageWidth - $this->_leftMargin, $this->_yPosition);
            $this->_yPosition -= $noteLineHeight;
        }
        $this->_page->restoreGS();
    }

    public function setFooter()
    {
        $this->_page->drawLine($this->_leftMargin, 60, $this->_pageWidth - $this->_leftMargin, 60);
        $this->_page->setFont($this->_boldFont, 12);
        $this->_page->drawText('© ' . date('Y') . ' Places to take the kids', $this->_leftMargin, 40);
    }

    public function render()
    {
        $this->setStyle();
        $this->setHeader();
        $this->wrapText($this->_introText);
        $this->getGraphSection();
        $this->getNotesSection();
        $this->setFooter();
        return $this->_page;
    }
}