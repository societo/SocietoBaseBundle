<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class BackendController extends Controller
{
    public function indexAction()
    {
        $errors = array();

        if (!is_writable($this->get('kernel')->getRootDir().'/../web/image')) {
            $errors[] = 'You must make sure that web/image directory has writable permission from web server.';
        }

        return $this->render('SocietoBaseBundle:Backend:index.html.twig', array(
            'errors' => $errors,
        ));
    }

    public function menuAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');

        $menu = $em->getRepository('SocietoBaseBundle:Menu')->findOneBy(array(
            'name' => 'global',
        ));
        if (!$menu) {
            $menu = new \Societo\BaseBundle\Entity\Menu('global');
        }

        $pages = $em->getRepository('SocietoPageBundle:Page')->findAll();
        $form = $this->get('form.factory')
            ->create(new \Societo\BaseBundle\Form\MenuType($pages));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($data['uri']) {
                    $menu->addUri($data['caption'], $data['uri']);
                } elseif ($data['route']) {
                    $menu->addRoute($data['caption'], $data['route']);
                }

                $em->persist($menu);
                $em->flush();

                $this->get('session')->setFlash('success', 'Changes are saved successfully');

                return $this->redirect($this->generateUrl('_backend_menu'));
            }
        }

        return $this->render('SocietoBaseBundle:Backend:menu.html.twig', array(
            'menu'  => $menu,
            'pages' => $pages,
            'form'  => $form->createView(),
        ));
    }

    public function deleteMenuAction($caption)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');

        $menu = $em->getRepository('SocietoBaseBundle:Menu')->findOneBy(array(
            'name' => 'global',
        ));
        if (!$menu || !$menu->hasItem($caption)) {
            throw $this->createNotFoundException();
        }

        $form = $this->get('form.factory')->createBuilder('form')->getForm();

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $menu->removeItem($caption);

                $em->persist($menu);
                $em->flush();

                $this->get('session')->setFlash('success', 'Changes are saved successfully');

                return $this->redirect($this->generateUrl('_backend_menu'));
            }
        }

        return $this->render('SocietoBaseBundle:Backend:delete_menu.html.twig', array(
            'menuItem' => $menu->getItem($caption),
            'caption' => $caption,
            'form' => $form->createView(),
        ));
    }

    public function pageAction()
    {
        return $this->forward('societo.page.controller.backend:listAction');
    }

    public function createPageAction()
    {
        return $this->forward('societo.page.controller.backend:createAction');
    }

    public function editPageAction($name)
    {
        return $this->forward('societo.page.controller.backend:editAction', array(
            'name' => $name,
        ));
    }

    public function addPageGadgetAction($page_name, $gadget_name)
    {
        return $this->forward('societo.page.controller.backend:addGadgetAction', array(
            'page_name'   => $page_name,
            'gadget_name' => $gadget_name,
        ));
    }

    public function editPageGadgetAction($id)
    {
        return $this->forward('societo.page.controller.backend:editGadgetAction', array(
            'id' => $id,
        ));
    }

    public function sortPageGadgetAction($page_name)
    {
        return $this->forward('societo.page.controller.backend:sortGadgetAction', array(
            'page_name'   => $page_name,
        ));
    }

    public function profileAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $profiles = $em->getRepository('SocietoBaseBundle:Profile')->findAll();

        return $this->render('SocietoBaseBundle:Backend:profile.html.twig', array(
            'profiles' => $profiles,
        ));
    }

    public function editProfileAction($id = null)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');

        if ($id) {
            $profile = $em->getRepository('SocietoBaseBundle:Profile')->find($id);
            if (!$profile) {
                throw $this->createNotFoundException();
            }
        } else {
            $profile = new \Societo\BaseBundle\Entity\Profile();
        }

        $form = $this->get('form.factory')
            ->create(new \Societo\BaseBundle\Form\ProfileType($em), $profile);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $profile = $form->getData();

                $em->persist($profile);
                $em->flush();

                $this->get('session')->setFlash('success', 'Changes are saved successfully');

                return $this->redirect($this->generateUrl('_backend_profile'));
            }
        }

        return $this->render('SocietoBaseBundle:Backend:edit_profile.html.twig', array(
            'form' => $form->createView(),
            'profile' => $profile,
        ));
    }

    public function deleteProfileAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');

        $profile = $em->getRepository('SocietoBaseBundle:Profile')->find($id);
        if (!$profile) {
            throw $this->createNotFoundException();
        }

        $form = $this->get('form.factory')->createBuilder('form')->getForm();

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->remove($profile);
                $em->flush();

                $this->get('session')->setFlash('success', 'Changes are saved successfully');

                return $this->redirect($this->generateUrl('_backend_profile'));
            }
        }

        return $this->render('SocietoBaseBundle:Backend:delete_profile.html.twig', array(
            'form' => $form->createView(),
            'profile' => $profile,
        ));
    }

    public function configAction($category = null)
    {
        $plugins = $this->get('kernel')->getBundle('SocietoPluginBundle')->getSkinPlugins();

        $availableCategories = array(
            'general'        => new \Societo\BaseBundle\Form\Config\GeneralType(),
            'authentication' => new \Societo\BaseBundle\Form\Config\AuthenticationType(),
            'appearance'     => new \Societo\BaseBundle\Form\Config\AppearanceType($plugins),
        );

        if (null === $category) {
            $category = key($availableCategories);
        }

        if (!isset($availableCategories[$category])) {
            throw new \Exception();
        }

        $request = $this->get('request');

        $form = $this->get('form.factory')
            ->create($availableCategories[$category])
            ->setData($this->get('societo.site_config'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $form->getData()->flush();

                $this->get('session')->setFlash('success', 'Changes are saved successfully');

                return $this->redirect($this->generateUrl('_backend_config'));
            }
        }

        return $this->render('SocietoBaseBundle:Backend:config.html.twig', array(
            'form'       => $form->createView(),
            'category'   => $category,
            'categories' => array_keys($availableCategories),
        ));
    }

    public function memberAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');
        $maxResults = 20;

        $search = $request->query->get('search');
        $active = ($request->query->get('filter') !== 'inactive');
        $repository = $active ? 'SocietoBaseBundle:Member' : 'SocietoBaseBundle:SignUp';

        if (!empty($search['nickname'])) {
            $builder = $em->getRepository($repository)->getSearchQuery($search);
        } else {
            $builder = $em->getRepository($repository)->getAllMembersQuery();
        }
        $adapter = new DoctrineORMAdapter($builder->getQuery());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta
            ->setMaxPerPage($maxResults)
            ->setCurrentPage($this->get('request')->query->get('page', 1))
        ;

        return $this->render('SocietoBaseBundle:Backend:member.html.twig', array(
            'members' => $pagerfanta->getCurrentPageResults(),
            'search' => $search,
            'active' => $active,
            'pagerfanta' => $pagerfanta,
        ));
    }

    public function memberDeleteAction($member)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');
        $form = $this->get('form.factory')
            ->createBuilder('form')
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em->remove($member);
                $em->flush();

                $this->get('session')->setFlash('success', 'Changes are saved successfully');

                return $this->redirect($this->generateUrl('_backend_member'));
            }
        }

        return $this->render('SocietoBaseBundle:Backend:delete_member.html.twig', array(
            'member' => $member,
            'form' => $form->createView(),
        ));
    }

    public function memberEditAction($member)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $profiles = $em->getRepository('SocietoBaseBundle:Profile')->findAll();
        $request = $this->get('request');

        $form = $this->get('form.factory')
            ->createBuilder(new \Societo\BaseBundle\Form\MemberType())
            ->add('profile', new \Societo\BaseBundle\Form\MemberProfileType($em, $profiles, $this->get('validator'), true))
            ->getForm();
        $form->setData($member);

        // TODO: hard copy from frontend
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                foreach ($form->getData()->getProfile() as $key => $value) {
                    if (null === $value) {
                        continue;
                    }

                    $memberProfile = $member->getRawProfile($key, true);
                    if ($memberProfile) {
                        $memberProfile->setValue($value);
                    } else {
                        $profile = $em->getRepository('SocietoBaseBundle:Profile')->findOneBy(array('name' => $key));
                        $memberProfile = new \Societo\BaseBundle\Entity\MemberProfile($member, $profile, $value);
                    }

                    $em->persist($memberProfile);
                }

                $em->flush();

                $this->get('session')->setFlash('success', 'Changes are saved successfully');

                return $this->redirect($this->generateUrl('_backend_member_edit', array('member' => $member)));
            }
        }

        return $this->render('SocietoBaseBundle:Backend:edit_member.html.twig', array(
            'member'   => $member,
            'form' => $form->createView(),
        ));
    }

    public function memberActivateAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $signUp = $em->getRepository('SocietoBaseBundle:SignUp')->find($id);

        $dispatcher = $this->get('event_dispatcher');
        $handler = new \Societo\AuthenticationBundle\RegistrationHandler($em, $dispatcher);
        $handler->setSignUp($signUp);
        $handler->register();

        $this->get('session')->setFlash('success', 'Changes are saved successfully');

        return $this->redirect($this->generateUrl('_backend_member_inactive'));
    }

    public function pluginAction()
    {
        $plugins = array();

        foreach ($this->get('kernel')->getBundles() as $bundle) {
            if ($bundle instanceof \Societo\PluginBundle\Plugin\SocietoPlugin) {
                $plugins[$bundle->getName()] = array(
                    'version' => $bundle->getVersion(),
                    'author' => $bundle->getAuthor(),
                    'description' => $bundle->getDescription(),
                    'config_route' => $bundle->getConfigRoute(),
                );
            }
        }

        return $this->render('SocietoBaseBundle:Backend:plugin.html.twig', array(
            'plugins' => $plugins,
        ));
    }

    public function recipeAction()
    {
        if (!empty($_POST['url'])) {
            $xml = simplexml_load_file($_POST['url']);
            $recipe = array();

            if ($xml) {
                $recipe = $this->parseRecipeXml($xml);
            }

            return $this->render('SocietoBaseBundle:Backend:recipe_confirm.html.twig', array('recipe' => $recipe, 'url' => $_POST['url']));
        }

        return $this->render('SocietoBaseBundle:Backend:recipe.html.twig');
    }

    public function recipeCookAction()
    {
        if ($_POST['url']) {
            $xml = simplexml_load_file($_POST['url']);
            $recipe = array();

            if ($xml) {
                $recipe = $this->parseRecipeXml($xml);
            }

            if ($recipe['missing_plugins']) {
                throw new \Exception();
            }

            $em = $this->get('doctrine.orm.entity_manager');

            foreach ($recipe['pages'] as $pageName => $pageData) {
                $page = $em->getRepository('SocietoPageBundle:Page')->findOneBy(array(
                    'name' => $pageName,
                ));
                if (!$page) {
                    $page = new \Societo\PageBundle\Entity\Page($pageName, $pageData['url'], '');
                    $config = $this->getPageConfig();
                    $this->addEntryToPageConfig($config, $pageName, array(
                        'pattern' => $pageData['url'],
                        'defaults' => array(
                            '_controller' => 'SocietoPageBundle:Frontend:handle',
                            'name' => $pageName,
                        ),
                        'requirements' => array(),
                        'options' => array(),
                    ));

                    $em->persist($page);
                    $em->persist($config);
                }

                // gadgets
                $positions = array('head', 'sub_content', 'main_content', 'foot', 'side');
                foreach ($positions as $position) {
                    if (!isset($pageData[$position])) {
                        continue;
                    }

                    foreach ($pageData[$position] as $gadgetData) {
                        $gadgetName = isset($gadgetData['gadget']) ? $gadgetData['gadget'] : '';
                        unset($gadgetData['gadget']);
                        $caption = isset($gadgetData['caption']) ? $gadgetData['caption'] : '';
                        unset($gadgetData['caption']);
                        $gadget = new \Societo\PageBundle\Entity\PageGadget($page, $position, $gadgetName);
                        $gadget->setCaption($caption);
                        foreach ($gadgetData as $k => $v) {
                            $gadget->setParameter($k, json_decode($v));
                        }
                        $em->persist($gadget);
                    }
                }
            }

            $menu = $em->getRepository('SocietoBaseBundle:Menu')->findOneBy(array(
                'name' => 'global',
            ));
            if (!$menu) {
                $menu = new \Societo\BaseBundle\Entity\Menu('global');
            }

            foreach ($recipe['menus'] as $title => $item) {
                if (!empty($item['url'])) {
                    $menu->addUri($title, $item['url']);
                } elseif (!empty($item['route'])) {
                    $menu->addRoute($title, $item['route']);
                }
            }

            $em->persist($menu);
            $em->flush();

            $this->get('session')->setFlash('success', 'Societo has just made cook the specified recipe. Eat up!');

            return $this->redirect($this->generateUrl('_backend_recipe'));
        }
    }

    public function recipeExportAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $plugins = array();
        foreach ($this->get('kernel')->getBundles() as $bundle) {
            if ($bundle instanceof \Societo\PluginBundle\Plugin\SocietoPlugin) {
                $plugins[] = $bundle->getName();
            }
        }

        $pages = array();
        foreach ($em->getRepository('SocietoPageBundle:Page')->findAll() as $page) {
            $gadgets = array();
            foreach ($page->getGadgets() as $gadget) {
                if (!isset($gadgets[$gadget->getPoint()])) {
                    $gadgets[$gadget->getPoint()] = array();
                }

                $gadgets[$gadget->getPoint()][] = $gadget;
            }

            $pages[] = array(
                'name' => $page->getName(),
                'path' => $page->getPath(),
                'gadgets' => $gadgets,
            );
        }

        $menu = $em->getRepository('SocietoBaseBundle:Menu')->findOneBy(array(
            'name' => 'global',
        ));

        $response = $this->render('SocietoBaseBundle:Backend:recipe_export.xml.twig', array(
            'plugins' => $plugins,
            'pages' => $pages,
            'menu' => $menu ? $menu->getParameters() : array(),
        ));
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    private function parseRecipeXml($xml)
    {
        $recipe = array(
            'title' => (string)$xml->meta->title,
            'installed_plugins' => array(),
            'missing_plugins' => array(),
            'pages' => array(),
            'menus' => array(),
        );

        foreach ($xml->xpath('/recipe/plugins/plugin/@name') as $plugin) {
            $pluginName = (string)$plugin;
            try {
                $this->get('kernel')->getBundle($pluginName);
                $recipe['installed_plugins'][] = $pluginName;
            } catch (\Exception $e) {
                $recipe['missing_plugins'][] = $pluginName;
            }
        }

        foreach ($xml->xpath('/recipe/pages/page') as $page) {
            $pageName = (string)$page['name'];
            $url = (string)$page['url'];
            if ($pageName && $url) {
                $recipe['pages'][$pageName] = array('url' => $url);
            } else {
                continue;
            }

            foreach ($page->xpath('*[gadget]') as $point) {
                foreach ($point->children() as $gadget) {
                    $_parameters = array('gadget' => (string)$gadget['name']);
                    foreach ($gadget->parameter as $parameter) {
                        $_parameters[(string)$parameter['name']] = (string)$parameter['value'];
                    }

                    $recipe['pages'][$pageName][$point->getName()][] = $_parameters;
                }
            }
        }

        foreach ($xml->xpath('/recipe/menu/item') as $item) {
            $recipe['menus'][(string)$item['title']] = array(
                'url' => (string)$item['url'],
                'route' => (string)$item['route'],
            );
        }

        return $recipe;
    }

    // TODO: remove this method because this is hard copied method from src/Societo/PageBundle/Controller/BackendController.php
    private function getPageConfig()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $config = $em->getRepository('SocietoConfigDatabaseBundle:Config')->findOneBy(array(
            'name' => 'routing',
        ));
        if (!$config) {
            $config = new \Societo\Config\DatabaseBundle\Entity\Config('routing');
        }

        return $config;
    }

    // TODO: remove this method because this is hard copied method from src/Societo/PageBundle/Controller/BackendController.php
    private function addEntryToPageConfig($config, $key, $entry)
    {
        $list = $config->getValue();
        if (!$list) {
            $list = array();
        }

        if (preg_match_all('/\{([^\}]+\?)\}/', $entry['pattern'], $matches)) {
            foreach ($matches[1] as $match) {
                $param = substr($match, 0, -1);
                $entry['pattern'] = str_replace($match, $param, $entry['pattern']);
                $entry['defaults'][$param] = null;
            }
        }

        $list[$key] = $entry;

        $config->setValue($list);
    }
}
