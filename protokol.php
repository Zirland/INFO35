<?php
date_default_timezone_set('Europe/Prague');
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
require_once 'dbconnect.php';

$link = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if ($link === false) {
    die("CHYBA: Nepovedlo se připojit. " . mysqli_connect_error());
}
mysqli_set_charset($link, "utf8");

require "tfpdf/tfpdf.php";
//include "phpqrcode/phpqrcode.php";

$id = $_GET["id"];
$provozovatel = $_SESSION['provozovatel'];
$qrcode = "";

switch ($provozovatel) {
    case 'SPEL':
        $img_logo = 'SPEL.png';
        $logo_sirka = 180;
        $logo_vyska = 43;
        $investor_L1 = 'Ředitelství silnic a dálnic ČR';
        $dodavatel_L1 = 'SPEL, a.s. Kolín';
        $dodavatel_L2 = 'Třídvorská 1402, 280 02 Kolín V';
        $dodavatel_nazev = 'SPEL, a.s.';
        $pozice_dodavatel = 'Pozice SPEL';
        $mesto = 'Kolíně';
        break;

    case 'ViaSalis':
        $img_logo = 'vinci.png';
        $logo_sirka = 170;
        $logo_vyska = 44;
        $investor_L1 = 'Ministerstvo dopravy ČR';
        $investor_L2 = 'nábřeží Ludvíka Svobody 1222/12, 110 15 Praha 1';
        $dodavatel_L1 = 'EUROVIA CZ, a.s., závod DIVia';
        $dodavatel_L2 = 'U Michelského lesa 1581/2, Michle, 140 00 Praha 4';
        $dodavatel_nazev = 'EUROVIA CZ, a.s., závod DIVia';
        $pozice_dodavatel = 'Pozice Via';
        $mesto = 'Praze';
        $cislo_prokotolu = " č. $id";
        break;
}

$query73 = "SELECT datum, silnice, hlasky, projekt, osoba FROM testovani WHERE id = $id;";
if ($result73 = mysqli_query($link, $query73)) {
    while ($row73 = mysqli_fetch_row($result73)) {
        $datum = $row73[0];
        $silnice = $row73[1];
        $hlasky = $row73[2];
        $projekt = $row73[3];
        $zadatel = $row73[4];

        $textsilnice = (substr($silnice, 0, 1) != "D") ? "silnice I/{$silnice}" : "dálnice {$silnice}";

        $query84 = "SELECT jmeno FROM test_osoby WHERE id = '$zadatel';";
        if ($result84 = mysqli_query($link, $query84)) {
            while ($row84 = mysqli_fetch_row($result84)) {
                $podpis = $row84[0];
            }
        }

        $query91 = "UPDATE testovani SET overeno = '1' WHERE id = $id;";
        $prikaz91 = mysqli_query($link, $query91);

        $datum_format = date("d.m.Y", strtotime($datum));

        $hlasky_array = explode("|", $hlasky);
        $hlasky_list = implode(",", $hlasky_array);

        $query99 = "SELECT min(CAST(kilometr AS DOUBLE)), max(CAST(kilometr AS DOUBLE)) FROM hlasky WHERE silnice = '$silnice' AND id IN ($hlasky_list);";
        if ($result99 = mysqli_query($link, $query99)) {
            while ($row99 = mysqli_fetch_row($result99)) {
                $km_min = $row99[0];
                $km_max = $row99[1];

                $km_min = str_replace(".", ",", $km_min);
                $km_max = str_replace(".", ",", $km_max);
            }
        }
    }
}


