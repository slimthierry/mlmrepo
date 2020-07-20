<?php

namespace Drewlabs\Core\Dompdf;

use Dompdf\Dompdf as PHPDomPdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @package [[Drewlabs\Core\Dompdf]]
 */
interface DomPdfable {

    /**
     * Get PHP DomPdf instance
     *
     * @return PHPDomPdf
     */
    public function getDOMPdfProvider();

    /**
     * Set the paper size (default A4)
     *
     * @param string $paper
     * @param string $orientation
     * @return $this
     */
    public function setPaperOrientation($paper, $orientation = 'portrait');


    /**
     * Load a HTML string
     *
     * @param string $string
     * @param string $encoding Not used yet
     * @return static
     */
    public function loadHTML($string, $encoding = null);

    /**
     * Load a HTML file
     *
     * @param string $file
     * @return static
     */
    public function loadFile($file);

    /**
     * Add metadata to the document
     *
     * @param array $info
     * @return static
     */
    public function addInfo($info);

    /**
     * Update the PHP DOM PDF Options
     *
     * @param array $options
     * @return static
     */
    public function setPHPDomPdfOptions(array $options);

    /**
     * Output the PDF as a string.
     *
     * @return string The rendered PDF as string
     */
    public function printDocument();

    /**
     * Save the PDF to a file. $flags parameter modified how the file write operation is performed.
     *
     * @param $filePath
     * @param $flags
     * @return static
     */
    public function writeDocument($filePath, $flags = null);

    /**
     * Make the PDF downloadable by the user
     *
     * @param string $filename
     * @param string $disposition
     * @return BinaryFileResponse
     */
    public function download($filename = 'document.pdf', $disposition = 'attachment');
    /**
     * Return a response with the PDF to show in the browser
     *
     * @param string $filename
     * @param \Closure $callback
     * @return StreamedResponse
     */
    public function streamDownload($filename = 'document.pdf', $callback = null, $disposition = 'attachment');
}
