<?php
/**
 *  _______                   _______ _______ _______  _____
 * (_______)                 (_______|_______|_______)(_____)
 *  _____    ____ _____ ____  ______  _______ ______  _  __ _
 * |  ___)  / ___) ___ |  _ \(_____ \(_____  |  ___ \| |/ /| |
 * | |_____| |   | ____| | | |_____) )     | | |___) )   /_| |
 * |_______)_|   |_____)_| |_(______/      |_|______/ \_____/
 *
 * @author Eren5960
 * @link https://github.com/Eren5960
 * @date 12 Mayıs 2020
 */
declare(strict_types=1);
 
namespace Eren5960\JoinStats;
 
use Eren5960\JoinStats\provider\ProviderManager;
use Eren5960\JoinStats\provider\StatsProvider;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat;
use function trim;
use function fgets;
use function is_numeric;
use const STDIN;

final class SetupPlugin{
	/** @var null|StatsProvider */
	private $select = null;

    public function __construct(){
    	Terminal::writeLine(Loader::PREFIX . TextFormat::AQUA . 'Welcome to JoinStats setup wizard.');
    	Terminal::writeLine(Loader::PREFIX . TextFormat::AQUA . 'Available providers: ');
    	/** @var StatsProvider[] $providers */
    	$providers = [];

    	foreach(ProviderManager::all() as $name => $provider){
    		if(array_search($provider, $providers) === false){
    			$providers[] = $provider;
		    }
	    }

    	foreach($providers as $i => $provider){
			Terminal::writeLine(Loader::PREFIX . TextFormat::GRAY . '[' . TextFormat::GOLD . $i . TextFormat::GRAY . '] => ' . TextFormat::YELLOW . $provider->getName());
	    }

    	$i = -1;
    	do{
    		++$i;

		    if($i === 0){
			    Terminal::write(Loader::PREFIX . TextFormat::GREEN . 'Please type the provider type: ' . TextFormat::WHITE);
		    }else{
			    Terminal::writeLine(Loader::PREFIX . TextFormat::RED . 'Type is incorrect!' . TextFormat::WHITE);
			    Terminal::write(Loader::PREFIX . TextFormat::GREEN . 'Please type the provider type (int): ' . TextFormat::WHITE);
		    }
    		$get = trim(fgets(STDIN));
	    }while(!(is_numeric($get) && isset($providers[intval($get)])));

    	$this->select = $providers[(int) $get];
    }

	/**
	 * @return null|StatsProvider
	 */
	public function getSelect() : ?StatsProvider{
		return $this->select;
	}
}