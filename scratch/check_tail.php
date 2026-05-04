<?php
$file = 'storage/backups/unb_news_2026-05-03_09-13-42.sql';
if (!file_exists($file)) {
    die("File not found\n");
}
$handle = fopen($file, 'rb');
fseek($handle, -1000, SEEK_END);
$tail = fread($handle, 1000);
fclose($handle);
echo $tail;
