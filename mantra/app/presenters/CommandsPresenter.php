<?php

class CommandsPresenter extends BasePresenter {
    
    public function actionDefault() {
        
        $commands = $this->db->getInfo()->getCommandList();
        
        foreach ($commands as $name => $command) {
            $help = str_replace("\n", '<br>', $command['help']);
            $help = preg_replace_callback('/(http:\\S+)/umi', array(__CLASS__, 'linkCb'), $help);
            $commands[$name]['help'] = $help;
        }
        
        $this->template->commands = $commands;
        
    }
    
    /**
     * TODO: sanitize!
     */
    public function linkCb($address) {
        return "<a href='$address[0]'>$address[0]</a>";
    }
    
}
