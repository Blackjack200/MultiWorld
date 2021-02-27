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

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

class VoidGenerator extends Generator {
	public function __construct(int $seed, array $options = []) {
		$this->options = $options;
		parent::__construct($seed, $options);
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
	
	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void {
		$chunk = $world->getChunk($chunkX, $chunkZ);
		for ($x = 0; $x < 16; ++$x) {
			for ($z = 0; $z < 16; ++$z) {
				for ($y = 0; $y < 168; ++$y) {
					$spawn = $this->getSpawn();
					if ($spawn->getX() >> 4 === $chunkX && $spawn->getZ() >> 4 === $chunkZ) {
						$chunk->setFullBlock(0, 64, 0, VanillaBlocks::GRASS()->getFullId());
					} else {
						$chunk->setFullBlock($x, $y, $z, 0);
					}
				}
			}
		}
	}
	
	public function getSpawn() : Vector3 {
		return new Vector3(256, 65, 256);
	}
	
	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void {
	
	}
}