<?php

use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;
use Mantra\FormFactory;
use Mantra\Formater;
use Nette\Json;

class CommandPresenter extends BasePresenter {
    
    public function actionDefault($database) {
        $this->template->form = $this->getComponent('form');
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $form->addGroup();
        $form->addTextArea('command', 'Command (JSON)', 80, 20)
            ->setEmptyValue('{"": 1}')
            ->addRule(Form::FILLED, 'Command must be filled.');
        
        $form->addSubmit('run', 'Run command')->onClick[] = array($this, 'runCommand');
        
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function runCommand(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        $result = $this->db->runCommand($values['command'], $this->database);
        
        //dump($result);
        
        //$this->flashMessage("Command succesfully runned on '$this->database'.");
        
        $formater = new Formater;
        $formater->html = TRUE;
        $this->template->result = $formater->formatJson($result);
        $this->template->copy   = Json::encode($result);
    }
    
}
