<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\DependencyInjection;

use Societo\BaseBundle\DependencyInjection\SocietoBaseExtension as TargetExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SocietoBaseExtension extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder();
        $loader = new TargetExtension();
        $loader->load(array(array()), $container);

        $configs = array(
            'twig.xml', 'activity.xml', 'config.xml', 'page_gadget.xml',
        );

        $counts = 0;
        foreach ($container->getResources() as $resource) {
            if (in_array(basename((string)$resource), $configs)) {
                $counts++;
            }
        }

        $this->assertEquals(count($configs), $counts);

        $definitions = $container->getDefinitions();
        $parameterBag = $container->getParameterBag();

        $this->assertEquals('Societo\BaseBundle\SiteConfig', $parameterBag->get('societo.site_config.class'));
        $this->assertEquals('Societo\BaseBundle\Templating\GlobalVariables', $parameterBag->get('templating.globals.class'));
        $this->assertEquals('Societo\BaseBundle\EventListener\ParamConverterListener', $parameterBag->get('sensio_framework_extra.converter.listener.class'));
    }
}
