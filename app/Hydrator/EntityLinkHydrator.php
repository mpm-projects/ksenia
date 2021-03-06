<?php

namespace Hydrators;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Hydrator\HydratorInterface;
use Doctrine\ODM\MongoDB\UnitOfWork;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ODM. DO NOT EDIT THIS FILE.
 */
class EntityLinkHydrator implements HydratorInterface
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
        if (isset($data['itemId'])) {
            $value = $data['itemId'];
            $return = (string) $value;
            $this->class->reflFields['itemId']->setValue($document, $return);
            $hydratedData['itemId'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['type'])) {
            $value = $data['type'];
            $return = (string) $value;
            $this->class->reflFields['type']->setValue($document, $return);
            $hydratedData['type'] = $return;
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
        if (isset($data['url'])) {
            $value = $data['url'];
            $return = (string) $value;
            $this->class->reflFields['url']->setValue($document, $return);
            $hydratedData['url'] = $return;
        }

        /** @Field(type="boolean") */
        if (isset($data['isPublished'])) {
            $value = $data['isPublished'];
            $return = (bool) $value;
            $this->class->reflFields['isPublished']->setValue($document, $return);
            $hydratedData['isPublished'] = $return;
        }

        /** @Field(type="date") */
        if (isset($data['createdAt'])) {
            $value = $data['createdAt'];
            if ($value instanceof \MongoDate) { $return = new \DateTime(); $return->setTimestamp($value->sec); } elseif (is_numeric($value)) { $return = new \DateTime(); $return->setTimestamp($value); } elseif ($value instanceof \DateTime) { $return = $value; } else { $return = new \DateTime($value); }
            $this->class->reflFields['createdAt']->setValue($document, clone $return);
            $hydratedData['createdAt'] = $return;
        }

        /** @Field(type="date") */
        if (isset($data['updatedAt'])) {
            $value = $data['updatedAt'];
            if ($value instanceof \MongoDate) { $return = new \DateTime(); $return->setTimestamp($value->sec); } elseif (is_numeric($value)) { $return = new \DateTime(); $return->setTimestamp($value); } elseif ($value instanceof \DateTime) { $return = $value; } else { $return = new \DateTime($value); }
            $this->class->reflFields['updatedAt']->setValue($document, clone $return);
            $hydratedData['updatedAt'] = $return;
        }

        /** @ReferenceOne */
        if (isset($data['menu'])) {
            $reference = $data['menu'];
            if (isset($this->class->fieldMappings['menu']['simple']) && $this->class->fieldMappings['menu']['simple']) {
                $className = $this->class->fieldMappings['menu']['targetDocument'];
                $mongoId = $reference;
            } else {
                $className = $this->unitOfWork->getClassNameForAssociation($this->class->fieldMappings['menu'], $reference);
                $mongoId = $reference['$id'];
            }
            $targetMetadata = $this->dm->getClassMetadata($className);
            $id = $targetMetadata->getPHPIdentifierValue($mongoId);
            $return = $this->dm->getReference($className, $id);
            $this->class->reflFields['menu']->setValue($document, $return);
            $hydratedData['menu'] = $return;
        }
        return $hydratedData;
    }
}