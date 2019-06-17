# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]

## [1.4.8] - 2019-06-17
### Changed
- settings to get order code to grouping PLP
- setting to when storeId is zero
- fix get product
- remove cond is static
- add attribute category_ids to blacklist because skyhub not support
- Adding check if attribute is static and send when empty
- Update setup to next version to be released
- settings general
- settings to create order when not informed the region code
- settings to queue continue when occur error in process of update status to order during execution of request
- settings to invoiced order when order init with status delivered without invoice

## [1.4.7] - 2019-03-26
### Changed
- Showing estimated delivery information in the order in the admin panel;
- Adding custom blacklist of product attributes that are integrated to Skyhub;
- Showing estimated delivery date in admin view order when exists;
- Adding possibility to remove items from the product integration queue;
- Fixing bug in order status update when it is delivered
- Adding flag on products to allow or not to be integrated into the marketplace

## [1.4.6] - 2019-01-28
### Changed
- Showing button to reset product's integration history only on the product's queue page;
- Fixing bug with orders created with interest value;
- Showing discount amount on orders page;

## [1.4.5] - 2019-01-10
### Changed
- Fixing bug at quote when the first order or the queue throws an exception and the next orders are being created without currency;
- Fixing products integration. Now, the module has a filter to allow the integration to work only with products atatched with enabled integration websites;
- Showing multiple payment methods at orders page;
- Removing duplicate order billing / shipping addresses information (complement and reference);
- Fixing BUG when "compliance mode" is activated;
- Fixing BUG at order status update when the order is "holded" in magento;
- Writing first functional tests.

### Added
- Adding csv / xls export option to orders errors page listing;

## [1.4.4] - 2018-11-09
### Changed
- Fixing problem at orders creation when cart rules are being applied to it;
- Fixing exception throw when casting attribute "cost";

## [1.4.3] - 2018-10-25
### Changed
- Fixing PLP listing feature.
- Fixing product's configurable children integration problem;
### Added
- Adding csv / xls export option to orders errors page listing;

## [1.4.2] - 2018-10-23Fixing bug at quote when the first
### Changed
- Allowing order cancellation even after invoice creation;
- Fixing products with variation integration - variation attributes are set correctly now;
- Improving product's integration error messages on admin product's page;
### Added
- PLP now is available to be consulted by magento API;

## [1.4.1] - 2018-09-03
### Changed
- Fix problem with status orders queue that wasn't beeing cleaned;
- Fix problem with order status mapping between Skyhub and magento;
- Products attributes with 0 value wasn't beeing sent to Skyhub;
- Minor fixes.

## [1.4.0] - 2018-08-21
### Changed
- Fix permission to "see" "Skyhub Integrate Button" at product page;
- Fix BUG at product integration on save when the flag "Integrate on save" is set as "true"; Products was being sent to skyhub with no stock, then later the cron correct it;
- Product attributes massivelly updated now are being placed at the queue to be integrated;
### Added
- PLP now is available to be managed at magento admin.

## [1.3.6] - 2018-07-23
### Changed
- Fix pass argument by reference;
- Entities clean cron fix;


## [1.3.5] - 2018-07-13
### Changed
- Catalog product inventory rules improved;


## [1.3.4] - 2018-07-11
### Changed
- SkyHub programs ACL implemented;


## [1.3.3] - 2018-06-26
### Added
- Order payment info em order detail page;
- Product attributes and categories cleaning feature;

### Changed
- Solved problem with order creation via admin panel;
- Categories integration adjustment;
- Special price product integration adjustment;

## [1.3.2] - 2018-06-11
### Added
- Added a new column in bseller_skyhub_entity table to force entities integration;

### Changed
- Solved problem with order creation without SkyHub data;
- Solved problem with product specifications in case of zero stock quantity;
- Solved problem with SkyHub entity queue table query;

## [1.3.1] - 2018-05-25
### Added
- Solved problem with order statuses queue which wasn't decreasing magento queue;
- Solved problem with order consuming which wasn't decreasing skyhub queue;
- Solved problem with orders created with the option to create with default magento increment_id which wasn't beeing deleted from skyhub;
- Solved problem with canceled orders at skyhub which wasn't beeing deleted but magento wasn't cleaning skyhub queue;
- Solved problem with configurable products sons which wasn't being updated at skyhub by integration;

### Changed
- Minor fixes.

## [1.3.0] - 2018-05-18
### Added
- Customer attributes mapping
- Multistore enabled
- The JSON of the orders now is stored and can be acessible at order's page. 

### Changed
- Minor fixes.

## [1.2.6] - 2018-05-17
### Added
- Added a script file to execute all integrations (line creation and execution).
- Added logic to create a customer email if the order doesn't contains it. A email creation pattern can be configurated at module config panel.
- Products integration now is called after a order placed too (only for the order items). This behavior can be configured at module config panel. 

### Changed
- Minor fixes.
- Changed the behavior of "deleted" product. Now, the product will be disabled and the stock set to 0 at skyhub.

## [1.2.5] - 2018-04-25
### Added
- Now, it's available an option to use the magento default incrementId at order creation process.
### Changed
- Adapting the order process creation to do not conflict with "Bizz Commerce" skyhub module;

## [1.2.4] - 2018-04-19
### Changed
- Minor fixes.

## [1.2.3] - 2014-04-06
### Changed
- Minor fixes.

## [1.2.2] - 2018-04-06
### Changed
- Minor fixes.

## [1.2.1] - 2018-04-05
### Changed
- Minor fixes.  

## [1.2.0] - 2018-03-29
### Changed
- Product integration fixes. Now only the changed products are being queued for integration.
- Changed validation to display the attributes mapping notification in admin.
- Changes to product attributes requirements. Now, only some attributes are really required for product integration.
- Created a product visibility filter in module configuration. Now you can select what product visibility can be integrated.
- Fix to first and last names when importing an order from SkyHub. Now, if there’s only the first name in the order the last name will be the same for first name as well.
- Fix to customer telephone in order import. If the customer does not provide a phone number a default number will be created.
- Minor fixes.

## 1.0.0 - 2018-03-14
### Added
- Products integration
- Product attributes integration
- Product categories integration
- Orders integration
- Base module
