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

namespace czechpmdevs\multiworld\generator\void;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class VoidGenerator extends Generator {
	protected ChunkManager $level;
	
	protected Random $random;
	
	private array $options;
	
	public function __construct(array $settings = []) {
		$this->options = $settings;
	}
	
	public function getSettings() : array {
		return [];
	}
	
	public function getName() : string {
		return "void";
	}
	
	public function init(ChunkManager $level, Random $random) : void {
		$this->level = $level;
		$this->random = $random;
	}
	
	public function generateChunk(int $chunkX, int $chunkZ) : void {
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		for ($x = 0; $x < 16; ++$x) {
			for ($z = 0; $z < 16; ++$z) {
				for ($y = 0; $y < 168; ++$y) {
					$spawn = $this->getSpawn();
					if ($spawn->getX() >> 4 === $chunkX && $spawn->getZ() >> 4 === $chunkZ) {
						$chunk->setBlockId(0, 64, 0, Block::GRASS);
					} else {
						$chunk->setBlockId($x, $y, $z, Block::AIR);
					}
				}
			}
		}
		$chunk->setGenerated(true);
	}
	
	public function getSpawn() : Vector3 {
		return new Vector3(256, 65, 256);
	}
	
	public function populateChunk(int $chunkX, int $chunkZ) : void {
	}
}