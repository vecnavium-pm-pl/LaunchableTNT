<?php

namespace Vecnavium\LaunchableTNT;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\event\Listener;
use pocketmine\entity\object\FallingBlock;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\world\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\world\World;
use pocketmine\entity\Location;

class LaunchableTNT extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->checkUpdate();
    }

    public function checkUpdate(bool $isRetry = false): void {

        $this->getServer()->getAsyncPool()->submitTask(new CheckUpdateTask($this, $isRetry));
    }


    public function onExplode(EntityExplodeEvent $event): void
    {
        foreach ($event->getBlockList() as $block) {
            if ($block instanceof Solid) {
                $nbt = $this->createBaseNBT($block->getPosition());
                $nbt->setInt("TileID", $block->getId());
                $nbt->setByte("Data", $block->getDamage());
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player->getInventory()->getItemInHand()->getId() === ItemIds::TNT) {
			if($event->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;
            $entity = $this->createEntity($player->getLocation(), $this->createBaseNBT($player->getPosition()));
            $entity->setMotion($player->getDirectionVector()->normalize()->multiply(2));
            $entity->spawnToAll();
        }
    }

    public function createEntity(Location $loca, CompoundTag $nbt) : ?Entity{
            return new PrimedTNT($loca, $nbt);
    }

    public function createBaseNBT(Position $pos, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0): CompoundTag {
        return CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($pos->x),
                new DoubleTag($pos->y),
                new DoubleTag($pos->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($motion !== null ? $motion->x : 0.0),
                new DoubleTag($motion !== null ? $motion->y : 0.0),
                new DoubleTag($motion !== null ? $motion->z : 0.0)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($yaw),
                new FloatTag($pitch)
            ]));
    }
}