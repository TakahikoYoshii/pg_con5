<?php

/**
 * |--------------------------------------------------------------------------
 * | BlockStacker Class
 * |--------------------------------------------------------------------------
 */
class BlockStacker
{
    /**
     * |--------------------------------------------------------------------------
     * | フィールド初期化
     * |--------------------------------------------------------------------------
     */
    public $inputInfo = []; // 入力用の配列
    public $total; // 入力された箱の総数
    public $memory; // メモ化用の配列
    public $highest = 0; // 一番高い積み上げ方の高さ
    public $highestIndex = 0; // そのときの添字
    public $boxCorrespondenceTable = ['front', 'back', 'left', 'right', 'top', 'bottom']; // 面の変換表

    /**
     * |--------------------------------------------------------------------------
     * | 入力されたものを配列に格納します。
     * |--------------------------------------------------------------------------
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
     * |--------------------------------------------------------------------------
     * | メモ化に必要な配列を作成します。
     * |--------------------------------------------------------------------------
     */
    public function initializeMemory()
    {
        $totalPlane = 6 * $this->total;
        $this->memory[1][1] = 0;
        $this->memory[2][1] = 1;
        $this->memory[3][1] = 2;
        $this->memory[4][1] = 3;
        $this->memory[5][1] = 4;
        $this->memory[6][1] = 5;
        for ($i = 7; $i <= $totalPlane; ++$i) {
            $this->memory[$i] = [];
        }
    }

    /**
     * |--------------------------------------------------------------------------
     * | 結果を出力します。
     * |--------------------------------------------------------------------------
     */
    public function output()
    {
        $outputString = "{$this->highest}";
        foreach ($this->memory[$this->highestIndex] as $key => $plane) {
            $outputString .= "\n{$key} " . $this->boxCorrespondenceTable[$plane];
        }
        print $outputString;
    }

    /**
     * |--------------------------------------------------------------------------
     * | 一番軽い箱から一箱ずつ、６つの面を処理します。
     * |--------------------------------------------------------------------------
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
     * |--------------------------------------------------------------------------
     * | ひとつの箱のひとつの面に対する処理です。
     * |--------------------------------------------------------------------------
     */
    public function createStack($target, $index, $planeNumber)
    {
        $numberOfLiterBox = 6 * ($index - 1);
        $memoryIndex = 6 * ($index - 1) + $planeNumber;
        $maxHeight = 0; // 今処理しているものの中で一番の高さ
        $stack = []; // そのときの積み上げ方

        for ($i = 1; $i < $numberOfLiterBox; ++$i) {
            // どれにも積めなかったときの為
            if (empty($stack)) {
                $stack = $this->memory[$i];
            }
            if ($this->canBeStacked($target, $i)) {
                $newHeight = count($this->memory[$i]) + 1;
                if ($newHeight > $maxHeight) {
                    $stack = $this->memory[$i];
                    $oppositePlane = ($planeNumber > 3) ? $planeNumber - 4 : $planeNumber + 2;
                    $stack[$index] = $oppositePlane;
                    $maxHeight = $newHeight;
                }
            }
        }
        // 処理後メモ化
        if (!empty($stack)) {// 一段目のときのみfalse
            $this->memory[$memoryIndex] = $stack;
        }

        // 現在の高さが一番なら更新
        if ($this->highest < $maxHeight) {
            $this->highest = $maxHeight;
            $this->highestIndex = $memoryIndex;
        }
    }

    /**
     * |--------------------------------------------------------------------------
     * | 箱が積み上げられるかどうか判断します。
     * |--------------------------------------------------------------------------
     */
    public
    function canBeStacked($target, $memoryIndex)
    {
        $top = end($this->memory[$memoryIndex]);
        $inputIndex = floor($memoryIndex / 6) + 1;
        if ($target == $this->inputInfo[$inputIndex][$top]) {
            return true;
        }
        return false;
    }
}

// 計測スタート
$time_start = microtime(true);
//$mem = memory_get_usage(true);
//$mem = number_format($mem);
//print("Memory:{$mem}");

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
//echo "\n";
//var_dump($stacker->memory);
//echo $stacker->total;
//echo "\n";
//echo $stacker->highest;
//echo "\n";
//echo $stacker->highestIndex;

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
