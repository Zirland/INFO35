<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once 'dbconnect.php';

$link = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if ($link === false) {
    die("CHYBA: Nepovedlo se připojit. " . mysqli_connect_error());
}
mysqli_set_charset($link, "utf8");

$krajNames = [
    '19' => 'Hlavní město Praha',
    '27' => 'Středočeský kraj',
    '35' => 'Jihočeský kraj',
    '43' => 'Plzeňský kraj',
    '51' => 'Karlovarský kraj',
    '60' => 'Ústecký kraj',
    '78' => 'Liberecký kraj',
    '86' => 'Královéhradecký kraj',
    '94' => 'Pardubický kraj',
    '108' => 'Kraj Vysočina',
    '116' => 'Jihomoravský kraj',
    '124' => 'Olomoucký kraj',
    '141' => 'Zlínský kraj',
    '132' => 'Moravskoslezský kraj'
];

$krajMails = [
    '19' => 'pha.tctv112@hzscr.cz',
    '27' => 'stc.tctv112@hzscr.cz',
    '35' => 'jhc.tctv112@hzscr.cz',
    '43' => 'plk.tctv112@hzscr.cz',
    '51' => 'kvk.tctv112@hzscr.cz',
    '60' => 'ulk.tctv112@hzscr.cz',
    '78' => 'lbk.tctv112@hzscr.cz',
    '86' => 'hkk.kopis@hzscr.cz',
    '94' => 'pak.tctv112@hzscr.cz',
    '108' => 'vys.tctv112@hzscr.cz',
    '116' => 'jhm.tctv112@hzscr.cz',
    '124' => 'olk.tctv112@hzscr.cz',
    '141' => 'zlk.tctv112@hzscr.cz',
    '132' => 'msk.dohledibc@hzscr.cz'
];

function getGroupedDataByOkres($link, $date)
{
    $query = "
        SELECT k.kraj, h.silnice, MIN(CAST(h.kilometr AS DECIMAL(10, 1))) AS min_kilometr, MAX(CAST(h.kilometr AS DECIMAL(10, 1))) AS max_kilometr
        FROM test_result tr
        LEFT JOIN hlasky h ON tr.id_hlaska=h.id
        LEFT JOIN obce o ON o.kod = h.obecKod
        LEFT JOIN okresy k ON k.kod = o.okres
        WHERE tr.id_test IN (SELECT t.id FROM testovani t WHERE t.datum = ?)
        GROUP BY h.silnice, k.kraj
        ORDER BY k.kraj, h.silnice";

    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 's', $date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $groupedData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $groupedData[$row['kraj']][] = [
            'silnice' => $row['silnice'],
            'min_kilometr' => $row['min_kilometr'],
            'max_kilometr' => $row['max_kilometr'],
        ];
    }
    mysqli_stmt_close($stmt);
    return $groupedData;
}

$date = date('Y-m-d');

$subject = 'Plánované testy SOS hlásek dne ' . date('d.m.Y', strtotime($date));
$content = 'Dobrý den,<br/>informujeme vás o plánovaných testech SOS hlásek ohlášených na dnešní den.<br/><br/>';
$groupedData = getGroupedDataByOkres($link, $date);
foreach ($groupedData as $kraj => $data) {
    $krajName = $krajNames[$kraj] ?? $kraj;
    $content .= "<h3>$krajName</h3>";
    $content .= "<ul>";
    foreach ($data as $row) {
        $content .= "<li>Silnice {$row['silnice']} úsek od km {$row['min_kilometr']} do km {$row['max_kilometr']}</li>";
    }
    $content .= "</ul>";

    $recipients[] = $krajMails[$kraj] ?? '';
}

$content .= "<br/><br/>S pozdravem,<br/>
<b>Jan Bessa Urbánek | O2 IT Services s.r.o.</b><br/>
Specialista pro zákaznická řešení<br/>
Provoz center tísňové komunikace<br/>
Za Brumlovkou 2/266, 140 00  Praha 4 - Michle<br/>
<b>M</b> +420 724 979 459 | <b>T</b> +420 2714 62414<br/>
<a href='mailto:Jan.BessaUrbanek@o2its.cz'>Jan.BessaUrbanek@o2its.cz</a>";

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host = $mail_host;
    $mail->SMTPAuth = true;
    $mail->Username = $mail_username;
    $mail->Password = $mail_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = "UTF-8";

    //Recipients
    $mail->setFrom($mail_username, 'Testování hlásek');
    foreach ($recipients as $recipient) {
        $mail->addAddress($recipient);
    }
    $mail->addBCC('zirland@gmail.com');

    //Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $content;

    if ($groupedData) {
        $mail->send();
        echo 'Message has been sent';
    } else {
        echo 'No data to send';
    }

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

mysqli_close($link);
