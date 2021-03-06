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

namespace czechpmdevs\multiworld\api;

use czechpmdevs\multiworld\generator\void\VoidGenerator;
use pocketmine\Server;
use pocketmine\world\format\io\BaseWorldProvider;
use pocketmine\world\generator\Flat;
use pocketmine\world\generator\normal\Normal;
use pocketmine\world\World;

class WorldManagementAPI {
	
	public const GENERATOR_NORMAL = 0;
	public const GENERATOR_NORMAL_CUSTOM = 1;
	public const GENERATOR_HELL = 2;
	public const GENERATOR_ENDER = 3;
	public const GENERATOR_FLAT = 4;
	public const GENERATOR_VOID = 5;
	public const GENERATOR_SKYBLOCK = 6;
	
	public const GENERATOR_HELL_OLD = 7;
	
	public static function generateLevel(string $levelName, int $seed = 0, int $generator = WorldManagementAPI::GENERATOR_NORMAL) : bool {
		if (self::isLevelGenerated($levelName)) {
			return false;
		}
		
		$generatorClass = Normal::class;
		
		switch ($generator) {
			case self::GENERATOR_FLAT:
				$generatorClass = Flat::class;
				break;
			case self::GENERATOR_VOID:
				$generatorClass = VoidGenerator::class;
				break;
		}
		
		return Server::getInstance()->getWorldManager()->generateWorld($levelName, $seed, $generatorClass);
	}
	
	public static function isLevelGenerated(string $levelName) : bool {
		return Server::getInstance()->getWorldManager()->isWorldGenerated($levelName) && !in_array($levelName, [".", ".."]);
	}
	
	public static function removeLevel(string $name) : int {
		if (self::isLevelLoaded($name)) {
			$level = self::getLevel($name);
			
			if (count($level->getPlayers()) > 0) {
				foreach ($level->getPlayers() as $player) {
					$player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
				}
			}
			
			$level->getServer()->getWorldManager()->unloadWorld($level);
		}
		
		return self::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . $name);
	}
	
	public static function isLevelLoaded(string $levelName) : bool {
		return Server::getInstance()->getWorldManager()->isWorldLoaded($levelName);
	}
	
	public static function getLevel(string $name) : ?World {
		return Server::getInstance()->getWorldManager()->getWorldByName($name);
	}
	
	private static function removeDir(string $dirPath) : int {
		$files = 1;
		if (basename($dirPath) == "." || basename($dirPath) == "..") {
			return 0;
		}
		foreach (scandir($dirPath) as $item) {
			if ($item != "." || $item != "..") {
				if (is_dir($dirPath . DIRECTORY_SEPARATOR . $item)) {
					$files += self::removeDir($dirPath . DIRECTORY_SEPARATOR . $item);
				}
				if (is_file($dirPath . DIRECTORY_SEPARATOR . $item)) {
					$files += self::removeFile($dirPath . DIRECTORY_SEPARATOR . $item);
				}
			}
			
		}
		rmdir($dirPath);
		return $files;
	}
	
	private static function removeFile(string $path) : int {
		unlink($path);
		return 1;
	}
	
	public static function renameLevel(string $oldName, string $newName) {
		if (self::isLevelLoaded($oldName)) self::unloadLevel(self::getLevel($oldName));
		
		$from = Server::getInstance()->getDataPath() . "/worlds/" . $oldName;
		$to = Server::getInstance()->getDataPath() . "/worlds/" . $newName;
		
		rename($from, $to);
		
		self::loadLevel($newName);
		$provider = self::getLevel($newName)->getProvider();
		
		if (!$provider instanceof BaseWorldProvider) return;
		$provider->getWorldData()->getCompoundTag()->setString("LevelName", $newName);
		$provider->getWorldData()->save();
		
		self::unloadLevel(self::getLevel($newName));
		self::loadLevel($newName); // reloading the level
	}
	
	public static function unloadLevel(World $level) : bool {
		return $level->getServer()->getWorldManager()->unloadWorld($level);
	}
	
	public static function loadLevel(string $name) : bool {
		return self::isLevelLoaded($name) ? false : Server::getInstance()->getWorldManager()->loadWorld($name);
	}
	
	public static function getAllLevels() : array {
		$levels = [];
		foreach (glob(Server::getInstance()->getDataPath() . "/worlds/*") as $world) {
			if (count(scandir($world)) >= 4) { // don't forget to .. & .
				$levels[] = basename($world);
			}
		}
		return $levels;
	}
}