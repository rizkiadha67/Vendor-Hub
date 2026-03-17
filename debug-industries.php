<?php
require 'wp-load.php';
$terms = get_terms(['taxonomy' => 'vh_industry', 'hide_empty' => false]);
foreach($terms as $t) {
    echo $t->name . " => " . $t->slug . PHP_EOL;
}
