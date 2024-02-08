<?php
$text = 'This is a test text. It should be chunked into smaller pieces.';

require_once __DIR__ . '/vendor/autoload.php';

$chunker = new GPT_Toolkit\Chunker($text);
$chunks = $chunker->chunk(3800);

var_dump($chunks);

// $joined = implode(' ', array_map(function($chunk) {
//     return $chunk['content'];
// }, $chunks));

// echo $joined;