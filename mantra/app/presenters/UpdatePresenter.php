<?php

use Mantra\FormFactory;
use Nette\Forms\Form;
use Nette\Forms\ISubmitterControl;

class UpdatePresenter extends BasePresenter {
    
    public function createComponentForm() {
        $form = FormFactory::create($this, 'form');
        
        $form->addGroup();
        $form->addTextArea('query', t('Query (JSON)'), 80, 10)
            ->setEmptyValue('{"": ""}')
            ->addRule(Form::FILLED, t('Query must be specified.'));
        
        $form->addTextArea('modifier', t('Changes (JSON)'), 80, 10)
            ->setEmptyValue('{"": ""}');
        
        $form->addCheckbox('delete', t('Delete matching items'));
        $form->addCheckbox('upsert', t('Insert if no match found (upsert)'));
        $form->addCheckbox('single', t('Update/delete just one item'));
        
        $form['modifier']->addConditionOn($form['delete'], Form::EQUAL, FALSE)
                ->addRule(Form::FILLED, t('When updating, you must specify the changes.'));
        
        //$form['delete']->addCondition(Form::EQUAL, TRUE)->toggle($form['modifier']->getHtmlId());
        //$form['delete']->addCondition(Form::EQUAL, TRUE)->toggle($form['modifier']->getLabelPrototype()->getHtmlId());
        //$form['delete']->addCondition(Form::EQUAL, TRUE)->toggle($form['upsert']->getHtmlId());
        //$form['delete']->addCondition(Form::EQUAL, TRUE)->toggle($form['upsert']->getLabelPrototype()->getHtmlId());
        
        $form->addSubmit('update', t('Update'))->onClick[] = array($this, 'updateItems');
        
        $form->addProtection(t('Protection timeout expired. Pleas, try again.'));
        
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
        
        $db = $this->db->database($this->database);
        
        if ($delete) {
            $db->delete($values['query'], $values['single'], $this->collection);
        } else {
            $db->update($values['query'], $values['modifier'], $values['single'], $values['upsert'], $this->collection);
        }
        
        $affected = $db->isSync() ? ' (' . $db->getAffectedItems() . ' items affected)' : ' (asynchronous)';
        
        if ($delete) {
            $this->flashMessage("Items in '$this->database.$this->collection' was deleted$affected.");
        } else {
            $this->flashMessage("Items in '$this->database.$this->collection' was updated$affected.");
        }
        
        //$this->redirect('Collection:default');
    }
    
}
