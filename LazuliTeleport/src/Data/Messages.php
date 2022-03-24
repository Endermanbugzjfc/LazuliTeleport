<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

use function str_replace;

class Messages
{
    public ?MessageEntry $tpaRequestReceive = null;
    public ?MessageEntry $tpaRequestReceiveOffline = null;
    public ?MessageEntry $tpaRequestSend = null;
    public ?MessageEntry $tpaRequestSendOffline = null;
    public ?MessageEntry $tpaRequestAccepted = null;
    public ?MessageEntry $tpaRequestAcceptedWaiting = null;
    public ?MessageEntry $tpaRequestRejected = null;
    public ?MessageEntry $tpaExceedRequesteeLimit = null;
    public ?MessageEntry $tpaCoolDown = null;

    public ?MessageEntry $tpahereRequestReceive = null;
    public ?MessageEntry $tpahereRequestReceiveOffline = null;
    public ?MessageEntry $tpahereRequestSend = null;
    public ?MessageEntry $tpahereRequestSendOffline = null;
    public ?MessageEntry $tpahereRequestAccepted = null;
    public ?MessageEntry $tpahereRequestAcceptedWaiting = null;
    public ?MessageEntry $tpahereRequestRejected = null;
    public ?MessageEntry $tpahereExceedRequesteeLimit = null;
    public ?MessageEntry $tpahereCoolDown = null;

    public ?MessageEntry $teleportationCancelledRequstor = null;
    public ?MessageEntry $teleportationCancelledRequstorMoved = null;
    public ?MessageEntry $teleportationCancelledRequstee = null;
    public ?MessageEntry $requestorHasUnresolvedRequest = null;
    public ?MessageEntry $requesteeHasUnresolvedRequest = null;

    public ?MessageEntry $requestSelf = null;
    public ?MessageEntry $internalServerError = null;
    public ?MessageEntry $noTeleportationRequest = null;
    public ?MessageEntry $cannotTeleportOfflinePlayer = null;
    public ?MessageEntry $playerNotFound = null;

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
    public ?string $actionBlockAll = null;
    public ?string $actionUnblockAll = null;
    public ?string $forceModeToggleLabel = null;
    public ?string $forceModeWaitDurationSliderLabel = null;
    public ?string $playerFinderNoTargetsSelected = null;

