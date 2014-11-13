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
    public $highest = 1; // 一番高い積み上げ方の高さ
    public $highestIndex = 1; // そのときの添字
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

        if ($this->total < 1) {
            exit(1);
        }

        // 箱の情報を取得
        for ($i = 1; $i <= $this->total; $i++) {
            $this->inputInfo[$i] = preg_split('/\s/', trim(fgets($stdIn)));
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
        $this->memory = array_fill(0, $totalPlane + 1, null);
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
        echo $outputString;
    }

    /**
     * |--------------------------------------------------------------------------
     * | 一番軽い箱から一箱ずつ、６つの面を１面ずつ渡します。
     * |--------------------------------------------------------------------------
     */
    public function createStack()
    {
        foreach ($this->inputInfo as $index => $box) {
            yield [$box[0], $index, 1];
            yield [$box[1], $index, 2];
            yield [$box[2], $index, 3];
            yield [$box[3], $index, 4];
            yield [$box[4], $index, 5];
            yield [$box[5], $index, 6];
        }
    }

    /**
     * |--------------------------------------------------------------------------
     * | ひとつの箱のひとつの面に対する処理です。
     * |--------------------------------------------------------------------------
     */
    public function execute()
    {
        $createStack = $this->createStack();
        foreach ($createStack as $current) {
            $numberOfLiterBox = 6 * ($current[1] - 1);
            $memoryIndex = 6 * ($current[1] - 1) + $current[2];
            $maxHeight = 0; // 今処理しているものの中で一番の高さ
            $stack = []; // そのときの積み上げ方

            for ($i = 1; $i < $numberOfLiterBox; ++$i) {
                $top = end($this->memory[$i]);
                $opposite = ($top > 2) ? $top - 3 : $top + 3;
                $inputIndex = key($this->memory[$i]);
                if ($current[0] === $this->inputInfo[$inputIndex][$opposite]) {
                    $newHeight = count($this->memory[$i]) + 1;
                    if ($newHeight > $maxHeight) {
                        $stack = $this->memory[$i];
                        $stack[$current[1]] = $current[2] - 1;
                        $maxHeight = $newHeight;
                    }
                }
            }
            // 処理後メモ化
            if (!empty($stack)) {
                $this->memory[$memoryIndex] = $stack;
            } else {
                // どれにも積めなかったときの為
                $this->memory[$memoryIndex][$current[1]] = $current[2] - 1;
            }

            // 現在の高さが一番なら更新
            if ($this->highest < $maxHeight) {
                $this->highest = $maxHeight;
                $this->highestIndex = $memoryIndex;
            }
        }
    }

//    /**   createStack にそのまま書いた方が速かった */
//     * |--------------------------------------------------------------------------
//     * | 箱が積み上げられるかどうか判断します。
//     * |--------------------------------------------------------------------------
//     */
//    public
//    function canBeStacked($target, $memoryIndex)
//    {
//        $top = end($this->memory[$memoryIndex]);
//        $inputIndex = key($this->memory[$memoryIndex]);
//        if ($target === $this->inputInfo[$inputIndex][$top]) {
//            return true;
//        }
//        return false;
//    }
}

// メモリ上限解放 環境により必要
ini_set('memory_limit', -1);

// 計測スタート
$time_start = microtime(true);

// 実際の処理
$stacker = new BlockStacker();
$stacker->getInput();
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

