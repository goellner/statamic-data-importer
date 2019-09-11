# statamic-data-importer
Imports data from CSV to Statamic collection

![Demo Animation](https://github.com/goellner/statamic-data-importer/raw/master/demo.gif?raw=true)

## Install

Copy DataImporter folder to `site/addons/`.

Run `php please update:addons` to install the addon.

## Usage

Select "Import data" in the control panel and follow instructions

## Import Arrays

To import arrays the data in a CSV field has to be delimited. Defaults delimiter is set to `|`. If your CSV data already contains the `|` symbol, you can change the delimiter in the import to something else.
