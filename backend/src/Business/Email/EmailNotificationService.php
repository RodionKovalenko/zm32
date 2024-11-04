<?php

namespace App\Business\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailNotificationService
{
    private $mailer;

    public function __construct(private readonly ?string $exportMailSender, private readonly ?string $exportMailSenderPassword)
    {
        // Initialize PHPMailer
        $this->mailer = new PHPMailer(true);

        // Set up SMTP or basic mail configurations here
        $this->configureSMTP();
    }

    private function configureSMTP()
    {
        // Configure SMTP settings
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com'; // Example for Gmail
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->exportMailSender;
        $this->mailer->Password = $this->exportMailSenderPassword;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587; // TCP port to connect to
    }

    public function sendEmail($to, $subject, $body, $altBody = '', $attachments = [])
    {
        try {
            // Set sender and recipient, making sure they are strings
            if (is_array($to)) {
                throw new \Exception("Recipient address must be a string, array given.");
            }
            $this->mailer->setFrom('rodion.kovalenko@npo-applications.de', 'Rodion Kovalenko');
            $this->mailer->addAddress($to);

            // Validate and set subject
            if (is_array($subject)) {
                throw new \Exception("Email subject must be a string, array given.");
            }
            $this->mailer->Subject = $subject;

            // Email content
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = $altBody ?: strip_tags($body);

            // Attach files if provided
            foreach ($attachments as $filePath) {
                if (file_exists($filePath)) {
                    $this->mailer->addAttachment($filePath);
                }
            }

            // Send email
            return $this->mailer->send();
        } catch (Exception $e) {
            // Log and handle error
            error_log("Email could not be sent. Error: {$this->mailer->ErrorInfo}");
            return false;
        } catch (\TypeError $te) {
            error_log("TypeError: " . $te->getMessage());
            return false;
        }
    }

    /**
     * Send a templated email
     *
     * @param string $to Recipient email address
     * @param string $templatePath Path to the email template
     * @param array $templateVars Variables for the email template
     * @param string $subject Email subject
     * @return bool True if email was sent successfully, False otherwise
     */
    public function sendTemplatedEmail($to, $templatePath, $templateVars, $subject)
    {
        // Load the template file
        $body = $this->loadTemplate($templatePath, $templateVars);
        if (!$body) {
            return false;
        }

        // Send the email
        return $this->sendEmail($to, $subject, $body);
    }

    /**
     * Load an email template and replace variables
     *
     * @param string $templatePath Path to the template file
     * @param array $templateVars Associative array of variables for template replacement
     * @return string|false Processed template content or false if template file not found
     */
    private function loadTemplate($templatePath, $templateVars)
    {
        if (!file_exists($templatePath)) {
            return false;
        }

        // Load template content
        $templateContent = file_get_contents($templatePath);

        // Replace variables
        foreach ($templateVars as $key => $value) {
            $templateContent = str_replace("{{ $key }}", $value, $templateContent);
        }

        return $templateContent;
    }
}
