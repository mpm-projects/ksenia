<?php

namespace Hydrators;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Hydrator\HydratorInterface;
use Doctrine\ODM\MongoDB\UnitOfWork;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ODM. DO NOT EDIT THIS FILE.
 */
class EntityProjectHydrator implements HydratorInterface
{
    private $dm;
    private $unitOfWork;
    private $class;

    public function __construct(DocumentManager $dm, UnitOfWork $uow, ClassMetadata $class)
    {
        $this->dm = $dm;
        $this->unitOfWork = $uow;
        $this->class = $class;
    }

    public function hydrate($document, $data, array $hints = array())
    {
        $hydratedData = array();

        /** @Field(type="id") */
        if (isset($data['_id'])) {
            $value = $data['_id'];
            $return = $value instanceof \MongoId ? (string) $value : $value;
            $this->class->reflFields['id']->setValue($document, $return);
            $hydratedData['id'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['title'])) {
            $value = $data['title'];
            $return = (string) $value;
            $this->class->reflFields['title']->setValue($document, $return);
            $hydratedData['title'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['description'])) {
            $value = $data['description'];
            $return = (string) $value;
            $this->class->reflFields['description']->setValue($document, $return);
            $hydratedData['description'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['language'])) {
            $value = $data['language'];
            $return = (string) $value;
            $this->class->reflFields['language']->setValue($document, $return);
            $hydratedData['language'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['client'])) {
            $value = $data['client'];
            $return = (string) $value;
            $this->class->reflFields['client']->setValue($document, $return);
            $hydratedData['client'] = $return;
        }

        /** @Field(type="collection") */
        if (isset($data['tags'])) {
            $value = $data['tags'];
            $return = $value;
            $this->class->reflFields['tags']->setValue($document, $return);
            $hydratedData['tags'] = $return;
        }

        /** @Many */
        $mongoData = isset($data['images']) ? $data['images'] : null;
        $return = new \Doctrine\ODM\MongoDB\PersistentCollection(new \Doctrine\Common\Collections\ArrayCollection(), $this->dm, $this->unitOfWork);
        $return->setHints($hints);
        $return->setOwner($document, $this->class->fieldMappings['images']);
        $return->setInitialized(false);
        if ($mongoData) {
            $return->setMongoData($mongoData);
        }
        $this->class->reflFields['images']->setValue($document, $return);
        $hydratedData['images'] = $return;

        /** @ReferenceOne */
        if (isset($data['poster'])) {
            $reference = $data['poster'];
            if (isset($this->class->fieldMappings['poster']['simple']) && $this->class->fieldMappings['poster']['simple']) {
                $className = $this->class->fieldMappings['poster']['targetDocument'];
                $mongoId = $reference;
            } else {
                $className = $this->unitOfWork->getClassNameForAssociation($this->class->fieldMappings['poster'], $reference);
                $mongoId = $reference['$id'];
            }
            $targetMetadata = $this->dm->getClassMetadata($className);
            $id = $targetMetadata->getPHPIdentifierValue($mongoId);
            $return = $this->dm->getReference($className, $id);
            $this->class->reflFields['poster']->setValue($document, $return);
            $hydratedData['poster'] = $return;
        }

        /** @Many */
        $mongoData = isset($data['owner']) ? $data['owner'] : null;
        $return = new \Doctrine\ODM\MongoDB\PersistentCollection(new \Doctrine\Common\Collections\ArrayCollection(), $this->dm, $this->unitOfWork);
        $return->setHints($hints);
        $return->setOwner($document, $this->class->fieldMappings['owner']);
        $return->setInitialized(false);
        if ($mongoData) {
            $return->setMongoData($mongoData);
        }
        $this->class->reflFields['owner']->setValue($document, $return);
        $hydratedData['owner'] = $return;

        /** @Field(type="boolean") */
        if (isset($data['isPublished'])) {
            $value = $data['isPublished'];
            $return = (bool) $value;
            $this->class->reflFields['isPublished']->setValue($document, $return);
            $hydratedData['isPublished'] = $return;
        }
        return $hydratedData;
    }
}