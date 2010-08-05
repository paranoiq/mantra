<?php

use Mantra\FormFactory;
use Mantra\Formater;
use Nette\Forms\ISubmitterControl;

class ProcessPresenter extends BasePresenter {
    
    public function actionDefault() {
        $formater = new Formater;
        $formater->html = TRUE;
        
        $processList = $this->db->getProcessList();
        foreach ($processList as $key => $process) {
            $processList[$key]['query'] = isset($process['query']) ? $formater->formatJson($process['query'] ?: new StdClass) : '';
            $processList[$key]['secs_running'] = isset($process['secs_running']) ?: 0;
            $processList[$key]['lockType'] = isset($process['lockType']) ?: '';
        }
        
        $this->template->processList = $processList;
        
        $this->template->form = $this->getComponent('form');
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $container = $form->addContainer('process');
        foreach ($this->template->processList as $process) {
            $container->addCheckbox($process['opid']);
        }
        
        $form->addSubmit('kill', "Kill process")->onClick[] = array($this, 'killProcess');
        
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function killProcess(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        
        foreach ($values['process'] as $processId => $checked) {
            if (!$checked) continue;
            
            $this->db->terminateProcess($processId);
            
            $this->flashMessage("Process '$processId' was killed.");
        }
        
        $this->redirect('Process:default');
    }
    
}
