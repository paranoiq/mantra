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
        $form->addTextArea('object', t('Object (JSON)'), 80, 20)
            ->setEmptyValue('{"": ""}')
            ->addRule(Form::FILLED, t('Object must be filled.'));
        
        $form->addSubmit('insert', t('Insert'))->onClick[] = array($this, 'insertItem');
        
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
        return $form;
    }
    
    public function insertItem(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        $this->db->database($this->database)->insert($values['object'], $this->collection);
           
        $this->flashMessage(t("A new item was inserted into collection '%'.", "$this->database.$this->collection"));
        
        $this->redirect('Insert:default');
    }
    
}
