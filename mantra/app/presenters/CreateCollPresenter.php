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
        $form->addText('collection', t('Name'))
            ->addRule(Form::FILLED, t('Collection name must be filled.'))
            ->addRule(Form::MAX_LENGTH, t('Collection name is too long.'), 127 - strlen($this->database))
            ->addRule(Form::REGEXP, t('Collection name includes an invalid character. Allowed are all ASCII characters except controls, space, ", $ and DEL.'), 
                '/^[!#\x25-\x2D\x2F-\x7E]+(\.[!#\x25-\x2D\x2F-\x7E]+)*$/');
        
        $form->addText('size', t('Initial size [MB]'))
            ->addCondition(Form::FILLED)
            ->addRule(Form::FLOAT, t('Size must be a positive number.'))
            ->addRule(Form::RANGE, t('Size must be a positive number.'), array(0, NULL));
        $form->addCheckbox('noIndex', t("No index on field '_id'"));
        
        $form->addCheckbox('capped', t('Capped (fixed size)'));
        $form->addText('max', t('Maximum elements'))
            ->addCondition(Form::FILLED)
            ->addConditionOn($form['capped'], Form::FILLED)
                ->addRule(Form::FLOAT, t('Maximum elements must be a positive number.'))
                ->addRule(Form::RANGE, t('Maximum elements must be a positive number.'), array(0, NULL));
        
        $form->addSubmit('create', t('Create collection'));
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
        return $form;
    }
    
    public function createCollection($form) {
        $values = $form->getValues();
        
        $options = array();
        if (!empty($values['capped'])) $options['capped'] = TRUE;
        if (!empty($values['size']))   $options['size'] = $values['size'] * 1048576;
        if (!empty($values['max']) || !empty($options['capped'])) $options['max'] = $values['max'];
        if (!empty($values['noIndex'])) $options['autoIndexId'] = FALSE;
        
        $this->db->database($this->database)->createCollection($values['collection'], $options);
        
        $this->flashMessage(t("Collection '%' was created.", $values['collection']));
        
        $this->redirect('Database:default');
    }
    
}
