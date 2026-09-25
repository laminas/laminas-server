<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('Exception', 'src/Exception')
    ->layer('Contract', [
        'src/Client.php',
        'src/ClientInterface.php',
        'src/Server.php',
        'src/ServerInterface.php',
    ])
    ->layer('Method', 'src/Method')
    ->layer('Definition', 'src/Definition.php')
    ->layer('ReflectionException', 'src/Reflection/Exception')
    ->layer('Reflection', ['src/Reflection.php', 'src/Reflection'], 'src/Reflection/Exception')
    ->layer('Cache', 'src/Cache.php')
    ->layer('Server', 'src/AbstractServer.php')
    ->ruleset([
        'Exception'           => [],
        'Contract'            => [],
        'Method'              => ['Exception'],
        'Definition'          => ['+Method'],
        'ReflectionException' => ['Exception'],
        'Reflection'          => ['+ReflectionException'],
        'Cache'               => ['Contract', '+Definition'],
        'Server'              => ['Contract', '+Definition', '+Reflection'],
    ]);
