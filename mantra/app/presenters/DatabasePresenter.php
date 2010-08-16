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
    }
    
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $collList = $this->db->database($this->database)->getInfo()->getCollectionList();
        
        $container = $form->addContainer('coll');
        foreach ($collList as $coll) {
            $container->addCheckbox(Tools::escapeId($coll));
        }
        
        $form->addSubmit('empty', t('Empty collection'))->onClick[] = array($this, 'emptyCollection');
        $form->addSubmit('drop', t('Drop collection'))->onClick[] = array($this, 'dropCollection');
        $form->addSubmit('dropIndexes', t('Drop indexes'))->onClick[] = array($this, 'dropIndexes');
        $form->addSubmit('reindex', t('Reindex'))->onClick[] = array($this, 'reindexCollection');
        $form->addSubmit('validate', t('Validate indexes'))->onClick[] = array($this, 'validateCollection');
        $form->addSubmit('validateData', t('Validate data'))->onClick[] = array($this, 'validateData');
        
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
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
            
            $this->flashMessage(t("Collection '%' was validated.", "$this->database.$collection"));
        }
        
        $this->redirect('Database:default');
    }
    
    
    public function emptyCollection(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['coll'] as $name => $checked) {
            if (!$checked) continue;
            
            $collection = Tools::unescapeId($name);
            $this->db->database($this->database)->emptyCollection($collection);
            
            $this->flashMessage(t("Collection '%' was emptied.", "$this->database.$collection"));
        }
        
        $this->redirect('Database:default');
    }
    
    
    public function dropCollection(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['coll'] as $name => $checked) {
            if (!$checked) continue;
            
            $collection = Tools::unescapeId($name);
            $this->db->database($this->database)->dropCollection($collection);
            
            $this->flashMessage(t("Collection '%' was dropped.", "$this->database.$collection"));
        }
        
        $this->redirect('Database:default');
    }
    
    
    public function dropIndexes(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['coll'] as $name => $checked) {
            if (!$checked) continue;
            
            $collection = Tools::unescapeId($name);
            $this->db->database($this->database)->dropIndexes($collection);
            
            $this->flashMessage(t("Indexes on collection '%' was dropped.", "$this->database.$collection"));
        }
        
        $this->redirect('Database:default');
    }
    
    
    public function reindexCollection(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['coll'] as $name => $checked) {
            if (!$checked) continue;
            
            $collection = Tools::unescapeId($name);
            $this->db->database($this->database)->reindexCollection($collection);
            
            $this->flashMessage(t("Collection '%' was reindexed.", "$this->database.$collection"));
        }
        
        $this->redirect('Database:default');
    }
    
}
