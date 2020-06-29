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

namespace Eren5960\JoinStats\provider;

interface StatsProvider{
	public function setup(string $file): void;

	public function getName(): string;

	public function getSuffix(): string;

	public function save(): void;

	public function destroy(): void;

	/** Add to the total number of players and the number of hours */
	public function addCount(): void;

	/** get number of players */
	public function getCount(int $date, bool $total): int;

	public function getData(): array;
}