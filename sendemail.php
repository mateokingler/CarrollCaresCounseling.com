<?php
/**
 * Contact Form Handler for Carroll Cares Counseling
 * Securely processes contact form submissions
 */

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Configuration - UPDATE THIS TO YOUR ACTUAL EMAIL
define('SITE_EMAIL', 'anna@carrollcarescounseling.com'); // Change to your actual email

// Sanitize and validate input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get and sanitize form data
$name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
$phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
$subject = isset($_POST['subject']) ? sanitize_input($_POST['subject']) : 'Contact Form Submission';
$message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';

// Validate required fields
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validate_email($email)) {
    $errors[] = 'Please enter a valid email address';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// Check for errors
if (!empty($errors)) {
    echo "<div class='alert alert-danger'>" . implode('<br>', $errors) . "</div>";
    exit;
}

// Prepare email
$to = SITE_EMAIL;
$email_subject = "New Contact Form Submission: " . $subject;

// Build HTML message body
$email_body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Contact Form Submission</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4a7c59; border-bottom: 2px solid #4a7c59; padding-bottom: 10px;">
            New Contact Form Submission
        </h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; width: 30%;">Name:</td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">' . $name . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Email:</td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">' . $email . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Phone:</td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">' . $phone . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Subject:</td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">' . $subject . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold; vertical-align: top;">Message:</td>
                <td style="padding: 10px;">' . nl2br($message) . '</td>
            </tr>
        </table>
        <p style="margin-top: 20px; font-size: 12px; color: #666;">
            This message was sent from the Carroll Cares Counseling website contact form.
        </p>
    </div>
</body>
</html>';

// Set headers - use site email as From to avoid spam filters
// Reply-To allows you to reply directly to the person who submitted the form
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: Carroll Cares Counseling <" . SITE_EMAIL . ">\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email
$mail_sent = mail($to, $email_subject, $email_body, $headers);

if ($mail_sent) {
    echo "<div class='alert alert-email-success'>Thank you, we have received your message and will get back to you shortly.</div>";
} else {
    echo "<div class='alert alert-danger'>Sorry, there was an error sending your message. Please try again or call us directly at (913) 213-1465.</div>";
}
?>
