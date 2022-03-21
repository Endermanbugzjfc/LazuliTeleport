<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Utils;

use Attribute;

/**
 * {@link Utils::override()} omits properties with this attribute.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class NoOverride
{
}