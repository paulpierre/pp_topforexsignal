<?php
global $controllerID,$controllerObject,$controllerFunction;

print '<pre>';
print print_r($_REQUEST,true);
error_log(print_r($_REQUEST,true));

file_put_contents('unbounce.txt', print_r($_REQUEST,true));

?>