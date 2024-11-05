<?php
include "lib/simple_html_dom.php";
$url = 'https://bjk.com.tr/tr/fikstur/1/1/614/579/4445';
$htmlContent = file_get_contents($url);
$html = str_get_html($htmlContent);

if (!$html) {
    echo "Error fetching the URL";
    exit;
}

$table = $html->find('.f-table', 0);
if ($table) {
    $fixtures = $table->find('tr'); // Tüm <tr> etiketlerini bul
    foreach ($fixtures as $match) {
        if (strpos($match->innertext, 'Beşiktaş') !== false) {
            $dateTimeElement = $match->find('td.a span', 0);
            $dateTime = $dateTimeElement->innertext;
            $opponentElement = $match->find('td.d', 0);
            $opponent = $opponentElement->innertext;
            if ($opponent == "Beşiktaş") {
                $opponentElement = $match->find('td.cc', 0);
            } else {
                $opponentElement = $match->find('td.cc', 0);
            }
            $opponent = $opponentElement->innertext;
            echo "Beşiktaş'ın rakibi: " . trim($opponent) . "<br>";
            $data = $dateTime;
            $dateEndPos = strpos($data, '<br');
            $dateTime = trim(substr($data, 0, $dateEndPos));

            $smallStartPos = strpos($data, '<small>') + strlen('<small>');
            $smallEndPos = strpos($data, '</small>', $smallStartPos);
            $stadium = trim(substr($data, $smallStartPos, $smallEndPos - $smallStartPos));

            $stadium = strip_tags($stadium);
            $dateTime= strip_tags($dateTime);
            echo $dateTime;
            $dateTime = DateTime::createFromFormat('d.m.Y H.i', trim($dateTime))->format('H:i Y.m.d');

            echo "Tarih ve saat: " . trim($dateTime) . "<br>";
            echo "Stadyum: ". trim($stadium). "<br>";
        }
    }
    $matchInfo = [
        "opponent" => trim($opponent),
        "dateTime" => trim($dateTime),
        "stadium" => trim($stadium)
    ];
    json_encode($matchInfo);
    $infoFile = "../src/data/infos.json";
    file_put_contents($infoFile, strval(json_encode($matchInfo)));

} else {
    echo "Fikstür tablosu bulunamadı.";
}
