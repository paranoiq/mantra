<?php

/**
 * @todo: namespaced collections
 * @todo: range in size and max elemtnts Form::MIN 
 */

use Mantra\FormFactory;
use Nette\Forms\Form;

class RenameCollPresenter extends BasePresenter {
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        $form->onSubmit[] = array($this, 'renameCollection');
        
        $form->addGroup();
        $form->addText('collection', 'New name')
            ->addRule(Form::FILLED, 'Collection name must be filled.')
            ->addRule(Form::REGEXP, 'Collection name includes an invalid character. Only letters, numbers and underscore are allowed.', 
                '/^[_a-zA-Z][_a-zA-Z0-9]*$/');
        $form->addText('database', 'Move to database')
            ->addCondition(Form::FILLED)
                ->addRule(Form::REGEXP, 'Collection name includes an invalid character. Only letters, numbers and underscore are allowed.', 
                    '/^[_a-zA-Z][_a-zA-Z0-9]*$/');
        
        $form->addSubmit('rename', 'Rename collection');
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function renameCollection($form) {
        $values = $form->getValues();
        $newDatabase = $values['database'] ? $values['database'] : $this->database;
        
        $this->db->renameCollection($values['collection'], $this->collection, $newDatabase, $this->database);
        
        $this->flashMessage("Collection '$this->database.$this->collection' was renamed to '" . ($newDatabase ? "$newDatabase." : '') . "$values[collection]'.");
        
        $this->redirect('Database:default');
    }
    
}
