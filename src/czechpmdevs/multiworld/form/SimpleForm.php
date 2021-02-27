<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\form;

use czechpmdevs\multiworld\MultiWorld;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SimpleForm implements Form {
	public const FORM_MENU = 0;
	
	public array $data = [];
	
	public int $mwId;
	
	public function __construct(string $title = "TITLE", string $content = "Content") {
		$this->data["type"] = "form";
		$this->setTitle($title);
		$this->setContent($content);
	}
	
	public function setTitle(string $text) {
		$this->data["title"] = $text;
	}
	
	public function setContent(string $text) {
		$this->data["content"] = $text;
	}
	
	public function addButton(string $text) {
		$this->data["buttons"][] = ["text" => $text];
	}
	
	public function handleResponse(Player $player, $data) : void {
		MultiWorld::getInstance()->formManager->handleFormResponse($player, $data, $this);
	}
	
	public function jsonSerialize() : array {
		return $this->data;
	}
}