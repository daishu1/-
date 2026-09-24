<?php
if (PHP_SAPI !== 'cli') exit;
define('IN_CRONLITE',true);
require dirname(__DIR__).'/include/location_common.php';
$db=location_database();
$password=bin2hex(random_bytes(16));
$hash=password_hash($password,PASSWORD_DEFAULT);
$stmt=$db->prepare("UPDATE list_admin SET password=? WHERE username='admin'");
$stmt->bind_param('s',$hash); $stmt->execute();
echo "Admin account: admin\nInitial password: ".$password."\nSave it securely. Running again resets this password.\n";
