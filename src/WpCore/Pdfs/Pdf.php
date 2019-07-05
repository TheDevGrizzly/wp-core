<?php

namespace WpCore\Pdfs;

use mikehaertl\wkhtmlto\Pdf as wkhtmlToPdf;
use WpCore\Bootstrap\Blade;

class Pdf
{
    public $pdf;

    public $options = [];

    public $content;

    public function generate(): void
    {
        $this->pdf = new wkhtmlToPdf($this->content);
        $options = config('pdf.options');
        foreach ($this->options as $key => $value) {
            $options[$key] = $value;
        }
        $this->pdf->setOptions($options);
        $this->pdf->send();
    }

    /**
     * Helper function to tell which view to render
     * @param  string $view The view to render
     * @param  array  $with Data to pass to the view
     * @return string       Html rendered
     */
    protected function view($view, $with = []): string
    {
        echo Blade::render($view, $with);
    }
}
