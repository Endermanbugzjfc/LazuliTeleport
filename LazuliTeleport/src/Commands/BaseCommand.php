<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use CortexPE\Commando\BaseCommand as CommandoBaseCommand;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use function strtolower;

abstract class BaseCommand extends CommandoBaseCommand
{
    use BaseCommandTrait;

    protected function prepare() : void
    {
        LazuliTeleport::getInstance()->getSingletonsHolder()->register($this);
        $pluginName = LazuliTeleport::getInstance()->getName();
        $lowerPluginName = strtolower($pluginName);
        $internalName = $this->getInternalName();
        $this->setPermission("$lowerPluginName.$internalName");
    }

    protected function registerSubCommandAndSetPermission(
        BaseSubCommand $command
    ) : void {
        $this->registerSubCommand($command);
        $command->setPermission("{$this->getPermission()}.{$command->getName()}");
    }
}