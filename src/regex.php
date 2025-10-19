<?php
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);  
$html = file_get_contents("https://zck-krakow.pl/funerals", false, stream_context_create($arrContextOptions));

preg_match_all("/<h4><strong>(\D*?)<\/strong><\/h4>(<table.+?<\/table>)/", $html, $matches, PREG_SET_ORDER);
echo "<pre>";
foreach ($matches as $match) {
    echo $match[1];
    echo $match[2];
}
echo "</pre>";
