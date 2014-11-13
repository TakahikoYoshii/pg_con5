<?php

// テストデータ作成
$testData = "500";
function generator($from, $to)
{
    for ($i = $from; $i <= $to; ++$i) {
        $val = null;
        for ($j = 0; $j < 6; ++$j) {
            $val .= rand(1, 100) . " ";
        }
        yield $val;
    }
}

$g = generator(1, 500);
foreach ($g as $val) {
    $testData .= "\n" . $val;
}

echo $testData;
