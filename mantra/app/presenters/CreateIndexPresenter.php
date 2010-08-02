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
        $this->template->form = $this->getComponent('form');
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $form->addGroup();
        
        $form->addText('name', 'Index name')
            ->addCondition(Form::FILLED)
            ->addRule(Form::REGEXP, 'Index name includes an invalid character. Allowed are all ASCII characters except controls, space, ", $ and DEL.', 
                '/^[!#\x25-\x7E]+$/');
        
        $keys   = $form->addContainer('key');
        $orders = $form->addContainer('order');
        
        $count = $this->counter();
        for ($n = 0; $n < $count; $n++) {
            $keys->addText($n, 'Key')
                ->addCondition(Form::FILLED)
                    ->addRule(Form::REGEXP, 'Key name include an invalid character. Only letters, numbers and underscore are allowed.', 
                        '/^(([ !"#\x25-\x2D\x2F-\x7E][\x20-\x2D\x2F-\x7E]*)|\$)(\.(([ !"#\x25-\x2D\x2F-\x7E][\x20-\x2D\x2F-\x7E]*)|\$))*$/');
            $orders->addCheckbox($n, 'descending');
        }
        
        $form->addSubmit('more', '+')->onClick[] = array($this, 'more');
        $form->addSubmit('less', '−')->onClick[] = array($this, 'less');
        if ($count < 2) $form['less']->setDisabled();
        
        $form->addCheckbox('unique', 'Unique index');
        $form->addCheckbox('dropDups', 'Drop duplicates on unique');
        $form->addCheckbox('background', 'Create in background');
        
        $form->addSubmit('create', 'Create index')->onClick[] = array($this, 'createIndex');
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
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
            $button->parent->addError('You must specify at least one key.');
            return;
        }
        
        $options = array();
        if ($values['unique']) $options['unique'] = 1;
        if ($values['dropDups']) $options['dropDups'] = 1;
        if ($values['background']) $options['background'] = 1;
        if ($values['name']) $options['name'] = $values['name'];
        
        $this->db->createIndex($keys, $options, $this->collection, $this->database);
        
        $this->flashMessage("Index " . ($values['name'] ? "'$values[name]' " : '') . "on collection '$this->database.$this->collection' was created.");
        
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
