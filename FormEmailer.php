<?php

use Processwire\WireMail;

class FormEmailer{

    public static function sendEmail($to, $from, $subject, $data){
        $template = self::template($data);
        $mail = new WireMail();
        $mail->to($to);
        $mail->from($from);
        $mail->subject($subject);
        $mail->bodyHTML($template);
        $mail->send();
    }

    public static function template($data){
        $templateContent = "";
        foreach ($data['formFields'] as $field) {
            $templateContent .= "<p>{$field['label']}: {$field['value']}</p>";
        }

        return str_replace("{{content}}", $templateContent, file_get_contents(__DIR__ . '/email/template.html') );
    }
}