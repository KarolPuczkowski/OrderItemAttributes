# SkiDev Order Item Attributes

## Overview

The SkiDev_OrderItemAttributes Magento 2 module allows you to specify certain product attributes that should be transferred to the `sales_order_item` table when a quote is converted into an order. This enables the storage of selected product attribute values along with each order item for future reference.

## Features

- Adds a new option to the **EAV attribute model** that determines whether the attribute value should be transferred to the order item.
- Transfers specified product attributes from the **quote item** to the **order item** in JSON format.
- Supports various product attribute types such as:
  - Text attributes
  - Dropdown attributes (stores label values)
  - Multiselect attributes (stores selected option labels)

## Installation

- **1.** Clone or download this repository and place it in the app/code/SkiDev/OrderItemAttributes directory of your Magento 2 installation.

- **2.** Run the following commands to enable the module and set up the database schema:

```
bin/magento module:enable SkiDev_OrderItemAttributes
bin/magento setup:upgrade
bin/magento cache:flush
```

## Schema Changes

This module introduces two new database columns:

- **1.** `transfer_to_order_item` in the `catalog_eav_attribute` table:
  - Type: `SMALLINT`
  - Description: Determines whether this attribute should be transferred to the order item.
- **2.** `product_attributes` in the `sales_order_item` table:
  - Type: `TEXT`
  - Description: Stores the product attributes (in JSON format) transferred from the quote item.

## Functionality

### Adding Transfer-to-Order Option to Attributes

In the Magento admin panel, a new option is added to the attribute creation/edit form to mark an attribute for transfer to the order item:

- When an attribute is edited in the admin, you will see a field labeled Transfer to Order Item. Selecting "Yes" will ensure that the attribute's value is transferred when the quote is converted to an order.

### Observer: Save Product Attributes to Order Item

The module uses an observer that listens to the `sales_convert_quote_item_to_order_item` event. During the conversion from quote to order, this observer:

- **1.** Iterates through all quote items.
- **2.** For each quote item:
  - Retrieves the associated product and its attributes.
  - If an attribute has the `transfer_to_order_item` flag set to 1, its value is processed.
  - Depending on the attribute type:
    - **Text, Textarea, Date** attributes: The value is saved directly.
    - **Dropdown** attributes: The label of the selected option is saved.
    - **Multiselect** attributes: The labels of the selected options are saved as an array.
- **3.** All relevant attributes are encoded into a JSON string and stored in the `product_attributes` field in the `sales_order_item` table.

## Example JSON Data Stored in Order Item

The `product_attributes` field stores data in JSON format. For example:

```json
{
  "color": "Red",
  "size": "Large",
  "custom_options": ["Option 1", "Option 2"]
}
```
