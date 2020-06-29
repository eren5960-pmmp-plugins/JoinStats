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
 * @link   https://github.com/Eren5960
 * @date   12 Mayıs 2020
 */
declare(strict_types=1);

namespace Eren5960\JoinStats\utils;

trait ProviderCountTrait{
	public function addCount(): void{
		$date = date('d-m-Y');
		$this->getConfig()->setNested($date . '.' . 'total', $this->getConfig()->getNested($date . '.' . 'total', 0) + 1);
		$this->getConfig()->setNested($date . '.' . date('H'), $this->getConfig()->getNested($date . '.' . date('H'), 0) + 1);
	}

	public function getCount(int $date, bool $total): int{
		return $this->getConfig()->getNested(date('d-m-Y', $date) . '.' . ($total ? 'total' : date('H', $date)), 0);
	}
}