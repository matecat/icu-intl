<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 20/01/26
 * Time: 13:13
 *
 */

if (!@include_once 'lib/Bootstrap.php') {
    die("Location: configMissing");
}

Bootstrap::start();

$chars = range('a', 'z');
$str = 'abcdefghijklmnopqrstuvwxyz';

$t1 = 0;
$t2 = 0;
for ($i = 0; $i < 20; $i++) {

    for ($j = 0; $j < 300000; $j++) {
        $start = microtime(true);
        $x = implode('', array_slice($chars, 5, 8));
        $end = microtime(true);
        $t1 += $end - $start;
    }

    for ($j = 0; $j < 300000; $j++) {
        $start = microtime(true);
        $x = mb_substr($str, 5, 8);
        $end = microtime(true);
        $t2 += $end - $start;
    }

}

echo $t1 . "\n";
echo $t2 . "\n";