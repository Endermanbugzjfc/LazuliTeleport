<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

class Messages
{
    public ?MessageEntry $tpaRequestRecieve = null;
    public ?MessageEntry $tpaRequestSend = null;
    public ?MessageEntry $tpaRequestAccepted = null;
    public ?MessageEntry $tpaRequestAcceptedWaiting = null;
    public ?MessageEntry $tpaRequestRejected = null;
    public ?MessageEntry $tpaExceedRequesteeLimit = null;
    public ?MessageEntry $tpaCoolDown = null;

    public ?MessageEntry $tpahereRequestRecieve = null;
    public ?MessageEntry $tpahereRequestSend = null;
    public ?MessageEntry $tpahereRequestAccepted = null;
    public ?MessageEntry $tpahereRequestAcceptedWaiting = null;
    public ?MessageEntry $tpahereRequestRejected = null;
    public ?MessageEntry $tpahereExceedRequesteeLimit = null;
    public ?MessageEntry $tpahereCoolDown = null;

    public ?MessageEntry $teleportationCancelledRequstor = null;
    public ?MessageEntry $teleportationCancelledRequstee = null;
    public ?MessageEntry $requestorHasUnresolvedRequest = null;
    public ?MessageEntry $requesteeHasUnresolvedRequest = null;

    public ?MessageEntry $requestSelf = null;
    public ?MessageEntry $internalServerError = null;

    public ?MessageEntry $blockPlayer = null;
    public ?MessageEntry $unblockPlayer = null;
    public ?MessageEntry $gotBlocked = null;

    public ?MessageEntry $forceModeEnabled = null;
    public ?MessageEntry $forceModeDisabled = null;

    public ?string $playerFinderTitle = null;
    public ?string $playerFinderLabel = null;
    public ?string $playerFinderPlaceholder = null;
    public ?string $playerFinderSearchResultHeader = null;
    public ?string $playerFinderSearchResultEntry = null;
    public ?string $playerFinderActionSelectorLabel = null;
    public ?string $actionTpahere = null;
    public ?string $actionTpa = null;
    public ?string $actionBlock = null;
    public ?string $actionUnblock = null;
    public ?string $forceModeToggleLabel = null;
    public ?string $forceModeWaitDurationSliderLabel = null;
    public ?string $forceModeWaitDurationSliderMin = null;
    public ?string $forceModeWaitDurationSliderStep = null;
    public ?string $forceModeWaitDurationSliderTotalSteps = null;

    public static function getDefault() : self
    {
        $self = new self();
        $formTitle = "{Bold}{DarkAqua}Teleportation Request";
        $receiveTpa = "{Aqua}{Requestor} {Gold}wants to teleport to you.";
        $commandHint = " {Italic}{DarkGray}(/tpaccept or /tpareject)";
        $self->tpaRequestRecieve = MessageEntry::createForm($formTitle, $receiveTpa, $receiveTpa . $commandHint);
        $self->tpaRequestSend = $requestSend = MessageEntry::createChat("{Yellow}Waiting for {Gold}{Requestee}{Yellow}'s response to your {Green}{RequestType} {Yellow}request...");
        $self->tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $self->tpaRequestAcceptedWaiting = MessageEntry::createChat("{Yellow}You will be teleporting to {Aqua}{Requestee} after {Green}{TeleportationWaitDuration}...");
        $self->tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $self->tpaRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpa request.");
        $self->tpaExceedRequesteeLimit = MessageEntry::createChat("{Red}You cannot teleport to more than {TpaRequesteeLimit} players!");
        $self->tpaCoolDown = MessageEntry::createChat("{Red}You must wait for {TpaCoolDown} more.");

        $receiveTpahere = "{Aqua}{Requestor} {Gold}wants to teleport you to him.";
        $self->tpahereRequestRecieve = MessageEntry::createForm($formTitle, $receiveTpahere, $receiveTpahere . $commandHint);
        $self->tpahereRequestSend = $requestSend;
        $self->tpahereRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting {Aqua}{Requestee} {Yellow}to you...");
        $self->tpahereRequestAcceptedWaiting = MessageEntry::createChat("{Aqua}{Requestee} {Yellow}will be teleporting to you after {Green}{WaitDuration}...");
        $self->tpahereRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpahere request.");
        $self->tpahereExceedRequesteeLimit = MessageEntry::createChat("{Red}Your teleport-license only allows teleporting at most {Green}{TpahereRequesteeLimit} {Red}players at the same time!");
        $self->tpahereCoolDown = MessageEntry::createChat("{Red}You must wait for {TpahereCoolDown} more.");

        $self->teleportationCancelledRequstor = MessageEntry::createChat("{Yellow}Teleportation cancelled because you moved.");
        $self->teleportationCancelledRequstee = MessageEntry::createChat("{Aqua}{Requestor} {Yellow}cancelled the teleportation. {Italic}{DarkGray}(By accident?)");
        $self->requestorHasUnresolvedRequest = MessageEntry::createChat("{Red}Sorry, you must wait until the previous requestee responses you!");
        $self->requesteeHasUnresolvedRequest = MessageEntry::createChat("{Red}self player has another teleportation request at the moment. Try again later!");

        $self->requestSelf = MessageEntry::createChat("{Red}Cannot send request to yourself!");
        $self->internalServerError = MessageEntry::createChat("{Red}Sorry, an internal server error had occurred! Please report self problem to an admin.");

        $self->blockPlayer = MessageEntry::createChat("{Aqua}{Target} {Yellow}will not be able to send any tpa or tpahere requests from now on. {Aqua}You can unblock him by running the command again.");
        $self->unblockPlayer = MessageEntry::createChat("{Yellow}Unblocked {Aqua}{Target}.");
        $self->gotBlocked = MessageEntry::createChat("{Red}You have been blocked by self player!");

        $self->forceModeEnabled = MessageEntry::createChat(
            <<<EOT
            {Yellow}Force mode enabled.
            {White}Teleportation wait duration: {Green}{ForcedTeleportationWaitDuration}
            EOT
        );
        $self->gotBlocked = MessageEntry::createChat("{Yellow}Force mode disabled.");

        $self->playerFinderTitle = "{Bold}{DarkAqua}Player Finder";
        $self->playerFinderLabel = "{Bold}{Gray}(Press \"Submit\" to proceed, press \"X\" to cancel.)";
        $self->playerFinderPlaceholder = "Enter first few letters in player's name";
        $self->playerFinderSearchResultHeader = "{Bold}{Gold}Found {Green}{ResultCount} {Gold}players:";
        $self->playerFinderSearchResultEntry = "{Gold}{ResultPlayer}";
        $self->playerFinderActionSelectorLabel = "{Aqua}Action";
        $self->actionTpahere = "Tpahere";
        $self->actionTpa = "Tpa";
        $self->actionBlock = "Block";
        $self->actionUnblock = "Unblock";

        return $self;
    }
}