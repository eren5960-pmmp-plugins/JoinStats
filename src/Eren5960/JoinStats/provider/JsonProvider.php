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

use Eren5960\JoinStats\utils\ProviderCountTrait;
use pocketmine\utils\Config;

class JsonProvider implements StatsProvider {
    use ProviderCountTrait;

    /** @var string */
    private $file;
    /** @var Config */
    private $config;

    public function setup(string $file): void {
        $this->file = $file;
        $this->config = new Config($file, Config::JSON);
    }

    public function getName(): string {
        return 'Json';
    }

    public function getSuffix(): string {
        return 'json';
    }

    public function save(): void {
        $this->config->save();
    }

    public function getFile(): string {
        return $this->file;
    }

    public function getConfig(): Config {
        return $this->config;
    }

    public function getData(): array {
        return $this->config->getAll();
    }

    public function destroy(): void {
        $this->file = null;
        $this->config = null;
    }
}
