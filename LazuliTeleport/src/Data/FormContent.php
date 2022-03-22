<?php

namespace Endermanbugzjfc\LazuliTeleport\Data;

class FormPage {
	public ?bool $enabled = true;

	public ?string $title = null;

	public ?string $pageDropdownLabel = null;
	public ?string $pageFindPlayer = null;
	public ?string $pageListPlayer = null;
	public ?string $pageViewTeleportationRequest = null;
	public ?string $pageExit = null;

	public ?bool $enablePageDropdownLabel = null;
	public ?bool $enablePageFindPlayer = null;
	public ?bool $enablePageListPlayer = null;
	public ?bool $enablePageViewTeleportationRequest = null;
	public ?bool $enablePageExit = null;

	public ?string $playerSearchBarLabel = null;
	public ?string $playerSearchPlaceholder = null;

	public ?string $playerToggleLabel = null;

	public ?string $actionSliderLabel;
	public ?string $actionTpa = null;
	public ?string $actionTpahere = null;
	public ?string $actionBlock = null;

	public ?string $forceModeToggleLabel = null;

	public ?string $forceModeWaitDurationSliderLabel = null;
	public ?int $forceModeWaitDurationMin = null;
	public ?int $forceModeWaitDurationStep = null;
	public ?int $forceModeWaitDurationTotalSteps = null;

	public ?string $requestDetailsTpa = null;
	public ?string $requestDetailsTpahere = null;

	public ?string $requestResponseSliderLabel = null;
	public ?string $requestAccept = null;
	public ?string $requestReject = null;
}