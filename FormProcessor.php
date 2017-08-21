<?php
use Processwire\Page;
class FormProcessor{

    public $emailTo;
    public $emailFrom;
    public $emailSubject;

    public function handleSubmission($data){



        $data = $this->validate($data);
        $page = $this->saveEntry($data);
        FormEmailer::sendEmail($page);
    }

    public function validate($data){
        foreach ($data['formFields'] as $index => $field) {
            if(isset($field['value'])){
				$field['value'] = $this->sanitizer->text($field['value'], ['maxLength' => 16384]);
			}
        }

        return $data;
    }

    public function saveEntry($data){
        $page = new Page();
	    $title 					= sprintf("%s %s", $this->formTemplate->title, " Submission");
        $page->template 			= $this->templates->get('dynamic_form_entry');;
	    $page->parent 				= $this->formTemplate;
        $page->dynamic_form_entry = json_encode($data);
        $page->title = $title;
        $page->name = $this->sanitizer->pageName( time() . $title );
        $page->save();

        return $page;
    }

    public function saveFormSubmission(){

    }


}