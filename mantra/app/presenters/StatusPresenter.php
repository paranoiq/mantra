<?php

use Mantra\FormFactory;
use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;

class StatusPresenter extends BasePresenter {
    
    public function actionDefault() {
        
        $this->template->version = $this->db->getInfo()->getVersionInfo();
        
        $status = $this->db->getInfo()->getServerStatus();
        $status['uptime'] = round($status['uptime'] / 3600, 1) . ' h';
        $status['uptimeEstimate'] = round($status['uptimeEstimate'] / 3600, 1) . ' h';
        $status['globalLock']['totalTime'] = round($status['globalLock']['totalTime'] / 1000000, 3) . ' s';
        $status['globalLock']['lockTime'] = round($status['globalLock']['lockTime'] / 1000000, 3) . ' s';
        $status['globalLock']['ratio'] = number_format($status['globalLock']['ratio'] * 100, 6, '.', '') . ' %';
        $status['backgroundFlushing']['average_ms'] = round($status['backgroundFlushing']['average_ms'], 3);
        $status['mem']['resident'] .= ' MB';
        $status['mem']['virtual'] .= ' MB';
        $status['mem']['mapped'] .= ' MB';
        $this->template->status = $status;
        
        $this->template->masterSlave = $this->db->isMaster() ? 'master' : 'slave';
        
        $this->template->cmdLine = implode(' ', $this->db->getInfo()->getStartupOptions());
        
        $servers = $this->db->getServers();
        list($server, $port) = explode(':', $servers[0]);
        $this->template->firstServer = $server;
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form', FormFactory::NAKED);
        
        $form->addSubmit('shutdown', t('Shutdown server'))->onClick[] = array($this, 'shutdownServer');
        //$form->addSubmit('lock', 'Lock write')->onClick[] = array($this, 'lockWrite');
        //$form->addSubmit('unlock', 'Unlock write')->onClick[] = array($this, 'unlockWrite');
        
        //$form[($this->db->isLocked() ? 'lock' : 'unlock')]->setDisabled();
        
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
        return $form;
    }
    
    public function shutdownServer(ISubmitterControl $button) {
        $this->db->shutdownServer();
        
        $this->flashMessage(t('Server was shut down.'));
        
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
