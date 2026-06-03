<?php
require_once __DIR__ . '/functions.php';

/** Sends a new-enquiry notification via PHP mail() (works on Hostinger). */
function send_enquiry_email($enq)
{
    $mail = cfg('mail');
    if (empty($mail['enabled'])) return false;

    $to = $mail['notify_to'];
    $subject = 'New Medical Ozone Care Enquiry - ' . ($enq['enquiry_type'] ?? 'Enquiry')
        . (!empty($enq['interested_product']) ? ' · ' . $enq['interested_product'] : '');

    $rows = [
        'Name' => $enq['name'], 'Phone' => $enq['phone'], 'Email' => $enq['email'],
        'Enquiry Type' => $enq['enquiry_type'], 'Interested Product' => $enq['interested_product'],
        'Message' => $enq['message'], 'Source' => $enq['source'],
    ];
    $html = '<div style="font-family:Arial,sans-serif;max-width:560px"><h2 style="color:#008b7a">New Enquiry — Medical Ozone Care</h2><table style="border-collapse:collapse;width:100%">';
    foreach ($rows as $k => $v) {
        $html .= '<tr><td style="padding:8px 10px;border:1px solid #e3edf1;background:#f3fafb;font-weight:bold;width:38%">'
            . e($k) . '</td><td style="padding:8px 10px;border:1px solid #e3edf1">' . (e($v) ?: '-') . '</td></tr>';
    }
    $html .= '</table><p style="color:#60758b;font-size:12px">Sent automatically from the Medical Ozone Care website.</p></div>';

    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $mail['from'];
    if (!empty($enq['email'])) $headers[] = 'Reply-To: ' . $enq['email'];

    return @mail($to, $subject, $html, implode("\r\n", $headers));
}
