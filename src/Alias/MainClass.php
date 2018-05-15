<?php

namespace Alias;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class MainClass extends PluginBase implements Listener {
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$simpleauth = $this->getServer()->getPluginManager()->getPlugin("SimpleAuth");
		if($simpleauth == null) {
			$this->getLogger()->info(TextFormat::YELLOW . "You do not have SimpleAuth");
			$this->getLogger()->info(TextFormat::YELLOW . "You can only get a players alias if he/she is online!");
		}
	}

	public function onDisable() {
	}

	public function onJoin(PlayerJoinEvent $event) {
		if(!is_dir($this->getDataFolder() . "players/")) {
			@mkdir($this->getDataFolder() . "players/", 0777, true);
		}
		$name = $event->getPlayer()->getDisplayName();
		$ip = $event->getPlayer()->getAddress();
		$file = new Config($this->getDataFolder() . "players/" . $ip . ".txt", CONFIG::ENUM);
		$file->set($name);
		$file->save();
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		switch($command->getName()) {
			case "alias":
				if(!isset($args[0])) {
					$sender->sendmessage(TextFormat::RED . "Usage: " . $command->getUsage() . "");
					return true;
				}
				$name = strtolower($args[0]);
				$player = $this->getServer()->getPlayer($name);
				if($player instanceOf Player) {
					$ip = $player->getPlayer()->getAddress();
					$file = new Config($this->getDataFolder() . "players/" . $ip . ".txt");
					$names = $file->getAll(true);
					$names = implode(', ', $names);
					$sender->sendMessage(TextFormat::GREEN . "[Alias] Showing players who joined from the same IP as " . $name . "...");
					$sender->sendMessage(TextFormat::AQUA . $names);
					return true;
					break;
				} else {
					$simpleauth = $this->getServer()->getPluginManager()->getPlugin("SimpleAuth");
					if($simpleauth !== null) {
						$saconfig = $simpleauth->getDataProvider()->getPlayerData($name);
						if($saconfig !== null && isset($saconfig['ip']) && strlen($saconfig['ip']) > 0) {
							$lastip = $saconfig['ip'];
							$file = new Config($this->getDataFolder() . "players/" . $lastip . ".txt");
							$names = $file->getAll(true);
							$names = implode(', ', $names);
							$sender->sendMessage(TextFormat::GREEN . "[Alias] Showing players who joined from the same IP as " . $name . "...");
							$sender->sendMessage(TextFormat::AQUA . $names . "");
							return true;
						} else {
							$sender->sendMessage(TextFormat::RED . "Player not found");
						}
					} else {
						$sender->sendMessage(TextFormat::RED . "SimpleAuth is not enabled, the player must be online");
					}
				}
				return true;
		}
	}
}