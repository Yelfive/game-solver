<?php

include __DIR__ . '/solver.php';

$source = [
    '503620805',
    '048001920',
    '000000000',
    '910004008',
    '080306000',
    '002010000',
    '069008050',
    '001000000',
    '000900003',
];

function get_time()
{
    list($ms, $s) = explode(' ', microtime());
    return $s * 1000 + intval($ms * 1000);
}

$t1 = get_time();
$solver = (new \gs\sudoku\Solver());
$solver->resolve($source);
$t2 = get_time();

echo 'Time elapsed: ';
echo $t2 - $t1;
echo " ms\n";

$solver->output();

