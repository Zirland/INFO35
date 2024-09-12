<?php
$heslo = 'password';
$heslo_hash = password_hash($heslo, PASSWORD_DEFAULT);
echo $heslo_hash;
?>