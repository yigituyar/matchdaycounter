<?php
include "../lib/simple_html_dom.php";
$url = 'https://www.mackolik.com/takim/be%C5%9Fikta%C5%9F/ma%C3%A7lar/2ez9cvam9lp9jyhng3eh3znb4';
$htmlContent = file_get_contents($url);
$html = str_get_html($htmlContent);

function getMatchTime($matchUrl)
{
    $matchTimePage = file_get_contents($matchUrl);
    $matchTimeHtml = str_get_html($matchTimePage);
    $matchTimeElement = $matchTimeHtml->find('.p0c-soccer-match-details-header__score-time', 0);
    $matchTime = $matchTimeElement->innertext;
    return $matchTime;
}



if (!$html) {
    echo "Error fetching the URL";
    exit;
}

$table = $html->find('.page-team-fixtures__container', 0);
if ($table) {
    $count = 0;
    $fixtures = $table->find('.p0c-team-matches__teams-container'); // Tüm <tr> etiketlerini bul
    foreach ($fixtures as $match) {
        $matchHtml = $match->innertext;

        if (strpos($matchHtml, 'p0c-team-matches__button--start-time') !== false) {
            $dateTimeElement = $match->find('.p0c-team-matches__button', 0);
            $dateTimeMD = $dateTimeElement->find("span", 0)->innertext;
            $dateTimeY = $dateTimeElement->find("span", 1)->innertext;
            $date = $dateTimeMD . trim($dateTimeY);
            $matchURL = $dateTimeElement->href;
            $dateTime = $date . getMatchTime($matchURL);
            $dateTime = DateTime::createFromFormat('d.m Y H:i', trim($dateTime));
            $dateTime = $dateTime->modify('+3 hours');

            $dateTime = $dateTime->format('H:i Y.m.d');



            $opponent = strpos($match->find(".p0c-team-matches__team-full-name", 0)->innertext, "Beşiktaş") !== false
                ? $match->find(".p0c-team-matches__team-full-name", 1)->innertext
                : $match->find(".p0c-team-matches__team-full-name", 0)->innertext;
            echo "<a href='" . $matchURL . "'>maç sayfası </a>";
            echo $dateTime;
            echo " Rakip takım: " . trim($opponent) . "<br>";


            $matchData = [
                "matchUrl" => $matchURL,
                "dateTime" => $dateTime,
                "opponent" => trim($opponent)
            ];
            if(trim($opponent) !=="BAY"){
                $matchInfo[] = $matchData;
            }
        }
    }
    usort($matchInfo, function ($a, $b) {
        // Tarih formatını DateTime nesnesine dönüştür
        $dateA = DateTime::createFromFormat('H:i Y.m.d', $a['dateTime']);
        $dateB = DateTime::createFromFormat('H:i Y.m.d', $b['dateTime']);
        
        // Karşılaştırma
        return $dateA <=> $dateB;
    });
    json_encode($matchInfo);
    $infoFile = "../src/data/infos.json";
    file_put_contents($infoFile, strval(json_encode($matchInfo)));
} else {
    echo "Fikstür tablosu bulunamadı.";
}
?>