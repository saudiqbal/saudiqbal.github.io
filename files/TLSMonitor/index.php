<!DOCTYPE html>
<html lang="en">
<head>
<title>TLS Monitoring Server</title>
<meta name="viewport" content="user-scalable=yes, initial-scale=1, width=device-width">
<link rel="icon" href="data:;base64,iVBORw0KGgo=">
<style>
body{
margin:0px;
padding:0px;
background-color: #ededed;
}
hr {
	background-color: rgba(0,0,0,0.12);
	border: none;
	height: 1px;
	margin: 0;
}
pre {
	white-space: break-spaces;
	word-break: break-word;
}
table {
border-collapse: collapse;
}

th, td {
padding: 4px;
text-align: left;
}

tr:hover {background-color: #5555ff;}
.button {
	background-color: transparent;
	border: none;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	color: inherit;
	cursor: pointer;
	display: inline-block;
	font-family: Roboto, sans-serif;
	font-size: 14px;
	font-weight: 500;
	letter-spacing: 0.75px;
	line-height: 36px;
	min-width: 64px;
	padding: 0 8px;
	text-align: center;
}

.button:hover, .action-icon:hover {
	background-color: rgba(158,158,158,0.2);
}
.button:active, .action-icon:active {
	background-color: rgba(158,158,158,0.4);
}
.button:focus:not(:active), .action-icon:focus:not(:active) {
	background-color: rgba(0,0,0,.12);
}
.cardfooter {
	display: inline-block;
	vertical-align: middle;
	line-height: 20px;
	padding: 5px 16px;
}
.action-icon {
	-moz-border-radius: 50%;
	-webkit-border-radius: 50%;
	border-radius: 50%;
	box-sizing: border-box;
	cursor: pointer;
	margin: 0 2px;
	outline: none;
	padding: 6px;
}
.action-icons {
	color: rgba(0,0,0,0.54);
}
.actions {
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	min-height: 20px;
	padding: 0px;
	position: relative;
	z-index: 1;
}
.divider {
	display: block;
	height: 24px;
}
.card {
	background-color: #fff;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	-moz-box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
	-webkit-box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
	box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
	color: rgba(0,0,0,.87);
	margin: 15px;
	width: calc( 100% - 50px );
	overflow: auto;
	position: relative;
	word-break: break-all;
}
.card::after {
	clear: both;
}
.card::after, .card::before {
	content: "";
	display: block;
}
.content {
	margin: 0 auto;
	max-width: 1440px;
}
.float-left {
	float: left!important;
}
.float-right {
	float: right!important;
}
.primary-text + .secondary-text, .secondary-text + .primary-text, .optional-header + .primary-text {
	margin-top: calc(24px/2/2); /* margin-top is 50% of the primary title font size. */
}
.primary-title {
	padding: 16px;
}
.primary-text {
	font-size: 24px;
	line-height: 24px;
}
.primary-title + .supporting-text, .optional-header + .supporting-text {
	padding-top: 0;
}
.primary-title .optional-header {
	padding-left: 0;
	padding-right: 0;
}
.secondary-text .action-icon {
	font-size: inherit;
	margin: 0;
	padding: 0;
}
.subhead, .secondary-text {
	color: rgba(0,0,0,.54);
	font-size: 14px;
}
.supporting-text {
	font-size: 12px;
	padding: 16px;
}
.footer{
text-align: center;
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
/* ===== MOBILE ===== */
@media screen and (max-width: 640px) {
.content {
	margin: 0 auto;
	max-width: 100%;
}
.card {
	margin: 15px auto;
	width: calc(100% - 10px);
}
}
/* ===== DARK MODE ===== */
@media (prefers-color-scheme: dark) {
body{
background: #121212;
color: #E0E0E0;
}
.card {
	background-color: #444444;
	-moz-box-shadow: 0 3px 1px -2px rgba(255,255,255,.1), 0 2px 2px 0 rgba(255,255,255,.4), 0 1px 5px 0 rgba(255,255,255,.4);
	-webkit-box-shadow: 0 3px 1px -2px rgba(255,255,255,.1), 0 2px 2px 0 rgba(255,255,255,.4), 0 1px 5px 0 rgba(255,255,255,.4);
	box-shadow: 0 3px 1px -2px rgba(255,255,255,.1), 0 2px 2px 0 rgba(255,255,255,.4), 0 1px 5px 0 rgba(255,255,255,.4);
	color: #E0E0E0;
}
}
</style>
</head>
<body>
<div class="header">
<span class="headerlogo">TLS Monitoring Server</span>
<input class="menu-btn" type="checkbox" id="menu-btn" />
<label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>
<ul class="menu">
<li><a href="/">Home</a></li>
</ul>
</div>
<div class="content">
<div class="card">
<div class="primary-title">
<div class="primary-text">TLS certificate status</div>
</div>
<div class="supporting-text">
<?php
$dbh  = new PDO("sqlite:/var/www/html/TLSMonitor/TLS.db");
$query =  "SELECT name, value, timestamp FROM domains";
echo "<table style=\"font-size: 14px;\">\n";
echo "<tr><th>Domain</th style=\"padding-left: 20px;\"><th></th><th style=\"padding-left: 20px;\">Expiring</th></tr>\n";
foreach ($dbh->query($query) as $row)
{
if ($row[1] == 'ER')
{
	$domainstatus = ' <svg height="15" width="15" class="blinkingdot"><circle cx="10" cy="10" r="5" fill="#FF0000"></svg>';
}
elseif ($row[1] == 'OK')
{
	$domainstatus = ' <svg height="15" width="15"><circle cx="10" cy="10" r="5" fill="#00FF00"></svg>';
}
echo "<tr>\n";
echo "<td>" . $row[0] . "</td>";
echo "<td style=\"padding-left: 20px;\">" . $domainstatus . "</td>";
echo "<td style=\"padding-left: 20px;\">" . $row[2] . "</td>\n";
echo "</tr>\n";
}
echo "</table>";
$dbh = null;
?>
</div>
</div>
</div>
</body>
</html>