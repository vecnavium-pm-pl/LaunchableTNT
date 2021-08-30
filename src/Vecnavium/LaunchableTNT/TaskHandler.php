<?php

namespace Vecnavium\LaunchableTNT;

use Vecnavium\LaunchableTNT\LaunchableTNT;
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
use pocketmine\plugin\Plugin;

class TaskHandler implements Listener

{

    private Main $plugin;

    public function __construct(LaunchableTNT $plugin)
    {

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

}

