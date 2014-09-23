<?php
/**
 * This file is part of the JQuery Bundle for Symfony.
 *
 * @copyright   Copyright (C) 2014 Hassan Amouhzi. All rights reserved.
 * @license     The MIT License (MIT), see LICENSE.md
 */
 
namespace Anezi\Bundle\JQueryBundle\Composer;

use Composer\Script\Event;

class ScriptHandler
{
    public static function copyJQueryToBundle(Event $event)
    {
        $IO = $event->getIO();

        $IO->write("Copying JQuery files ... ", FALSE);

        $vendor = 'vendor';
        $jquery = $vendor . '/components/jquery';
        $bundle = $vendor . '/symfony-bundle/jquery-bundle';
        
        if(!file_exists($bundle . '/Anezi/Bundle/JQueryBundle/Resources')) {
            mkdir($bundle . '/Anezi/Bundle/JQueryBundle/Resources');
        }
        
        if(!file_exists($bundle . '/Anezi/Bundle/JQueryBundle/Resources/public')) {
            mkdir($bundle . '/Anezi/Bundle/JQueryBundle/Resources/public');
        }
        
        if(!file_exists($bundle . '/Anezi/Bundle/JQueryBundle/Resources/public/js')) {
            mkdir($bundle . '/Anezi/Bundle/JQueryBundle/Resources/public/js');
        }

        copy(
            $jquery . '/jquery.js',
            $bundle . '/Anezi/Bundle/JQueryBundle/Resources/public/js/jquery.js'
        );

        copy(
            $jquery . '/jquery.js',
            $bundle . '/Anezi/Bundle/JQueryBundle/Resources/public/js/jquery.min.js'
        );

        copy(
            $jquery . '/jquery.js',
            $bundle . '/Anezi/Bundle/JQueryBundle/Resources/public/js/jquery-migrate.js'
        );

        copy(
            $jquery . '/jquery.js',
            $bundle . '/Anezi/Bundle/JQueryBundle/Resources/public/js/jquery-migrate.min.js'
        );

        $IO->write(" <info>OK</info>");
    }
}
