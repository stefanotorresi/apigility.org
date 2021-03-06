<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $config = $serviceManager->get('Config');
        $viewModel->forkme = $config['links']['forkme'];

    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array('factories' => array(
            'Application\GithubReleases' => function ($services) {
                $releases = array();
                if (file_exists('data/releases.json')) {
                    $json     = file_get_contents('data/releases.json');
                    $releases = json_decode($json);
                }
                return new GithubReleases($releases);
            },
        ));
    }

    public function getControllerConfig()
    {
        return array('factories' => array(
            'Application\Controller\Download' => function ($controllers) {
                $services = $controllers->getServiceLocator();
                return new Controller\DownloadController($services->get('Application\GithubReleases'));
            },
        ));
    }
}