$pdf = new tFPDF('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
$pdf->AddFont('DejaVu', 'B', 'DejaVuSans-Bold.ttf', true);
$pdf->AddFont('DejaVu', 'I', 'DejaVuSans-Oblique.ttf', true);
$pdf->SetFont('DejaVu', '', 20);

$logo_x = floor((210 - $logo_sirka) / 2);
$pdf->Image($img_logo, $logo_x, 10, $logo_sirka, $logo_vyska, 'PNG');
$pdf->Ln(50);
$pdf->Cell(0, 12, "Protokol z funkční zkoušky$cislo_prokotolu", 0, 1, 'C');
$pdf->SetFontSize(15);
$pdf->Cell(0, 9, 'Telefonické spojení SOS hlásek s linkou 112', 0, 1, 'C');
$pdf->Ln(12);
$pdf->SetFontSize(12);

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(25, 6, 'Projekt:', 0, 0, 'L');
$pdf->Cell(0, 6, $projekt, 0, 1, 'L');

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(25, 6, 'Investor:', 0, 0, 'L');
$pdf->Cell(0, 6, $investor_L1, 0, 1, 'L');

if ($investor_L2 != "") {
    $pdf->Cell(35, 6, '', 0, 0, 'L');
    $pdf->Cell(0, 6, $investor_L2, 0, 1, 'L');
}

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(25, 6, 'Dodavatel:', 0, 0, 'L');
$pdf->Cell(0, 6, $dodavatel_L1, 0, 1, 'L');

$pdf->Cell(35, 6, '', 0, 0, 'L');
$pdf->Cell(0, 6, $dodavatel_L2, 0, 1, 'L');
$pdf->Ln(12);

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->MultiCell(0, 6, "Dodavatel $dodavatel_nazev provedl dne $datum_format funkční zkoušku spojení SOS hlásek s\u{00A0}centrem tísňové komunikace linky 112.", 0, 'L');

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(0, 6, "Hlásky byly testovány na úsecích $textsilnice (km $km_min – $km_max).", 0, 1, 'L');
$pdf->Ln(6);

$qrcode .= "$podpis | $datum_format | $silnice | $km_min | $km_max | ";

$pdf->SetFont('DejaVu', 'B', 8);
$w = [5, 25, 20, 15, 15];
$pdf->Cell($w[0], 5, '', 0, 0, 'C');
$pdf->Cell($w[1], 5, 'Zařízení', 1, 0, 'C');
$pdf->Cell($w[2], 5, 'Typ', 1, 0, 'C');
$pdf->Cell($w[3], 5, 'Počet', 1, 0, 'C');
$pdf->Cell($w[4], 5, 'Stav', 1, 1, 'C');

$pdf->SetFont('DejaVu', '', 8);
$query237 = "SELECT hlavni, typ, count(*) FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' GROUP BY hlavni, typ ORDER BY hlavni DESC;";
if ($result237 = mysqli_query($link, $query237)) {
    while ($row237 = mysqli_fetch_row($result237)) {
        $hlavni_hlasky = $row237[0];
        $typ_hlasky = $row237[1];
        $pocet_hlasek = $row237[2];

        $hlavni = ($hlavni_hlasky == "1") ? "Hláska hlavní" : "Hláska vedlejší";

        $query246 = "SELECT popis FROM enum_typ WHERE id = '$typ_hlasky';";
        if ($result246 = mysqli_query($link, $query246)) {
            while ($row246 = mysqli_fetch_row($result246)) {
                $nazevtypu = $row246[0];
            }
        }

        $stav = 0;
        $query254 = "SELECT typ, hlavni, SUM(`status`) FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' GROUP BY typ, hlavni ORDER BY typ, hlavni DESC;";
        if ($result254 = mysqli_query($link, $query254)) {
            while ($row254 = mysqli_fetch_row($result254)) {
                $typ_kontrola = $row254[0];
                $hlavni_kontrola = $row254[1];
                $status_kontrola = $row254[2];

                if ($typ_hlasky == $typ_kontrola && $hlavni_kontrola == $hlavni_hlasky) {
                    $stav += $status_kontrola;
                }
            }
        }

        $pdf->Cell($w[0], 5, '', 0, 0, 'C');
        $pdf->Cell($w[1], 5, $hlavni, 1, 0, 'L');
        $pdf->Cell($w[2], 5, $nazevtypu, 1, 0, 'C');
        $pdf->Cell($w[3], 5, $pocet_hlasek, 1, 0, 'C');
        $pdf->Cell($w[4], 5, ($stav > 0) ? "Chyba" : "OK", 1, 1, 'C');

        $qrcode .= "H$hlavni_hlasky | $nazevtypu | $pocet_hlasek | ";
        $qrcode .= ($stav > 0) ? "Chyba | " : "OK | ";
    }
}
$pdf->Ln(6);

$qrcode = substr($qrcode, 0, -2);
//QRcode::png($qrcode, 'QRcode.png');
//$pdf->Image('QRcode.png', 150, 145, 25, 25, 'PNG');

$pdf->SetFontSize(12);
$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(0, 6, 'Funkční zkouška systému:', 0, 1, 'L');

$pdf->SetFont('DejaVu', 'B', 8);
$w = [5, 20, 20, 25, 15, 15, 15, 20, 20, 25];
$pdf->Cell($w[0], 5, '', 0, 0, 'C');
$pdf->Cell($w[1], 5, 'Typ', 1, 0, 'C');
$pdf->Cell($w[2], 5, 'Označení', 1, 0, 'C');
$pdf->Cell($w[3], 5, 'Směr', 1, 0, 'C');
$pdf->Cell($w[4], 5, 'Zkouška', 1, 0, 'C');
$pdf->Cell($w[5], 5, 'SOS–IZS', 1, 0, 'C');
$pdf->Cell($w[6], 5, 'IZS–SOS', 1, 0, 'C');
$pdf->Cell($w[7], 5, $pozice_dodavatel, 1, 0, 'C');
$pdf->Cell($w[8], 5, 'Pozice 112', 1, 0, 'C');
$pdf->Cell($w[9], 5, 'Poznámka', 1, 1, 'C');

$pdf->SetFont('DejaVu', '', 8);
$query160 = "SELECT typ, kilometr, smer, zkouska, hovorOUT, hovorIN, lokaceSPEL, lokace112, poznamka, `status` FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' ORDER BY CAST(kilometr AS DOUBLE), smer DESC;";
if ($result160 = mysqli_query($link, $query160)) {
    while ($row160 = mysqli_fetch_row($result160)) {
        $typ = $row160[0];
        $kilometr = $row160[1];
        $smer = $row160[2];
        $zkouska = $row160[3];
        $hovor_out = $row160[4];
        $hovor_in = $row160[5];
        $lokaceSPEL = $row160[6];
        $lokace112 = $row160[7];
        $poznamka = $row160[8];

        $smer_nazev = SmerNazev($silnice, $smer, $kilometr);
        $kilometr = str_replace(".", ",", $kilometr);

        $x = $w[0] + $w[1] + $w[2] + $w[3] + 10;
        $y = floor($pdf->GetY());

        if ($y > 265) {
            $pdf->AddPage();
            $y = 15;
            $pdf->SetFont('DejaVu', 'B', 8);
            $pdf->Cell($w[0], 5, '', 0, 0, 'C');
            $pdf->Cell($w[1], 5, 'Typ', 1, 0, 'C');
            $pdf->Cell($w[2], 5, 'Označení', 1, 0, 'C');
            $pdf->Cell($w[3], 5, 'Směr', 1, 0, 'C');
            $pdf->Cell($w[4], 5, 'Zkouška', 1, 0, 'C');
            $pdf->Cell($w[5], 5, 'SOS–IZS', 1, 0, 'C');
            $pdf->Cell($w[6], 5, 'IZS–SOS', 1, 0, 'C');
            $pdf->Cell($w[7], 5, $pozice_dodavatel, 1, 0, 'C');
            $pdf->Cell($w[8], 5, 'Pozice 112', 1, 0, 'C');
            $pdf->Cell($w[9], 5, 'Poznámka', 1, 1, 'C');
            $pdf->SetFont('DejaVu', '', 8);
        }

        $smer_delka = $pdf->GetStringWidth($smer_nazev);
        $pozn_delka = $pdf->GetStringWidth($poznamka);
        $smer_vyska = floor($smer_delka / 20);
        $pozn_vyska = floor($pozn_delka / 22);
        $h = ($smer_vyska > 0 || $pozn_vyska > 0) ? (max($smer_vyska, $pozn_vyska) + 1) * 4 : 5;

        $smer_radek = $h / ($smer_vyska + 1);
        $pozn_radek = $h / ($pozn_vyska + 1);

        $kilometr = str_replace(".", ",", $kilometr);

        $query185 = "SELECT popis FROM enum_typ WHERE id = '$typ';";
        if ($result185 = mysqli_query($link, $query185)) {
            while ($row185 = mysqli_fetch_row($result185)) {
                $nazev_typu = $row185[0];
            }
        }

        $pdf->Cell($w[0], $h, '', 0, 0, 'C');
        $pdf->Cell($w[1], $h, $nazev_typu, 1, 0, 'C');
        $pdf->Cell($w[2], $h, $kilometr, 1, 0, 'C');
        $pdf->MultiCell($w[3], $smer_radek, $smer_nazev, 1, 'C');
        $pdf->SetXY($x, $y);
        $pdf->Cell($w[4], $h, ($zkouska == "1") ? "\u{2611}" : "\u{2610}", 1, 0, 'C');
        $pdf->Cell($w[5], $h, ($hovor_out == "1") ? "\u{2611}" : "\u{2610}", 1, 0, 'C');
        $pdf->Cell($w[6], $h, ($hovor_in == "1") ? "\u{2611}" : "\u{2610}", 1, 0, 'C');
        $pdf->Cell($w[7], $h, ($lokaceSPEL == "1") ? "\u{2611}" : "\u{2610}", 1, 0, 'C');
        $pdf->Cell($w[8], $h, ($lokace112 == "1") ? "\u{2611}" : "\u{2610}", 1, 0, 'C');
        $pdf->MultiCell($w[9], $pozn_radek, $poznamka, 1, 'L');
        $pdf->SetXY(10, $y + $h);
    }
}
$pdf->SetFont('DejaVu', 'I', 8);
$pdf->Cell(10, 25, '', 0, 0, 'L');
$pdf->MultiCell(0, 5, "Zkouška spojení – test volání ze SOS hlásky do veřejné telekomunikační sítě.
SOS–IZS – test volání ze SOS hlásky na telefonní linku 112.
IZS–SOS – test volání z telefonní linky 112 na SOS hlásku.
$pozice_dodavatel – kontrola údajů evidovaných u dodavatele.
Pozice 112 – kontrola údajů zobrazených na lince 112.", 0, 'L');

$pdf->SetFont('DejaVu', '', 12);
$pdf->Ln(6);
$y = $pdf->GetY();
if ($y > 245) {
    $pdf->AddPage();
    $y = 10;
}
$pdf->SetXY(10, $y);
$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(55, 6, 'Funkční zkoušky provedli:', 0, 0, 'L');
$pdf->Cell(50, 6, '', 0, 0, 'L');
$pdf->Cell(0, 6, '', 0, 1, 'C');

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(55, 6, 'Za dodavatele:', 0, 0, 'L');
$pdf->Cell(50, 6, '', 0, 0, 'L');
$pdf->Cell(0, 6, '', 0, 1, 'C');

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(55, 6, $dodavatel_nazev, 0, 0, 'L');
$pdf->Cell(50, 6, '', 0, 0, 'L');
$pdf->Cell(0, 6, '', 0, 1, 'C');

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(55, 6, '', 0, 0, 'L');
$pdf->Cell(50, 6, $podpis, 0, 0, 'L');
$pdf->Cell(0, 6, '……………………', 0, 1, 'C');

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(55, 6, '', 0, 0, 'L');
$pdf->Cell(50, 6, '', 0, 0, 'L');
$pdf->Cell(0, 6, 'podpis', 0, 1, 'C');

$pdf->Ln(12);
$y = $pdf->GetY();
if ($y > 250) {
    $pdf->AddPage();
    $y = 20;
}
$pdf->SetXY(10, $y);
$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(55, 6, 'Za TCTV 112:', 0, 0, 'L');
$pdf->Cell(50, 6, '', 0, 0, 'L');
$pdf->Cell(0, 6, '', 0, 1, 'C');

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(55, 6, '', 0, 0, 'L');
$pdf->Cell(50, 6, 'Ing. Bessa Urbánek Jan', 0, 0, 'L');
$pdf->Cell(0, 6, '……………………', 0, 1, 'C');

$pdf->Cell(10, 6, '', 0, 0, 'L');
$pdf->Cell(55, 6, '', 0, 0, 'L');
$pdf->Cell(50, 6, '', 0, 0, 'L');
$pdf->Cell(0, 6, 'podpis', 0, 1, 'C');

$pdf->Ln(12);
$pdf->Cell(10, 6, '', 0, 0, 'L');

$dnes_datum = date("d.m.Y", time());
$pdf->Cell(0, 6, "V $mesto dne $dnes_datum", 0, 1, 'L');

$pdf->Output();

// unlink("QRcode.png");