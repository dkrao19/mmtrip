<?php
/* ============================
   SAFE SESSION START
============================ */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ============================
   SAFE DB INCLUDE (FIXED PATH)
============================ */
require_once __DIR__ . "/../../../config/database.php";

/* ============================
   VALIDATION
============================ */
if (empty($_SESSION['pnr'])) {
    // Do NOT break success page
    return;
}


/* ============================
   GENERATE INVOICE
============================ */

// Example invoice file name
$invoiceFile = "INV-" . date('YmdHis') . "-" . $_SESSION['pnr']['pnr'] . ".pdf";

/* Ensure directory exists */
$invoiceDir = __DIR__ . "/../../../assets/invoices";
if (!is_dir($invoiceDir)) {
    mkdir($invoiceDir, 0755, true);
}

/* DUMMY PDF (REPLACE WITH REAL PDF LOGIC LATER) */
file_put_contents(
    $invoiceDir . "/" . $invoiceFile,
    "Invoice for PNR: " . $_SESSION['pnr']['pnr']
);

/* STORE IN SESSION */
$_SESSION['invoice'] = $invoiceFile;

/* OPTIONAL JSON RESPONSE (API USAGE) */
if (php_sapi_name() !== 'cli') {
    echo json_encode([
        'success' => true,
        'invoice' => $invoiceFile
    ]);
}
