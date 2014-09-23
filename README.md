JQuery Bundle for Symfony
=========================

Add the jQuery framework inside the Symfony2 framework.

Installation
============

Add bundle to your composer.json file
-------------------------------------

    // ...
    "require": {
        // ...
        "symfony-bundle/jquery-bundle": "1.10.*";
        // for JQuery 1.10
        // ...
    },
    "scripts": {
        // ...
        "post-install-cmd": [
            // ...
            // insert it before Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets
            "Anezi\\Bundle\\JQueryBundle\\Composer\\ScriptHandler::copyJQueryToBundle",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
            // ...
        ],
        "post-update-cmd": [
            // ...
            // insert it before Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets
            "Anezi\\Bundle\\JQueryBundle\\Composer\\ScriptHandler::copyJQueryToBundle",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
            // ...
        ]
    },

Add bundle to your application kernel
-------------------------------------

    // app/AppKernel.php
    
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Anezi\Bundle\JQueryBundle\JQueryBundle(),
            // ...
        );
    }

Download the bundle using Composer
---------------------------------

    $ composer update symfony-bundle/jquery-bundle
    
Install assets
--------------

Update assets using composer post command:

    $ composer run-script post-update-cmd

Usage
=====

Refer to the full jquery file in your HTML template:

    <script type="text/javascript" src="{{ asset('bundles/jquery/js/jquery.js') }}"></script>

Refer to the minimal jquery file:

    <script type="text/javascript" src="{{ asset('bundles/jquery/js/jquery.min.js') }}"></script>

Refer to the full jquery migrate file:

    <script type="text/javascript" src="{{ asset('bundles/jquery/js/jquery-migrate.min.js') }}"></script>

Refer to the minimal jquery migrate file:

    <script type="text/javascript" src="{{ asset('bundles/jquery/js/jquery-migrate.min.js') }}"></script>

License
=======

This bundle is available under the MIT license.
