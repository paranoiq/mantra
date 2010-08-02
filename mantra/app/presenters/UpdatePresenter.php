<?php

use Mantra\FormFactory;
use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;

class UpdatePresenter extends BasePresenter {
    
    public function actionDefault($database) {
        $this->template->form = $this->getComponent('form');
    }
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $form->addGroup();
        $form->addTextArea('query', 'Query (JSON)', 80, 10)
            ->setEmptyValue('{"": ""}')
            ->addRule(Form::FILLED, 'Query must be specified.');
        
        $form->addTextArea('modifier', 'Modifier (JSON)', 80, 10)
            ->setEmptyValue('{"": ""}');
        
        $form->addCheckbox('delete', 'Delete matching items');
        $form->addCheckbox('upsert', 'Insert if no match found');
        $form->addCheckbox('single', 'Update/delete just one item');
        
        $form['modifier']->addConditionOn($form['delete'], Form::EQUAL, FALSE)
                ->addRule(Form::FILLED, 'When updating, you must specify the modifier.');
        
        //$form['delete']->addCondition(Form::EQUAL, TRUE)->toggle($form['modifier']->getHtmlId());
        //$form['delete']->addCondition(Form::EQUAL, TRUE)->toggle($form['modifier']->getLabelPrototype()->getHtmlId());
        //$form['delete']->addCondition(Form::EQUAL, TRUE)->toggle($form['upsert']->getHtmlId());
        //$form['delete']->addCondition(Form::EQUAL, TRUE)->toggle($form['upsert']->getLabelPrototype()->getHtmlId());
        
        $form->addSubmit('update', 'Update')->onClick[] = array($this, 'updateItems');
        
        $form->addProtection('Protection timeout expired. Pleas, try again.');
        
        $params = $this->getRequest()->getParams();
        if (isset($params['items'])) {
            $query = '{"_id": { "$in": [{"$oid": "' . implode('"}, {"$oid": "', $params['items']) . '"}]}}';
            $form->setDefaults(array('query' => $query));
        }
        
        return $form;
    }
    
    public function updateItems(ISubmitterControl $button) {
        $values = $button->parent->getValues();
        $delete = $values['delete'];
        
        //$this->db->setSafeMode();
        
        if ($delete) {
            $this->db->delete($values['query'], $values['single'], $this->collection, $this->database);
        } else {
            $this->db->update($values['query'], $values['modifier'], $values['single'], $values['upsert'], 
                $this->collection, $this->database);
        }
        
        $affected = $this->db->isSync() ? ' (' . $this->db->getAffectedItems() . ' items affected)' : ' (asynchronous)';
        
        if ($delete) {
            $this->flashMessage("Items in '$this->database.$this->collection' was deleted$affected.");
        } else {
            $this->flashMessage("Items in '$this->database.$this->collection' was updated$affected.");
        }
        
        //$this->redirect('Collection:default');
    }
    
}
