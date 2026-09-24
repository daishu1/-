<?php
// Session data is server-side; neither a record id nor a client-supplied key authorizes writes.
function location_session() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('probe_location');
        session_start(['cookie_httponly'=>true, 'cookie_secure'=>true, 'cookie_samesite'=>'Lax', 'use_strict_mode'=>true]);
    }
    header('Cache-Control: no-store');
    header('Referrer-Policy: no-referrer');
    header('Permissions-Policy: geolocation=(self)');
}
function location_database() {
    require __DIR__.'/config.php';
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli($dbconfig['host'],$dbconfig['user'],$dbconfig['pwd'],$dbconfig['dbname'],$dbconfig['port']);
    $db->set_charset('utf8');
    return $db;
}
