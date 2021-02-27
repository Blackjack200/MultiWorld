<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2020  CzechPMDevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace czechpmdevs\multiworld;

use czechpmdevs\multiworld\api\FileBrowsingApi;
use czechpmdevs\multiworld\api\WorldManagementAPI;
use czechpmdevs\multiworld\command\GameruleCommand;
use czechpmdevs\multiworld\command\MultiWorldCommand;
use czechpmdevs\multiworld\generator\void\VoidGenerator;
use czechpmdevs\multiworld\util\ConfigManager;
use czechpmdevs\multiworld\util\FormManager;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\plugin\PluginBase;

class MultiWorld extends PluginBase {
	private static MultiWorld $instance;
	
	public LanguageManager $languageManager;
	
	public ConfigManager $configManager;
	
	public FormManager $formManager;
	
	/** @var Command[] $commands */
	public array $commands = [];
	
	public static function getInstance() : MultiWorld {
		return self::$instance;
	}
	
	public static function getPrefix() : string {
		return ConfigManager::getPrefix();
	}
	
	public function onLoad() {
		$start = (bool) !(self::$instance instanceof $this);
		self::$instance = $this;
		
		if ($start) {
			$generators = [
				"void" => VoidGenerator::class,
			];
			
			foreach ($generators as $name => $class) {
				GeneratorManager::addGenerator($class, $name, true);
			}
		}
	}
	
	public function onEnable() {
		$this->configManager = new ConfigManager($this);
		$this->languageManager = new LanguageManager($this);
		$this->formManager = new FormManager($this);
		
		$this->commands = [
			"multiworld" => $cmd = new MultiWorldCommand(),
			"gamerule" => new GameruleCommand()
		];
		
		foreach ($this->commands as $command) {
			$this->getServer()->getCommandMap()->register("MultiWorld", $command);
		}
		
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $cmd), $this);
		$this->test();
	}
	
	private function test() {
		if (WorldManagementAPI::isLevelGenerated("Test")) {
			WorldManagementAPI::removeLevel("Test");
		}
		WorldManagementAPI::generateLevel("Test", rand(0, 100), WorldManagementAPI::GENERATOR_NORMAL_CUSTOM);
		
		foreach (FileBrowsingApi::getAllSubdirectories($this->getServer()->getDataPath() . "/plugins/MultiWorld/resources/") as $dir) {
			@mkdir($this->getDataFolder() . FileBrowsingApi::removePathFromRoot($dir, "resources"));
		}
		
		foreach ($this->getResources() as $resource) {
			$this->saveResource($resource->getFilename());
		}
	}
}
