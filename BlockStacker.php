<?php

/**
|--------------------------------------------------------------------------
| BlockStacker Class
|--------------------------------------------------------------------------
*/

class BlockStacker
{
    /**
    |--------------------------------------------------------------------------
    | フィールド初期化
    |--------------------------------------------------------------------------
    */
    public $inputInfo = []; // 入力用の配列
    public $total; // 入力された箱の総数
    public $memory; // メモ化用の配列
    public $highest = 0; // 一番高い積み上げ方の高さ
    public $highestIndex = 0; // そのときの添字
    public $boxCorrespondenceTable = ['front', 'back', 'left', 'right', 'top', 'bottom']; // 面の変換表

    /**
    |--------------------------------------------------------------------------
    | 入力されたものを配列に格納します。
    |--------------------------------------------------------------------------
    */
    public function getInput()
    {
        // 高速化のため標準入力を変数に代入する
        $stdIn = STDIN;

        // 箱の総数取得
        $this->total = fgets($stdIn);

        // 箱の情報を取得
        for ($i = 1; $i <= $this->total; $i++) {
            $line = trim(fgets($stdIn));
            $this->inputInfo[$i] = preg_split('/\s/', $line);
        }
    }

    /**
    |--------------------------------------------------------------------------
    | メモ化に必要な配列を作成します。
    |--------------------------------------------------------------------------
    */
    public function initializeMemory()
    {
        $totalPlane = 6 * $this->total;
        $this->memory[0][] = $this->inputInfo[1][0];
        $this->memory[1][] = $this->inputInfo[1][1];
        $this->memory[2][] = $this->inputInfo[1][2];
        $this->memory[3][] = $this->inputInfo[1][3];
        $this->memory[4][] = $this->inputInfo[1][4];
        $this->memory[5][] = $this->inputInfo[1][5];
        for ($i = 6; $i <= $totalPlane; ++$i) {
            $this->memory[$i] = [];
        }
    }

    /**
    |--------------------------------------------------------------------------
    | 結果を出力します。
    |--------------------------------------------------------------------------
    */
    public function output()
    {
        $outputString = "{$this->highest}";
        foreach ($this->memory[$this->highestIndex] as $key => $plane) {
            $outputString .= "\n" . $this->boxCorrespondenceTable[$key];
        }
        print $outputString;
    }

    /**
    |--------------------------------------------------------------------------
    | 一番軽い箱から一箱ずつ、６つの面を処理します。
    |--------------------------------------------------------------------------
    */
    public function execute()
    {
        foreach ($this->inputInfo as $index => $box) {
            $this->createStack($box[0], $index, 1);
            $this->createStack($box[1], $index, 2);
            $this->createStack($box[2], $index, 3);
            $this->createStack($box[3], $index, 4);
            $this->createStack($box[4], $index, 5);
            $this->createStack($box[5], $index, 6);
        }
    }

    /**
    |--------------------------------------------------------------------------
    | ひとつの箱のひとつの面に対する処理です。
    |--------------------------------------------------------------------------
    */
    public function createStack($target, $index, $planeNumber)
    {
        $numberOfLiterBox = 6 * ($index - 1);
        $memoryIndex = 6 * ($index - 1) + $planeNumber;
        $maxHeight = 0; // 今処理しているものの中で一番の高さ
        $stack = []; // そのときの積み上げ方

        for ($i = 1; $i < $numberOfLiterBox; ++$i) {
            if ($this->canBeStacked($target, $i)) {
                $newHeight = count($this->memory[$i]) + 1;
                if ($newHeight > $maxHeight) {
                    $stack = $this->memory[$i];
                    $stack[] = $target;
                    $maxHeight = $newHeight;
                }
            }
        }
        if (!empty($stack)) {
            $this->memory[$index] = $stack;
        }

        if ($this->highest < $maxHeight) {
            $this->highest = $maxHeight;
            $this->highestIndex = $memoryIndex;
        }
    }

    /**
    |--------------------------------------------------------------------------
    | 箱が積み上げられるかどうか判断します。
    |--------------------------------------------------------------------------
    */
    public
    function canBeStacked($target, $memoryIndex)
    {
        $top = end($this->memory[$memoryIndex]);
//        echo $top;
        if ($target === $top) {
            return true;
        }
        return false;
    }
}

// 計測スタート
$time_start = microtime(true);
$mem = memory_get_usage(true);
$mem = number_format($mem);
print("Memory:{$mem}");

// 実際の処理
$stacker = new BlockStacker();
$stacker->getInput();
if ($stacker->total < 1) {
    exit(1);
}
$stacker->initializeMemory();
$stacker->execute();
$stacker->output();

//echo "\n";
//var_dump($stacker->inputInfo);
//echo "\n";
var_dump($stacker->memory);
echo $stacker->total;

// 計測結果表示
$mem = memory_get_usage(true);
$mem = number_format($mem);
echo "\n";
print("Memory:{$mem}");
$mem = memory_get_peak_usage(true);
$mem = number_format($mem);
echo "\n";
print("Memory:{$mem}");
$time = microtime(true) - $time_start;
echo "\n";
echo "{$time} 秒";
echo "\n";
