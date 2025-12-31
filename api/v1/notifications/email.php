<?php
/**
 * SIMPLE EMAIL SENDER (NO PHPMailer, NO COMPOSER)
 * Works on shared hosting immediately
 */
function sendBookingEmail($to, $subject, $body, $attachment = null)
{
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: MMTrips <noreply@mmtrips.com>\r\n";

    // NOTE: attachments not supported with mail()
    // but email will still send

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    return @mail($to, $subject, $body, $headers);
}
