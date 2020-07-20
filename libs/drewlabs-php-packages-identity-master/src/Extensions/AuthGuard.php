<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Drewlabs\Core\Jwt\JwtAuthGuard;
use Illuminate\Contracts\Auth\Guard;

final class AuthGuard extends JwtAuthGuard implements Guard
{
}
