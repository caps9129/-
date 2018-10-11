<?php
$lines = gzfile('http://tisvcloud.freeway.gov.tw/history/vd/20180910/vd_value_2356.xml.gz');
foreach ($lines as $line) {
    echo $line;
}
?>