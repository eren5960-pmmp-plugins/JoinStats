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
 
namespace Eren5960\JoinStats\utils;
 
trait SingletonTrait{
	/** @var null|self */
    private static $instance = null;

    public static function getInstance(): self{
    	return self::$instance;
    }

    public static function setInstance(self $instance): void{
    	self::$instance = $instance;
    }

    public static function reset(): void{
    	self::$instance = null;
    }
}