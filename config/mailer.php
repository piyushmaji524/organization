<?php
if (defined('MAILER_PHP_LOADED')) return;
define('MAILER_PHP_LOADED', true);
// ============================================================
// PHPMailer Configuration
// Install via: composer require phpmailer/phpmailer
// Update SMTP credentials before deploying
// ============================================================

require_once __DIR__ . '/db.php';

// If PHPMailer (vendor/) is not installed, define silent stubs so API endpoints
// still work without email — forms submit successfully, notifications are skipped.
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    if (!function_exists('notify_contact'))           { function notify_contact(array $msg): void {} }
    if (!function_exists('notify_rsvp'))              { function notify_rsvp(array $rsvp, string $eventTitle): void {} }
    if (!function_exists('notify_application'))       { function notify_application(array $app): void {} }
    if (!function_exists('notify_application_status')){ function notify_application_status(string $email, string $name, string $status, string $note): void {} }
    return;
}

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function get_notification_email(): string {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'notification_email'");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 'admin@sarakyouth.org';
    } catch (Exception $e) {
        return 'admin@sarakyouth.org';
    }
}

function send_email(string $to, string $toName, string $subject, string $htmlBody): bool {
    $mail = new PHPMailer(true);
    try {
        // SMTP Config — loaded from environment variables
        $mail->isSMTP();
        $mail->Host       = getEnv('SMTP_HOST', 'smtp.hostinger.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = getEnv('SMTP_USERNAME', 'contact@yourdomain.com');
        $mail->Password   = getEnv('SMTP_PASSWORD', '');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)getEnv('SMTP_PORT', '993');
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(
            getEnv('SMTP_FROM_EMAIL', 'noreply@sarakyouth.org'),
            getEnv('SMTP_FROM_NAME', 'Sarak Youth Development Council')
        );
        $mail->addAddress($to, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = email_template($subject, $htmlBody);
        $mail->AltBody = strip_tags($htmlBody);
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

function email_template(string $title, string $body): string {
    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;background:#FAF5EE;margin:0;padding:20px}
.card{background:#fff;max-width:600px;margin:0 auto;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1)}
.header{background:#7B1C2E;color:#C9A84C;padding:30px;text-align:center}
.header h1{margin:0;font-size:22px}
.body{padding:30px;color:#333;line-height:1.6}
.footer{background:#1A0A0F;color:#888;padding:15px;text-align:center;font-size:12px}
</style></head>
<body>
<div class="card">
  <div class="header"><h1>Sarak Youth Development Council</h1></div>
  <div class="body">$body</div>
  <div class="footer">© Sarak Youth Development Council | Powered by Gunayatan</div>
</div>
</body></html>
HTML;
}

// ── Notification helpers ──────────────────────────────────

if (!function_exists('notify_contact')):
function notify_contact(array $msg): void {
    $to = get_notification_email();
    $body = "<h3>New Contact Message</h3>
             <p><strong>From:</strong> {$msg['name']} ({$msg['email']})</p>
             <p><strong>Phone:</strong> {$msg['phone']}</p>
             <p><strong>Subject:</strong> {$msg['subject']}</p>
             <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($msg['message'])) . "</p>";
    send_email($to, 'Admin', 'New Contact Message — ' . $msg['subject'], $body);
}

function notify_rsvp(array $rsvp, string $eventTitle): void {
    $to = get_notification_email();
    $body = "<h3>New RSVP Received</h3>
             <p><strong>Event:</strong> $eventTitle</p>
             <p><strong>Name:</strong> {$rsvp['name']}</p>
             <p><strong>Phone:</strong> {$rsvp['phone']}</p>
             <p><strong>Email:</strong> {$rsvp['email']}</p>
             <p><strong>Attendees:</strong> {$rsvp['attendee_count']}</p>";
    send_email($to, 'Admin', "New RSVP: $eventTitle", $body);
}

function notify_application(array $app): void {
    $to = get_notification_email();
    $body = "<h3>New Membership Application</h3>
             <p><strong>Name:</strong> {$app['full_name']}</p>
             <p><strong>Age:</strong> {$app['age']}</p>
             <p><strong>Phone:</strong> {$app['phone']}</p>
             <p><strong>Email:</strong> {$app['email']}</p>
             <p><strong>Education:</strong> {$app['education']}</p>
             <p><strong>Occupation:</strong> {$app['occupation']}</p>
             <p><strong>Referral:</strong> {$app['referral']}</p>";
    send_email($to, 'Admin', 'New Membership Application — ' . $app['full_name'], $body);
}

function notify_application_status(string $email, string $name, string $status, string $note): void {
    if (empty($email)) return; // phpcs:ignore
    if ($status === 'approved') {
        $subject = 'Congratulations! Your Membership is Approved';
        $body = "<h3>Welcome to the Sarak Youth Development Council!</h3>
                 <p>Dear $name,</p>
                 <p>We are delighted to inform you that your membership application has been <strong>approved</strong>.</p>
                 <p>You are now an official member of the Sarak Youth Development Council. We look forward to your active participation.</p>
                 " . ($note ? "<p><strong>Note from Admin:</strong> $note</p>" : '') . "
                 <p>Warmly,<br>Secretary, Sarak Youth Development Council</p>";
    } else {
        $subject = 'Update on Your Membership Application';
        $body = "<h3>Membership Application Update</h3>
                 <p>Dear $name,</p>
                 <p>Thank you for applying to the Sarak Youth Development Council. After careful review, we regret to inform you that your application could not be approved at this time.</p>
                 " . ($note ? "<p><strong>Reason:</strong> $note</p>" : '') . "
                 <p>You are welcome to apply again in the future.</p>
                 <p>Regards,<br>Secretary, Sarak Youth Development Council</p>";
    }
    send_email($email, $name, $subject, $body);
}
endif; // function_exists('notify_contact')
