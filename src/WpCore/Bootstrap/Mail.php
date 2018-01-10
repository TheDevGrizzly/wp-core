<?php

namespace WpCore\Bootstrap;

/**
 * Class Mail for smtp parameters
 */
class Mail
{
    /**
     * Actions and filters to edit smtp parameters
     */
    public function __construct()
    {
        add_action('phpmailer_init', [$this, 'smptCredentials']);
        add_filter('wp_mail_from', [$this, 'setMailFromAddress']);
        add_filter('wp_mail_from_name', [$this, 'setMailFromName']);
    }

    /**
     * Edit smtp parameters
     * @param  \PHPMailer $mail PHPMailer instance
     * @return \PHPMailer       Edited PHPMailer instance
     */
    public function smptCredentials(\PHPMailer $mail)
    {
        $mail->IsSMTP();
        $mail->SMTPAuth = config('mail.username') && config('mail.password');

        $mail->Host = config('mail.host');
        $mail->Port = config('mail.port');
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');

        return $mail;
    }

    /**
     * Set mail from address
     */
    public function setMailFromAddress()
    {
        define('MAIL_FROM_ADDRESS', config('mail.from.address'));
        return MAIL_FROM_ADDRESS;
    }

    /**
     * Set mail from name
     */
    public function setMailFromName()
    {
        define('MAIL_FROM_NAME', config('mail.from.name'));
        return MAIL_FROM_NAME;
    }
}
