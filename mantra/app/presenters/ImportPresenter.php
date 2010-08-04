<?php

use Mantra\FormFactory;
use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;

class ImportPresenter extends BasePresenter {
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $form->addGroup();
        
        $form->addFile('file', 'File')
            ->addRule(Form::FILLED, 'Select the file to import')
            ->addRule(Form::MIME_TYPE, 'File must be either JSON or CSV.', 'application/json,text/csv,application/octet-stream');
        $form->addSelect('type', 'Type', array('csv,' => 'CSV ,', 'csv;' => 'CSV ;'/*, 'json' => 'JSON'*/));
        
        $form->addSubmit('import', 'Import')->onClick[] = array($this, 'importFile');
        
        
    }
    
    public function importFile(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        if ($values['file']->getError() != 0) {
            $this->flashMessage('Error receiving file.');
            return;
        }
        
        if (substr($values['type'], 0, 3) == 'csv') {
            $count = $this->importCsv($values['file']->getTemporaryFile(), substr($values['type'], 3, 1));
        } else {
            $count = $this->importJson($values['file']->getTemporaryFile());
        }
        
        $this->flashMessage("File was succesfully loaded ($count items).");
        
        $this->redirect('Select:default');
    }
    
    private function importCsv($fileName, $separator) {
        $file = fopen($fileName, 'r');
        
        $head = fgetcsv($file, 0, $separator, '"', '"');
        
        $n = 0;
        $b = 0;
        $batch = array();
        while ($row = fgetcsv($file, 0, $separator, '"', '"')) {
            $n++;
            $b++;
            $obj = array();
            foreach ($row as $i => $val) {
                $obj[$head[$i]] = $val;
            }
            $batch[] = $obj;
            
            if ($n > 1000) {
                $this->db->batchInsert($batch, $this->collection, $this->database);
                $b = 0;
                $batch = array();
            }
        }
        if ($batch) $this->db->batchInsert($batch, $this->collection, $this->database);
        
        return $n;
    }
    
    private function importJson($file) {
        /// 
    }
    
}
