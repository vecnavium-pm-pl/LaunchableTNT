<?php

namespace Vecnavium\ThrowableTNT;

use Vecnavium\ThrowableTNT\ThrowableTNT;
use pocketmine\block\Solid;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\entity\object\FallingBlock;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;

class TaskHandler implements Listener

{

    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player->getInventory()->getItemInHand()->getId() === Item::TNT) {
            $entity = Entity::createEntity("PrimedTNT", $player->getLevel(), Entity::createBaseNBT($player));
            $entity->setMotion($player->getDirectionVector()->normalize()->multiply(2));
            $entity->spawnToAll();
        }
    }


    public function onExplode(EntityExplodeEvent $event): void
    {
        foreach ($event->getBlockList() as $block) {
            if ($block instanceof Solid) {
                $nbt = Entity::createBaseNBT($block);
                $nbt->setInt("TileID", $block->getId());
                $nbt->setByte("Data", $block->getDamage());
                $entity = new FallingBlock($event->getEntity()->getLevel(), $nbt);
                $entity->setMotion(new Vector3(0, 3, 0));
                $entity->spawnToAll();
            }
        }
    }
}

