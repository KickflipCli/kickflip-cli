<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/
use Illuminate\Config\Repository;
use Kickflip\Enums\ConsoleVerbosity;
use Kickflip\KickflipHelper;
use Kickflip\Logger;

if (!class_exists('\KickflipHelper')) {
    class_alias(KickflipHelper::class, '\KickflipHelper', true);
}

$app = new Kickflip\Application(
    dirname(__DIR__),
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Kickflip\KickflipKernel::class,
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Illuminate\Foundation\Exceptions\Handler::class,
);

/*
|--------------------------------------------------------------------------
| Kickflip customizations
|--------------------------------------------------------------------------
|
| Setup both of the kickflip config repos; one for debug timings and one for CLI data/state.
|
*/
$app->singleton('kickflipTimings', static fn () => new Repository());
Logger::bootKickflipTimings(app('kickflipTimings'));
$app->singleton('kickflipCli', static fn () => new Repository());
KickflipHelper::bootKickflipState(app('kickflipCli'));
app('kickflipCli')->set('output.verbosity', ConsoleVerbosity::normal());

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
