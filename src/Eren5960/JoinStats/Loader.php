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
 
use Eren5960\JoinStats\command\JoinStatsCommand;
use Eren5960\JoinStats\provider\JsonProvider;
use Eren5960\JoinStats\provider\ProviderManager;
use Eren5960\JoinStats\provider\StatsProvider;
use Eren5960\JoinStats\provider\YamlProvider;
use Eren5960\JoinStats\utils\SingletonTrait;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase implements Listener{
	public const PREFIX = TextFormat::GRAY . '[' . TextFormat::GOLD . 'Join' . TextFormat::WHITE . 'Stats' . TextFormat::GRAY . ']' . TextFormat::RESET . ' ';

	use SingletonTrait;
	/** @var StatsProvider */
	private $provider = null;

	public function onLoad(){
		self::setInstance($this);
		ProviderManager::add(new YamlProvider(), ["Yaml"]);
		ProviderManager::add(new JsonProvider(), ["Json"]);
	}

	public function onEnable(){
		$this->reloadConfig();
		$this->getServer()->getCommandMap()->register('joinstats', new JoinStatsCommand('joinstats', 'See stats', null, ['js']));
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		if($this->provider === null){
			$provider = $this->getConfig()->get('provider-class', null);

			if($provider === null){
				$this->setup();
			}elseif(ProviderManager::get($provider) === null || $this->getConfig()->get('provider-file', false) === false){
				$this->getLogger()->critical('Config data is incorrect!');
				$this->setup();
			}else{
				$this->setProvider(ProviderManager::get($provider), $this->getConfig()->get('provider-file'));
			}
		}

		if($this->getConfig()->exists('timezone') && $this->getConfig()->get('timezone') !== false){
			date_default_timezone_set($timezone = $this->getConfig()->get('timezone'));
			$this->getLogger()->info(TextFormat::GRAY . 'Timezone setted to ' . TextFormat::GOLD . $timezone);
		}else{
			$this->getLogger()->critical('Timezone value is not set from config. If you don\'t set this up, the wrong times and dates are required.');
			$this->getLogger()->info(TextFormat::GRAY . 'Timezone is set to "' . TextFormat::GOLD . 'Europe/Istanbul' . TextFormat::GRAY . '" for this run.');
			date_default_timezone_set('Europe/Istanbul');

			$this->getConfig()->set('timezone', false);
			$this->getConfig()->save();
		}
	}

	private function setup(): void{
		$selector = new SetupPlugin();
		$this->getConfig()->set('provider-class', get_class($selector->getSelect()));
		$this->getConfig()->set('provider-file', $file = $this->getDataFolder() . 'provider_config.' . $selector->getSelect()->getSuffix());
		$this->setProvider($selector->getSelect(), $file);
		$this->getConfig()->save();
	}

	public function setProvider(StatsProvider $provider, string $file, bool $save = false){
		if($this->provider !== null){
			if($save){
				$this->provider->save();
			}
			$this->provider->destroy();
		}

		$this->provider = $provider;
		$provider->setup($file);
		$this->getLogger()->info(self::PREFIX . TextFormat::GRAY . 'Provider setted to ' . TextFormat::GREEN . $provider->getName());
	}

	public function getProvider(): ?StatsProvider{
		return $this->provider;
	}

	public function onDisable(){
		if($this->provider !== null){
			$this->provider->save();
			$this->provider->destroy();
			$this->provider = null;
		}
	}

	public function onPlayerJoin(PlayerJoinEvent $event): void{
		$this->provider->addCount();
	}
}