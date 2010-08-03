<?php

use Mantra\FormFactory;
use Phongo\Tools;
use Nette\Forms\ISubmitterControl;

class ProcessPresenter extends BasePresenter {
    
    public function actionDefault() {
        
        $this->db->getProcessList();
        
    }
    
}
