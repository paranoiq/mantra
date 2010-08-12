<?php

use Mantra\FormFactory;
use Phongo\Tools;
use Nette\Forms\ISubmitterControl;

class DatabasePresenter extends BasePresenter {
    
    public function actionDefault($database) {
        $collections = $this->db->database($this->database)->getInfo()->getCollectionInfo();
        foreach ($collections as $collection => $info) {
            $stats = $this->db->database($this->database)->size(array(), $collection);
            
            $collections[$collection]['id'] = Tools::escapeId($collection);
            $collections[$collection]['dataSize'] = $stats['size'];
            $collections[$collection]['objects']  = $stats['numObjects'];
        }
        $this->template->collections = $collections;
        
        $stats = $this->db->database($database)->getInfo()->getDatabaseStats();
        $stats['avgObjSize'] = round($stats['avgObjSize'], 0);
        $this->template->databaseStats = $stats;
        
        $this->template->form = $this->getComponent('form');
    }
    
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $collList = $this->db->database($this->database)->getInfo()->getCollectionList();
        
        $container = $form->addContainer('coll');
        foreach ($collList as $coll) {
            $container->addCheckbox(Tools::escapeId($coll));
        }
        
        $form->addSubmit('empty', "Empty collection")->onClick[] = array($this, 'emptyCollection');
        $form->addSubmit('drop', "Drop collection")->onClick[] = array($this, 'dropCollection');
        $form->addSubmit('dropIndexes', "Drop indexes")->onClick[] = array($this, 'dropIndexes');
        $form->addSubmit('reindex', "Reindex")->onClick[] = array($this, 'reindexCollection');
        $form->addSubmit('validate', "Validate indexes")->onClick[] = array($this, 'validateCollection');
        $form->addSubmit('validateData', "Validate data")->onClick[] = array($this, 'validateData');
        
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    
    public function validateData(ISubmitterControl $button) {
        $this->validateCollection($button, TRUE);
    }
    
    
    public function validateCollection(ISubmitterControl $button, $validateData = FALSE) {
        $values = $button->parent->getValues();
        
        foreach ($values['coll'] as $name => $checked) {
            if (!$checked) continue;
            
            $collection = Tools::unescapeId($name);
            $this->db->database($this->database)->validateCollection($collection, $validateData);
            
            $this->flashMessage("Collection '$this->database.$collection' was validated.");
        }
        
        $this->redirect('Database:default');
    }
    
    
    public function emptyCollection(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['coll'] as $name => $checked) {
            if (!$checked) continue;
            
            $collection = Tools::unescapeId($name);
            $this->db->database($this->database)->emptyCollection($collection);
            
            $this->flashMessage("Collection '$this->database.$collection' was emptied.");
        }
        
        $this->redirect('Database:default');
    }
    
    
    public function dropCollection(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['coll'] as $name => $checked) {
            if (!$checked) continue;
            
            $collection = Tools::unescapeId($name);
            $this->db->database($this->database)->dropCollection($collection);
            
            $this->flashMessage("Collection '$this->database.$collection' was dropped.");
        }
        
        $this->redirect('Database:default');
    }
    
    
    public function dropIndexes(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['coll'] as $name => $checked) {
            if (!$checked) continue;
            
            $collection = Tools::unescapeId($name);
            $this->db->database($this->database)->dropIndexes($collection);
            
            $this->flashMessage("Indexes on collection '$this->database.$collection' was dropped.");
        }
        
        $this->redirect('Database:default');
    }
    
    
    public function reindexCollection(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['coll'] as $name => $checked) {
            if (!$checked) continue;
            
            $collection = Tools::unescapeId($name);
            $this->db->database($this->database)->reindexCollection($collection);
            
            $this->flashMessage("Collection '$this->database.$collection' was reindexed.");
        }
        
        $this->redirect('Database:default');
    }
    
}
