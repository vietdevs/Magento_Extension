<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Step\Customer\Model;

use Migration\ResourceModel\Adapter\Mysql;
use Migration\ResourceModel;
use Migration\Reader\GroupsFactory;

/**
 * The class retrieves data related to entity type code
 */
class EntityTypeCode
{
    /**
     * @var ResourceModel\Source
     */
    private $source;

    /**
     * @var array
     */
    private $eavAttributes;

    /**
     * @var array
     */
    private $documentAttributeTypes;

    /**
     * @var \Migration\Reader\Groups
     */
    private $readerGroups;

    /**
     * @param ResourceModel\Source $source
     * @param GroupsFactory $groupsFactory
     */
    public function __construct(
        ResourceModel\Source $source,
        GroupsFactory $groupsFactory
    ) {
        $this->source = $source;
        $this->readerGroups = $groupsFactory->create('customer_document_groups_file');
    }

    /**
     * Retrieves data for all attributes relative to $entityTypeCode entity type
     *
     * @param string $entityTypeCode
     * @return array
     */
    public function getAttributesData($entityTypeCode)
    {

        $this->eavAttributes[$entityTypeCode] = [];
        /** @var Mysql $adapter */
        $adapter = $this->source->getAdapter();
        $query = $adapter->getSelect()
            ->from(
                ['et' => $this->source->addDocumentPrefix('eav_entity_type')],
                []
            )->join(
                ['ea' => $this->source->addDocumentPrefix('eav_attribute')],
                'et.entity_type_id = ea.entity_type_id',
                [
                    'attribute_id',
                    'backend_type',
                    'attribute_code',
                    'entity_type_id'
                ]
            )->where(
                'et.entity_type_code = ?',
                $entityTypeCode
            );
        $attributes = $query->getAdapter()->fetchAll($query);
        $mapAttributeType = [
            'confirmation' => 'varchar',
            'created_in' => 'varchar',
            'default_billing' => 'int',
            'default_shipping' => 'int',
            'dob' => 'varchar',
            'firstname' => 'varchar',
            'gender' => 'int',
            'lastname' => 'varchar',
            'middlename' => 'varchar',
            'password_hash' => 'varchar',
            'prefix' => 'varchar',
            'suffix' => 'varchar',
            'taxvat' => 'varchar',
            'city' => 'varchar',
            'company' => 'varchar',
            'country_id' => 'varchar',
            'fax' => 'varchar',
            'postcode' => 'varchar',
            'region' => 'varchar',
            'region_id' => 'int',
            'street' => 'text',
            'telephone' => 'varchar'
        ];
        foreach ($attributes as $attribute) {
            if (in_array($attribute['attribute_code'], array_keys($mapAttributeType))) {
                $attribute['backend_type'] = $mapAttributeType[$attribute['attribute_code']];
            }
            $this->eavAttributes[$entityTypeCode][$attribute['attribute_code']] = $attribute;
        }
        return $this->eavAttributes[$entityTypeCode];
    }

    /**
     * Retrieves entity type code by document name
     *
     * @param string $sourceDocName
     * @return string|null
     */
    public function getEntityTypeCodeByDocumentName($sourceDocName)
    {
        if (empty($this->documentAttributeTypes)) {
            $entityTypeCodes = array_keys($this->readerGroups->getGroup('eav_entities'));
            foreach ($entityTypeCodes as $entityTypeCode) {
                $documents = $this->readerGroups->getGroup($entityTypeCode);
                $documents = array_keys($documents);
                foreach ($documents as $documentName) {
                    $this->documentAttributeTypes[$documentName] = $entityTypeCode;
                }
            }
        }
        return isset($this->documentAttributeTypes[$sourceDocName]) ?
            $this->documentAttributeTypes[$sourceDocName] :
            null;
    }
}
