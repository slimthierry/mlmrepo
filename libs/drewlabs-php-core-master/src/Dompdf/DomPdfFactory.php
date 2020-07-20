<?php

namespace Drewlabs\Core\Dompdf;

use Drewlabs\Contracts\Factory\IFactory;
use Dompdf\Dompdf as PHPDomPdf;

class DomPdfFactory implements IFactory
{
    /**
     *
     * @var DomPdfable
     */
    protected $pdf;

    /**
     * @inheritDoc
     */
    public function make($options = null)
    {
        if (!isset($options)) {
            $defaults = require __DIR__ . '/default.php';
            $options = [];
            foreach ($defaults as $key => $value) {
                $key = strtolower(str_replace('DOMPDF_', '', $key));
                $options[$key] = $value;
            }
        }
        $this->pdf = new Dompdf(new PHPDomPdf($options));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resolve()
    {
        return $this->pdf;
    }
}
