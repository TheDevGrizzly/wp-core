<?php

namespace WpCore\Mails;

use WpCore\Bootstrap\Blade;

/**
 * Class Mails, basic mail
 */
class Mail
{
    /**
     * Subject of the mail
     * @var string
     */
    protected $subject = '';

    /**
     * Blade template of the mail
     * @var string
     */
    protected $template;

    /**
     * Recipients of the mail
     * @var string|array
     */
    protected $tos;

    /**
     * Content of the mail
     * @var string
     */
    protected $content = '';

    /**
     * Headers of the mail
     * @var array
     */
    protected $headers = [
        'Content-Type: text/html; charset=UTF-8'
    ];

    /**
     * Attachment of the mail
     * @var array
     */
    protected $attachment = [];

    /**
     * Init mail
     * @param string|array $tos List of emails to send the mail to
     */
    public function __construct($tos)
    {
        if (!is_array($tos)) {
            $tos = [$tos];
        }
        $this->tos = $tos;
    }

    /**
     * Send mail
     * @param  array  $with Data to pass to the mail
     */
    public function send($with = [])
    {
        ob_start();
            $this->view($this->template, $with);
            $this->content = ob_get_contents();
        ob_end_clean();

        wp_mail($this->tos, $this->subject, $this->content, $this->headers, $this->attachment);
    }

    /**
     * Render view for the mail
     * @param  string $view View to render
     * @param  array  $with Data to pass to the view
     */
    public function view($view, $with = [])
    {
        echo Blade::render($view, $with);
    }

    /**
     * Add attachment to the mail
     * @param  string $file File path to add to the mail
     */
    public function attach($file)
    {
        $this->attachment[] = $file;
    }
}
