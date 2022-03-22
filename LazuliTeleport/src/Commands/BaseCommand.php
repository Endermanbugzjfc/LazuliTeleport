<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Commands;

use CortexPE\Commando\BaseCommand as CommandoBaseCommand;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use function strtolower;

abstract class BaseCommand extends CommandoBaseCommand
{
    use BaseCommandTrait;
}