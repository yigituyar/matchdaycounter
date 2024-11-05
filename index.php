<?php 
$url = 'https://bjk.com.tr/tr/fikstur/1/1/614/579/4445';
$html = file_get_contents($url);

if (!$html){
    echo "Error fetching the URL";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BJK MATCHDAY COUNTER</title>
    <link rel="stylesheet" href="src/style/style.css">
</head>
<body>
    <h1></h1>
</body>
</html>