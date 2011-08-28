<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle;

use Doctrine\ORM\Events;
use Societo\BaseBundle\Entity\MemberProfile;

class UpdateMemberDisplayNameEventSubscriber implements \Doctrine\Common\EventSubscriber
{
    public function onFlush($e)
    {
        $em = $e->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            if ($this->updateMemberDisplayNameByEntity($em, $entity)) {
                return;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            if ($this->updateMemberDisplayNameByEntity($em, $entity)) {
                return;
            }
        }
    }

    private function updateMemberDisplayNameByEntity($em, $entity)
    {
        if ($entity instanceof MemberProfile) {
            if ($this->getDisplayNameProfile() === $entity->getProfile()->getName()) {
                $metadata = $em->getClassMetadata('Societo\BaseBundle\Entity\Member');

                $member = $entity->getMember();
                $member->setDisplayName($entity->getValue());
                $em->persist($member);
                $em->getUnitOfWork()->computeChangeSet($metadata, $member);

                return true;
            }
        }
    }

    private function getDisplayNameProfile()
    {
        // TODO: be configurable
        return 'nickname';
    }

    public function getSubscribedEvents()
    {
        return array(Events::onFlush);
    }
}
