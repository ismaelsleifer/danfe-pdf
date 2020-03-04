<?php

namespace sleifer\danfepdf;

use NFePHP\DA\Legacy\Pdf as LegacyPdf;

class Pdf extends LegacyPdf{
    protected $javascript;
    protected $n_js;

    function IncludeJS($script, $isUTF8=false) {
        if(!$isUTF8)
            $script=utf8_encode($script);
        $this->javascript=$script;
    }

    function putjavascript() {


        $this->newobj();
        $this->n_js=$this->n;
        $this->out('<<');
        $this->out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->out('>>');
        $this->out('endobj');
        $this->newobj();
        $this->out('<<');
        $this->out('/S /JavaScript');
        $this->out('/JS '.$this->textstring($this->javascript));
        $this->out('>>');
        $this->out('endobj');
    }

    function putresources() {
        parent::putresources();
        if (!empty($this->javascript)) {
            $this->putjavascript();
        }
    }

    function putcatalog() {
        parent::putcatalog();
        if (!empty($this->javascript)) {
            $this->out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }

    function AutoPrint($printer='')
    {
        // Open the print dialog
        if($printer)
        {
            $printer = str_replace('\\', '\\\\', $printer);
            $script = "var pp = getPrintParams();";
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
            $script .= "pp.printerName = '$printer'";
            $script .= "print(pp);";
        }
        else
            $script = 'print(true);';
        $this->IncludeJS($script);
    }
}