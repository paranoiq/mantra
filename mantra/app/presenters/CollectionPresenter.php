<?php

use Mantra\FormFactory;
use Phongo\Tools;
use Nette\Forms\ISubmitterControl;

class CollectionPresenter extends BasePresenter {
    
    public function actionDefault($database) {
        $indexList = $this->db->database($this->database)->getInfo()->getIndexList($this->collection);
        $dbStats   = $this->db->database($this->database)->getInfo()->getCollectionStats($this->collection);
        
        $this->template->info = $this->db->database($this->database)->getInfo()->getCollectionInfo($this->collection);
        
        $indexes = array();
        foreach ($indexList as $index => $keys) {
            $indexes[$index]['id'] = Tools::escapeId($index);
            $indexes[$index]['keys'] = $keys;
            $indexes[$index]['size'] = $dbStats['indexSizes'][$index];
        }
        $this->template->indexes = $indexes;
        
        unset($dbStats['ns']);
        unset($dbStats['indexSizes']);
        $dbStats['avgObjSize'] = round($dbStats['avgObjSize'], 0);
        $this->template->stats = $dbStats;
        
        $this->template->form = $this->getComponent('form');
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $indexList = $this->db->database($this->database)->getInfo()->getIndexList($this->collection);
        
        $container = $form->addContainer('index');
        foreach ($indexList as $index => $keys) {
            $container->addCheckbox(Tools::escapeId($index));
        }
        
        $form->addSubmit('drop', 'Drop index')->onClick[] = array($this, 'dropIndex');
        
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function dropIndex(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['index'] as $name => $checked) {
            if (!$checked) continue;
            
            $index = Tools::unescapeId($name);
            $this->db->database($this->database)->dropIndex($index, $this->collection);
            
            $this->flashMessage("Index '$index' on collection '$this->database.$this->collection' was dropped.");
        }
        
        $this->redirect('Collection:default');
    }
    
}
