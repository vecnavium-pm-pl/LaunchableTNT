<?php

namespace Vecnavium\LaunchableTNT;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\Solid;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\event\Listener;
use pocketmine\entity\object\FallingBlock;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\entity\object\PrimedTNT;

class LaunchableTNT extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

    }

    public function onExplode(EntityExplodeEvent $event): void
    {
        foreach ($event->getBlockList() as $block) {
            if ($block instanceof Solid) {
                $nbt = Entity::createBaseNBT($block);
                $nbt->setInt("TileID", $block->getId());
                $nbt->setByte("Data", $block->getDamage());
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player->getInventory()->getItemInHand()->getId() === Item::TNT) {
            $entity = Entity::createEntity("PrimedTNT", $player->getLevel(), Entity::createBaseNBT($player));
            $entity->setMotion($player->getDirectionVector()->normalize()->multiply(2));
            $entity->spawnToAll();
        }
    }


}