<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use Endermanbugzjfc\LazuliTeleport\Data\Messages;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerFinderActionInterface;
use Endermanbugzjfc\LazuliTeleport\Player\PlayerSession;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use SOFe\InfoAPI\AnonInfo;
use SOFe\InfoAPI\CommonInfo;
use SOFe\InfoAPI\InfoAPI;
use SOFe\InfoAPI\StringInfo;

class PlayerNotFoundException extends TerminateCommandException
{
    protected ?PlayerSession $session = null;

    protected PlayerFinderActionInterface $action;

    protected string $input;

    protected string $infoNamespace;

    public static function create(
        ?PlayerSession $session = null,
        PlayerFinderActionInterface $action,
        string $input
    ) : self {
        $pluginName = LazuliTeleport::getInstance()->getName();
        $infoNamespace = "$pluginName.CommandPlayerFinder";
        $consoleMessage = Messages::getDefault()->playerNotFound;
        $consoleInfo = new class(
            $infoNamespace,
            [
                "Input" => new StringInfo($input)
            ],
            [
                new CommonInfo(Server::getInstance())
            ]
        ) extends AnonInfo {
        };
        $self = new self(InfoAPI::resolve($consoleMessage->chat ?? "", $consoleInfo));
        $self->session = $session;
        $self->action = $action;
        $self->input = $input;
        $self->infoNamespace = $infoNamespace;

        return $self;
    }

    public function handle(
        CommandSender $sender
    ) : void {
        $session = $this->session;
        if ($session === null) {
            parent::handle($sender);
            return;
        }
        $message = $session->getMessages()->playerNotFound;
        $pluginName = LazuliTeleport::getInstance()->getName();
        $input = $this->input;
        $info = new class(
            $this->infoNamespace,
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