    public static function getDefault() : self
    {
        // Parts:
        $formTitle = "{Bold}{DarkAqua}Teleportation Request";
        $tpaReceiveBase = "{Aqua}{Requestor} {Gold}wants to teleport to you";
        $tpaReceive = $tpaReceiveBase . "."; // Receive.
        $tpaReceiveOffline = $tpaReceiveBase . " {Time Elapsed} ago."; // Receive offline.
        $tpaSend[0] = "{Yellow}Waiting for {Aqua}{Requestee}{Yellow}'s response to your {Green}Tpa {Yellow}request..."; // Send.
        $tpaSend[1] = "{Aqua}{Requestee} is now offline, he will recieve your {Green}Tpa {Yellow}request once he comes online."; // Send offline.

        $sendFormTitle = $formTitle . " Sent";
        $position = ".\nPosition: {Green}{RequestorPosition}";
        $tpahereReceiveBase = "{Aqua}{Requestor} {Gold}wants to teleport you to him";
        $tpahereReceive = $tpahereReceiveBase . $position; // Receive.
        $tpahereReceiveOffline = $tpahereReceiveBase . " {Time Elapsed} ago" . $position; // Receive offline.
        foreach ($tpaSend as $index => $text) {
            $tpahereSend[$index] = str_replace("Tpa", "Tpahere", $text);
        }
        $commandHint = "\n{Italic}{DarkGray}(/tpaccept or /tpareject)";
        $cancelButton = "{Red}Cancel Request (Press Escape)";
        $cancelled = MessageEntry::createChat("{Yellow}Teleportation cancelled.");

        $self = new self();
        $self->tpaRequestReceive = MessageEntry::createForm(
            $formTitle,
            $tpaReceive,
            $tpaReceive . $commandHint
        );
        $self->tpaRequestReceiveOffline = MessageEntry::createForm(
            $formTitle,
            $tpaReceiveOffline,
            $tpaReceiveOffline . $commandHint
        );
        $self->tpaRequestSend = MessageEntry::createForm(
            $sendFormTitle,
            $tpaSend[0],
            $tpaSend[0] . $commandHint,
            $cancelled,
            null,
            $cancelButton
        );
        $self->tpaRequestSendOffline = MessageEntry::createForm(
            $sendFormTitle,
            $tpaSend[1],
            $tpaSend[1] . $commandHint,
            $cancelled,
            null,
            $cancelButton
        );
        $self->tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $self->tpaRequestAcceptedWaiting = MessageEntry::createChat("{Yellow}You will be teleporting to {Aqua}{Requestee} after {Green}{TeleportationWaitDuration}...");
        $self->tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $self->tpaRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpa request.");
        $self->tpaExceedRequesteeLimit = MessageEntry::createChat("{Red}You cannot teleport to more than {TpaRequesteeLimit} players!");
        $self->tpaCoolDown = MessageEntry::createChat("{Red}You must wait for {TpaCoolDown} more.");

        $self->tpahereRequestReceive = MessageEntry::createForm(
            $formTitle,
            $tpahereReceive,
            $tpahereReceive . $commandHint
        );
        $self->tpahereRequestReceiveOffline = MessageEntry::createForm(
            $formTitle,
            $tpahereReceiveOffline,
            $tpahereReceiveOffline . $commandHint
        );
        $self->tpahereRequestSend = MessageEntry::createForm(
            $sendFormTitle,
            $tpahereSend[0],
            $tpahereSend[0] . $commandHint,
            $cancelled,
            null,
            $cancelButton
        );
        $self->tpahereRequestSendOffline = MessageEntry::createForm(
            $sendFormTitle,
            $tpahereSend[1],
            $tpahereSend[1] . $commandHint,
            $cancelled,
            null,
            $cancelButton
        );
        $self->tpahereRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting {Aqua}{Requestee} {Yellow}to you...");
        $self->tpahereRequestAcceptedWaiting = MessageEntry::createChat("{Aqua}{Requestee} {Yellow}will be teleporting to you after {Green}{WaitDuration}...");
        $self->tpahereRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpahere request.");
        $self->tpahereExceedRequesteeLimit = MessageEntry::createChat("{Red}Your teleport-license only allows teleporting at most {Green}{TpahereRequesteeLimit} {Red}players at the same time!");
        $self->tpahereCoolDown = MessageEntry::createChat("{Red}You must wait for {TpahereCoolDown} more.");

        $self->teleportationCancelledRequstor = $cancelled;
        $self->teleportationCancelledRequstorMoved = MessageEntry::createChat("{Yellow}Teleportation cancelled because you moved.");
        $self->teleportationCancelledRequstee = MessageEntry::createChat("{Aqua}{Requestor} {Yellow}cancelled the teleportation. {Italic}{DarkGray}(By accident?)");
        $self->requestorHasUnresolvedRequest = MessageEntry::createChat("{Red}Sorry, you must cancel the previous request or wait until the requestee responses you!");
        $self->requesteeHasUnresolvedRequest = MessageEntry::createChat("{Red}self player has another teleportation request at the moment. Try again later!");

        $self->requestSelf = MessageEntry::createChat("{Red}Cannot send request to yourself!");
        $self->internalServerError = MessageEntry::createChat("{Red}Sorry, an internal server error had occurred! Please report this problem to an admin.");
        $self->noTeleportationRequest = MessageEntry::createActionbar("{Red}You have no teleportation request.");
        $self->cannotTeleportOfflinePlayer = MessageEntry::createChat("{Red}You do not have the permission to teleport offline player(s) {Requestee}.");
        $self->playerNotFound = MessageEntry::createChat("{Red}No player has been found using keywords \"{Input}\".");

        $target = "{Aqua}{Target}";
        $block = "{Yellow}will not be able to send any tpa or tpahere requests";
        $blockFormTitle = "{Bold}{Red}Block Confirmation";
        $blockFormBody = "{Yellow}Are you sure that you want to block $target? $block.";
        $blockCommandHint = " from now on. {Aqua}You can unblock him / them by running the command again.";
        $cancelBlock = new MessageEntry();
        $self->blockPlayer = MessageEntry::createForm($blockFormTitle, $blockFormBody, $block . $blockCommandHint, $cancelBlock);
        $self->unblockPlayer = MessageEntry::createChat("{Yellow}Unblocked $target.");
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
        $self->actionBlock = "Block All";
        $self->actionUnblock = "Unblock All";
        $self->forceModeToggleLabel = "{Aqua}(Admin) Force-accept request";
        $self->forceModeWaitDurationSliderLabel = "{Aqua}Teleportation wait duration";
        $self->playerFinderNoTargetsSelected = "{Bold}{Red}Please select at least one player!";

        return $self;
    }
}