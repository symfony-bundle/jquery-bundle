<?php
/**
 * This file is part of the JQuery Bundle for Symfony.
 *
 * @copyright   Copyright (C) 2014 Hassan Amouhzi. All rights reserved.
 * @license     The MIT License (MIT), see LICENSE.md
 */
 
namespace Anezi\Bundle\JQueryBundle\Composer;

use Composer\Script\CommandEvent;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as SensioScriptHandler;

class ScriptHandler extends SensioScriptHandler
{
    public static function installAssets(CommandEvent $event)
    {
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'install assets');

        if (null === $consoleDir) {
            return;
        }

        if(!isset($options['jquery-version'])) {
            $event->getIO()->write(
                '<error>Option "jquery-version" for JQuery Bundle is missing!</error>'
            );
            return;
        }

        $jQueryVersion = $options['jquery-version'];

        $webDir = $options['symfony-web-dir'];

        if (!self::hasDirectory($event, 'symfony-web-dir', $webDir, 'install assets')) {
            return;
        }

        static::executeCommand(
            $event,
            $consoleDir,
            'jquery:assets:install -j "' . $jQueryVersion . '" ' . escapeshellarg($webDir)
        );
    }
}
