<?php
// Test script for API health endpoint
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['endpoint'] = 'health';

require 'api.php';
