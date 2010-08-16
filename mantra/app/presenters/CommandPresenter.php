<?php

use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;
use Mantra\FormFactory;
use Mantra\Formater;
use Nette\Json;

class CommandPresenter extends BasePresenter {
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $form->addGroup();
        $form->addTextArea('command', t('Command (JSON)'), 80, 20)
            ->setEmptyValue('{"": 1}')
            ->addRule(Form::FILLED, t('Command must be filled.'));
        
        $form->addSubmit('run', t('Run command'))->onClick[] = array($this, 'runCommand');
        
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
        return $form;
    }
    
    public function runCommand(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        $result = $this->db->database($this->database)->runCommand($values['command']);
        
        //dump($result);
        
        //$this->flashMessage("Command succesfully runned on '$this->database'.");
        
        $formater = new Formater;
        $formater->html = TRUE;
        $this->template->result = $formater->formatJson($result);
        $this->template->copy   = Json::encode($result);
    }
    
}
