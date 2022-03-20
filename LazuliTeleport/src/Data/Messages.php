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

    public MessageEntry $tpahereRequestRecieve;
    public MessageEntry $tpahereRequestSend;
    public MessageEntry $tpahereRequestAccepted;
    public MessageEntry $tpahereRequestAcceptedWaiting;
    public MessageEntry $tpahereRequestRejected;
    public MessageEntry $tpahereExceedRequesteeLimit;

    public MessageEntry $teleportationCancelledRequstor;
    public MessageEntry $teleportationCancelledRequstee;

    public MessageEntry $requestSelf;

    public function __construct()
    {
        $this->tpaRequestRecieve = MessageEntry::createChat("{Aqua}{Requestor} {Gold}wants to teleport to you. {Italic}{DarkGray}(/tpaccept or /tpareject)");
        $this->tpaRequestSend = MessageEntry::createChat("{Yellow}Waiting for {Gold}{Requestee}{Yellow}'s response to your {Green}tpa {Yellow}request...");
        $this->tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $this->tpaRequestAcceptedWaiting = MessageEntry::createChat("{Yellow}You will be teleporting to {Aqua}{Requestee} after {Green}{WaitDuration}...");
        $this->tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $this->tpaRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpa request.");
        $this->tpaExceedRequesteeLimit = MessageEntry::createChat("{Red}You cannot teleport to more than {TpaRequesteeLimit} players!");

        $this->tpahereRequestRecieve = MessageEntry::createChat("{Aqua}{Requestor} {Gold}wants to teleport you to him.");
        $this->tpahereRequestSend = MessageEntry::createChat("{Yellow}Waiting for {Gold}{Requestee}{Yellow}'s response to your {Green}tpahere {Yellow}request...");
        $this->tpahereRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting {Aqua}{Requestee} {Yellow}to you...");
        $this->tpahereRequestAcceptedWaiting = MessageEntry::createChat("{Aqua}{Requestee} {Yellow}will be teleporting to you after {Green}{WaitDuration}...");
        $this->tpahereRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpahere request.");
        $this->tpahereExceedRequesteeLimit = MessageEntry::createChat("{Red}Your teleport-license only allows teleporting at most {Green}{TpahereRequesteeLimit} {Red}players at the same time!");

        $this->teleportationCancelledRequstor = MessageEntry::createChat("{Yellow}Teleportation cancelled because you moved.");
        $this->teleportationCancelledRequstee = MessageEntry::createChat("{Aqua}{Requestor} {Yellow}cancelled the teleportation. {Italic}{DarkGray}(By accident?)");

        $this->requestSelf = MessageEntry::createChat("{Red}Cannot send request to yourself!");
    }
}