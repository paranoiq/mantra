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
        $form->addText('collection', t('New name'))
            ->addRule(Form::FILLED, t('Collection name must be filled.'))
            ->addRule(Form::REGEXP, t('Collection name includes an invalid character. Allowed are all ASCII characters except controls, space, \", $ and DEL.'), 
                '/^[!#\x25-\x2D\x2F-\x7E]+(\.[!#\x25-\x2D\x2F-\x7E]+)*$/');
        $form->addText('database', t('Move to database'))
            ->addCondition(Form::FILLED)
                ->addRule(Form::REGEXP, t("Database name includes an invalid character. Allowed characters are numbers, letters and following symbols: !#%&\'()+-,;>=<@[]^_`{}~"), 
                    "/^[-!#%&'()+,0-9;>=<@A-Z\[\]^_`a-z{}~]+$/");
        
        $form->addSubmit('rename', t('Rename collection'));
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
        return $form;
    }
    
    public function renameCollection($form) {
        $values = $form->getValues();
        $newDatabase = $values['database'] ? $values['database'] : $this->database;
        
        $this->db->database($this->database)->renameCollection($values['collection'], $newDatabase, $this->collection);
        
        $this->flashMessage(t("Collection '%' was renamed to '%'.", $this->database.$this->collection, ($newDatabase ? "$newDatabase." : '') . $values['collection']));
        
        $this->redirect('Database:default');
    }
    
}
