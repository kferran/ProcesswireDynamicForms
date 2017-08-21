<?php

require_once __DIR__ .'/FormProcessor.php';
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
		wire('config')->scripts->add(wire('config')->urls->ProcessDynamicForms . 'client/public/javascripts/app.js');
		// wire('config')->scripts->add('/javascripts/app.js');

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
	}

	public function  ___executeGetForm(){
		if(!$formId = $this->input->get->id){
			exit;
		}

		$page = $this->pages->get("id=$formId");
		$entries = [];
		if($page->hasChildren()) {
			foreach ($page->children() as $entry) {
				$formEntry = json_decode($entry->dynamic_form_entry);
				$entries[] = [
					'title' => $entry->title,
					'id' => $entry->id,
					'fields' => $formEntry->formFields
				];
			}
		}

		echo json_encode([
			'formSettings' => $page->dynamic_form_settings ? json_decode($page->dynamic_form_settings) : [],
			'formFields' => $page->dynamic_form_fields ? json_decode($page->dynamic_form_fields) : [],
			'formEntries' => $entries,
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
		$id = $post['formId'];
		$page = $this->pages->get("id=$id");
		$page->dynamic_form_settings = $post['data'];
		$page->save();
		exit;
	}

	public function ___executeSaveForm(){
		$post = json_decode(file_get_contents("php://input"), true);
		$p = new \Processwire\Page();
		$title = $post['form']['formName'];
        $p->template 			= $this->templates->get('dynamic_form_entry');;
	    $p->parent 				= $this->pages->get('/admin/setup/dynamicforms/');
        $p->title = $title;
        $p->name = $this->sanitizer->pageName( time() . $title );
		$p->save();
		echo json_encode(['formId' => $p->id]);
		exit;
	}

	public function ___executeDeleteForm(){
		$post = json_decode(file_get_contents("php://input"), true);
		$form = $this->pages->get($post['formId']);
		$this->pages->delete($form);
		exit;
	}

	public function store($data){
		$this->formTemplate = $this->pages->get("id={$data['formTemplateId']}");

		if($data['formSettings']){
			$this->emailSubject = $data['formSettings']['subject'];

			if($data['formSettings']['to']){
				$this->emailTo = $data['formSettings']['to'];
			}

			if($data['formSettings']['from']){
				$this->emailFrom = $data['formSettings']['from'];
			}

			foreach ($data['formSettings']['conditionalTos'] as $to) {
				foreach ($data['formFields'] as $field) {
					if($field['label'] == 'Email Address'){
						$this->emailFrom = $field['value'];
					}
					// If label (I am contacting because) == I am contacting because and value (Shipping == Shipping)
					if($field['label'] == $to['field'] && $to['value'] == $field['value']){
						$this->emailTo = $to['to'];
					}
				}
			}
		}

		$this->saveFormSubmission($data);
	}

	public function saveFormSubmission($data){
		// Validate
		foreach ($data['formFields'] as $index => $field) {
            if(isset($field['value'])){
				$field['value'] = $this->sanitizer->text($field['value'], ['maxLength' => 16384]);
			}
		}

		// Save Page
		$p = new \Processwire\Page();
	    $title 					= sprintf("%s %s", $this->formTemplate->title, " Submission");
        $p->template 			= $this->templates->get('dynamic_form_entry');;
	    $p->parent 				= $this->formTemplate;
        $p->dynamic_form_entry = json_encode($data);
        $p->title = $title;
        $p->name = $this->sanitizer->pageName( time() . $title );

		$p->save();

		// Send Email
		$this->sendEmail($data);

		// Success
		echo 'success';
		exit;
	}

	public function sendEmail($data){
        $template = $this->template($data);
        $mail = new WireMail();
        $mail->to($this->emailTo);
        $mail->from($this->emailFrom);
        $mail->subject($this->emailSubject);
        $mail->bodyHTML($template);
        $mail->send();
	}

	public function template($data){
        $templateContent = "";
        foreach ($data['formFields'] as $field) {
			if(isset($field['label']) && isset($field['value'])){
				$templateContent .= "<p><strong>{$field['label']}</strong> {$field['value']}</p>";
			}
        }

        return str_replace("{{content}}", $templateContent, file_get_contents(__DIR__ . '/email/template.html') );
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

