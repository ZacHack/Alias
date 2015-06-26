<?php

namespace Alias;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\Iplayer;
use pocketmine\OfflinePlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class MainClass extends PluginBase implements Listener{
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(!is_dir($this->getDataFolder()."players/lastip")){
			@mkdir($this->getDataFolder()."players/lastip", 0777, true);
		}
		if(!is_dir($this->getDataFolder()."players/ip")){
			@mkdir($this->getDataFolder()."players/ip", 0777, true);
		}
    }
	public function onDisable(){}
	public function onJoin(PlayerJoinEvent $event){
		$name = $event->getPlayer()->getDisplayName();
		$ip = $event->getPlayer()->getAddress();
		if(is_file($this->getDataFolder()."players/lastip/".$name[0]."/".$name.".yml")){
			unlink($this->getDataFolder()."players/lastip/".$name[0]."/".$name.".yml");
			$name = $event->getPlayer()->getDisplayName();
			$ip = $event->getPlayer()->getAddress();
			@mkdir($this->getDataFolder()."players/lastip/".$name[0]."", 0777, true);
			$lastip = new Config($this->getDataFolder()."players/lastip/".$name[0]."/".$name.".yml", CONFIG::YAML, array(
				"lastip" => "".$ip."",
			));
			$lastip->save();
			@mkdir($this->getDataFolder()."players/ip/".$ip[0]."", 0777, true);
			$ipfile = new Config($this->getDataFolder()."players/ip/".$ip[0]."/".$ip.".txt", CONFIG::ENUM);
			$ipfile->set($name);
			$ipfile->save();
		}else{
			$name = $event->getPlayer()->getDisplayName();
			$ip = $event->getPlayer()->getAddress();
			@mkdir($this->getDataFolder()."players/lastip/".$name[0]."", 0777, true);
			$lastip = new Config($this->getDataFolder()."players/lastip/".$name[0]."/".$name.".yml", CONFIG::YAML, array(
				"lastip" => "".$ip."",
		));
			$lastip->save();
			@mkdir($this->getDataFolder()."players/ip/".$ip[0]."", 0777, true);
			$ipfile = new Config($this->getDataFolder()."players/ip/".$ip[0]."/".$ip.".txt", CONFIG::ENUM);
			$ipfile->set($name);
			$ipfile->save();
		}
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		switch($command->getName()){
			case "alias":
				if(!isset($args[0])){
					$sender->sendMessage(TextFormat::RED."Usage: ".$command->getUsage()."");
					return true;
				}
				$name = strtolower($args[0]);
				$player = $this->getServer()->getPlayer($name);
				if($player instanceOf Player){
					$ip = $player->getPlayer()->getAddress();
					$file = new Config($this->getDataFolder()."players/ip/".$ip[0]."/".$ip.".txt");
					$names = $file->getAll(true);
					$names = implode(', ', $names);
					$sender->sendMessage(TextFormat::GREEN."[Alias] Showing alias of ".$name."...");
					$sender->sendMessage(TextFormat::BLUE."[Alias] ".$names."");
					return true;
				}else{
					if(!is_file($this->getDataFolder()."players/lastip/".$name[0]."/".$name.".yml")){
						$sender->sendMessage(TextFormat::YELLOW."[Alias] Error: Player does not exist!");
						return true;
					}else{
						$lastip = new Config($this->getDataFolder()."players/lastip/".$name[0]."/".$name.".yml");
						$ip = $lastip->get("lastip");
						$file = new Config($this->getDataFolder()."players/ip/".$ip[0]."/".$ip.".txt");
						$names = $file->getAll(true);
						if($names == null){
							$sender->sendMessage(TextFormat::YELLOW."[Alias] Error: Player does not exist!");
							break;
						}else{
							$names = implode(', ', $names);
							$sender->sendMessage(TextFormat::GREEN."[Alias] Showing alias of ".$name."...");
							$sender->sendMessage(TextFormat::BLUE."[Alias] ".$names."");
							return true;
						}
					}
				}
				return true;
		}
	}
}
/* 100 lines :O
:D
*/
