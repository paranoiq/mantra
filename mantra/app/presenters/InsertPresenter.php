<?php

use Mantra\FormFactory;
use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;

class InsertPresenter extends BasePresenter {
    
    public function actionDefault($database) {
        $this->template->form = $this->getComponent('form');
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $form->addGroup();
        $form->addTextArea('object', 'Object (JSON)', 80, 20)
            ->setEmptyValue('{"": ""}')
            ->addRule(Form::FILLED, 'Object must be filled.');
        
        $form->addSubmit('insert', 'Insert')->onClick[] = array($this, 'insertItem');
        
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function insertItem(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        $this->db->insert($values['object'], $this->collection, $this->database);
           
        $this->flashMessage("A new item was inserted into '$this->database.$this->collection'.");
        
        $this->redirect('Insert:default');
    }
    
}
