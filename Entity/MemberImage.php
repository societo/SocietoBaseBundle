<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Societo\BaseBundle\Repository\MemberImageRepository")
 * @ORM\Table(name="member_image")
 */
class MemberImage extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="Societo\Util\StorageBundle\Entity\File")
     * @ORM\JoinColumn(name="image_file_id", referencedColumnName="id")
     */
    private $imageFile;

    public function __construct($member, $imageFile)
    {
        $this->member = $member;
        $this->imageFile = $imageFile;
    }

    public function setMember($member)
    {
        $this->member = $member;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function getFilename()
    {
        return $this->imageFile->getFilename();
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
    }
}
