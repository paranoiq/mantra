<?php

/**
 * @todo: namespaced collections
 * @todo: range in size and max elemtnts Form::MIN 
 */

use Mantra\FormFactory;
use Nette\Forms\Form;
use Nette\Environment;
use Nette\Forms\ISubmitterControl;

class CreateIndexPresenter extends BasePresenter {
    
    public function actionDefault() {
        $this->counter(($this->getRequest()->getMethod() == 'POST') ? 0 : NULL);
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $form->addGroup();
        
        $form->addText('name', t('Index name'))
            ->addCondition(Form::FILLED)
            ->addRule(Form::REGEXP, 'Index name include an invalid character. All characters except controls, space, dolar and dor are allowed.', 
                '/^[!#\x25-\x7E]+$/');
        
        $keys   = $form->addContainer('key');
        $orders = $form->addContainer('order');
        
        $count = $this->counter();
        for ($n = 0; $n < $count; $n++) {
            $keys->addText($n, t('Field'))
                ->addCondition(Form::FILLED)
                    ->addRule(Form::REGEXP, 'Field name include an invalid character. All characters except controls, space, dolar and dor are allowed.', 
                        '/^(([ !"#\x25-\x2D\x2F-\x7E][\x20-\x2D\x2F-\x7E]*)|\$)(\.(([ !"#\x25-\x2D\x2F-\x7E][\x20-\x2D\x2F-\x7E]*)|\$))*$/');
            $orders->addCheckbox($n, t('descending'));
        }
        
        $form->addSubmit('more', '+')->onClick[] = array($this, 'more');
        $form->addSubmit('less', '−')->onClick[] = array($this, 'less');
        if ($count < 2) $form['less']->setDisabled();
        
        $form->addCheckbox('unique', t('Unique index'));
        $form->addCheckbox('dropDups', t('Drop duplicates on unique index'));
        $form->addCheckbox('background', t('Create in background'));
        
        $form->addSubmit('create', t('Create index'))->onClick[] = array($this, 'createIndex');
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
        return $form;
    }
    
    public function createIndex(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        $keys = array();
        foreach ($values['key'] as $n => $key) {
            if (!$key) continue;
            $keys[$key] = $values['order'][$n] ? -1 : 1;
        }
        if (!$keys) {
            $button->parent->addError(t('You must specify at least one field.'));
            return;
        }
        
        $options = array();
        if ($values['unique']) $options['unique'] = 1;
        if ($values['dropDups']) $options['dropDups'] = 1;
        if ($values['background']) $options['background'] = 1;
        if ($values['name']) $options['name'] = $values['name'];
        
        $this->db->database($this->database)->createIndex($keys, $options, $this->collection);
        
        $this->flashMessage("Index % on collection '%' was created.", ($values['name'] ? "'$values[name]'" : ''), $this->database.$this->collection);
        
        $this->redirect('Collection:default');
    }
    
    private function counter($inc = 0) {
        $session = Environment::getSession('CreateIndex');
        
        $count = isset($session->count) ? $session->count : 1;
        if ($inc === NULL) $count = 1;
        $count = $count + $inc;
        if ($count < 1) $count = 1;
        $session->count = $count;
        
        $this->template->count = $count;
        return $count;
    }
    
    public function more(ISubmitterControl $button) {
        $this->counter(1);
        $this->removeComponent($this->getComponent('form'));
        $this->template->form = $this->getComponent('form');
    }
    
    public function less(ISubmitterControl $button) {
        $this->counter(-1);
        $this->removeComponent($this->getComponent('form'));
        $this->template->form = $this->getComponent('form');
    }
    
}
