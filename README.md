# Mage2 Module Experius MissingTranslations

Add a CLI command to Collect missing translations in specified folder or the entire Magento 2 Root and add Admin Grid to display and update database/inline translations (Stores > Translations > Database/Inline Translations)

``
php bin/magento experius_missingtranslations:collect [-o|--output="..."] [-m|--magento] [-l|--locale="..."] [-d|--delimiter="..."] [-e|--enclosure="..."] [-s|--store="..."] [directory]
``

Use the command like this:

``
php bin/magento experius_missingtranslations:collect --output app/i18n/experius/nl_nl/nl_NL-missing.csv --magento --locale nl_NL
``

then edit the file, remove the suffig `missing` and eventually transform it to a language pack by adding a `language.xml` and a `registration.php`


Besides transforming the file to a language pack it is possible to add new translations through the admin interface, which can be found under Stores > Translations > Database / Inline only if you generated it to a file with the following filename a file in app/i18n/Vendor/locale_code/locale_code.csv . For example use:

``
php bin/magento experius_missingtranslations:collect --output app/i18n/experius/nl_nl/nl_NL.csv --magento --locale nl_NL
``


TO DO:

 - ``--vendor`` param
 - ``--module`` param
