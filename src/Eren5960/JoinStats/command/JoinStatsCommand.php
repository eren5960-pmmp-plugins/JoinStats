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
 
namespace Eren5960\JoinStats\command;

use Eren5960\JoinStats\Loader;
use jojoe77777\FormAPI\FormAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use function date;
use function mktime;
use function count;
use function array_map;
use function array_shift;
use function class_exists;
use function strtotime;

class JoinStatsCommand extends Command{
	private static $sub_commands = [];

	public function __construct(string $name, string $description = "", ?string $usageMessage = null, array $aliases = []){
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->setPermission('join.stats.command');
		self::initDefaultCommands();
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		$sub = array_shift($args);
		if($sender instanceof Player){
			if($sub === null){
				if(class_exists(FormAPI::class)){
					StatsUI::main($sender);
					return;
				}else{
					$sender->sendMessage(Loader::PREFIX . TextFormat::RED . 'You must have "FormAPI" to use this feature.');
				}
			}
		}

		if($sub === null || !isset(self::$sub_commands[$sub])){
			$this->sendHelp($sender);
			return;
		}

		$this->action($sub, $sender, $args);
	}

	public function action(string $sub, CommandSender $sender, array $args): void{
		(self::$sub_commands[$sub])($sender, $args);
	}

	public function sendHelp(CommandSender $sender): void{
		$sender->sendMessage(Loader::PREFIX . TextFormat::GREEN . 'Help for: ' . TextFormat::WHITE . '/joinstats help');
	}

	public static function add(string $name, \Closure $closure): void{
		Utils::validateCallableSignature(function(CommandSender $sender, array $args) : void{}, $closure);
		self::$sub_commands[$name] = $closure;
	}

	public static function initDefaultCommands(): void{
		if(!isset(self::$sub_commands['help'])){
			self::add('help', function(CommandSender $sender, array $args): void{
				$gray = TextFormat::GRAY;
				$sender->sendMessage(Loader::PREFIX . 'Available Commands: ' . TextFormat::EOL .
					$gray . '/joinstats ' . TextFormat::WHITE . 'day' . TextFormat::BLACK . ' => ' . $gray .
					'Find out how many people entered the server ' . TextFormat::AQUA . 'today' . TextFormat::EOL .

					$gray . '/joinstats ' . TextFormat::WHITE . 'hour' . TextFormat::BLACK . ' => ' . $gray .
					'Find out how many people entered the server this ' . TextFormat::AQUA . 'hour' . TextFormat::EOL .

					$gray . '/joinstats ' . TextFormat::WHITE . 'week' . TextFormat::BLACK . ' => ' . $gray .
					'Find out how many people entered the server last 1 ' . TextFormat::AQUA . 'week' . TextFormat::EOL .

					$gray . '/joinstats ' . TextFormat::WHITE . 'month' . TextFormat::BLACK . ' => ' . $gray .
					'Find out how many people entered the server last 1 ' . TextFormat::AQUA . 'month' . TextFormat::EOL .

					$gray . '/joinstats ' . TextFormat::WHITE . 'total' . TextFormat::BLACK . ' => ' . $gray .
					'Find out how many people entered the server ' . TextFormat::AQUA . 'total' . TextFormat::EOL .

					$gray . '/joinstats ' . TextFormat::WHITE . 'date ' . $gray . '<day> <month> <year> <hour: optional>' . TextFormat::BLACK . ' : ' . $gray .
					'Learn user logins with ' . TextFormat::AQUA . 'date.' . TextFormat::EOL .
					Loader::PREFIX . 'The plugin coded by ♥ ' . TextFormat::DARK_AQUA . 'Eren5960 ' . $gray . '(' . TextFormat::AQUA . 'github.com/eren5960' . $gray . ')' . PHP_EOL .
					Loader::PREFIX . TextFormat::GREEN . 'Don\'t forget to give stars if you like it.'
				);
			});
			self::add('day', function(CommandSender $sender, array $args): void{
				$provider = Loader::getInstance()->getProvider();
				$sender->sendMessage(Loader::PREFIX . 'Number of players logged on to the server today: ' .
					TextFormat::AQUA . $provider->getCount(strtotime(date('d-m-Y')), true));
			});
			self::add('hour', function(CommandSender $sender, array $args): void{
				$provider = Loader::getInstance()->getProvider();
				$sender->sendMessage(Loader::PREFIX . 'Number of players logged on to the server at this hour: '.
					TextFormat::AQUA . $provider->getCount(
					mktime((int) date('H'), 0, 0, (int) date('m'), (int) date('d'), (int) date('Y')), false));
			});
			self::add('week', function(CommandSender $sender, array $args): void{
				$provider = Loader::getInstance()->getProvider();
				$count = 0;
				$month = (int) date('m');
				$year = (int) date('Y');

				for($i=0;$i<7;$i++){
					$count += $provider->getCount(mktime(0, 0, 0, $month, ((int) date('d')) - $i, $year), true);
				}
				$sender->sendMessage(Loader::PREFIX . 'Number of players logged on to the server last 1 week: '.
					TextFormat::AQUA . $count);
			});
			self::add('month', function(CommandSender $sender, array $args): void{
				$provider = Loader::getInstance()->getProvider();
				$count = 0;
				$month = (int) date('m');
				$year = (int) date('Y');
				for($i=0;$i<30;$i++){
					$count += $provider->getCount(mktime(0, 0, 0, $month, ((int) date('d')) - $i, $year), true);
				}
				$sender->sendMessage(Loader::PREFIX . 'Number of players logged on to the server last 1 month: ' .
					TextFormat::AQUA .  $count);
			});
			self::add('year', function(CommandSender $sender, array $args): void{
				$provider = Loader::getInstance()->getProvider();
				$count = 0;
				$year = (int) date('Y');
				for($y=0;$y<12;$y++){
					for($i=0;$i<30;$i++){
						$count += $provider->getCount(mktime(0, 0, 0, ((int) date('m')) - $y, ((int) date('d')) - $i, $year), true);
					}
				}
				$sender->sendMessage(Loader::PREFIX . 'Number of players logged on to the server last 1 year: ' .
					TextFormat::AQUA .  $count);
			});
			self::add('total', function(CommandSender $sender, array $args): void{
				$provider = Loader::getInstance()->getProvider();
				$count = 0;
				$year = (int) date('Y');
				for($y=0;$y<24;$y++){ // 2 years
					for($i=0;$i<30;$i++){
						$count += $provider->getCount(mktime(0, 0, 0, ((int) date('m')) - $y, ((int) date('d')) - $i, $year), true);
					}
				}
				$sender->sendMessage(Loader::PREFIX . 'Number of players logged on to the server total: ' .
					TextFormat::AQUA . $count);
			});
			self::add('date', function(CommandSender $sender, array $args): void{
				$provider = Loader::getInstance()->getProvider();
				$data = array_map('\intval', $args);
				$count_data = count($data);
				$total = $count_data === 3;
				if($count_data < 3 || $count_data > 4){
					$sender->sendMessage(Loader::PREFIX . TextFormat::RED . 'Invalid string given');
					return;
				}
				$sender->sendMessage(Loader::PREFIX . 'Number of players logged on to the server: ' .
					TextFormat::AQUA. $provider->getCount(mktime($total ? 0 : $data[3], 0, 0, $data[1], $data[0], $data[2]), $total));
			});
		}
	}
}