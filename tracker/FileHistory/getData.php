<?php

$filename = "./logFiles/" . $_GET["id"] . ".log";
$from = $_GET["from"];
$to = $_GET["to"];
if($from=="current"){
    $from="1/1/2013";
}
if($to=="current"){
    $to="1/1/3013";
}

if (file_exists($filename)) {
    $fp = fopen($filename, "r");
    while (!feof($fp)) {
        $line = fgets($fp); //split the date
        $date = split("gr.", $line);

        $line = fgets($fp); //split the 


        list($header, $infoHash, $thresHold, $seeders) = split("=#=#=", $line);
        if ($date[0] != "") {

            //convert log date to d/m/Y format
            //May 10, 2013 5:17:07 PM
            $tem = strtotime($date[0]);


            $CurrentDate = DateTime::createFromFormat('d/m/Y', date("d", $tem) . '/' . date("m", $tem) . '/' . date("Y", $tem));
            $FromDate = DateTime::createFromFormat('d/m/Y', $from);
            $toDate = DateTime::createFromFormat('d/m/Y', $to);

            if ($CurrentDate >= $FromDate && $CurrentDate <= $toDate) {
                
                                
                echo date("d", $tem)."/".date("M", $tem) . "\t" . $infoHash . "\t" . $thresHold . "\t" . $seeders . "=#=#=";
                                
                
            }

          
//            echo $date[0]."\t".$infoHash."\t".$thresHold."\t".$seeders."=#=#=";
//            DebugLines
//            echo "<b>Date: </b>$date[0]<br>";
//            echo "<b>Hash: </b>$infoHash<br>";
//            echo "<b>Threshold: </b>$thresHold<br>";
//            echo "<b>seeders: </b>$seeders<br>";
        }
    }
}
?>
