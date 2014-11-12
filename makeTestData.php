<?php

// テストデータ作成
$testData = "10";
function generator($from, $to)
{
    for ($i = $from; $i <= $to; ++$i) {
        $val = null;
        for ($j = 0; $j < 6; ++$j) {
            $val .= rand(1, 10) . " ";
        }
        yield $val;
    }
}

$g = generator(1, 10);
foreach ($g as $val) {
    $testData .= "\n" . $val;
}

echo $testData;
