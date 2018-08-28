
# Last – static site generator for any Symfony project

[![Build Status](https://travis-ci.org/franzwilding/last.svg?branch=master)](https://travis-ci.org/franzwilding/last)
[![Test Coverage](https://api.codeclimate.com/v1/badges/e9334f9657fc4a65e24c/test_coverage)](https://codeclimate.com/github/franzwilding/last/test_coverage)

Last is a minimalistic static site generator for Symfony 4 applications. Once installed, Last provides a 
command that will simply create requests for all of your routes and dumps the result as a static file to a dist folder.

## Installation
*(A symfony/flex recipe will be created soon. )*

    composer require fw/last-bundle
    
Last should get registered automatically to config/bundles.php, if not add it by hand:

    ...
    Fw\LastBundle\FwLastBundle::class => ['all' => true],

## Usage
Now you can run the dump command and your symfony app gets saved as static html files. 

    # will dump to the defined dist folder, defaults to %project%/dist
    bin/console last:dump 
    
    # wil dump to the given folder
    bin/console last:dump --dist=./custom_dist_folder
    
## Is it production ready?
The core of Last is stable now, however the following is missing yet:

- [ ] Provide an symfony/flex recipe
- [ ] Ask the user before deleting or overriding an existing folder
- [ ] Automatically copy assets from public/build, public/bundles/* and other locations to dist folder after dumping

## Configuration

    fw_last:
        dist_folder: '%kernel.project_dir%/dist' # this is the default dist folder, if you don't set it 
        providers:
            static: true # Static provider is enabled per default, you can disable it here

## Request providers

Last runs requests against your kernel. So in order to make it work, all desired requests must be defined. To make 
things more easy, Last comes with a request provider for all static routes (routes without mandatory placeholders). So
you only need to implement a custom request provider if you want to include dynamic routes like *blog/article/{id}*. A
simple provider could look like this: 

    use Fw\LastBundle\Router\RouteProvider;

    class YourustomProvider implements RouteProvider
    {
        /**
         * {@inheritdoc}
         */
        public function getRoutes(): array
        {
            return [
                Request::create('blog/article/1'),
                Request::create('blog/article/2'),
                Request::create('blog/article/3'),
            ];
        }
    } 

And needs to be a tagged service, in order to make it visible for Last.

    Your\CustomBundle\Provider\CustomProvider:
            tags: ['fw.last.route_provider']

Of course you can inject any dependencies like an entity manager to create more advanced providers.
