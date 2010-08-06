<?php

namespace Mantra;

use Nette\Application\AppForm;

/**
 * Mantra Form factory
 * 
 * @author     Vlasta Neubauer
 * @copyright  2009 VSP Data a.s.
 * @package    NAIS
 */
class FormFactory {
    
    const NAKED = TRUE;
    
    /**
     * @return Nette\AppForm
     */
    static public function create($parent, $name, $method = NULL, $naked = FALSE) {
        $form = new AppForm();
        if ($method) $form->setMethod($method);
        $parent->addComponent($form, $name);
        
        //$form->renderer->wrappers['control']['.reset'] = 'button';
        $form->renderer->wrappers['label']['requiredsuffix'] = ' •';
        $form->renderer->wrappers['label']['container'] = 'td';
        
        if ($naked) {
            $form->renderer->wrappers['controls']['container'] = NULL;
            $form->renderer->wrappers['pair']['container']     = NULL;
            $form->renderer->wrappers['label']['container']    = NULL;
            $form->renderer->wrappers['control']['container']  = NULL;
        }
        
        return $form;
    }
    
    
    /**
     * Register Nette\FormContainer extensions
     */
    static public function register() {
        //Ruller::register();
        //Reset::register();
        //IconSubmitButton::register();
    } 
    
}