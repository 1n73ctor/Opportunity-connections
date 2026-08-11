<?php
// ---------------------------------------------------------------------
// Where the leads are delivered. Change this to the address that should
// receive the form submissions.
// ---------------------------------------------------------------------
$EmailTo = "surajwithawp@gmail.com";

// Address the mail is sent *from*. It must be on this site's own domain,
// otherwise most hosts / SPF checks silently drop the message.
$EmailFrom = "no-reply@opportunityconnectionsusa.com";

header("Content-Type: text/plain; charset=utf-8");

// Opening this file directly in a browser is a smoke test for the host: if you
// see the message below, PHP is running. If you see this file's source code (or
// it downloads), the host is serving static files only and the form cannot work.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "PHP is running on this host. ";
    echo function_exists("mail")
        ? "The mail() function is available."
        : "WARNING: the mail() function is disabled here, so the form cannot send.";
    exit;
}

if (!function_exists("mail")) {
    echo "Mail sending is disabled on this server.";
    exit;
}

$name         = trim(isset($_POST["cs1Name"]) ? $_POST["cs1Name"] : "");
$email        = trim(isset($_POST["cs1Email"]) ? $_POST["cs1Email"] : "");
$phone_number = trim(isset($_POST["cs1PhoneNum"]) ? $_POST["cs1PhoneNum"] : "");
$consent      = (isset($_POST["cs1Consent"]) && $_POST["cs1Consent"] === "yes") ? "Yes" : "No";

// Server side validation (the browser side can be bypassed)
if ($name === "" || $email === "" || $phone_number === "") {
    echo "Please fill in your name, email and phone number.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Please enter a valid email address.";
    exit;
}

$digits = preg_replace('/\D/', '', $phone_number);
if (strlen($digits) < 10 || strlen($digits) > 15) {
    echo "Please enter a valid phone number.";
    exit;
}

// Strip anything that could be used to inject extra mail headers
$name  = str_replace(array("\r", "\n"), " ", $name);
$email = str_replace(array("\r", "\n"), " ", $email);

$Subject = "New lead from " . $name;

$Body  = "Name: " . $name . "\n";
$Body .= "Email: " . $email . "\n";
$Body .= "Phone Number: " . $phone_number . "\n";
$Body .= "SMS consent: " . $consent . "\n";
$Body .= "Submitted: " . date("Y-m-d H:i:s") . "\n";
$Body .= "IP: " . (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "unknown") . "\n";

$headers  = "From: Opportunity Connections <" . $EmailFrom . ">\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

$success = mail($EmailTo, $Subject, $Body, $headers);

// The front end (js/functions.js -> cs1SubmitForm) checks for the exact
// string "success" — anything else is shown to the visitor as an error.
if ($success) {
    echo "success";
} else {
    // mail() returned false: the host accepted the script but its mail server
    // refused the message (usually a From address that does not exist on the
    // domain, or sendmail not configured).
    echo "The mail server rejected the message. Please call us instead.";
}
