<?php 
declare(strict_types=1);

namespace OnlyProxyConnect\Zeao;

use pocketmine\plugin\PluginBase;

use OnlyProxyConnect\Zeao\listener\ProxyListener;
use pocketmine\network\mcpe\protocol\ScriptCustomEventPacket;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\Binary;

class Loader extends PluginBase{
    public static $api;
    public function onEnable(): void{
        self::$api = $this;
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new ProxyListener($this), $this);
    }
    static function getAPI(): Loader{ //Used for API methods.
        return self::$api;
    }
      /**
     * @param Player $player
     * @param String $server
     *
     * @return bool
     * Transfers the Player $player to the server $serverName
     */
    static function transferServer(Player $player, string $serverName): bool
    {

        $pk = new ScriptCustomEventPacket();
        $pk->eventName = "bungeecord:main";
        $pk->eventData = Binary::writeShort(strlen("Connect")) . "Connect" . Binary::writeShort(strlen($serverName)) . $serverName;
        $player->sendDataPacket($pk);
        return true;
    }
     /**
     * @param string $playerName
     * @param string $message
     * @return bool
     * Sends a message $message to the player string: $playerName
     */
    static function sendMessage(string $playerName, string $message)
    {
        $sender = Server::getInstance()->getOnlinePlayers()[array_rand(Server::getInstance()->getOnlinePlayers())];
        if ($sender != null && $sender instanceof Player) {
            $pk = new ScriptCustomEventPacket();
            $pk->eventName = "bungeecord:main";
            $pk->eventData = Binary::writeShort(strlen("Message")) . "Message" . Binary::writeShort(strlen($playerName)) . $playerName . Binary::writeShort(strlen($message)) . $message;
            $sender->sendDataPacket($pk);
            return true;
        } else {
            self::getAPI()->getLogger()->warning("Error occurred whilst sending a message to the player.");
            return false;
        }

    }
}
