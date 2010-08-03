<?php

use Mantra\FormFactory;
use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;

class StatusPresenter extends BasePresenter {
    
    public function actionDefault() {
        
        $this->template->version = $this->db->info->getVersionInfo();
        
        $this->template->status = $this->db->info->getServerStatus();
        
        $this->template->masterSlave = $this->db->isMaster() ? 'master' : 'slave';
        
        $this->template->cmdLine = implode(' ', $this->db->info->getStartupOptions());
        
        $servers = $this->db->getServers();
        list($server, $port) = explode(':', $servers[0]);
        $this->template->firstServer = $server;
        
        $this->template->form = $this->getComponent('form');
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form', FormFactory::NAKED);
        
        $form->addSubmit('shutdown', 'Shutdown server')->onClick[] = array($this, 'shutdownServer');
        //$form->addSubmit('lock', 'Lock write')->onClick[] = array($this, 'lockWrite');
        //$form->addSubmit('unlock', 'Unlock write')->onClick[] = array($this, 'unlockWrite');
        
        $form[($this->db->isLocked() ? 'lock' : 'unlock')]->setDisabled();
        
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function shutdownServer(ISubmitterControl $button) {
        $this->db->shutdownServer();
        
        $this->flashMessage("Server was shut down.");
        
        $this->redirect('Home:default');
    }
    
    public function lockWrite(ISubmitterControl $button) {
        $this->db->lockWrite();
        
        $this->flashMessage("Server was file synced and locked.");
        
        $this->redirect('Status:default');
    }
    
    public function unlockWrite(ISubmitterControl $button) {
        $this->db->unlockWrite();
        
        $this->flashMessage("Server was unlocked.");
        
        $this->redirect('Status:default');
    }
    
}
