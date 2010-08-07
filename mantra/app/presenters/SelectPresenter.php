<?php

use Mantra\FormFactory;
use Mantra\Formater;
use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;
use Nette\Environment;
use Nette\Paginator;

class SelectPresenter extends BasePresenter {
    
    /** @persistent */
    public $q;
    
    
    public function actionDefault() {
        $this->counter(count($this->getRequest()->getParams()) <= 3 ? NULL : 0);
        
        $form = $this->getComponent('form');
        $form->setDefaults(array('limit' => 25));
        $values = $form->getValues();
        
        // query
        $query = array();
        if ($values['query']) $query = $values['query'];
        
        $cursor = $this->db->getDatabase($this->database)->find($query, NULL, $this->collection);
        
        // sorting
        if ($values['key']) {
            $order = array();
            foreach($values['key'] as $i => $key) {
                if (!$key) continue;
                $order[$key] = !empty($values['order'][$i]) ? -1 : 1;
            }
            $cursor->orderBy($order);
        }
        
        // pagination
        if (!$values['limit']) {
            $values['limit'] = 25;
            $form['limit']->value = 25;
        }
        $paginator = $this->preparePaginator($cursor->count(), $values['limit'], $values['page'], !$values['p']);
        $cursor->setLimit($values['limit']);
        $cursor->setOffset($paginator->offset);
        
        // fetching
        $items = array();
        while ($item = $cursor->fetch()) {
            $id = (string) $item['_id'];
            unset($item['_id']);
            
            $formater = new Formater();
            $formater->html = TRUE;
            $i = $formater->formatJson($item, TRUE);
            
            $items[$id] = $i;
        }
        $this->template->items = $items;
        
        $this->template->actionForm = $this->getComponent('actionForm');
        $this->template->form = $form;
        $form['p']->setValue(0);
    }
    
    private function preparePaginator($count, $limit, $page, $reset) {
        $session = Environment::getSession('Select');
        if ($reset) {
            $page = 1;
            $session->prevPage = $count / $limit / 2;
        }
        
        $paginator = new Paginator();
        $paginator->itemCount = $count;
        $paginator->itemsPerPage = $limit;
        $paginator->setPage($page);
        
        if ($paginator->pageCount < 2) {
            $steps = array($page);
        } else {
            $arr = range(max($paginator->firstPage, $page - 2), min($paginator->lastPage, $page + 2));
            
            $arr[] = $paginator->firstPage;
            $arr[] = round(($paginator->firstPage + $page) / 2, 0);
            $arr[] = $paginator->lastPage;
            $arr[] = round(($paginator->lastPage + $page) / 2, 0);
            
            $jump = abs($session->prevPage - $page);
            $arr[] = max(round($page - $jump / 2, 0), 1);
            $arr[] = max(round($page - $jump / 4, 0), 1);
            $arr[] = min(round($page + $jump / 4, 0), $paginator->lastPage);
            $arr[] = min(round($page + $jump / 2, 0), $paginator->lastPage);
            
            sort($arr);
            $steps = array_values(array_unique($arr));
        }
        
        $session->prevPage = $page;
        
        $query = Environment::getHttpRequest()->getUri()->getQuery();
        $query = preg_replace('/&page=[0-9]*/', '', $query);
        $query = preg_replace('/&p=[0-9]*/', '', $query); 
        $this->template->query = $query;
         
        $this->template->steps = $steps;
        $this->template->paginator = $paginator;
        
        return $paginator;
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form', Form::GET);
        
        $form->addTextArea('query', 'Query (JSON)', 60, 6)
            ->setEmptyValue('{"": ""}');
        
        $keys = $form->addContainer('key');
        $orders = $form->addContainer('order');
        
        $count = $this->counter();
        for ($n = 0; $n < $count; $n++) {
            $keys->addText($n)
                ->addCondition(Form::FILLED)
                    ->addRule(Form::REGEXP, 'Key name include an invalid character. Only letters, numbers and underscore are allowed.', 
                        '/^(([ !"#\x25-\x2D\x2F-\x7E][\x20-\x2D\x2F-\x7E]*)|\$)(\.(([ !"#\x25-\x2D\x2F-\x7E][\x20-\x2D\x2F-\x7E]*)|\$))*$/');
            $orders->addCheckbox($n, 'descending');
        }
        
        $form->addSubmit('more', '+')->onClick[] = array($this, 'more');
        $form->addSubmit('less', '−')->onClick[] = array($this, 'less');
        if ($count < 2) $form['less']->setDisabled();
        
        $form->addText('limit')
            ->addCondition(Form::FILLED)
                ->addRule(Form::INTEGER, 'Limit must be a positive number.')
                ->addRule(Form::RANGE, 'Limit must be a positive number.', array(1, PHP_INT_MAX));
        
        $form->addHidden('p', 0);
        $form->addHidden('page', 1);
        
        $form->addSubmit('select', 'Select');
        
        return $form;
    }
    
    public function createComponentActionForm() {
        $form = FormFactory::create($this, 'actionForm');
        
        $items = $form->addContainer('item');
        foreach ($this->template->items as $id => $item) {
            $items->addCheckbox($id);
        }
        
        $form->addCheckbox('all', 'all matching items');
        
        $form->addSubmit('update', 'Update')->onClick[] = array($this, 'updateItems');
        $form->addSubmit('clone', 'Clone')->onClick[] = array($this, 'cloneItems');
        $form->addSubmit('delete', 'Delete')->onClick[] = array($this, 'deleteItems');
        
        $form->addSelect('exportAction', NULL, 
            array('save' => 'save' , 'open' => 'open', 'zip' => 'zip'));
        $form->addSelect('exportFormat', NULL, 
            array('json' => 'JSON', 'yaml' => 'YAML', 'inline' => 'inline YAML'));
        $form->addSubmit('export', 'Export')->onClick[] = array($this, 'exportItems');
        
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        return $form;
    }
    
    public function deleteItems(ISubmitterControl $button) {
        $rawData = $button->parent->getHttpData();
        if (!isset($rawData['item'])) return;
        
        $deleted = 0;
        foreach ($rawData['item'] as $id => $on) {
            if ($on != 'on') continue;
            if (!preg_match('/^[0-9a-f]{24}$/i', $id)) continue;
            
            $this->db->getDatabase($this->database)->delete(array('_id' => new MongoId($id)), TRUE, $this->collection);
            $deleted++;
        }
        
        if ($deleted) $this->flashMessage("$deleted items deleted from '$this->database.$this->collection'.");
        
        $this->removeComponent($this->getComponent('actionForm'));
        $this->actionDefault();
    }
    
    public function updateItems(ISubmitterControl $button) {
        $rawData = $button->parent->getHttpData();
        if (!isset($rawData['item'])) return;
        
        $items = array();
        foreach ($rawData['item'] as $id => $on) {
            if ($on != 'on') continue;
            if (!preg_match('/^[0-9a-f]{24}$/i', $id)) continue;
            $items[] = $id;
        }
        
        $this->forward('Update:default', array('items' => $items));
    }
    
    private function counter($inc = 0) {
        $session = Environment::getSession('Select');
        
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
