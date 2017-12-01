<?php
spl_autoload_register(function ($class_name) {
    $parts = explode('\\', $class_name);
    include_once 'src/'.end($parts) . '.php';
});

include_once 'src/CustomPostType.php';