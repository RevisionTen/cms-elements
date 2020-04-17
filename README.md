# revision-ten/cms-elements

## Installation

#### Install via composer

The preferred method of installation is via [Packagist][] and [Composer][]. Run the following command to install the package and add it as a requirement to your project's `composer.json`:

```bash
composer req revision-ten/cms-elements
```

Enable the bundle by adding it to your bundles.php:
```PHP
RevisionTen\CmsElements\CmsElementsBundle::class => ['all' => true],
```

Add the desired elements to your CMS config by importing their configurations.

Example:
*cms.yaml*
```YAML
imports:
    - { resource: ../../vendor/revision-ten/cms-elements/Resources/config/vehicle/vehicle_offer.yaml }
    - { resource: ../../vendor/revision-ten/cms-elements/Resources/config/better_default/all.yaml }
```

#### List of elements:

##### Vehicle
- VehicleOffer
- VehicleFinancing
- VehicleEnVKV
- VehicleDatDisclaimer

##### Better default elements
- Image (requires [Lozad.js][])


[Lozad.js]: https://github.com/ApoorvSaxena/lozad.js
[packagist]: https://packagist.org/packages/revision-ten/cms-elements
[composer]: http://getcomposer.org/
