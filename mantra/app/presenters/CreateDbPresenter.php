<?php

use Mantra\FormFactory;
use Nette\Forms\Form;

class CreateDbPresenter extends BasePresenter {
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        $form->onSubmit[] = array($this, 'createDatabase');
        
        $form->addGroup();
        $form->addText('database', 'Name')
            ->addRule(Form::FILLED, 'Database name must be filled.')
            ->addRule(Form::REGEXP, 'Database name includes an invalid character. Allowed characters are numbers, letters and following symbols: !#%&\'()+-,;>=<@[]^_`{}~', 
                "/^[-!#%&'()+,0-9;>=<@A-Z\[\]^_`a-z{}~]+$/");
        $form->addSubmit('create', 'Create database');
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function createDatabase($form) {
        $values = $form->getValues();
        
        $this->db->createDatabase($values['database']);
        
        $this->flashMessage("Database '$values[database]' was created.");
        
        $this->redirect('Home:default');
    }
    
}
