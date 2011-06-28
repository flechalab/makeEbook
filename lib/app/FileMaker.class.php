<?php
namespace MakeEbook;

class FileMaker {

    private $html = false;
    private $pdf  = false;

    public function __construct() { }

    /**
     * return html var (string/bin)
     * @return string 
     */
    public function getHtml() {
        return $this->html;
    }

    /**
     * set the html var with data (html/bin)
     * @param string $html 
     */
    public function setHtml($html) {
        $this->html = $html;
    }

    /**
     * print html result
     */
    public function makeHtml() {
        $this->setImgRelPath();
        echo $this->html;
    }
    
    /**
     * use regex to convert the path from imgs to absolute paths (necessary to pdf files)
     */
    public function setImgAbsPath() {
        // removing slash(/) from first position of img path
        $pattern = '/<img([^>].*)src=["\']?[\/]?([^"\']*)["\']?([^>\/].*)?>/i';
        // add abs path (complete path) to img path
        $replace = '<img$1src="'. makeEbook::MAKEEBOOK_ROOT_PATH . makeEbook::MAKEEBOOK_FILESAVE_PATH . '$2" $3 />';
        // exec regex replacement
        $this->html = preg_replace($pattern, $replace, $this->html);
    }

    /**
     * use regex to convert imgs path to relative (html files)
     */
    public function setImgRelPath() {
        // removing slash(/) from first position of img path
        $pattern = '/<img([^>].*)src=["\']?[\/]?([^"\']*)["\']?([^>\/].*)?>/i';
        // setting the img path using actual path
        $replace = '<img$1src="$2" $3 />';
        // exec regex replacement
        $this->html = preg_replace($pattern, $replace, $this->html);
    }

    /**
     * generate file
     * @param string $filename
     */
    public function makeFile($filename) {
        ErrorHandler::set();
        try {
            
            $pathinfo = pathinfo($filename);
            if(!empty($pathinfo['dirname']) && !is_dir($pathinfo['dirname'])) {
                mkdir($pathinfo['dirname'], 0777, TRUE);
            }
            
            $this->setImgRelPath();
            $handle = fopen($filename, 'w');
            fwrite($handle, $this->html);
            fclose($handle);
            chmod($filename, 0777);
        }
        catch (\Exception $e) {
            throw new \Exception('Error to create File. ' . \PHP_EOL . $e->getMessage());
        }
    }

    /**
     * output data into PDF file
     * @param string $filename
     * @param string $title
     * @param string $header
     */
    public function makePdf($filename, $title=false, $header=false) {

        try {
            global $l;
            // create new PDF document
            $this->pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT,
                    true, 'UTF-8', false);

            // set document information
            $this->pdf->SetCreator(PDF_CREATOR);
            $this->pdf->SetAuthor('MakeEbook / Filemaker (Flechatools Libraries) by Flechaweb');
            $this->pdf->SetTitle($title);
            //$this->pdf->SetSubject('TCPDF Tutorial');
            //$this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');

            // set default header data
            //$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING);
            $this->pdf->SetHeaderData('', 0, $title, $header);

            // set header and footer fonts
            $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            //set margins
            $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            //set auto page breaks
            $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //set image scale factor
            $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            //set some language-dependent strings
            $this->pdf->setLanguageArray($l);

            // Set font
            // dejavusans is a UTF-8 Unicode font, if you only need to
            // print standard ASCII chars, you can use core fonts like
            // helvetica or times to reduce file size.
            //
            //$this->pdf->SetFont('dejavusans', '', 14, '', true);
            $this->pdf->SetFont('helvetica', '', 10);

            // Add a page
            // This method has several options, check the source code documentation.
            $this->pdf->AddPage();

            // Set some content to print
            $this->setImgAbsPath();
            $html = $this->html;

            // Print text using writeHTMLCell()
            $this->pdf->writeHTML($html);

            $this->pdf->lastPage();

            // Close and output PDF document
            // This method has several options, check the source code documentation.
            //$this->pdf->Output($filename, 'I');
            $this->pdf->Output($filename, 'F');
        }
        catch(\Exception $e) {
            throw new \Exception("PDF Generation problem ({$e->getMessage()})");
        }
    }
}
