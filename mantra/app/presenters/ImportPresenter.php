<?php

use Mantra\FormFactory;
use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;

class ImportPresenter extends BasePresenter {
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $maxSize = ((int) ini_get('upload_max_filesize') - 1) << 20;
        
        $form->addGroup('Import file');
        
        $form->addText('local', 'Select local file …', 60)
            ->setEmptyValue('/');
            //->addRule(Form::FILLED, 'Pleas fill in the path to file.');
        $form->addFile('upload', '… or upload file')
            ->addConditionOn($form['local'], ~Form::FILLED)
                ->addRule(Form::FILLED, 'Select the file to import')
                ->addRule(Form::MAX_FILE_SIZE, "File cannot be larger than $maxSize", $maxSize)
                ->addRule(Form::MIME_TYPE, 'File must be either JSON or  CSV.', 'application/json,text/csv,application/octet-stream');
        $form->addSelect('type', 'Type', array('csv,' => 'CSV ,', 'csv;' => 'CSV ;'/*, 'json' => 'JSON'*/));
        
        $form->addSubmit('import', 'Import')->onClick[] = array($this, 'uploadFile');
    }
    
    public function uploadFile(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        if (!empty($values['local'])) {
            if (!file_exists($values['local'])) {
                $button->parent->addError('File not found.');
                return;
            }
            
            if (!is_readable($values['local'])) {
                $button->parent->addError('File cannot be read from.');
                return;
            }
            
            if (substr($values['type'], 0, 3) == 'csv') {
                $count = $this->importCsv($values['local'], substr($values['type'], 3, 1));
            } else {
                $count = $this->importJson($values['local']);
            }
        } else {
            if ($values['upload']->getError() != 0) {
                $this->flashMessage('Error receiving file.');
                return;
            }
            
            if (substr($values['type'], 0, 3) == 'csv') {
                $count = $this->importCsv($values['upload']->getTemporaryFile(), substr($values['type'], 3, 1));
            } else {
                $count = $this->importJson($values['upload']->getTemporaryFile());
            }
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
            
            if ($b > 1000) {
                $this->db->database($this->database)->batchInsert($batch, $this->collection);
                $b = 0;
                $batch = array();
            }
        }
        if ($batch) $this->db->database($this->database)->batchInsert($batch, $this->collection);
        
        return $n;
    }
    
    private function importJson($file) {
        /// 
    }
    
}
