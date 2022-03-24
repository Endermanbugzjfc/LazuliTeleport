<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use pocketmine\command\CommandSender;
use SOFe\InfoAPI\AnonInfo;
use SOFe\InfoAPI\Info;
use SOFe\InfoAPI\StringInfo;

class PlayerNotFoundException extends TerminateCommandException
{
    protected PlayerSession $session;

    protected Info $info;

    public static function create(
        PlayerSession $session,
        string $input
    ) : self {
        $pluginName = LazuliTeleport::getInstance()->getName();

        $self = new self("Player not found");
        $self->session = $session;
        $self->info = new class(
            "$pluginName.CommandPlayerFinder",
            [
                "Input" => new StringInfo($input)
            ],
            [
                $session->getInfo()
            ]
        ) extends AnonInfo {
        };

        return $self;
    }

    public function handle(
        CommandSender $sender
    ) : void {
        $session = $this->session;
        $message = $session->getMessages()->playerNotFound;
        $session->displayMessage($message, $this->info);
    }
}