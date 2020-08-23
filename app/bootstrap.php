<?php

require 'core/ClassLoader.php';
require 'Env.php';

$loader = new ClassLoader();
$loader->registerDir(__DIR__.'/core');
$loader->registerDir(__DIR__.'/models');
$loader->register();