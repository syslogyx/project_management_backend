<?php
namespace App;

use PHPMailer\PHPMailer;

/**
 * @author Kalyani
 */
class MailUtility
{

    //Send an email
    public static function sendMail($subject, $mailBody, $recipients)
    {

        if (!empty($recipients) && count($recipients) > 0) {

            require base_path() . "/vendor/phpmailer/phpmailer/src/PHPMailer.php";
            require base_path() . "/vendor/phpmailer/phpmailer/src/SMTP.php";

            $mail = new PHPMailer\PHPMailer();
            $mail->IsSMTP(); // enable SMTP

            // $mail->SMTPDebug = 4; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = false; //'tls'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "mail.syslogyx.com";
            $mail->Port = 25; // or 587
            $mail->IsHTML(true);
            $mail->Username = "projectmg@syslogyx.com";
            $mail->Password = "J13sui2%";
            $mail->SetFrom("projectmg@syslogyx.com");
            $mail->Subject = $subject;
            $mail->Body = $mailBody;

            foreach ($recipients as $key => $value) {
                $mail->AddAddress($value);
                // $mail->
            }

            //Keep recipients in cc
            // $mail->addCC("joe@site.com","Joe Tailer");
            //Keep recipients in bcc
            // $mail->addBCC("joe@site.com","Joe Tailer");

            if (!$mail->Send()) {
                // echo "Mailer Error: " . $mail->ErrorInfo;
                return false;
            } else {
                // echo "Message has been sent";
                return true;
            }
        }

        return false;
    }

}
