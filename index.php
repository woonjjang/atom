<?php

// Allow workers to request clearing of opcode cache
if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] && $_GET['_opcache'] == 'clear')
{
  opcache_reset();
  print "Opcache reset.\n";
  exit();
}

require_once(dirname(__FILE__).'/config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('qubit', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
