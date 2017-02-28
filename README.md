# Mage2 Module Experius MissingTranslations

Add a CLI command to Collect missing translations in specified folder or the entire Magento 2 Root and add Admin Grid to display and update database/inline translations (Stores > Translations > Database/Inline Translations)

``
php bin/magento experius_missingtranslations:collect [-o|--output="..."] [-m|--magento] [-l|--locale="..."] [-s|--store="..."] [directory]
``

Use the command like this:

``
php bin/magento experius_missingtranslations:collect --output app/i18n/Experius/nl_NL/nl_NL.csv --magento --locale nl_NL
``

then edit the file and use the following command to merge the missing translations in the correct file with the core Magento 2 CLI command:

``
php bin/magento i18n:pack --mode merge app/i18n/Experius/nl_NL/nl_NL.csv nl_NL
``

If you generate a file in app/i18n/Vendor/locale_code/locale_code.csv it is possible to add new translations through the admin interface, which can be found under Stores > Translations > Database / Inline. For example use:

``
php bin/magento experius_missingtranslations:collect --output app/i18n/Experius/nl_NL/nl_NL.csv --magento --locale nl_NL
``


TO DO:

 - ``--vendor`` param
 - ``--module`` param
