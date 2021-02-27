<?php

declare(strict_types=1);

namespace czechpmdevs\multiworld\form;

use czechpmdevs\multiworld\MultiWorld;
use pocketmine\form\Form;
use pocketmine\player\Player;

class CustomForm implements Form {
	public const FORM_CREATE = 1;
	
	public array $data = [];
	
	public int $mwId;
	
	public function __construct(string $title = "") {
		$this->data["type"] = "custom_form";
		$this->data["title"] = $title;
		$this->data["content"] = [];
	}
	
	public function addInput(string $text) {
		$this->data["content"][] = ["type" => "input", "text" => $text];
	}
	
	public function addLabel(string $text) {
		$this->data["content"][] = ["type" => "label", "text" => $text];
	}
	
	public function addToggle(string $text, ?bool $default = null) {
		if ($default !== null) {
			$this->data["content"][] = ["type" => "toggle", "text" => $text, "default" => $default];
			return;
		}
		$this->data["content"][] = ["type" => "toggle", "text" => $text];
	}
	
	public function addDropdown(string $text, array $options) {
		$this->data["content"][] = ["type" => "dropdown", "text" => $text, "options" => $options];
	}
	
	public function handleResponse(Player $player, $data) : void {
		MultiWorld::getInstance()->formManager->handleCustomFormResponse($player, $data, $this);
	}
	
	public function jsonSerialize() : array {
		return $this->data;
	}
}