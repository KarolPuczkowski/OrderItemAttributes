<?php

namespace SkiDev\OrderItemAttributes\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ResourceConnection;

class SaveProductAttributesToOrderItem implements ObserverInterface
{
    protected $productRepository;
    protected $eavConfig;
    protected $resourceConnection;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        EavConfig $eavConfig,
        ResourceConnection $resourceConnection
    ) {
        $this->productRepository = $productRepository;
        $this->eavConfig = $eavConfig;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(Observer $observer)
    {
        // Get quote and order objects
        $quote = $observer->getQuote();
        $order = $observer->getOrder();

        // Loop through each item in the quote
        foreach ($quote->getAllItems() as $quoteItem) {
            $productId = $quoteItem->getProductId();
            $product = $this->productRepository->getById($productId);

            // Initialize an array to store the attributes that need to be transferred
            $transferAttributes = [];

            // Loop through each product attribute
            $attributes = $product->getAttributes();
            foreach ($attributes as $attribute) {
                // Check if the attribute has 'transfer_to_order_item' flag set to 1
                if ($attribute->getTransferToOrderItem() == 1) {
                    $attributeCode = $attribute->getAttributeCode();
                    $attributeValue = $product->getData($attributeCode);
                    $attributeFrontend = $attribute->getFrontend();

                    // Handle different types of attributes
                    switch ($attribute->getFrontendInput()) {
                        case 'text':
                        case 'textarea':
                        case 'date':
                            // For text-like attributes, store the value directly
                            $transferAttributes[$attributeCode] = $attributeValue;
                            break;

                        case 'select':
                            // For dropdown, get the label of the selected option
                            if ($attributeValue) {
                                $optionText = $attributeFrontend->getValue($product);
                                $transferAttributes[$attributeCode] = $optionText;
                            }
                            break;

                        case 'multiselect':
                            // For multiselect, get the labels of the selected options
                            if ($attributeValue) {
                                $selectedOptions = explode(',', $attributeValue);
                                $optionLabels = [];
                                foreach ($selectedOptions as $optionValue) {
                                    $optionText = $attributeFrontend->getOption($optionValue);
                                    $optionLabels[] = $optionText;
                                }
                                $transferAttributes[$attributeCode] = $optionLabels;
                            }
                            break;

                        default:
                            // Handle any other types if necessary
                            $transferAttributes[$attributeCode] = $attributeValue;
                            break;
                    }
                }
            }

            // Convert the collected attributes to JSON
            $attributesJson = json_encode($transferAttributes);

            // Get the corresponding order item
            $orderItem = $order->getItemByQuoteItemId($quoteItem->getId());

            if ($orderItem) {
                // Save the JSON string to the custom attribute in the sales_order_item table
                $orderItem->setData('product_attributes', $attributesJson);
            }
        }
    }
}
