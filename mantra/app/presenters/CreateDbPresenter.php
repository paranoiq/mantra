<?php

use Mantra\FormFactory;
use Nette\Forms\Form;

class CreateDbPresenter extends BasePresenter {
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        $form->onSubmit[] = array($this, 'createDatabase');
        
        $form->addGroup();
        $form->addText('database', t('Name'))
            ->addRule(Form::FILLED, t('Database name must be filled.'))
            ->addRule(Form::REGEXP, 'Database name includes an invalid character. Allowed characters are numbers, letters and following symbols: !#%&\'()+-,;>=<@[]^_`{}~', 
                "/^[-!#%&'()+,0-9;>=<@A-Z\[\]^_`a-z{}~]+$/");
        $form->addSubmit('create', t('Create database'));
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
        return $form;
    }
    
    public function createDatabase($form) {
        $values = $form->getValues();
        
        $this->db->createDatabase($values['database']);
        
        $this->flashMessage(t("Database '%' was created.", $values['database']));
        
        $this->redirect('Home:default');
    }
    
}
