<?php

use Mantra\FormFactory;
use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;

class ImportPresenter extends BasePresenter {
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $maxSize = (int) ini_get('upload_max_filesize') - 1;
        
        $form->addGroup(t('Import file'));
        
        $form->addText('local', t('Select file on server …'), 60)
            ->setEmptyValue('/');
            //->addRule(Form::FILLED, 'Pleas fill in the path to file.');
        $form->addFile('upload', t('… or upload file'))
            ->addConditionOn($form['local'], ~Form::FILLED)
                ->addRule(Form::FILLED, t('Select the file to import'))
                ->addRule(Form::MAX_FILE_SIZE, t('File cannot be larger than % MB.', $maxSize), $maxSize << 20)
                ->addRule(Form::MIME_TYPE, t('File must be either JSON or CSV.'), 'application/json,text/csv,application/octet-stream');
        $form->addSelect('type', t('Type'), array('csv,' => 'CSV ,', 'csv;' => 'CSV ;'/*, 'json' => 'JSON'*/));
        
        $form->addSubmit('import', t('Import'))->onClick[] = array($this, 'uploadFile');
    }
    
    public function uploadFile(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        if (!empty($values['local'])) {
            if (!file_exists($values['local'])) {
                $button->parent->addError(t('File was not found.'));
                return;
            }
            
            if (!is_readable($values['local'])) {
                $button->parent->addError(t('File cannot be read from.'));
                return;
            }
            
            if (substr($values['type'], 0, 3) == 'csv') {
                $count = $this->importCsv($values['local'], substr($values['type'], 3, 1));
            } else {
                $count = $this->importJson($values['local']);
            }
        } else {
            if ($values['upload']->getError() != 0) {
                $this->flashMessage(t('Error when receiving file.'));
                return;
            }
            
            if (substr($values['type'], 0, 3) == 'csv') {
                $count = $this->importCsv($values['upload']->getTemporaryFile(), substr($values['type'], 3, 1));
            } else {
                $count = $this->importJson($values['upload']->getTemporaryFile());
            }
        }
        
        $this->flashMessage(t('File was succesfully loaded and % items created.', $count));
        
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
