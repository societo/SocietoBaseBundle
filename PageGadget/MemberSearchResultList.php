<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\PageGadget;

use \Societo\PageBundle\PageGadget\AbstractPageGadget;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * Gadget for list of member search result.
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class MemberSearchResultList extends AbstractPageGadget
{
    const NOTHING_FOR_NO_QUERIES = 0;
    const ALL_FOR_NO_QUERIES = 1;

    protected $caption = 'Member Search Result List';

    protected $description = 'A list of member searching result';

    /**
     * {@inheritDoc}
     */
    public function execute($gadget, $parentAttributes, $parameters)
    {
        $results = array();
        $em = $this->get('doctrine.orm.entity_manager');
        $query = (array)$this->get('request')->query->get('profile', $this->get('request')->request->get('profile'));  // Request
        foreach ($query as $k => $v) {
            if ('' === $v) {
                unset($query[$k]);
            }
        }

        if ($query) {
            $builder = $em->getRepository('SocietoBaseBundle:Member')->getSearchQuery($query);
        } else {
            if (self::ALL_FOR_NO_QUERIES == $gadget->getParameter('result_for_no_queries', self::NOTHING_FOR_NO_QUERIES)) {
                $builder = $em->getRepository('SocietoBaseBundle:Member')->getAllMembersQuery();
            }
        }

        $adapter = new DoctrineORMAdapter($builder->getQuery());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta
            ->setMaxPerPage($gadget->getParameter('max_results'))
            ->setCurrentPage($this->get('request')->query->get('page', 1))
        ;

        return $this->render('SocietoBaseBundle:PageGadget:member_search_list.html.twig', array(
            'gadget'  => $gadget,
            'results'  => $pagerfanta->getCurrentPageResults(),
            'parameters' => $gadget->getParameters(),
            'route_to_more_page' => $gadget->getParameter('route_to_more_page'),
            'has_pager' => $gadget->getParameter('has_pager'),
            'attributes' => $parentAttributes,
            'pagerfanta' => $pagerfanta,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        // TODO: 
        return array(
            'result_for_no_queries' => array(
                'type'    => 'choice',
                'options' => array(
                    'choices' => array(
                        self::NOTHING_FOR_NO_QUERIES => 'Nothing results for no queries',
                        self::ALL_FOR_NO_QUERIES     => 'All results for no queries',
                    ),
                    'required' => true,
                ),
            ),

            'max_results' => array(
                'type' => 'text',
                'options' => array(
                    'required' => false,
                ),
            ),

            'route_to_more_page' => array(
                'type' => 'text',
                'options' => array(
                    'required' => false,
                ),
            ),

            'has_pager' => array(
                'type' => 'choice',
                'options' => array(
                    'choices' => array(
                        0 => 'No',
                        1 => 'Yes',
                    ),
                ),
            ),
        );
    }
}
