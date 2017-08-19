<?php

/**
 * Dynamic Forms Process Module
 */

class ProcessDynamicForms extends Process {

	const PAGE_NAME = 'forms';
	/**
	 * This is an optional initialization function called before any execute functions.
	 *
	 * If you don't need to do any initialization common to every execution of this module,
	 * you can simply remove this init() method.
	 *
	 */
	public function init() {
		$this->headline('Forms');
		// wire('config')->styles->add(wire('config')->urls->ProcessDynamicForms . 'client/public/stylesheets/app.css');
		// wire('config')->scripts->add(wire('config')->urls->ProcessDynamicForms . 'client/public/javascripts/app.js');
		wire('config')->scripts->add('/javascripts/app.js');

		parent::init(); // always remember to call the parent init
	}

	/**
	 * This function is executed when a page with your Process assigned is accessed.
 	 *
	 * This can be seen as your main or index function. You'll probably want to replace
	 * everything in this function.
	 *
	 */
	public function ___execute() {
		$markup = $this->modules->get('InputfieldMarkup');
        $markup->value = '<div id="dynamic-forms"></div>';
        return $markup->render();

		return $out;
	}

	public function ___executeGetForms(){
		$pages = $this->page->children('include=all');
		$response = [];
		foreach ($pages as $page) {
			$response[] = [
				'name' => $page->title,
				'id' => $page->id,
			];
		}
		echo json_encode($response);
		exit;
		return;
	}

	public function  ___executeGetForm(){
		if(!$formId = $this->input->get->id){
			exit;
		}
		$page = $this->pages->get("id=$formId");
		$entries = [];
		if($page->hasChildren()) {
			foreach ($page->children() as $entry) {
				$entries[] = [
					'title' => $entry->title,
					'id' => $entry->id
				];
			}
		}

		echo json_encode([
			'formSettings' => $page->dynamic_form_settings ? $page->dynamic_form_settings : [],
			'formFields' => $page->dynamic_form_fields ? $page->dynamic_form_fields : [],
			'entries' => $entries,
		]);
		exit;
	}

	public function ___executeSaveFormFields(){
		$post = json_decode(file_get_contents("php://input"), true);
		$id = $post['id'];
		$page = $this->pages->get("id=$id");
		$page->dynamic_form_fields = $post['formFields'];
		$page->save();
		exit;
	}

	public function ___executeSaveFormSettings(){
		$post = json_decode(file_get_contents("php://input"), true);
		$id = $post['id'];
		$page = $this->pages->get("id=$id");
		$page->dynamic_form_settings = $post['data'];
		$page->save();
		exit;
	}

	/**
	 * Called only when your module is installed
	 *
	 * If you don't need anything here, you can simply remove this method.
	 *
	 */
	public function ___install() {
		$fields = [
			'dynamic_form_settings' => [
				'name' => 'dynamic_form_settings',
				'type' => 'FieldtypeTextarea',
				'label' => 'Form Settings'
			],
			'dynamic_form_fields' => [
				'name' => 'dynamic_form_fields',
				'type' => 'FieldtypeTextarea',
				'label' => 'Form Fields'
			],
			'dynamic_form_entry' => [
				'name' => 'dynamic_form_entry',
				'type' => 'FieldtypeTextarea',
				'label' => 'Form Entry'
			],
		];

		$templates = [
			'dynamic_form' => [
				'name' => 'dynamic_form',
				'label' => 'Form'
			],
			'dynamic_form_entry' => [
				'name' => 'dynamic_form_entry',
				'label' => 'Form Entry'
			]
		];

		foreach ($fields as $field) {
			if ($this->fields->get($field['name'])) {
				throw new WireException($this->_("Aborted installation. Looks like fields named 'form_settings' or 'form_fields' have already been installed."));
			}
		}

		foreach ($templates as $template) {
			if( $this->templates->get($template['name'])){
				throw new WireException($this->_("Aborted installation. Looks like a template with the name of form or form_entry have already been installed."));
			}
		}


		foreach ($fields as $field) {
			$f = new Field();
			$f->type = $this->modules->get($field['type']);
			$f->name = $field['name'];
			$f->label = $field['label'];
			$f->collapsed = 5;
			$f->maxlength = 2048;

			$f->tags = '-forms';
			$f->save();
		}

		foreach ($templates as $template) {
			$fieldGroup = new FieldGroup();
			$fieldGroup->name = $template['name'];
			$fieldGroup->add($this->fields->get('title'));

			foreach ($fields as $field) {
				// Only add the settings and form fields fields to the main form template
				if ($template['name']  == 'dynamic_form' && ($field['name'] == 'dynamic_form_settings' || $field['name'] == 'dynamic_form_fields')) {
					$f = $this->fields->get($field['name']);
					$fieldGroup->add($f);
				}else{
					$f = $this->fields->get($field['name']);
					$fieldGroup->add($f);
				}
			}
			$fieldGroup->save();

			// Create the templates
			$t = new Template();
			$t->name = $template['name'];
			$t->fieldgroup = $fieldGroup;
			$t->label = $template['label'];
			$t->parentTemplates = array($this->templates->get('admin')->id);// needs to be added as array of template IDs. Allowed template for parents = 'admin'
			$t->tags = '-forms';
			$t->save();
		}




		parent::___install(); // always remember to call parent method
	}

	/**
	 * Called only when your module is uninstalled
	 *
	 * This should return the site to the same state it was in before the module was installed.
	 *
	 * If you don't need anything here, you can simply remove this method.
	 *
	 */
	public function ___uninstall() {
		parent::___uninstall(); // always remember to call parent method
	}

}

