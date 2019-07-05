<?php

namespace WpCore\Spreadsheets;

use \PhpOffice\PhpSpreadsheet\Cell;
use \PhpOffice\PhpSpreadsheet\Spreadsheet as OfficeSpreadsheet;
use \PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Spreadsheet
{
    public $spreadsheet;

    public $current_sheet;

    public $params = false;

    public $params_sheet;

    public $filename = '';

    /**
     * Instanciate class with working sheet + params sheet
     */
    public function __construct()
    {
        $this->spreadsheet = new OfficeSpreadsheet();
        $this->current_sheet = $this->spreadsheet->getActiveSheet();
        if ($this->params) {
            $this->params_sheet = new Worksheet($this->spreadsheet, 'params');
            $this->params_sheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
            $this->spreadsheet->addSheet($this->params_sheet);
        }
    }

    /**
     * Download spreadsheet directly from browser
     */
    public function download()
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=' . $this->filename . '.xlsx');
        header('Cache-Control: max-age=0');

        // Global style
        $this->setAutoWidth($this->current_sheet);
        $this->setAutoFilters($this->current_sheet);

        // Output
        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
    }

    /**
     * Save spreadsheet in path
     * @param  string $path Where to save file
     */
    public function save($path)
    {
        // Global style
        $this->setAutoWidth($this->current_sheet);
        $this->setAutoFilters($this->current_sheet);

        // Output
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($path . '/' . $this->filename . '.xlsx');
    }

    /**
     * Set auto width on columns
     * @param Worksheet $worksheet Worksheet to perfom the task on
     */
    public function setAutoWidth($worksheet)
    {
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumn++;
        for ($col = 'A'; $col != $highestColumn; ++$col) {
            $worksheet->getColumnDimension($col)->setAutoSize(true);
        }
        return $this;
    }

    /**
     * Set auto Filters on columns
     * @param Worksheet $worksheet Worksheet to perfom the task on
     */
    public function setAutoFilters($worksheet)
    {
        $worksheet->setAutoFilter(
            $worksheet->calculateWorksheetDimension()
        );
        return $this;
    }

    /**
     * Convert objects to a string
     * @param  Array $entries  Array of object
     * @param  String $property Property to display
     * @param  String $glue     The glue to concatenate properties
     * @return String           Properties concatenated
     */
    public function objectsToString($entries, $property, $glue)
    {
        $array = $this->objectsToArray($entries, $property);
        return implode($glue, $array);
    }

    /**
     * Convert objects to an array
     * @param  Array $entries  Array of object
     * @param  String $property Property to display
     * @param  String $glue     The glue to concatenate properties
     * @return String           Properties concatenated
     */
    public function objectsToArray($entries, $property)
    {
        $array = [];
        foreach ($entries as $entry) {
            $array[] = $entry->{$property};
        }
        return $array;
    }

    /**
     * Create a validation rule for a column
     * @param  string  $column   Column name
     * @param  string  $formula1 Liste of values
     * @param  boolean|string $formula2 Max value
     * @return void
     */
    public function addValidation($column, $formula1, $formula2 = false)
    {
        $validation = $this->current_sheet->getCell($column . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(false);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Erreur');
        $validation->setError('La valeur n\'est pas dans la liste.');
        $validation->setFormula1($formula1);
        if ($formula2) {
            $validation->setFormula1($formula2);
        }
        for ($i = 3; $i < 999; $i++) {
            $this->current_sheet->getCell($column . $i)->setDataValidation(clone $validation);
        }
        return $this;
    }

    /**
     * Add formating to cells
     * @param string $cells  range of cells
     * @param string $format Format to apply
     */
    public function addFormating($cells, $format)
    {
        $this->current_sheet->getStyle($cells)->getNumberFormat()->setFormatCode($format);
        return $this;
    }

    /**
     * Adds a formula to a column
     * @param string $column  Name of the column
     * @param string $formula Formula to apply
     */
    public function addFormula($column, $formula)
    {
        for ($i = 2; $i < 999; $i++) {
            $iterative_formula = str_replace('$', $i, $formula);
            $this->current_sheet->setCellValue($column . $i, $iterative_formula);
        }
        return $this;
    }

    /**
     * Adds a Comment to a cell
     * @param String $cell    Name of the cell
     * @param String $title   Title to add
     * @param String $comment Comment to add
     */
    public function addComment($cell, $comment, $width = '150pt', $height = '55.5pt')
    {
        $this->current_sheet->getComment($cell)->setWidth($width)->setHeight($height);
        $this->current_sheet->getComment($cell)
            ->getText()->createTextRun("Le monde Immo:\r\n")
            ->getFont()->setBold(true);
        $this->current_sheet->getComment($cell)->getText()->createTextRun($comment);
        return $this;
    }

    /**
     * Add a parameter for a column
     * @param string $start  Where to put the parameter
     * @param Array $values Values to put in the parameters sheet
     */
    public function addParams($start, $values)
    {
        $values = array_chunk($values, 1);
        $this->params_sheet->fromArray($values, null, $start);
        return $this;
    }

    /**
     * Add a worksheet to the spreadsheet
     * @param String $title Title of the worksheet
     */
    public function addWorksheet($title)
    {
        $worksheet = new Worksheet($this->spreadsheet, $title);
        $this->spreadsheet->addSheet($worksheet);
        return $worksheet;
    }
}
