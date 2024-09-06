<?php
namespace SkiDev\OrderItemAttributes\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;

class Index extends AbstractMain implements TabInterface
{
    /**
     * @var AttributeFactory
     */
    protected $_attributeFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Eav\Helper\Data $eavData,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory,
        \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $eavData,
            $yesnoFactory,
            $inputTypeFactory,
            $propertyLocker,
            $data
        );
    }

    /**
     * Tab settings
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('SkiDev: Order Item Attributes');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('SkiDev: Order Item Attributes');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $catalogAttributeObject = $this->getAttributeObject();
        
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset(
            'amasty_ogrid_index_fieldset',
            ['legend' => __('SkiDev: Order Item Attributes'), 'collapsable' => true]
        );

        $yesno = $this->_yesnoFactory->create()->toOptionArray();

        $fieldset->addField(
            'transfer_to_order_item',
            'select',
            [
                'name' => 'transfer_to_order_item',
                'label' => __('Transfer to order item'),
                'title' => __('Transfer to order item'),
                'note' => __('Select "Yes" to add this attribute to the order item while converting from quote item.'),
                'values' => $yesno
            ]
        );

        $this->setForm($form);

        return $this;
    }

    public function getAfter()
    {
        return 'front';
    }
}
