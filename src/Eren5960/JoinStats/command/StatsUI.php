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
use Eren5960\JoinStats\provider\StatsProvider;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_values;
use function mktime;
use function date;
use function intval;
use function floor;
use function explode;

/** TODO: Clean UP! */

class StatsUI{
    public static function main(Player $player): void{
		$data = self::getData(Loader::getInstance()->getProvider());
		$form = new SimpleForm(function (Player $player, ?int $index) use($data): void{
			if($index !== null){
				self::months($player, array_values($data)[$index], array_keys($data)[$index]);
			}
		});
		$form->setTitle('Join Stats - Years');
		//$form->setContent(); TODO: total count
		foreach($data as $year => $months){
			$form->addButton('# ' . $year . ' #');
		}
		$form->sendToPlayer($player);
    }

    public static function months(Player $player, array $months, int $year): void{
    	$total = $months['total'];
	    unset($months['total']);
	    $form = new SimpleForm(function (Player $player, ?int $index) use($months, $total, $year): void{
		    if($index !== null){
			    self::days($player, $months + ['total' => $total], array_values($months)[$index], $year, date("F", mktime(0, 0, 0, (int) array_keys($months)[$index], 0)));
		    }
	    });
	    $form->setTitle('Join Stats - Months of ' . $year);
	    $form->setContent(TextFormat::GRAY . 'Number of players logged into the server this year: ' . TextFormat::AQUA . $total);
	    foreach($months as $month => $days){
		    $form->addButton(date("F", mktime(0, 0, 0, $month, 0)));
	    }
	    $form->sendToPlayer($player);
    }

	public static function days(Player $player, array $months, array $days, int $year, string $month): void{
		$total = $days['total'];
		unset($days['total']);
		$form = new SimpleForm(function (Player $player, ?int $index) use($months, $days, $total, $year, $month): void{
			if($index === null || ($index !== 0 && $index % 7 === 0)){
				self::months($player, $months, $year);
			}else{
				if($index > 7){
					$index -= intval(floor($index / 8));
				}

				self::hours($player, $months, $days + ['total' => $total], array_values($days)[$index], $year, $month, array_keys($days)[$index]);
			}
		});
		$form->setTitle('Join Stats - Days of ' . $month);
		$form->setContent(TextFormat::GRAY . 'Number of players logged into the server this month: ' . TextFormat::AQUA . $total);

		$i = 0;
		foreach($days as $day => $hours){
			$form->addButton(date('l', $day));
			if(++$i > 6){
				$i = 0;
				$form->addButton('--------------');
			}
		}
		$form->sendToPlayer($player);
	}

	public static function hours(Player $player, array $months, array $days, array $hours, int $year, string $month, int $day): void{
		$form = new CustomForm(function (Player $player) use($months, $days, $year, $month): void{
			self::days($player, $months, $days, $year, $month);
		});
		$form->setTitle('Join Stats - ' . date('d/m/Y', $day));
		$total = 0;
		foreach($hours as $hour => $count){
			$total += $count;
			$form->addLabel(TextFormat::YELLOW . $hour . TextFormat::GRAY . ':00-' . TextFormat::YELLOW . ($hour + 1) . TextFormat::GRAY . ':00 => ' . TextFormat::GOLD . $count . ' players');
		}
		$form->addLabel(TextFormat::DARK_AQUA . 'Total player count' . TextFormat::GRAY . ' => ' . TextFormat::AQUA . $total . ' players');
		$form->sendToPlayer($player);
	}

	public static function getData(StatsProvider $provider) : array{
		$data = [];
		foreach($provider->getData() as $index => $hour_data){
			$day = (int) explode('-', $index)[0];
			$month = (int) explode('-', $index)[1];
			$year = (int) explode('-', $index)[2];
			$mkindex = mktime(0, 0, 0, $month, $day, $year);
			if(!isset($data[$year])){
				$data[$year] = ['total' => 0];
			}
			if(!isset($data[$year][$month])){
				$data[$year][$month] = ['total' => 0];
			}
			foreach($hour_data as $hour => $count){
				if($hour === 'total'){
					$data[$year][$month]['total'] += $count;
					$data[$year]['total'] += $count;
				}else{
					$data[$year][$month][$mkindex][$hour] = $count;
				}
			}
		}
		return $data;
	}
}