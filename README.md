# Ezzar Visual Layers

`Ezzar_VisualLayers` is a Magento 2 module for adding editable product-page visual layer sections. It lets you upload a layered image, place numbered markers on that image, and connect each marker to product information.

The module is designed for Hyva product pages. The frontend template uses Tailwind CSS utility classes and Alpine.js.

## Features

- Admin-managed visual layer records.
- Upload a layered image for each visual.
- Add any number of marker/layer rows.
- Set marker X/Y positions as image-relative percentages.
- Drag or click markers in the admin preview to adjust marker positions.
- Responsive frontend markers that stay attached to the image as it scales.
- Frontend accordion content linked to each image marker.
- Product attribute source model for assigning visuals to products.

## Demos

### Admin Panel

<video width="100%" controls>
  <source src="demo/admin-panel.mp4" type="video/mp4">
  Your browser does not support the video tag.
</video>

### Frontend

<video width="100%" controls>
  <source src="demo/frontend.mp4" type="video/mp4">
  Your browser does not support the video tag.
</video>

## Requirements

- Magento Open Source or Adobe Commerce 2.4.x.
- PHP `^8.1`, `^8.2`, or `^8.3`.
- Hyva frontend theme for the PDP output.
- Tailwind CSS available in the active Hyva theme.
- Alpine.js available in the active Hyva theme.

## Installation

### Composer (recommended)

```bash
composer require ezzaro14/module-visual-layers 
bin/magento module:enable Ezzar_VisualLayers
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
```

### Install From GitHub

```bash
composer config repositories.ezzaro14-module-visual-layers vcs https://github.com/Ezzaro14/module-visual-layers.git
composer require ezzaro14/module-visual-layers:dev-main
```

### Enable The Magento Module

From the Magento root:

```bash
bin/magento module:enable Ezzar_VisualLayers
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

## Database And Attribute Changes

The module creates two tables:

- `ezzar_visual_layer_visual`
- `ezzar_visual_layer`

The module also creates this product attribute:

- `visual_layer_id`

The attribute is not assigned to any product attribute set. After installation, assign it to the attribute sets where you want to use visual layers.

## Usage

### Create A Visual Layer

1. Go to `Catalog > Ezzar > Visual Layers`.
2. Click `Add New Visual Layer`.
3. Enter an internal `Title`.
4. Optionally enter a `Frontend Title`.
5. Upload a `Layered Image`.
6. Add `Image Alt Text`.
7. Add one or more layer rows.
8. Save.

### Layer Row Fields

Each layer row has these fields:

- `Active`: whether this layer should render on the frontend.
- `Order`: sort order for accordion and marker output.
- `Marker`: label shown inside the marker circle.
- `Title`: accordion row title.
- `What It Is`: first accordion content section.
- `What It Does`: second accordion content section.
- `X %`: marker horizontal position from the image left edge.
- `Y %`: marker vertical position from the image top edge.

`X %` and `Y %` are stored as percentages, so frontend markers remain attached to the same relative point on the image at different viewport sizes.

### Admin Marker Preview

The image preview in admin shows the current markers. You can:

- Click to move the active row marker.
- Drag markers on the preview image.
- Use the numeric `X %` and `Y %` fields for precise positioning.

## Product Assignment

After creating a visual:

1. Open a product that uses an attribute set containing `visual_layer_id`.
2. Find the `Visual Layer` select field.
3. Choose the visual to display.
4. Save the product.
5. Clean cache if needed.

## Frontend Output

The module renders on product detail pages through:

```text
view/frontend/layout/catalog_product_view.xml
view/frontend/templates/product/visual-layers.phtml
```

The block is inserted into the Hyva product page block:

```xml
<referenceBlock name="product.detail.page">
```

The section renders only when all of these are true:

- The current page has a product in the Magento registry.
- The product has a `visual_layer_id` value.
- The selected visual exists.
- The selected visual is active.
- The selected visual has an image.
- The selected visual has at least one active layer row.

The frontend layout is:

- Desktop: accordion content on the left, layered image on the right.
- Mobile: image first, accordion below.

Markers and accordion buttons share the same Alpine `openLayer` state.

## Hyva Tailwind Setup

If the module is installed through Composer, make sure your Hyva Tailwind build scans the module templates. Otherwise some Tailwind classes may be purged from the generated CSS.

In your Hyva theme Tailwind content configuration, include the module templates. The exact file depends on the Hyva version and theme setup, but the path usually looks similar to:

```js
content: [
    '../../../../../../vendor/ezzaro14/module-visual-layers/view/frontend/templates/**/*.phtml',
]
```

If the module is installed in `app/code`, use the matching local path instead:

```js
content: [
    '../../../../../../app/code/Ezzar/VisualLayers/view/frontend/templates/**/*.phtml',
]
```

After changing Tailwind content paths, rebuild the Hyva theme CSS and deploy static content as required for your environment.

## Troubleshooting

### The Visual Does Not Show On The PDP

Check:

- The active theme is Hyva or has a `product.detail.page` layout block.
- The product attribute set contains `visual_layer_id`.
- The product has a Visual Layer selected.
- The selected visual is active.
- The visual has a layered image.
- At least one layer row is active.
- Magento layout, block HTML, and full page cache have been cleaned.

### The Markup Shows But Looks Unstyled

This usually means the Hyva Tailwind build is not scanning the module templates. Add the module template path to the Tailwind content configuration and rebuild the theme CSS.

### The Accordion Does Not Open Or Close

The frontend template uses Alpine.js attributes such as `x-data`, `x-text`, `:class`, and `@click`. Confirm Alpine is loading on the product page.

## Development Notes

Uploaded images are stored under:

```text
pub/media/visual_layers/
```

## Uninstall

To disable the module:

```bash
bin/magento module:disable Ezzar_VisualLayers
bin/magento setup:upgrade
bin/magento cache:clean
```

To remove the Composer package:

```bash
composer remove ezzaro14/module-visual-layers
bin/magento setup:upgrade
bin/magento cache:clean
```

Magento will not automatically remove historical module data unless an uninstall routine or manual database cleanup is added. Back up the database before removing tables or attributes manually.
