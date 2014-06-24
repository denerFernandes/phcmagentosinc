<?php

require 'vendor/autoload.php';

use Sinc\Grelha;
use Sinc\Produtos;

$comand = isset($argv[1]) ? $argv[1] : '';

/**
 * Ponto de entrada e  selecção do COMANDO a correr
 * 
 * @todo estes comandos deveriam ser enviados para uma QUEUE
 */		
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
		echo "Copyright Jose Pinto bluecor@gmail.com 2014\r\n";
		echo "\r\n";
		echo "missing argument: grelha produtos\r\n";
		break;
}
