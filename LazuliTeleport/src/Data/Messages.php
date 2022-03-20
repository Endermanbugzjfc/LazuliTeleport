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

    public function __construct()
    {
        $tpaRequestRecieve = MessageEntry::createChat("{Aqua}{Requestor} {Gold}wants to teleport to you. {Italic}{DarkGray}(/tpaccept or /tpareject)");
        $tpaRequestSend = MessageEntry::createChat("{Yellow}Waiting for {Gold}{Requestee}{Yellow}'s response to your {Green}tpa {Yellow}request...");
        $tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $tpaRequestAcceptedWaiting = MessageEntry::createChat("{Yellow}You will be teleporting to {Aqua}{Requestee} after {Green}{WaitDuration}...");
        $tpaRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting to {Aqua}{Requestee}...");
        $tpaRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpa request.");
        $tpaExceedRequesteeLimit = MessageEntry::createChat("{Red}You cannot teleport to more than {TpaRequesteeLimit} players!");

        $tpahereRequestRecieve = MessageEntry::createChat("{Aqua}{Requestor} {Gold}wants to teleport you to him.");
        $tpahereRequestSend = MessageEntry::createChat("{Yellow}Waiting for {Gold}{Requestee}{Yellow}'s response to your {Green}tpahere {Yellow}request...");
        $tpahereRequestAccepted = MessageEntry::createChat("{Yellow}Teleporting {Aqua}{Requestee} {Yellow}to you...");
        $tpahereRequestAcceptedWaiting = MessageEntry::createChat("{Aqua}{Requestee} {Yellow}will be teleporting to you after {Green}{WaitDuration}...");
        $tpahereRequestRejected = MessageEntry::createChat("{Red}{Requestee} rejected your tpahere request.");
        $tpahereExceedRequesteeLimit = MessageEntry::createChat("{Red}Your teleport-license only allows teleporting at most {Green}{TpahereRequesteeLimit} {Red}players at the same time!");

        $teleportationCancelledRequstor = MessageEntry::createChat("{Yellow}Teleportation cancelled because you moved.");
        $teleportationCancelledRequstee = MessageEntry::createChat("{Aqua}{Requestor} {Yellow}cancelled the teleportation. {Italic}{DarkGray}(By accident?)");
    }
}