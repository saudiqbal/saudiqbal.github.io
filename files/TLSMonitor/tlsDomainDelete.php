<?php
$db = new PDO("sqlite:/var/www/html/TLSMonitor/TLS.db");
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Process Form
$formerror = 0;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['Name'])) {
$NameDelete = $_GET['Name'];
}
else
{
	$formerror = 1;
}
if ($formerror == 0){
// Delete Notebook
$stmt = $db->prepare("DELETE FROM domains WHERE name = :id");
$stmt->execute([':id' => $NameDelete]);
header("Location: tlsmanagement.php");
exit;
}
else
{
echo "Error deleting note";
}
?>