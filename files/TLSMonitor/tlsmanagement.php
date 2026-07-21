<?php
function isValidDomainOrSubdomain(string $domain): bool {
	// Overall length check
	if (strlen($domain) > 253 || strlen($domain) < 3) {
		return false;
	}
	$regex = '/^([a-z0-9_]([-a-z0-9]*[a-z0-9])?\.)+[a-z]{2,63}$/i';
	return (bool) preg_match($regex, $domain);
}
$formerror = 0;
$db = new PDO("sqlite:/var/www/html/TLSMonitor/TLS.db");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_hostname'])) {
$hostname = $_POST['hostname'];
if(empty($hostname))
{
$formerror = 1;
}
$hostname = trim($hostname);
if (!isValidDomainOrSubdomain($hostname)) {
$formerror = 1;
echo "Invalid hostname $hostname";
exit;
}
$sql = "SELECT 1 FROM domains WHERE name = :name LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute([':name' => $hostname]);
// fetch() returns the row if found, or false if it does not exist
$rowExists = $stmt->fetch() !== false;
if ($rowExists) {
	$formerror = 1;
	echo "Hostname already exists: $hostname";
	exit;
}
}
else
{
	$formerror = 1;
}
if ($formerror == 0){
// Insert new Note
$stmt = $db->prepare("INSERT INTO domains (name, value, timestamp) VALUES (:hostname, :value, :timestamp)");
$stmt->execute([':hostname' => $hostname, ':value' => '0', ':timestamp' => '0']);
header("Location: tlsmanagement.php");
exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>TLS Management</title>
<meta name="viewport" content="user-scalable=yes, initial-scale=1, width=device-width">
<link rel="icon" href="data:;base64,iVBORw0KGgo=">
<style>
body{
margin:0px;
padding:0px;
background-color: #ededed;
}
.header {
font-family: 'Open Sans', sans-serif;
background-color: #1a1a1a;
width: 100%;
z-index: 3;
font-weight:300;
line-height: 40px;
height: auto;
}
.header .headerlogo {
padding: 10px 20px;
color: #fff;
font-size: 18px;
}
.header .headertext {
padding: 10px 20px;
text-decoration: none;
color: #fff;
}
.header ul {
margin: 0;
padding: 0;
list-style: none;
overflow: hidden;
height: auto;
}
.header li a {
display: block;
padding: 0px 20px;
text-decoration: none;
color: #fff;
height: auto;
}
.header li a:hover,
.header input:hover {
opacity:.6;
}
.header > a {
display: block;
float: left;
padding: 0px 20px;
text-decoration: none;
}
.header ul {
clear: both;
max-height: 0;
transition: max-height .2s ease-out;
}
.header label {
cursor: pointer;
display: inline-block;
float: right;
padding: 19px 20px;
position: relative;
user-select: none;
}
.header label span {
background: #fff;
display: block;
height: 2px;
position: relative;
transition: background .2s ease-out;
width: 18px;
}
.header label span:before,
.header label span:after {
background: #fff;
content: '';
display: block;
height: 100%;
position: absolute;
transition: all .2s ease-out;
width: 100%;
}
.header label span:before {
top: 5px;
transform-origin: 50% 100%;
}
.header label span:after {
top: -5px;
transform-origin: 10% 100%;
}
.header input {
display: none;
}
.header input:checked ~ ul {
max-height: 240px;
}
.header input:checked ~ label span {
background: transparent;
}
.header input:checked ~ label span:before {
transform: rotate(-45deg);
}
.header input:checked ~ label span:after {
transform: rotate(45deg);
}
.header input:checked ~ label:not(.steps) span:before,
.header input:checked ~ label:not(.steps) .span:after {
top: 0;
}
.header .logout-btn {
color: #fff;
background: #4444;
box-shadow: 0px 0px 4px rgba(0,0,0) inset;
height: 40px;
}
.header #modal-1 {
vertical-align: middle;
height: 40px;
}
.blinkingdot {
-webkit-animation: 1s blink ease infinite;
-moz-animation: 1s blink ease infinite;
-ms-animation: 1s blink ease infinite;
-o-animation: 1s blink ease infinite;
animation: 1s blink ease infinite;
}
.table {
margin: 10px auto;
width: fit-content;
min-width: 800px;
box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
display: table;
}
.row {
display: table-row;
background: #f6f6f6;
font-size: 0.9em;
}
.row:nth-of-type(odd) {
background: #e9e9e9;
}
.row.header {
font-weight: 900;
color: #ffffff;
background: #ea6153;
}
.row.green {
background: #27ae60;
}
.row.blue {
background: #2980b9;
}
.cell {
padding: 3px 6px;
display: table-cell;
}
.fit-column {
width: 1px;
white-space: nowrap;
}
@keyframes "blink" {
from, to {
opacity: 0;
}
50% {
opacity: 1;
}
}
@-moz-keyframes blink {
from, to {
opacity: 0;
}
50% {
opacity: 1;
}
}
@-webkit-keyframes "blink" {
from, to {
opacity: 0;
}
50% {
opacity: 1;
}
}
@-ms-keyframes "blink" {
from, to {
opacity: 0;
}
50% {
opacity: 1;
}
}
@-o-keyframes "blink" {
from, to {
opacity: 0;
}
50% {
opacity: 1;
}
}
/* Base styling for the content (hidden by default) */
.togglecontent {
max-height: 0;
overflow: hidden;
transition: max-height 0.3s ease-out; /* Creates the slide effect */
background-color: #f6f6f6;
}
/* Expanded state class applied via JavaScript */
.togglecontent.expanded {
max-height: 200px; /* Set larger than expected content height */
}
/* Button & Arrow Icon Styling */
.toggle-button {
display: flex;
align-items: center;
gap: 10px;
padding: 10px 20px;
cursor: pointer;
}
/* Simple CSS triangle pointing down */
.icon-arrow {
width: 0;
height: 0;
border-left: 6px solid transparent;
border-right: 6px solid transparent;
border-top: 6px solid #333;
transition: transform 0.3s ease; /* Rotates smoothly */
}
/* Rotate icon 180 degrees up when active */
.toggle-button.active .icon-arrow {
transform: rotate(180deg);
}
.collapsible-container {
width: 800px;
margin: 25px auto;
}
.content-div {
width: 800px;
}
/* Login Group */
.login-group {
position: relative;
display: flex;
flex-wrap: wrap;
align-items: stretch;
font-family: monospace;
margin: 25px 50px;
}
.login-group-prepend {
margin-right: -1px;
}
.login-group>.login-control:not(:first-child), .login-group>.custom-select:not(:first-child) {
border-top-left-radius: 0;
border-bottom-left-radius: 0;
}
.login-group>.login-control, .login-group>.login-control-plaintext, .login-group>.custom-select, .login-group>.custom-file {
position: relative;
width: fit-content;
min-width: 0;
margin-bottom: 0;
}
.login-group>*, *::before, *::after {
box-sizing: border-box;
}
.login-group>.login-group-prepend>.btn, .login-group>.login-group-prepend>.login-group-text, .login-group:not(.has-validation)>.login-group-append:not(:last-child)>.btn, .login-group:not(.has-validation)>.login-group-append:not(:last-child)>.login-group-text, .login-group.has-validation>.login-group-append:nth-last-child(n+3)>.btn, .login-group.has-validation>.login-group-append:nth-last-child(n+3)>.login-group-text, .login-group>.login-group-append:last-child>.btn:not(:last-child):not(.dropdown-toggle), .login-group>.login-group-append:last-child>.login-group-text:not(:last-child) {
border-top-right-radius: 0;
border-bottom-right-radius: 0;
}
.login-group-text {
display: flex;
align-items: center;
padding: 0px;
margin-bottom: 0;
font-size: 1rem;
font-weight: 400;
line-height: 1;
text-align: center;
white-space: nowrap;
color: #495057;
background-color: #ffffff;
border: 1px solid #ced4da;
border-radius: .25rem;
}
.login-control:hover {
background-color: #79beff;
color: #1e1e1e;
}
.login-control {
display: block;
width: 50px;
height: calc(1em + 0.75rem + 1px);
padding: .375rem .75rem;
font-size: 1rem;
font-weight: 400;
line-height: 1;
color: #495057;
background-color: #e9ecef;
background-clip: padding-box;
border: 1px solid #ced4da;
border-radius: .25rem;
transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.login-group-prepend, .login-group-append {
display: flex;
}
/** ===== Pagination ===== **/
.pagination_style
{
padding-top:10px;
padding-bottom:10px;
text-align: center;
}
.pagination-button {
color: #6e6e6e;
font: bold 12px Helvetica, Arial, sans-serif;
text-decoration: none;
padding: 5px 18px;
position: relative;
display: inline-block;
-webkit-transition: border-color .218s;
-moz-transition: border .218s;
-o-transition: border-color .218s;
transition: border-color .218s;
background: #f3f3f3;
background: -webkit-gradient(linear,0% 40%,0% 70%,from(#F5F5F5),to(#F1F1F1));
background: -moz-linear-gradient(linear,0% 40%,0% 70%,from(#F5F5F5),to(#F1F1F1));
border: solid 1px #dcdcdc;
border-radius: 4px;
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
margin-right: 10px;
line-height: 20px;
}
.pagination-button:hover {
color: #333;
border-color: #999;
-moz-box-shadow: 0 2px 0 rgba(0, 0, 0, 0.2) -webkit-box-shadow:0 2px 5px rgba(0, 0, 0, 0.2);
box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
}
.pagination-button:active {
color: #000;
border-color: #444;
}
.left {
-webkit-border-top-right-radius: 0;
-moz-border-radius-topright: 0;
border-top-right-radius: 0;
-webkit-border-bottom-right-radius: 0;
-moz-border-radius-bottomright: 0;
border-bottom-right-radius: 0;
margin: 0;
}
.buttonDisabled {
color: #6e6e6e;
font: bold 12px Helvetica, Arial, sans-serif;
text-decoration: none;
padding: 5px 18px;
position: relative;
display: inline-block;
-webkit-transition: border-color .218s;
-moz-transition: border .218s;
-o-transition: border-color .218s;
transition: border-color .218s;
background: #f3f3f3;
background: -webkit-gradient(linear,0% 40%,0% 70%,from(#F5F5F5),to(#F1F1F1));
background: -moz-linear-gradient(linear,0% 40%,0% 70%,from(#F5F5F5),to(#F1F1F1));
border: solid 1px #dcdcdc;
border-radius: 4px;
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
margin-right: 10px;
line-height: 20px;
}
.leftDisabled {
-webkit-border-top-right-radius: 0;
-moz-border-radius-topright: 0;
border-top-right-radius: 0;
-webkit-border-bottom-right-radius: 0;
-moz-border-radius-bottomright: 0;
border-bottom-right-radius: 0;
margin: 0;
}
.middle {
border-radius: 0;
-webkit-border-radius: 0;
-moz-border-radius: 0;
border-left: solid 1px #f3f3f3;
margin: 0;
border-left: solid 1px rgba(255, 255, 255, 0);
}
.middleCurrent {
border-radius: 0;
-webkit-border-radius: 0;
-moz-border-radius: 0;
border-left: solid 1px #f3f3f3;
margin: 0;
border-left: solid 1px rgba(255, 255, 255, 0);
background: #dcdcdc;
}
.middle:hover {
-webkit-border-top-left-radius: 0;
-moz-border-radius-topleft: 0;
border-top-left-radius: 0;
-webkit-border-bottom-left-radius: 0;
-moz-border-radius-bottomleft: 0;
border-bottom-left-radius: 0;
}
.right {
-webkit-border-top-left-radius: 0;
-moz-border-radius-topleft: 0;
border-top-left-radius: 0;
-webkit-border-bottom-left-radius: 0;
-moz-border-radius-bottomleft: 0;
border-bottom-left-radius: 0;
margin: 0;
border-left: solid 1px #f3f3f3;
}
.right:hover {
-webkit-border-top-left-radius: 0;
-moz-border-radius-topleft: 0;
border-top-left-radius: 0;
-webkit-border-bottom-left-radius: 0;
-moz-border-radius-bottomleft: 0;
border-bottom-left-radius: 0;
margin: 0;
}
.rightDisabled {
-webkit-border-top-left-radius: 0;
-moz-border-radius-topleft: 0;
border-top-left-radius: 0;
-webkit-border-bottom-left-radius: 0;
-moz-border-radius-bottomleft: 0;
border-bottom-left-radius: 0;
border-left: 0px;
}
/** ===== End Pagination ===== **/
@media (min-width: 640px) {
.header {
height: 40px;
}
.header li {
float: left;
}
.header li a {
padding: 0px 30px;
}
.header ul {
clear: none;
float: right;
max-height: none;
}
.header label {
display: none;
}
}
@media screen and (min-width: 641px) {
.cell:not(:last-child){
white-space: nowrap;
}
.cell:last-child{
width: fit-content;
}
}
/* ===== MOBILE ===== */
@media screen and (max-width: 640px) {
.content {
margin: 0 auto;
max-width: 100%;
}
.table {
display: block;
}
.cell {
padding: 2px 16px;
display: block;
}
.row {
padding: 14px 0 7px;
display: block;
}
.row.header {
padding: 0;
height: 6px;
}
.row.header .cell {
display: none;
}
.row .cell {
margin-bottom: 10px;
}
.row .cell:before {
margin-bottom: 3px;
content: attr(data-title);
min-width: 98px;
font-size: 10px;
line-height: 10px;
font-weight: bold;
text-transform: uppercase;
color: #969696;
dis
}
}
/* ===== DARK MODE ===== */
@media (prefers-color-scheme: dark) {
body{
background: #4a4a4a;
color: #323232;
}
}
</style>
</head>
<body>
<div class="header">
<span class="headerlogo">TLS Monitoring Server</span>
<ul class="menu">
<li><a href="/">Home</a></li>
</ul>
</div>
<div class="collapsible-container">
<button id="toggle-btn" class="toggle-button"><span>Add Hostname</span><i class="icon-arrow"></i></button>
<div id="content-div" class="togglecontent">
<div style="display: flex;justify-content: center;align-items: center;"><form method="POST" id="hostname">
<div class="login-group"><div class="login-group-prepend"><span class="login-group-text"><input id="hostname" name="hostname" type="text" style="border: none; outline: none;width: 500px;" placeholder='Hostname' required></span></div><input type="submit" name="submit_hostname" id="submit" value="Submit" class="login-control"></div></div>
</form></div>
</div>
</div>
<div class="table">
<div class="row header blue">
<div class="cell">
Name
</div>
<div class="cell fit-column">
Expiring
</div>
<div class="cell fit-column">
</div>
<div class="cell fit-column">
</div>
</div>
<?php
if(isset($_GET['page_id']))
{
$page_id = $_GET['page_id'];
if(preg_match('/[^0-9]/', $page_id))
{
exit;
}
$page = $page_id;
}
$result = $db->query('SELECT name FROM domains');
$rows = $result->fetchAll();
$total_pages = count($rows);
$limit = 10;
$adjacents = 3;
if(isset($_GET['page']))
{
$page = $_GET['page'];
if(preg_match('/[^0-9]/i', $page))
{
echo "SQL Injection detected!";
exit();
}
}
if(isset($page))
$start = ($page - 1) * $limit; 			//first item to display on this page
else
$start = 0;
$result = $db->query("SELECT name, value, timestamp FROM domains DESC LIMIT '$start', '$limit'");
$i = 1;
$DAYS=14;
$indays=time() + (86400*$DAYS);
foreach($result as $row)
{
if (time() > $row['timestamp'])
{
	$domainstatus = ' <svg height="15" width="15" class="blinkingdot"><circle cx="10" cy="10" r="5" fill="#ff0000"></svg>';
}
elseif ($indays > $row['timestamp'] && time() < $row['timestamp'])
{
	$domainstatus = ' <svg height="15" width="15" class="blinkingdot"><circle cx="10" cy="10" r="5" fill="#FF7900"></svg>';
}
else
{
	$domainstatus = ' <svg height="15" width="15"><circle cx="10" cy="10" r="5" fill="#00FF00"></svg>';
}
echo "<div class=\"row\"><div class=\"cell\" data-title=\"Name\" style=\"vertical-align: top;white-space: nowrap;\">" . $row['name'] . "</div><div class=\"cell fit-column\" data-title=\"Expiring\" style=\"vertical-align: top;white-space: nowrap;\">$domainstatus " . date('Y-m-d H:i:s', $row['timestamp']) . "</div><div class=\"cell fit-column\" data-title=\"RelativeTimeStanp\" style=\"vertical-align: top;white-space: nowrap;\"><span class=\"relativetimestamp\" style=\"display: none;\">" . date('Y-m-d H:i:s', $row['timestamp']) . "</span></div><div class=\"cell fit-column\" data-title=\"Edit\" style=\"vertical-align: top;\"><a href=\"tlsDomainDelete.php?Name=" . $row['name'] . "\" onclick=\"javascript:return confirm('Delete permanently?')\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"#ff0000\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M3 6h18\"></path><path d=\"M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6\"></path><path d=\"M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2\"></path></svg></a></div>" . "</div>\n";
}
echo '</div>' . "\xA";
$db = NULL;
// Pagination Starts
function pagination($total_pages,$limit,$page,$file,$adjacents){
		if($page)
				$start = ($page - 1) * $limit; 			//first item to display on this page
			else
				$start = 0;								//if no page var is given, set start to 0

		/* Setup page vars for display. */
		if ($page == 0) $page = 1;					//if no page var is given, default to 1.
		$prev = $page - 1;							//anterior page is page - 1
		$siguiente = $page + 1;							//siguiente page is page + 1
		$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;						//last page minus 1
		$link_previous = "&#10094; Previous";
		$link_next = "Next &#10095;";

		$p = false;
		if(strpos($file,"?")>0)
			$p = true;

		//ob_start();
		if($lastpage > 1){
			echo "<nav class=\"pagination_keyboard\">\n";
			echo "<div class=\"pagination_style\">\n";
				//anterior button
				if($page > 1)
								if($p)
									echo "<span class=\"pagination-prev\"><a href=\"$file$prev\" class=\"pagination-button left\">$link_previous</a></span>";
									else
									echo "<span class=\"pagination-prev\"><a href=\"$file$prev\" class=\"pagination-button left\">$link_previous</a></span>";
					else
						echo "<span class=\"buttonDisabled leftDisabled\">$link_previous</span>";
				//pages
				if ($lastpage < 7 + ($adjacents * 2)){//not enough pages to bother breaking it up
						for ($counter = 1; $counter <= $lastpage; $counter++){
								if ($counter == $page)
										echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
									else
												if($p)
												echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
												else
												echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
							}
					}
				elseif($lastpage > 5 + ($adjacents * 2)){//enough pages to hide some
						//close to beginning; only hide later pages
						if($page < 1 + ($adjacents * 2)){
								for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
										if ($counter == $page)
												echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
											else
														if($p)
														echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
														else
														echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
									}
								echo "";
										if($p){
										echo "<a href=\"$file$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}else{
										echo "<a href=\"$file?page=$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file?page=$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}

							}
						//in middle; hide some front and some back
						elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)){
										if($p){
										echo "<a href=\"{$file}1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"{$file}2\" class=\"pagination-button middle\">2</a>";
										}else{
										echo "<a href=\"$file?page=1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"$file?page=2\" class=\"pagination-button middle\">2</a>";
										}
								echo "";
								for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
									if ($counter == $page)
											echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
										else
													if($p)
													echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
													else
													echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
								echo "";
										if($p){
										echo "<a href=\"$file$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}else{
										echo "<a href=\"$file?page=$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file?page=$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}
							}
						//close to end; only hide early pages
						else{
										if($p){
										echo "<a href=\"{$file}1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"{$file}2\" class=\"pagination-button middle\">2</a>";
										}else{
										echo "<a href=\"$file?page=1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"$file?page=2\" class=\"pagination-button middle\">2</a>";
										}
								echo "";
								for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
									if ($counter == $page)
											echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
										else
													if($p)
													echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
													else
													echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
							}
					}
				if ($page < $counter - 1)
								if($p)
								echo "<span class=\"pagination-next\"><a href=\"$file$siguiente\" class=\"pagination-button right\">$link_next</a></span>";
								else
								echo "<span class=\"pagination-next\"><a href=\"$file?page=$siguiente\" class=\"pagination-button rightDisabled\">$link_next</a></span>";
					else
						echo "<span class=\"buttonDisabled rightDisabled pagination-next\">$link_next</span>";

			echo "\n</div>\n";
			echo "</nav>\n";
			}
	}
// Pagination Ends
if(!isset($page))
$page=1;
echo pagination($total_pages,$limit,$page,$_SERVER["PHP_SELF"]."?page_id=",$adjacents);
if(empty($rows))
{
echo '<div id="WarningMainContent">
<div id="Warningalignleft"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path style="fill:#ff5500" d="M19.64 16.36 11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg></div>
<div id="Warningalignright">No notes were found in your notebook!</div>
</div>';
}
?>
</div>
</div>
</div>
</div>
<script>
// Relative TimeStamp
var number_of_elements = document.getElementsByClassName('relativetimestamp').length;
var i=0;
while (i<number_of_elements) {
var rtivalue = document.getElementsByClassName("relativetimestamp")[i].innerText;
rtivalue = getRelativeTimeString(new Date(rtivalue))
document.getElementsByClassName('relativetimestamp')[i].textContent = "("+rtivalue+")";
document.getElementsByClassName('relativetimestamp')[i].style.display = "";
i++;
}
function getRelativeTimeString(date, lang = navigator.language) {
const timeMs = typeof date === "number" ? date : date.getTime();
const deltaSeconds = Math.round((timeMs - Date.now()) / 1000);
const cutoffs = [60, 3600, 86400, 86400 * 365, 86400 * 365, 86400 * 365, Infinity];
const units = ["second", "minute", "hour", "day", "week", "day", "year"];
const unitIndex = cutoffs.findIndex(cutoff => cutoff > Math.abs(deltaSeconds));
const divisor = unitIndex ? cutoffs[unitIndex - 1] : 1;
const rtf = new Intl.RelativeTimeFormat(lang, { numeric: "auto", style: "long", });
return rtf.format(Math.round(deltaSeconds / divisor), units[unitIndex]);
}

// Toggle button
const toggleBtn = document.getElementById('toggle-btn');
const contentDiv = document.getElementById('content-div');
// Listen for a click event on the button
toggleBtn.addEventListener('click', () => {
// Toggle the '.expanded' class to open/close the div
contentDiv.classList.toggle('expanded');
// Toggle the '.active' class to rotate the icon
toggleBtn.classList.toggle('active');
});
</script>
</body>
</html>