<?php

use Mantra\FormFactory;
use Phongo\Tools;
use Nette\Forms\ISubmitterControl;

class HomePresenter extends BasePresenter {
    
    public function actionDefault() {
        
        $list = $this->db->getDatabaseList();
        $databases = array();
        foreach ($list as $db) {
            $stats = $this->db->info->getDatabaseStats($db);
            
            $databases[$db]['id'] = Tools::escapeId($db);
            $databases[$db]['collections'] = count($this->db->getCollectionList($db));
            $databases[$db]['objects']  = $stats['objects'];
            $databases[$db]['dataSize'] = $stats['dataSize'];
            $databases[$db]['fileSize'] = $stats['fileSize'];
        }
        
        $this->template->databases = $databases; 
        
        $this->template->form = $this->getComponent('form');
        
        list($this->template->version) = explode(',', $this->db->info->getVersionInfo());
    }
    
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $list = $this->db->getDatabaseList();
        $container = $form->addContainer('db');
        foreach ($list as $db) {
            $container->addCheckbox(Tools::escapeId($db));
        }
        
        $form->addSubmit('drop', "Drop database")->onClick[] = array($this, 'dropDatabase');
        
        $form->addSubmit('repair', "Repair database")->onClick[] = array($this, 'repairDatabase');
        $form->addCheckbox('backup', 'Backup original files');
        $form->addCheckbox('preserve', 'Preserve damaged cloned files');
        
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    
    public function dropDatabase(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['db'] as $name => $checked) {
            if (!$checked) continue;
            
            $database = Tools::unescapeId($name);
            $this->db->dropDatabase($database);
            
            $this->flashMessage("Database '$database' was dropped.");
        }
        
        $this->redirect('Home:default');
    }
    
    
    public function repairDatabase(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['db'] as $name => $checked) {
            if (!$checked) continue;
            
            $database = Tools::unescapeId($name);
            $this->db->repairDatabase($database, $values['preserve'], $values['backup']);
            
            $this->flashMessage("Database '$database' was repaired.");
        }
        
        $this->redirect('Home:default');
    }
    
}
