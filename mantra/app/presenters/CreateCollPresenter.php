<?php

/**
 * @todo: namespaced collections
 * @todo: range in size and max elemtnts Form::MIN 
 */

use Mantra\FormFactory;
use Nette\Forms\Form;

class CreateCollPresenter extends BasePresenter {
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        $form->onSubmit[] = array($this, 'createCollection');
        
        $form->addGroup();
        $form->addText('collection', 'Name')
            ->addRule(Form::FILLED, 'Collection name must be filled.')
            ->addRule(Form::MAX_LENGTH, 'Collection name is too long.', 127 - strlen($this->database))
            ->addRule(Form::REGEXP, 'Collection name includes an invalid character. Allowed are all ASCII characters except controls, space, ", $ and DEL.', 
                '/^[!#\x25-\x2D\x2F-\x7E]+(\.[!#\x25-\x2D\x2F-\x7E]+)*$/');
        
        $form->addCheckbox('capped', 'Capped (fixed size)');
        $form->addText('size', 'Size in bytes')
            ->addConditionOn($form['capped'], Form::FILLED)
                ->addRule(Form::NUMERIC, 'Size must be a number.');
        $form->addText('max', 'Maximum elements')
            ->addConditionOn($form['capped'], Form::FILLED)
                ->addRule(Form::NUMERIC, 'Maximum elements must be a number.');
        
        $form->addSubmit('create', 'Create collection');
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function createCollection($form) {
        $values = $form->getValues();
        
        $this->db->getDatabase($this->database)->createCollection($values['collection']);
        
        $this->flashMessage("Collection '$values[collection]' was created.");
        
        $this->redirect('Database:default');
    }
    
}
