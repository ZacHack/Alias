<?php

/*
__Pocketmine Plugin__
name=Alias
version=1.0.0
author=ZacHack
class=alias
apiversion=9,10,11
*/

class alias implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$this->server = ServerAPI::request();
	}

	public function init(){
		$this->path = $this->api->plugin->configPath($this);
		if(!is_dir($this->path.'players/')) mkdir($this->path.'players/');
		$this->api->console->register("alias", "<username>", array($this, "cmd"));
		$this->api->addHandler("player.join", array($this, "join"));
	}

	public function join($data){
		$name = $data->iusername;
		$ip = $data->ip;
		$file = new Config($this->path.'players/'.$ip.'.txt', CONFIG_LIST);
		$file->set($name);
		$file->save();
	}

	public function cmd($cmd, $args, $issuer){
		if(!isset($args[0])){
			return '[Alias] Usage: /alias <name>';
		}
		$name = strtolower($args[0]);
		$player = $this->api->player->get($name);
		if($player === false){
			if(!file_exists(DATA_PATH.'players/'.$name.'.yml')){
				return "[Alias] Player doesn't exists";
			}
			$player = $this->api->player->getOffline($name);
			$ip = $player->get('lastIP');
		}else{
			$ip = $player->ip;
		}
		if(file_exists($this->path.'players/'.$ip.'.txt')){
			$file = new Config($this->path.'players/'.$ip.'.txt', CONFIG_LIST);
			$names = $file->getAll(true);
			$names = implode(', ', $names);
			if($issuer instanceOf player){
				$issuer->sendChat("[Alias] Showing alias's of ".$name."");
				$issuer->sendChat("/[Alias] ".$names."");
			}else{
				console("[Alias] Showing alias's of ".$name."");
				console("[Alias] ".$names."");
			}
		}else{
			return '[Alias] No names found';
		}
	}
	public function __destruct(){}
}
