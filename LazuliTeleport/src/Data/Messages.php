<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Data;

class Messages
{
    public MessageEntry $tpaRequestRecieve;
    public MessageEntry $tpaRequestSend;
    public MessageEntry $tpaRequestAccepted;
    public MessageEntry $tpaRequestRejected;

    public MessageEntry $tpahereRequestRecieve;
    public MessageEntry $tpahereRequestSend;
    public MessageEntry $tpahereRequestAccepted;
    public MessageEntry $tpahereRequestRejected;

    public MessageEntry $waitingToTeleport;
    public MessageEntry $teleportationCancelledRequstor;
    public MessageEntry $teleportationCancelledRequstee;
}