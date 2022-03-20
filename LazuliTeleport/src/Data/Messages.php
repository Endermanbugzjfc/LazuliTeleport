<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

class Messages
{
    public MessageEntry $tpaRequestRecieve;
    public MessageEntry $tpaRequestSend;
    public MessageEntry $tpaRequestAccepted;
    public MessageEntry $tpaRequestAcceptedWaiting;
    public MessageEntry $tpaRequestRejected;
    public MessageEntry $tpaExceedRequesteeLimit;
    public MessageEntry $tpaCoolDown;

    public MessageEntry $tpahereRequestRecieve;
    public MessageEntry $tpahereRequestSend;
    public MessageEntry $tpahereRequestAccepted;
    public MessageEntry $tpahereRequestAcceptedWaiting;
    public MessageEntry $tpahereRequestRejected;
    public MessageEntry $tpahereExceedRequesteeLimit;
    public MessageEntry $tpahereCoolDown;

    public MessageEntry $teleportationCancelledRequstor;
    public MessageEntry $teleportationCancelledRequstee;
    public MessageEntry $requesteeHasUnresolvedRequest;

    public MessageEntry $requestSelf;

    public MessageEntry $blockPlayer;
    public MessageEntry $unblockPlayer;
    public MessageEntry $gotBlocked;

    public MessageEntry $forceModeEnabled;
    public MessageEntry $forceModeDisabled;

    public function __construct()
    {
        $this->tpaRequestRecieve = MessageEntry::createChat("{Aqua}{Requestor} {Gold}wants to teleport to you. {Italic}{DarkGray}(/tpaccept or /tpareject)");
        $this->tpaRequestSend = $requestSend = MessageEntry::createChat("{Yellow}Waiting for {Gold}{Requestee}{Yellow}'s response to your {Green}{RequestType} {Yellow}request...");
        $this->tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $this->tpaRequestAcceptedWaiting = MessageEntry::createChat("{Yellow}You will be teleporting to {Aqua}{Requestee} after {Green}{WaitDuration}...");
        $this->tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $this->tpaRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpa request.");
        $this->tpaExceedRequesteeLimit = MessageEntry::createChat("{Red}You cannot teleport to more than {TpaRequesteeLimit} players!");
        $this->tpaCoolDown = MessageEntry::createChat("{Red}You must wait for {TpaCoolDown} more.");

        $this->tpahereRequestRecieve = MessageEntry::createChat("{Aqua}{Requestor} {Gold}wants to teleport you to him.");
        $this->tpahereRequestSend = $requestSend;
        $this->tpahereRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting {Aqua}{Requestee} {Yellow}to you...");
        $this->tpahereRequestAcceptedWaiting = MessageEntry::createChat("{Aqua}{Requestee} {Yellow}will be teleporting to you after {Green}{WaitDuration}...");
        $this->tpahereRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpahere request.");
        $this->tpahereExceedRequesteeLimit = MessageEntry::createChat("{Red}Your teleport-license only allows teleporting at most {Green}{TpahereRequesteeLimit} {Red}players at the same time!");
        $this->tpahereCoolDown = MessageEntry::createChat("{Red}You must wait for {TpahereCoolDown} more.");

        $this->teleportationCancelledRequstor = MessageEntry::createChat("{Yellow}Teleportation cancelled because you moved.");
        $this->teleportationCancelledRequstee = MessageEntry::createChat("{Aqua}{Requestor} {Yellow}cancelled the teleportation. {Italic}{DarkGray}(By accident?)");
        $this->requesteeHasUnresolvedRequest = MessageEntry::createChat("{Red}This player has another teleportation request at the moment. Try again later!");

        $this->requestSelf = MessageEntry::createChat("{Red}Cannot send request to yourself!");
        $this->blockPlayer = MessageEntry::createChat("{Aqua}{Target} {Yellow}will not be able to send any tpa or tpahere requests from now on. {Aqua}You can unblock him by running the command again.");
        $this->unblockPlayer = MessageEntry::createChat("{Yellow}Unblocked {Aqua}{Target}.");
        $this->gotBlocked = MessageEntry::createChat("{Red}You have been blocked by this player!");

        $this->forceModeEnabled = MessageEntry::createChat(
            <<<EOT
            {Yellow}Force mode enabled.
            {White}Teleportation wait duration: {Green}{ForceWaitDuration}
            EOT
        );
        $this->gotBlocked = MessageEntry::createChat("{Yellow}Force mode disabled.");
    }
}