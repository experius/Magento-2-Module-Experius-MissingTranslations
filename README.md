Mage2 Module Experius MissingTranslations
====================

Add a CLI command to Collect missing translations in specified folder or the entire Magento 2 Root and add Admin Grid to display and update or add database/inline translations `(Stores > Translations > Database/Inline Translations)`


   ``experius/module-missingtranslations``
   
 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Configuration](#markdown-header-configuration)
 - [Additional Information](#markdown-header-additional-information)
 - [Todo](#markdown-header-todo)

- - -

# Main Functionalities

 - CLI
 - Functionality B - **v1.0.8**
 - ~~Functionality C~~ - **v1.0.8**


## CLI

```
php bin/magento experius_missingtranslations:collect [-o|--output="..."] [-m|--magento] [-l|--locale="..."] [-d|--delimiter="..."] [-e|--enclosure="..."] [-s|--store="..."] [directory]
```

Use the command like this:

```
php bin/magento experius_missingtranslations:collect --output app/i18n/experius/missing/nl_NL.csv --magento --locale nl_NL
```

then edit the file, remove the suffig `missing` and eventually transform it to a language pack by adding a `language.xml` and a `registration.php`


## Missing Translations

Besides transforming the file to a language pack it is possible to add new translations through the admin interface, which can be found under Stores > Translations > Database / Inline only if you generated it to a file with the following filename a file in app/i18n/Vendor/missing/locale_code.csv . For example use:

```
php bin/magento experius_missingtranslations:collect --output app/i18n/experius/missing/nl_NL.csv --magento --locale nl_NL
```

# Additional information


# TODO:

 - ``--vendor`` param
 - ``--module`` param
