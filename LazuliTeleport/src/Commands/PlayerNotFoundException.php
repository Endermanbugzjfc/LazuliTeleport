<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerFinderActionInterface;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use pocketmine\command\CommandSender;
use SOFe\InfoAPI\AnonInfo;
use SOFe\InfoAPI\StringInfo;

class PlayerNotFoundException extends TerminateCommandException
{
    protected PlayerSession $session;

    protected PlayerFinderActionInterface $action;

    protected string $input;

    public static function create(
        PlayerSession $session,
        PlayerFinderActionInterface $action,
        string $input
    ) : self {
        $self = new self("Player not found");
        $self->session = $session;
        $self->action = $action;
        $self->input = $input;

        return $self;
    }

    public function handle(
        CommandSender $sender
    ) : void {
        $session = $this->session;
        $message = $session->getMessages()->playerNotFound;
        $pluginName = LazuliTeleport::getInstance()->getName();
        $input = $this->input;
        $info = new class(
            "$pluginName.CommandPlayerFinder",
            [
                "Input" => new StringInfo($input)
            ],
            [
                $session->getInfo()
            ]
        ) extends AnonInfo {
        };

        $session->displayMessage($message, $info, function (bool $data) use (
            $session,
            $input
        ) : bool {
            if (!$data) {
                return false;
            }
            $session->openPlayerFinder($this->action, $input);
            return true;
        });
    }
}