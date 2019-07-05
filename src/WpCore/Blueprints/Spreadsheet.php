<?php

namespace Ayctor\Spreadsheets;

use WpCore\Spreadsheets\Spreadsheet;

class Blueprint extends Spreadsheet
{
    public $filename = 'Example';

    public function __construct()
    {
        parent::__construct();

        $header = ['example'];

        // Setting header
        $this->current_sheet->fromArray($header, null, 'A1');
    }
}
