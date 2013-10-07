# PrestaComposerPublic Bundle

[![Build Status](https://travis-ci.org/prestaconcept/PrestaComposerPublicBundle.png)](https://travis-ci.org/prestaconcept/PrestaComposerPublicBundle)

[![PrestaComposerPublicBundle on Knpbundles](http://knpbundles.com/prestaconcept/PrestaComposerPublicBundle/badge)](http://knpbundles.com/prestaconcept/PrestaComposerPublicBundle)

## Introduction

The goal of this bundle is to provide a simple way to include public 3rd-party 
libraries (javascripts, css, pictures ...) into your projects and keep its up-to-date.

1. Add the library to composer.json
2. Configure the library in PrestaComposerPublicBundle
3. Use the library as any other assets in the project.

## Why this bundle?

Because there's lot of meta bundles that provides libraries like jQuery, bootstrap
and only the most populars are maintain and up-to-date.
The probably fastest other way is to freeze the library into your project and sometimes hack it ...
Wait ?! nooooooo ! Nooooo! 

So now make it better: start configure it using composer, use it as any other assets
and then keep it up-to-date simply with composer.


## Installation

1. Add the bundle to the `composer.json` requirements' section :
    
    ~~~json
    "presta/composer-public-bundle": "1.*"
    ~~~

2. Append the following command to post install/update's section of `composer.json` like this :
    
    ~~~json
        "scripts": {
            "post-install-cmd": [
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile",
                "Presta\\ComposerPublicBundle\\Composer\\ScriptHandler::ComposerPublic"
            ],
            "post-update-cmd": [
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile",
                "Presta\\ComposerPublicBundle\\Composer\\ScriptHandler::ComposerPublic"
            ]
        },
    ~~~
    
3. Add bundle to `app/AppKernel.php` :
    
    ~~~
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                //...
                new Presta\ComposerPublicBundle\PrestaComposerPublicBundle(),
            );
    
           return $bundles;
        }
    }
    ~~~


## Usage

Add the library in `composer.json`.

A real world example, should be easier to remember. I want to add a jQuery plugin 
into my project :

Unfortunatly the library is not in [packagist][2], I add it following [the documentation][1].

~~~json
{
    ...
    "repositories": [
        { 
            "type": "package",
            "package": { 
                "name": "wesnolte/Pajinate",
                "version": "1.0.0",
                "source": { 
                    "url": "https://github.com/wesnolte/Pajinate.git",
                    "type": "git",
                    "reference": "master"
                } 
            }
        }
    ],
    ...
}
~~~

And then add it to the `require` section :

~~~json
{
    ...
    "require": {
        ...
        "wesnolte/Pajinate": "1.0.*"
    },
    ...
}
~~~

Finally you need to add an entry for this library in the PrestaComposerPublicBundle
configuration.

Eg. `app/config/config.yml`:

~~~yaml
presta_composer_public:
    symlink: true
    blend:
        wesnolte/Pajinate:
            vendor: wesnolte
            name: Pajinate
            path: /
~~~

Or shortly:

~~~yaml
presta_composer_public:
    blend:
        wesnolte/Pajinate: ~
~~~

Launch the command `app/console config:dump-reference PrestaComposerPublicBundle`
for more details.


Finally you only need to install your vendors: 

~~~bash
composer.phar install
~~~

**Note:**

> Since the library is in your vendors, you can also launch the following command 
to store or restore the library in the PrestaComposerPublicBundle folder.

At last but not least, do not forget to include your assets:

~~~twig
{# layout.html.twig #}
{% javascripts
        ...
    '@PrestaComposerPublicBundle/Resources/public/wesnolte/Pajinate/jquery.pajinate.js'
%}
    <script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}
~~~

## Final thought

If you're still not convinced by PrestaComposerPublicBundle, please consider the great [component installer for composer][3].

[1]: http://getcomposer.org/doc/05-repositories.md#package-2
[2]: https://packagist.org/
[3]: https://github.com/RobLoach/component-installer


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/prestaconcept/prestacomposerpublicbundle/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

