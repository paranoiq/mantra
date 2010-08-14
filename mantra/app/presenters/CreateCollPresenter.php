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
        
        $form->addText('size', 'Initial size [MB]')
            ->addCondition(Form::FILLED)
            ->addRule(Form::FLOAT, 'Size must be a positive number.')
            ->addRule(Form::RANGE, 'Size must be a positive number.', array(0, NULL));
        $form->addCheckbox('capped', 'Capped (fixed size)');
        $form->addText('max', 'Maximum elements')
            ->addCondition(Form::FILLED)
            ->addConditionOn($form['capped'], Form::FILLED)
                ->addRule(Form::FLOAT, 'Maximum elements must be a positive number.')
                ->addRule(Form::RANGE, 'Maximum elements must be a positive number.', array(0, NULL));
        
        $form->addSubmit('create', 'Create collection');
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function createCollection($form) {
        $values = $form->getValues();
        
        $capped = empty($values['capped']) ? FALSE : TRUE;
        $size = empty($values['size']) ? 0 : $values['size'] * 1048576;
        $max = (empty($values['max']) || !$capped) ? 0 : $values['max'];
        
        $this->db->database($this->database)->createCollection($values['collection'], $capped, $size, $max);
        
        $this->flashMessage("Collection '$values[collection]' was created.");
        
        $this->redirect('Database:default');
    }
    
}
