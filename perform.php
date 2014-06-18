<?php

require 'vendor/autoload.php';

use Sinc\Grelha;
use Sinc\Produtos;

$comand = isset($argv[1]) ? $argv[1] : '';
		
switch ($comand) {
	
	case "produtos":
		$job = new Produtos();
		$job->setup();
		$job->perform();
		break;
	
	case "grelha":
		$job = new Grelha();
		$job->setup();
		$job->perform();
		break;
		
	default:
		echo "PHC Magento Sinc v1\r\n";
		echo "Copyright José Pinto jpinto@josemariapinto.pt 2014\r\n";
		echo "\r\n";
		echo "missing argument: grelha produtos\r\n";
		break;
}
