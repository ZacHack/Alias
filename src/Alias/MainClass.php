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
		
		$config = new Config($this->getDataFolder()."config.yml", CONFIG::YAML, array(
			"CID/IP" => "CID",
			));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(!is_dir($this->getDataFolder()."players/lastip")){
			@mkdir($this->getDataFolder()."players/lastip", 0777, true);
		}
		if(!is_dir($this->getDataFolder()."players/ip")){
			@mkdir($this->getDataFolder()."players/ip", 0777, true);
		}
		if(!is_dir($this->getDataFolder()."players/cid")){
			@mkdir($this->getDataFolder()."players/cid", 0777, true);
		}
		if(!is_dir($this->getDataFolder()."players/lastcid")){
			@mkdir($this->getDataFolder()."players/lastcid", 0777, true);
		}
    }
	public function onDisable(){}
	public function onJoin(PlayerJoinEvent $event){
		$name = $event->getPlayer()->getDisplayName();
		$ip = $event->getPlayer()->getAddress();
		$cid = $event->getPlayer()->getClientId();
		if(is_file($this->getDataFolder()."players/lastcid/".$name[0]."/".$name.".yml")){
			unlink($this->getDataFolder()."players/lastcid/".$name[0]."/".$name.".yml");
			$name = $event->getPlayer()->getDisplayName();
			$cid = $event->getPlayer()->getClientId();
			@mkdir($this->getDataFolder()."players/lastcid/".$name[0]."", 0777, true);
			$lastcid = new Config($this->getDataFolder()."players/lastcid/".$name[0]."/".$name.".yml", CONFIG::YAML, array(
				"lastcid" => "".$cid."",
			));
			$lastcid->save();
			$cidfile = new Config($this->getDataFolder()."players/cid/".$cid.".txt", CONFIG::ENUM);
			$cidfile->set($name);
			$cidfile->save();
		}else{
			$name = $event->getPlayer()->getDisplayName();
			$cid = $event->getPlayer()->getClientId();
			@mkdir($this->getDataFolder()."players/lastcid/".$name[0]."", 0777, true);
			$lastcid = new Config($this->getDataFolder()."players/lastcid/".$name[0]."/".$name.".yml", CONFIG::YAML, array(
				"lastcid" => "".$cid."",
				));
			$lastcid->save();
			$cidfile = new Config($this->getDataFolder()."players/cid/".$cid.".txt", CONFIG::ENUM);
			$cidfile->set($name);
			$cidfile->save();
		}
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
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		switch($command->getName()){
			case "alias":
				if(!isset($args[0])){
					$sender->sendMessage(TextFormat::YELLOW."Usage: ".$command->getUsage()."");
					return true;
				}
				$config = new Config($this->getDataFolder()."config.yml", CONFIG::YAML);
				$switch = $config->get("CID/IP");
				if($switch == "CID"){
					$name = strtolower($args[0]);
					$player = $this->getServer()->getPlayer($name);
					if($player instanceOf Player){
						$cid = $player->getPlayer()->getClientId();
						$file = new Config($this->getDataFolder()."players/cid/".$cid.".txt");
						$names = $file->getAll(true);
						$names = implode(', ', $names);
						$sender->sendMessage(TextFormat::BLUE."[Alias] Showing alias of ".$name."...");
						$sender->sendMessage(TextFormat::GREEN."[Alias] ".$names."");
						return true;
					}else{
						if(!is_file($this->getDataFolder()."players/lastcid/".$name[0]."/".$name.".yml")){
							$sender->sendMessage(TextFormat::YELLOW."[Alias] Error: Player does not have any client ID records!");
							return true;
						}else{
							$lastcid = new Config($this->getDataFolder()."players/lastcid/".$name[0]."/".$name.".yml");
							$cid = $lastcid->get("lastcid");
							$file = new Config($this->getDataFolder()."players/cid/".$cid.".txt");
							$names = $file->getAll(true);
							if($names == null){
								$sender->sendMessage(TextFormat::YELLOW."[Alias] Error: Player does not have any client ID records!");
								return true;
							}else{
								$names = implode(', ', $names);
								$sender->sendMessage(TextFormat::BLUE."[Alias] Showing alias of ".$name."...");
								$sender->sendMessage(TextFormat::GREEN."[Alias] ".$names."");
								return true;
							}
						}
					}
				}elseif($switch == "IP"){
					$name = strtolower($args[0]);
					$player = $this->getServer()->getPlayer($name);
					if($player instanceOf Player){
						$ip = $player->getPlayer()->getAddress();
						$file = new Config($this->getDataFolder()."players/ip/".$ip[0]."/".$ip.".txt");
						$names = $file->getAll(true);
						$names = implode(', ', $names);
						$sender->sendMessage(TextFormat::BLUE."[Alias] Showing alias of ".$name."...");
						$sender->sendMessage(TextFormat::GREEN."[Alias] ".$names."");
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
								$sender->sendMessage(TextFormat::YELLOW."[Alias] Error: Player does not have any IP records!");
								return true;
							}else{
							$names = implode(', ', $names);
							$sender->sendMessage(TextFormat::GREEN."[Alias] Showing alias of ".$name."...");
							$sender->sendMessage(TextFormat::BLUE."[Alias] ".$names."");
							return true;
							}
						}
					}
				}else{
					$sender->sendMessage(TextFormat::YELLOW."[Alias] Error! Please make sure your config is set properly!");
					return true;
				}
				return true;
			case "setalias":
				if(!isset($args[0])){
					$sender->sendMessage(TextFormat::YELLOW."Usage: ".$command->getUsage()."");
					return true;
				}
				$args[0] = strtoupper($args[0]);
				$config = new Config($this->getDataFolder()."config.yml", CONFIG::YAML);
				unlink($this->getDataFolder()."config.yml");
				$config = new Config($this->getDataFolder()."config.yml", CONFIG::YAML, array(
				"CID/IP" => "".$args[0]."",
				));
				$sender->sendMessage(TextFormat::GREEN."[Alias] You have changed the setting to use ".$args[0]."");
				return true;
			case "aliasip":
				if(!isset($args[0])){
					$sender->sendMessage(TextFormat::YELLOW."Usage: ".$command0>getUsage()."");
					return true;
				}
				$name = strtolower($args[0]);
				$player = $this->getServer()->getPlayer($name);
				if($player instanceOf Player){
					$ip = $player->getPlayer()->getAddress();
					$file = new Config($this->getDataFolder()."players/ip/".$ip[0]."/".$ip.".txt");
					$names = $file->getAll(true);
					$names = implode(', ', $names);
					$sender->sendMessage(TextFormat::BLUE."[Alias] Showing alias of ".$name."...");
					$sender->sendMessage(TextFormat::GREEN."[Alias] ".$names."");
					return true;
				}else{
					if(!is_file($this->getDataFolder()."players/lastip/".$name[0]."/".$name.".yml")){
						$sender->sendMessage(TextFormat::YELLOW."[Alias] Error: Player does not have any IP records!");
						return true;
					}else{
						$lastip = new Config($this->getDataFolder()."players/lastip/".$name[0]."/".$name.".yml");
						$ip = $lastip->get("lastip");
						$file = new Config($this->getDataFolder()."players/ip/".$ip[0]."/".$ip.".txt");
						$names = $file->getAll(true);
						if($names == null){
							$sender->sendMessage(TextFormat::YELLOW."[Alias] Error: Player does not have any IP records!");
							return true;
						}else{
						$names = implode(', ', $names);
						$sender->sendMessage(TextFormat::GREEN."[Alias] Showing alias of ".$name."...");
						$sender->sendMessage(TextFormat::BLUE."[Alias] ".$names."");
						return true;
						}
					}
				}
				return true;
			case "aliascid":
				if(!isset($args[0])){
					$sender->sendMessage(TextFormat::YELLOW."Usage: ".$command0>getUsage()."");
					return true;
				}
				$name = strtolower($args[0]);
				$player = $this->getServer()->getPlayer($name);
				if($player instanceOf Player){
					$cid = $player->getPlayer()->getClientId();
					$file = new Config($this->getDataFolder()."players/cid/".$cid.".txt");
					$names = $file->getAll(true);
					$names = implode(', ', $names);
					$sender->sendMessage(TextFormat::BLUE."[Alias] Showing alias of ".$name."...");
					$sender->sendMessage(TextFormat::GREEN."[Alias] ".$names."");
					return true;
				}else{
					if(!is_file($this->getDataFolder()."players/lastcid/".$name[0]."/".$name.".yml")){
						$sender->sendMessage(TextFormat::YELLOW."[Alias] Error: Player does not have any client ID records!");
						return true;
					}else{
						$lastcid = new Config($this->getDataFolder()."players/lastcid/".$name[0]."/".$name.".yml");
						$cid = $lastcid->get("lastcid");
						$file = new Config($this->getDataFolder()."players/cid/".$cid.".txt");
						$names = $file->getAll(true);
						if($names == null){
							$sender->sendMessage(TextFormat::YELLOW."[Alias] Error: Player does not have any client ID records!");
							return true;
						}else{
							$names = implode(', ', $names);
							$sender->sendMessage(TextFormat::BLUE."[Alias] Showing alias of ".$name."...");
							$sender->sendMessage(TextFormat::GREEN."[Alias] ".$names."");
							return true;
						}
					}
				}
				return true;
			case "checkalias":
				$config = new Config($this->getDataFolder()."config.yml");
				$setting = $config->get("CID/IP");
				$sender->sendMessage(TextFormat::GREEN."[Alias] Alias is set to ".$setting."");
				return true;
		}
	}
}
