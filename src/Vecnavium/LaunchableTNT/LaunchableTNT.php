<?php

namespace Vecnavium\LaunchableTNT;

use pocketmine\entity\Entity;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use Vecnavium\LaunchableTNT\Listener;

class LaunchableTNT extends PluginBase
{

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new TaskHandler($this), $this);
    }

}