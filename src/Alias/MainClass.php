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

class MainClass extends PluginBase implements Listener{
    //TODO get 'LastIp' from simpleauth
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onDisable(){}
    public function onJoin(PlayerJoinEvent $event){
        if(!is_dir($this->getDataFolder()."players/")){
            @mkdir($this->getDataFolder()."players/", 0777, true);
        }
        $name = $event->getPlayer()->getDisplayName();
        $ip = $event->getPlayer()->getAddress();
        $file = new Config($this->getDataFolder() . "players/".$ip.".txt", CONFIG::ENUM);
        $file->set($name);
        $file->save();
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        switch($command->getName()){
            case "alias":
                if(!isset($args[0])){
                    $sender->sendmessage(TextFormat::RED."Usage: ".$command->getUsage()."");
                    return true;
                }
                $name = strtolower($args[0]);
                $player = $this->getServer()->getPlayer($name);
                if($player instanceOf Player){
                    $ip = $player->getPlayer()->getAddress();
                    $file = new Config($this->getDataFolder() . "players/".$ip.".txt");
                    $names = $file->getAll(true);
                    $names = implode(', ', $names);
                    $sender->sendMessage(TextFormat::GREEN."[Alias] Showing alias of ".$name."...");
                    $sender->sendMessage(TextFormat::BLUE."[Alias] ".$names."");
                    return true;
                }else{
                    $sender->sendMessage(TextFormat::YELLOW."Player does not exist");
                    return true;
                }
                return true;
        }
    }
}
