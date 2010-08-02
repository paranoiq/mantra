<?php

use Mantra\FormFactory;
use Phongo\Tools;
use Nette\Forms\ISubmitterControl;

class CollectionPresenter extends BasePresenter {
    
    public function actionDefault($database) {
        $indexList = $this->db->getIndexList($this->collection, $this->database);
        $dbStats = $this->db->info->getCollectionStats($this->collection, $this->database);
        
        $count = $this->db->count(array(), $this->collection, $this->database);
        
        $indexes = array();
        foreach ($indexList as $index => $keys) {
            $indexes[$index]['id'] = Tools::escapeId($index);
            $indexes[$index]['keys'] = $keys;
            $indexes[$index]['size'] = $dbStats['indexSizes'][$index];
        }
        $this->template->indexes = $indexes;
        
        unset($dbStats['indexSizes']);
        $dbStats['avgObjSize'] = round($dbStats['avgObjSize'], 0);
        $this->template->stats = $dbStats;
        
        $this->template->form = $this->getComponent('form');
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $indexList = $this->db->getIndexList($this->collection, $this->database);
        
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
            $this->db->dropIndex($index, $this->collection, $this->database);
            
            $this->flashMessage("Index '$index' on collection '$this->database.$this->collection' was dropped.");
        }
        
        $this->redirect('Collection:default');
    }
    
}
