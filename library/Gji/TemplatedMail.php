<?php

namespace Gji;

use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mime;
use Zend\Debug\Debug;

class TemplatedMail
{
    public static function createMailFromTemplate($renderer, $template, $vars)
    {
        $viewModel = new ViewModel;
        $viewModel->setTemplate($template)
            ->setVariables($vars['body']);
        $html = new Mime\Part($renderer->render($viewModel));
        $html->type = "text/html";
        $body = new Mime\Message;
        $body->setParts(array($html));
        $msg = new Mail\Message;
        $msg->setSubject($vars['subject'])
            ->setBody($body);
        return $msg;
    }
}
