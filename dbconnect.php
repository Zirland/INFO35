<?php
$DB_SERVER = '';
$DB_USERNAME = '';
$DB_PASSWORD = '';
$DB_NAME = '';

function SmerNazev($silnice, $smer, $kilometr)
{
    switch ($silnice) {
        case 'D0':
            if ($smer == "+" && $kilometr < 30) {
                $smer_nazev = "letiště";
            } elseif ($smer == "+" && $kilometr < 65) {
                $smer_nazev = "Štěrboholy";
            } elseif ($smer == "+") {
                $smer_nazev = "letiště";
            } elseif ($smer == "-" && $kilometr > 65) {
                $smer_nazev = "Brno";
            } elseif ($smer == "-" && $kilometr > 30) {
                $smer_nazev = "Liberec";
            } else {
                $smer_nazev = "Brno";
            }
            break;

        case 'D1':
            if ($smer == "+" && $kilometr < 189) {
                $smer_nazev = "Brno";
            } elseif ($smer == "+" && $kilometr < 273) {
                $smer_nazev = "Přerov";
            } elseif ($smer == "+") {
                $smer_nazev = "Bohumín";
            } elseif ($smer == "-" && $kilometr > 273) {
                $smer_nazev = "Přerov";
            } elseif ($smer == "-" && $kilometr > 203) {
                $smer_nazev = "Brno";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D2':
            $smer_nazev = ($smer == "+") ? "Lanžhot" : "Brno";
            break;

        case 'D3':
            $smer_nazev = ($smer == "+") ? "Kaplice" : "Praha";
            break;

        case 'D4':
            $smer_nazev = ($smer == "+") ? "Písek" : "Praha";
            break;

        case 'D5':
            $smer_nazev = ($smer == "+") ? "Rozvadov" : "Praha";
            break;

        case 'D6':
            if ($smer == "+" && $kilometr < 112) {
                $smer_nazev = "Karlovy Vary";
            } elseif ($smer == "+") {
                $smer_nazev = "Cheb";
            } elseif ($smer == "-" && $kilometr > 112) {
                $smer_nazev = "Karlovy Vary";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D7':
            $smer_nazev = ($smer == "+") ? "Chomutov" : "Praha";
            break;

        case 'D8':
            $smer_nazev = ($smer == "+") ? "Petrovice" : "Praha";
            break;

        case 'D11':
            $smer_nazev = ($smer == "+") ? "Jaroměř" : "Praha";
            break;

        case '20':
            $smer_nazev = ($smer == "+") ? "České Budějovice" : "Plzeň";
            break;

        case 'D35':
            if ($smer == "+" && $kilometr < 160) {
                $smer_nazev = "Vysoké Mýto";
            } elseif ($smer == "+") {
                $smer_nazev = "Lipník nad Bečvou";
            } elseif ($smer == "-" && $kilometr > 220) {
                $smer_nazev = "Mohelnice";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case '35':
            if ($smer == "+" && $kilometr < 210) {
                $smer_nazev = "Mohelnice";
            } elseif ($smer == "+") {
                $smer_nazev = "Valašské Meziříčí";
            } elseif ($smer == "-" && $kilometr > 285) {
                $smer_nazev = "Hranice";
            } else {
                $smer_nazev = "Vysoké Mýto";
            }
            break;

        case '38':
            $smer_nazev = ($smer == "+") ? "Znojmo" : "Havlíčkův Brod";
            break;

        case 'D46':
            $smer_nazev = ($smer == "+") ? "Olomouc" : "Vyškov";
            break;

        case 'D48':
            $smer_nazev = ($smer == "+") ? "Český Těšín" : "Bělotín";
            break;

        case 'D49':
            $smer_nazev = ($smer == "+") ? "Fryšták" : "Hulin";
            break;

        case 'D52':
            $smer_nazev = ($smer == "+") ? "Mikulov" : "Brno";
            break;

        case 'D55':
            $smer_nazev = ($smer == "+") ? "Uherské Hradiště" : "Kroměříž";
            break;

        case 'D56':
            $smer_nazev = ($smer == "+") ? "Frýdek-Místek" : "Ostrava";
            break;

        case '57':
            $smer_nazev = ($smer == "+") ? "Vsetín" : "Valašské Meziříčí";
            break;

        case '58':
            $smer_nazev = ($smer == "+") ? "Ostrava" : "Rožnov pod Radhošťem";
            break;

        default:
            $smer_nazev = $smer;
            break;
    }

    return $smer_nazev;
}

$mail_host = '';
$mail_username = '';
$mail_password = '';
$mail_bcc = '';