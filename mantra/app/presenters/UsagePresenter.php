<?php

class UsagePresenter extends BasePresenter {
    
    public function actionDefault() {
        
        $this->template->usage = $this->db->info->getCollectionsUsage();
        
    }
    
}
