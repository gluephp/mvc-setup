<?php namespace Glue\Mvc;

use Glue\App;
use Glue\Interfaces\ServiceProviderInterface;
use Glue\Whoops\ProductionHandler as WhoopsProductionHandler;
use Maer\Security\Csrf\Csrf;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(App $app)
    {
        $this->setErrorPages($app);

        Controller::setApp($app);

        /**
         * Register the CSRF package
         */
        $app->singleton('Maer\Security\Csrf\Csrf', function ($app) {
            // Start a session, if it's not already started
            $app->session;
            return new Csrf();
        });
        $app->alias('Maer\Security\Csrf\Csrf', 'csrf');

        $this->extendPlates($app);
    }

    /**
     * Extend plates view engine
     *
     * @param  Glue\App $app
     */
    protected function extendPlates($app)
    {
        $app->plates->registerFunction('csrfToken', function ($name = null) use ($app) {
            return $app->csrf->getToken($name);
        });
    }

    /**
     * Register all error handlers
     *
     * @param Glue\App $app
     */
    protected function setErrorPages($app)
    {
        WhoopsProductionHandler::setErrorCallback(function () use ($app) {
            return $app->plates->render('errors/server-error');
        });

        $app->router->notFound(function () use ($app) {
            return $app->plates->render('errors/not-found');
        });

        $app->router->methodNotAllowed(function () use ($app) {
            return $app->plates->render('errors/method-not-allowed');
        });
    }
}