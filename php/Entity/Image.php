<?php

namespace Entity;

use Doctrine\MongoDB\GridFSFile;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JsonSerializable;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Image
 * @package Entity
 * @ODM\Document
 * @ODM\HasLifecycleCallbacks
 */
class Image implements JsonSerializable
{
    /** @ODM\Id */
    private $id;
    /** @ODM\String */
    private $title;
    /** @ODM\String */
    private $description;
    /** @ODM\Boolean */
    private $isPublished;
    /** @ODM\Date */
    private $createdAt;
    /** @ODM\Date */
    private $updatedAt;
    /** @ODM\ReferenceOne(targetDocument="\Entity\User") */
    private $owner;
    /** @ODM\File */
    private $file;
    /**
     * @ODM\String
     */
    private $filename;
    /**
     * @ODM\String
     */
    private $basename;
    /**
     * @ODM\String
     */
    private $mimeType;
    /**
     * @ODM\ReferenceOne(targetDocument="\Entity\Project")
     * @var \Entity\Project
     */
    private $project;
    /**
     * @ODM\String
     */
    private $md5;
    /**
     * @ODM\String
     */
    private $extension;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return GridFSFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @param Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return Project
     */
    function getProject()
    {
        return $this->project;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function getMd5()
    {
        return $this->md5;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function __toString()
    {
        return $this->filename;
    }

    public function getBasename()
    {
        return $this->basename;
    }

    public function setBasename($basename)
    {
        $this->basename = $basename;
    }

    public function getIsPublished()
    {
        return $this->isPublished;
    }

    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return array(
            "id"=>$this->getId(),
            "_id"=>$this->getId(),
            "title" => $this->getTitle(),
            "description" => $this->getDescription(),
            "createdAt" => $this->getCreatedAt(),
            "updatedAt" => $this->getUpdatedAt(),
            "filename" => $this->getFilename(),
            "basename" => $this->getBasename()
        );
    }

    /**
     * @ODM\PrePersist
     */
    function beforeSave()
    {
        if (null == $this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime());
        }
        $this->setUpdatedAt(new \DateTime());
        if (null != $this->getFile()) {
            $mime = MimeTypeGuesser::getInstance()->guess($this->getFile()->getFilename());
            $this->setMimeType($mime);
            $i = preg_match("/\.(?<ext>\w+)$/", $this->getFile()->getFilename(), $matches);
            if ($i > 0) {
                $this->setExtension($matches['ext']);
            }
        }
        $this->setBasename(basename($this->getFilename()));
    }
}