<?php

namespace Vecnavium\LaunchableTNT;

use pocketmine\block\TNT;
use pocketmine\entity\Location;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\Listener;
use pocketmine\entity\object\PrimedTNT;

class LaunchableTNT extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->checkUpdate();
    }

    public function checkUpdate(bool $isRetry = false): void {

        $this->getServer()->getAsyncPool()->submitTask(new CheckUpdateTask($this->getDescription()->getName(), $this->getDescription()->getVersion()));
    }

    public function onClick(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item->getBlock() instanceof TNT) {
            if ($player->isSurvival()) {
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
            }
            $entity = new PrimedTNT(Location::fromObject($player->getPosition()->asVector3(), $player->getWorld()));
            $entity->setMotion($player->getDirectionVector()->normalize()->multiply(2));
            $entity->spawnToAll();
        }
    }
}