<?php


namespace Forix\ProductWizard\Model\ResourceModel\Group;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        $this->_init(
            'Forix\ProductWizard\Model\Group',
            'Forix\ProductWizard\Model\ResourceModel\Group'
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }


    /**
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('group_id', 'title');
    }

    protected function _beforeLoad()
    {
        $this->setOrder('sort_order', self::SORT_ORDER_ASC);
        return parent::_beforeLoad(); // TODO: Change the autogenerated stub
    }

    /**
     * @param $stepIndex
     * @return $this
     */
    public function addStepToFilter($stepIndex){
        $this->addFieldToFilter('step_id', $stepIndex);
        return $this;
    }

    /**
     * @param $wizardId
     * @return $this
     */
    public function addWizardToFilter($wizardId){
        $this->addFieldToFilter('wizard_id', $wizardId);
        return $this;
    }
}