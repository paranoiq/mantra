<?php

use Mantra\FormFactory;
use Phongo\Tools;
use Nette\Forms\ISubmitterControl;

class HomePresenter extends BasePresenter {
    
    public function actionDefault() {
        
        $list = $this->db->getInfo()->getDatabaseList();
        $databases = array();
        foreach ($list as $db) {
            $stats = $this->db->database($db)->getInfo()->getDatabaseStats();
            
            $databases[$db]['id'] = Tools::escapeId($db);
            $databases[$db]['collections'] = count($this->db->database($db)->getInfo()->getCollectionList());
            $databases[$db]['objects']  = $stats['objects'];
            $databases[$db]['dataSize'] = $stats['dataSize'];
            $databases[$db]['fileSize'] = $stats['fileSize'];
        }
        
        $this->template->databases = $databases;
        
        list($this->template->version) = explode(',', $this->db->getInfo()->getVersionInfo());
    }
    
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $list = $this->db->getInfo()->getDatabaseList();
        $container = $form->addContainer('db');
        foreach ($list as $db) {
            $container->addCheckbox(Tools::escapeId($db));
        }
        
        $form->addSubmit('drop', t('Drop database'))->onClick[] = array($this, 'dropDatabase');
        
        $form->addSubmit('repair', t('Repair database'))->onClick[] = array($this, 'repairDatabase');
        $form->addCheckbox('backup', t('Backup original files'));
        //$form->addCheckbox('preserve', t('Preserve cloned files'));
        
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
        return $form;
    }
    
    
    public function dropDatabase(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['db'] as $name => $checked) {
            if (!$checked) continue;
            
            $database = Tools::unescapeId($name);
            $this->db->database($database)->drop();
            
            $this->flashMessage(t("Database '%' was dropped.", $database));
        }
        
        $this->redirect('Home:default');
    }
    
    
    public function repairDatabase(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['db'] as $name => $checked) {
            if (!$checked) continue;
            
            $database = Tools::unescapeId($name);
            $this->db->database($database)->repair($values['preserve'], $values['backup']);
            
            $this->flashMessage(t("Database '%' was repaired.", $database));
        }
        
        $this->redirect('Home:default');
    }
    
}
