<?php
/** Enquiry submission endpoint. Handles AJAX (JSON reply) and plain form POST (redirect). */
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mailer.php';

$ajax = (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch')
    || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

function reply($ajax, $ok, $msg, $code = 200)
{
    http_response_code($code);
    if ($ajax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok, 'message' => $ok ? $msg : null, 'error' => $ok ? null : $msg]);
    } else {
        $back = $_SERVER['HTTP_REFERER'] ?? url('contact.php');
        $sep = strpos($back, '?') !== false ? '&' : '?';
        redirect($back . $sep . ($ok ? 'sent=1' : 'err=1') . '#contact');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') reply($ajax, false, 'Method not allowed', 405);

$clean = fn($s, $max = 2000) => is_string($s) ? trim(mb_substr($s, 0, $max)) : '';
$name = $clean($_POST['name'] ?? '', 120);
$phone = $clean($_POST['phone'] ?? '', 40);
$email = $clean($_POST['email'] ?? '', 160);
$product = $clean($_POST['interested_product'] ?? ($_POST['product'] ?? ''), 200);
$type = $clean($_POST['enquiry_type'] ?? '', 60) ?: 'Product Information';
$message = $clean($_POST['message'] ?? '', 4000);
$source = $clean($_POST['source'] ?? '', 40) ?: 'website';
$honeypot = $clean($_POST['company_website'] ?? '', 100);

if ($honeypot !== '') reply($ajax, true, c('contact.success', 'Thank you!')); // silently drop bots
if ($name === '') reply($ajax, false, 'Please enter your name.', 400);
if (preg_replace('/\D/', '', $phone) === '' || strlen(preg_replace('/\D/', '', $phone)) < 7)
    reply($ajax, false, 'Please enter a valid phone number.', 400);
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
    reply($ajax, false, 'Please enter a valid email address.', 400);
if (!in_array($type, enquiry_types(), true)) $type = 'Product Information';

try {
    q('INSERT INTO enquiries (name,phone,email,interested_product,enquiry_type,message,source) VALUES (?,?,?,?,?,?,?)',
        [$name, $phone, $email ?: null, $product ?: null, $type, $message ?: null, $source]);
    $enq = ['name' => $name, 'phone' => $phone, 'email' => $email, 'interested_product' => $product,
        'enquiry_type' => $type, 'message' => $message, 'source' => $source];
    @send_enquiry_email($enq);
} catch (Throwable $e) {
    reply($ajax, false, 'Something went wrong. Please call or WhatsApp us.', 500);
}

reply($ajax, true, ch('contact.success', 'Thank you! Your enquiry has been received.'));
