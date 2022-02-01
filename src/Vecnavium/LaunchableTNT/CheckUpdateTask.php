<?php

declare(strict_types=1);

namespace Vecnavium\LaunchableTNT;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use function is_array;
use function json_decode;
use function version_compare;
use function vsprintf;

class CheckUpdateTask extends AsyncTask{

    private const POGGIT_URL = "https://poggit.pmmp.io/releases.json?name=";
    private string $version;
    private string $name;
    private bool $retry;

    public function __construct(LaunchableTNT $plugin, bool $retry) {
        $this->retry = $retry;
        $this->name = $plugin->getDescription()->getName();
        $this->version = $plugin->getDescription()->getVersion();
        #$this->storeLocal([$plugin]);
    }

    public function onRun(): void {
		$poggitData = Internet::getURL(self::POGGIT_URL . $this->name, 10, [], $err);
        $version = $this->version;
        $date = "";
        $updateUrl = "";
        if($poggitData !== null){
            $poggit = json_decode($poggitData->getBody(), true);
            foreach($poggit as $pog){
                if(version_compare($version, $pog["version"], ">=")){
                    continue;
                }
				$date = $pog["last_state_change_date"];
                $version = $pog["version"];
				$updateUrl = $pog["html_url"];
            }
        }

        $this->setResult([$version, $date, $updateUrl]);
    }

    public function onCompletion(): void {
        /** @var LaunchableTNT $plugin */
        $plugin = Server::getInstance()->getPluginManager()->getPlugin($this->name);
        if($plugin === null){
            return;
        }

        if ($this->getResult() === null) {
            $plugin->getLogger()->debug("Update Check has failed!");

            if (!$this->retry) {
                $plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($plugin): void {
                    $plugin->checkUpdate(true);
                }), 30);
            }

            return;
        }

        [$latestVersion, $updateDateUnix, $updateUrl] = $this->getResult();

        if ($latestVersion != "" || $updateDateUnix != null || $updateUrl !== "") {
            $updateDate = date("j F Y", (int)$updateDateUnix);

            if ($this->version !== $latestVersion) {
                $plugin->getLogger()->notice("LaunchableTNT v$latestVersion has been released on $updateDate. Download the new update at $updateUrl");
                $plugin->cachedUpdate = [$latestVersion, $updateDate, $updateUrl];
            }
        }
    }
}