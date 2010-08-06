<?php

use Nette\Environment;
use Nette\Application\Presenter;
use Nette\SmartCachingIterator;
use Nette\Forms\Form;
use Mantra\Language;
use Mantra\FormFactory;

class BasePresenter extends Presenter {
    
    /** @var Phongo\IConnection */
    protected $db;
    /** @var string language */
    public $lang;
    
    /** @var string
     *  @persistent */
    public $database;
    /** @var string
     *  @persistent */
    public $collection;
    
    
    public function startup() {
        parent::startup();
        
        // language
        $this->lang = Language::detectLanguage();
        $this->template->lang = $this->lang;
        
        // connection
        $this->db = Environment::getService('Phongo\IConnection');
        $this->db->connect();
        $this->db->setSafeMode();
        
        // server status
        $this->template->locked = $this->db->isLocked();
        
        // servers
        $servers = $this->db->getServers();
        $servers = array_map(function($item) { return preg_replace('/:27017$/', '', $item); }, $servers);
        $this->template->servers = implode(',', $servers);
        
        // server status
        $this->template->master = $this->db->isMaster() ? 'master' : 'slave';
        
        // collections
        if ($this->database) {
            $collList = $this->db->info->getCollectionList($this->database);
            $this->template->collList = $collList;
        } else {
            $this->template->collList = array();
        }
    }
    
    
    public function beforeRender() {
        $langForm = $this->getComponent('langForm');
        $langForm['language']->setValue($this->lang);
        
        $dbForm = $this->getComponent('dbForm');
        $dbForm['database']->setValue($this->database);
    }
    
    
    public function createComponentLangForm($name) {
        $form = FormFactory::create($this, 'langForm', NULL, FormFactory::NAKED);
        $form->onSubmit[] = array($this, 'selectLanguage');
        
        $select = $form->addSelect('language', 'Language:', Language::getAvailableLanguages());
        $select->getControlPrototype()->attrs['onchange'] = 'this.form.submit();';
        
        return $form;
    }
    
    public function createComponentDbForm($name) {
        $form = FormFactory::create($this, 'dbForm', NULL, FormFactory::NAKED);
        $form->onSubmit[] = array($this, 'selectDatabase');
        
        $dbList = $this->db->info->getDatabaseList();
        $dbs = array();
        foreach ($dbList as $db) {
            $dbs[$db] = $db;
        }
        
        $select = $form->addSelect('database', NULL, array_merge(array('(select database)'), $dbs))->skipFirst();
        $select->getControlPrototype()->attrs['onchange'] = 'this.form.submit();';
        
        return $form;
    }
    
    
    public function selectLanguage(Form $form) {
        $lang = $form['language']->getValue();
        $this->lang = $lang;
        
        $session = Environment::getSession('default');
        $session->lang = $lang;
    }
    
    
    public function selectDatabase(Form $form) {
        $this->database = $form['database']->getValue();
        $this->redirect('Database:default', array('database' => $this->database, 'collection' => NULL));
    }
    
    
    public function formatTemplateFiles($presenter, $view) {
        $appDir = Environment::getVariable('appDir');
        return array(
            "$appDir/presenters/$presenter.$view.phtml");
    }
    
}
