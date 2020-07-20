<?php

namespace Drewlabs\Core\Dompdf;

use Dompdf\Dompdf as PHPDomPdf;
use Dompdf\Options;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @package [[Drewlabs\Core\Dompdf]]
 */
class Dompdf implements DomPdfable {


    /**
     * PHP DomPdf instance
     *
     * @var PHPDomPdf
     */
    private $dompdf;

    /**
     * Undocumented variable
     *
     * @var boolean
     */
    private $rendered = false;

    /**
     * @param PHPDomPdf $dompdf
     */
    public function __construct(PHPDomPdf $dompdf){
        $this->dompdf = $dompdf;
    }


    /**
     * Get PHP DomPdf instance
     *
     * @return PHPDomPdf
     */
    public function getDOMPdfProvider(){
        return $this->dompdf;
    }

    /**
     * Set the paper size (default A4)
     *
     * @param string $paper
     * @param string $orientation
     * @return $this
     */
    public function setPaperOrientation($paper, $orientation = 'portrait'){
        $this->dompdf->setPaper($paper, $orientation);
        return $this;
    }


    /**
     * Load a HTML string
     *
     * @param string $string
     * @param string $encoding Not used yet
     * @return static
     */
    public function loadHTML($string, $encoding = null){
        $string = $this->transformSpecialCharacters($string);
        $this->dompdf->loadHtml($string, $encoding);
        $this->rendered = false;
        return $this;
    }
    /**
     * Load a HTML file
     *
     * @param string $file
     * @return static
     */
    public function loadFile($file){
        $this->dompdf->loadHtmlFile($file);
        $this->rendered = false;
        return $this;
    }

    /**
     * Add metadata to the document
     *
     * @param array $info
     * @return static
     */
    public function addInfo($info){
        foreach($info as $name=>$value){
            $this->dompdf->add_info($name, $value);
        }
        return $this;
    }

    /**
     * Update the PHP DOM PDF Options
     *
     * @param array $options
     * @return static
     */
    public function setPHPDomPdfOptions(array $options) {
        $options = new Options($options);
        $this->dompdf->setOptions($options);
        return $this;
    }

    /**
     * Output the PDF as a string.
     *
     * @return string The rendered PDF as string
     */
    public function printDocument(){
        if(!$this->rendered){
            $this->render();
        }
        return $this->dompdf->output();
    }

    /**
     * Save the PDF to a file. $flags parameter modified how the file write operation is performed.
     *
     * @param $filePath
     * @param $flags
     * @return static
     */
    public function writeDocument($filePath, $flags = null){
        file_put_contents($filePath, $this->printDocument(), $flags ? LOCK_EX : 0);
        return $this;
    }

    /**
     * Make the PDF downloadable by the user
     *
     * @param string $filename
     * @param string $disposition
     * @return BinaryFileResponse
     */
    public function download($filename = 'document.pdf', $disposition = 'attachment') {
        $filePath = storage_path("app" . DIRECTORY_SEPARATOR . uniqid() . "-$filename");
        $this->writeDocument($filePath);
        $response = new BinaryFileResponse($filePath, 200, array(
            'Content-Type' => 'application/pdf'
        ), true);

        if (! is_null($filename)) {
            return $response->setContentDisposition($disposition, $filename, 'document.pdf');
        }
        return $response->deleteFileAfterSend(true);
    }
    /**
     * Return a response with the PDF to show in the browser
     *
     * @param string $filename
     * @param \Closure $callback
     * @return StreamedResponse
     */
    public function streamDownload($filename = 'document.pdf', $callback = null, $disposition = 'attachment') {
        return $this->dompdf->stream($filename);
        // $response = new StreamedResponse($callback, 200, array(
        //     'Content-Type' => 'application/pdf'
        // ));

        // if (! is_null($filename)) {
        //     $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
        //         $disposition,
        //         $filename,
        //         'document.pdf'
        //     ));
        // }

        // return $response;
    }

    /**
     * Render the PDF document
     */
    protected function render(){
        if(!$this->dompdf){
            throw new Exception('PDF Provider not initialized');
        }
        $this->dompdf->render();
        $this->rendered = true;
    }

    public function setEncryption($password) {
       if (!$this->dompdf) {
           throw new Exception("PDF Provider not initialized");
       }
       $this->render();
       return $this->dompdf->getCanvas()->{'get_cpdf'}()->setEncryption("pass", $password);
    }


    protected function transformSpecialCharacters($subject){
        foreach(array('€' => '&#0128;','£' => '&pound;') as $search => $replace){
            $subject = str_replace($search, $replace, $subject);
        }
        return $subject;
    }
}
