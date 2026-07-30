<?php
/**
 * request/form.php
 * Receives the JSON payload posted by inc/multi-form.php's fetch() call and
 * inserts it as a new row in the `applications` table. Field names below
 * match the form's `name="…"` attributes exactly, so nothing needs
 * relabeling here if the form ever grows a new field — just add the column
 * in the database and a line below.
 */

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../inc/db.php';

function itr_respond(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    itr_respond(405, ['message' => 'Method not allowed.']);
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!is_array($payload)) {
    itr_respond(400, ['message' => 'Could not read the submitted form data.']);
}

/** Small helper: trim a posted field, or null if it's not a non-empty string. */
$field = function (string $key) use ($payload): ?string {
    $value = $payload[$key] ?? null;
    if (!is_string($value)) {
        return null;
    }
    $value = trim($value);
    return $value === '' ? null : $value;
};

$firstName = $field('first_name');
$lastName = $field('last_name');
$email = $field('email');
$phoneNumber = $field('phone_number');
$whatsappNumber = $field('whatsapp_number');
$occupation = $field('occupation');
$ppsNumber = $field('pps_number');
$maritalStatus = $field('marital_status');
$addressOne = $field('address_one');
$addressTwo = $field('address_two');
$county = $field('county');

$required = [
    'first_name' => $firstName, 'last_name' => $lastName, 'email' => $email,
    'phone_number' => $phoneNumber, 'whatsapp_number' => $whatsappNumber,
    'occupation' => $occupation, 'pps_number' => $ppsNumber, 'marital_status' => $maritalStatus,
    'address_one' => $addressOne, 'address_two' => $addressTwo, 'county' => $county,
];
foreach ($required as $key => $value) {
    if ($value === null) {
        itr_respond(422, ['message' => "Missing required field: {$key}."]);
    }
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    itr_respond(422, ['message' => 'Please provide a valid email address.']);
}

$validMaritalStatuses = ['Married', 'Single', 'Civil Partnership', 'Separated', 'Divorced', 'Widowed', 'Single Parent'];
if (!in_array($maritalStatus, $validMaritalStatuses, true)) {
    itr_respond(422, ['message' => 'Please choose a valid marital status.']);
}

// Combine the three date-of-birth fields (day/month/year) into one DATE value.
$dobDay = (int) ($payload['date_of_birth_day'] ?? 0);
$dobMonth = (int) ($payload['date_of_birth_month'] ?? 0);
$dobYear = (int) ($payload['date_of_birth_year'] ?? 0);
$dateOfBirth = null;
if ($dobDay && $dobMonth && $dobYear) {
    if (!checkdate($dobMonth, $dobDay, $dobYear)) {
        itr_respond(422, ['message' => 'Please provide a valid date of birth.']);
    }
    $dateOfBirth = sprintf('%04d-%02d-%02d', $dobYear, $dobMonth, $dobDay);
}

$maidenName = $field('maiden_name');
$eircode = $field('eircode');
$promotionCode = $field('promotion_code');

// The signature is either a base64 PNG data URL (drawn) or a typed name —
// both are valid ways to sign, so store whichever came through.
$signature = $field('signature');
if ($signature === null) {
    itr_respond(422, ['message' => 'Please add your signature before submitting.']);
}

try {
    $pdo = itr_db();
    $stmt = $pdo->prepare(
        'INSERT INTO applications
            (first_name, last_name, maiden_name, email, phone_number, whatsapp_number,
             occupation, pps_number, marital_status, date_of_birth,
             address_one, address_two, county, eircode, promotion_code,
             signature, rebate_type, rebate_amount, status, submitted_at)
         VALUES
            (:first_name, :last_name, :maiden_name, :email, :phone_number, :whatsapp_number,
             :occupation, :pps_number, :marital_status, :date_of_birth,
             :address_one, :address_two, :county, :eircode, :promotion_code,
             :signature, NULL, 0.00, \'New\', NOW())'
    );

    $stmt->execute([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'maiden_name' => $maidenName,
        'email' => $email,
        'phone_number' => $phoneNumber,
        'whatsapp_number' => $whatsappNumber,
        'occupation' => $occupation,
        'pps_number' => $ppsNumber,
        'marital_status' => $maritalStatus,
        'date_of_birth' => $dateOfBirth,
        'address_one' => $addressOne,
        'address_two' => $addressTwo,
        'county' => $county,
        'eircode' => $eircode,
        'promotion_code' => $promotionCode,
        'signature' => $signature,
    ]);

    itr_respond(200, [
        'message' => 'Thanks — your application has been received.',
        'id' => (int) $pdo->lastInsertId(),
    ]);
} catch (PDOException $e) {
    // Never leak DB details to the browser — log server-side, show a generic message.
    error_log('[itr-form] ' . $e->getMessage());
    itr_respond(500, ['message' => 'Sorry, something went wrong saving your application. Please try again shortly.']);
}
