<?php

use VCR\VCR;

require_once __DIR__ . '/../vendor/autoload.php';

VCR::configure()->setStorage('json');
VCR::turnOn();