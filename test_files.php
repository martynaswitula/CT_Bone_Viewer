<?php
$files = glob('/home/site/wwwroot/*.php');
foreach ($files as $f) {
    echo basename($f) . "<br>";
}
?>