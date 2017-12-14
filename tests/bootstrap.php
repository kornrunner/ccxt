<?php

use VCR\VCR;

require_once __DIR__ . '/../vendor/autoload.php';

VCR::configure()->setMode('new_episodes')->setStorage('json');
VCR::turnOn();
