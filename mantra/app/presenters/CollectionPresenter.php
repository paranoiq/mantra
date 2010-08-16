<?php

use Mantra\FormFactory;
use Phongo\Tools;
use Nette\Forms\ISubmitterControl;

class CollectionPresenter extends BasePresenter {
    
    public function actionDefault($database) {
        $indexList = $this->db->database($this->database)->getInfo()->getIndexList($this->collection);
        $stats = $this->db->database($this->database)->getInfo()->getCollectionStats($this->collection);
        
        $this->template->info = $this->db->database($this->database)->getInfo()->getCollectionInfo($this->collection);
        
        $indexes = array();
        foreach ($indexList as $index => $keys) {
            $indexes[$index]['id'] = Tools::escapeId($index);
            $indexes[$index]['keys'] = $keys;
            $indexes[$index]['size'] = $stats['indexSizes'][$index];
        }
        $this->template->indexes = $indexes;
        
        unset($stats['ns']);
        unset($stats['indexSizes']);
        $stats['avgObjSize'] = round($stats['avgObjSize'], 0);
        $this->template->stats = $stats;
        
        $usage = $this->db->database($this->database)->getInfo()->getUsage($this->collection);
        $this->template->usage = $usage;
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $indexList = $this->db->database($this->database)->getInfo()->getIndexList($this->collection);
        
        $container = $form->addContainer('index');
        foreach ($indexList as $index => $keys) {
            $container->addCheckbox(Tools::escapeId($index));
        }
        
        $form->addSubmit('drop', t('Drop index'))->onClick[] = array($this, 'dropIndex');
        
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
        return $form;
    }
    
    public function dropIndex(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['index'] as $name => $checked) {
            if (!$checked) continue;
            
            $index = Tools::unescapeId($name);
            $this->db->database($this->database)->dropIndex($index, $this->collection);
            
            $this->flashMessage(t("Index '%' on collection '%' was dropped.", $index, $this->database . '.' . $this->collection));
        }
        
        $this->redirect('Collection:default');
    }
    
}
