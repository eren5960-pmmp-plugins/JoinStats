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
 
namespace Eren5960\JoinStats\provider;

use function get_class;

abstract class ProviderManager{
	/** @var StatsProvider[] */
	private static $providers = [];

	/**
	 * @param StatsProvider $provider
	 * @param array         $save_names
	 * @param bool          $override
	 *
	 * @throws ProviderException
	 */
	public static function add(StatsProvider $provider, array $save_names = [], bool $override = false): void{
		if($override || !isset(self::$providers[get_class($provider)])){
			$save_names[] = get_class($provider);
			foreach($save_names as $name){
				self::$providers[$name] = $provider;
			}
		}else{
			throw new ProviderException(get_class($provider) . ' already registered!');
		}
	}

	/**
	 * @param string $name_or_class
	 *
	 * @return StatsProvider|null
	 */
	public static function get(string $name_or_class): ?StatsProvider{
		return self::$providers[$name_or_class] ?? null;
	}

	public static function all(): array{
		return self::$providers;
	}
}