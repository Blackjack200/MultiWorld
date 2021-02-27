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

use czechpmdevs\multiworld\command\MultiWorldCommand;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LoginPacket;

class EventListener implements Listener {
	public MultiWorld $plugin;
	
	private MultiWorldCommand $mwCommand;
	
	/** @var Item[][][] $inventories */
	private array $inventories = [];
	
	public function __construct(MultiWorld $plugin, MultiWorldCommand $mwCommand) {
		$this->plugin = $plugin;
		$this->mwCommand = $mwCommand;
	}
	
	public function onDataPacketReceive(DataPacketReceiveEvent $event) {
		$packet = $event->getPacket();
		if ($packet instanceof LoginPacket) {
			LanguageManager::$players[$packet->username] = $packet->locale;
		}
	}
}