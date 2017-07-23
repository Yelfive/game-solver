<?php

namespace gs\sudoku;

/**
 * ```example
 * $source = [
 *      '503620800',
 *      '048001920',
 *      '000000000',
 *      '910004008',
 *      '080306000',
 *      '002010000',
 *      '069008050',
 *      '001000000',
 *      '000900003',
 *  ];
 *  (new Sudoku)->resolve($source);
 * ```
 */
class Solver
{
    protected $x = 0;
    protected $y = 0;
    protected $data = [];
    protected $stack = [];
    /**
     * @var array Origin data stores here
     */
    protected $source = [];
    protected $result = [];

    public function resolve($data)
    {
        $this->data = $this->source = $data;
        while ($this->y < 9) {
            if (0 == $this->data[$this->y][$this->x]) {
                $this->tries();
            }

            if (++$this->x === 9) {
                $this->x = 0;
                $this->y++;
            }

            if ($this->y >= 9 && $this->tryAgain()) {
                $this->y = 0;
            }
        }
    }

    public function getStack()
    {
        return $this->stack;
    }

    public function getResult()
    {
        return $this->data;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function output()
    {
        foreach ($this->data as $y => $item) {
            $this->outputSeparator();
            echo "\n|";
            for ($x = 0; $x < 9; $x++) {
                $highlight = $this->source[$y][$x] == 0;
                if ($highlight) echo "\033[47;30m";
                echo " {$item[$x]} ";
                if ($highlight) echo "\033[0m";
                echo "|";
            }
        }
        $this->outputSeparator();
        echo "\n\n";
    }

    protected function outputSeparator()
    {
        echo "\n+" . str_repeat('---+', 9);
    }

    /**
     * @return bool Indicates if should try again from (0,0)
     */
    protected function tryAgain(): bool
    {
        foreach ($this->data as $item) {
            if (false !== strpos($item, '0')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Rollback the stack
     * @return int Returns next number should be tried
     */
    protected function rollback()
    {
        list($x, $y, $trial) = array_pop($this->stack);
        $this->data[$y][$x] = '0';
        if ($trial == 9) {
            if (!$this->stack) {
                die("Cannot rollback, no stack\n");
            }
            return $this->rollback();
        } else {
            $this->x = $x;
            $this->y = $y;
            return $trial + 1;
        }
    }

    protected function tries($from = 1)
    {
        for ($i = $from; $i < 10; $i++) {
            $match = $this->checkBlock($i) && $this->checkLine($i);
            if ($match) {
                $this->data[$this->y][$this->x] = $i;
                $this->stack[] = [$this->x, $this->y, $i];
                break;
            }
        }

        // No match
        if (!$match) {
            $next = $this->rollback();
            $this->tries($next);
        }
    }

    protected function checkBlock($value): bool
    {
        //[[]]
        foreach ($this->getBlockCoordinates() as list($x, $y)) {
            if ($value == $this->data[$y][$x]) {
                return false;
            }
        }
        return true;
    }

    protected function getBlockCoordinates()
    {
        $x = intval($this->x / 3) * 3;
        $y = intval($this->y / 3) * 3;
        $coordinates = [];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $coordinates[] = [$x + $i, $y + $j];
            }
        }
        return $coordinates;
    }

    protected function checkLine($value): bool
    {
        // $value should between 1 and 9
        // horizon
        for ($i = 0; $i < 9; $i++) {
            if ($this->data[$this->y][$i] == $value) {
                return false;
            }
        }

        // vertical
        for ($i = 0; $i < 9; $i++) {
            if ($this->data[$i][$this->x] == $value) {
                return false;
            }
        }

        return true;
    }
}
