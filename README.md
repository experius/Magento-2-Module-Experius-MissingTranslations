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

Besides transforming the file to a language pack it is possible to add new translations through the admin interface, which can be found under `Stores > Translations > Database / Inline`

**only if you generated it to a file with the following filename a file in app/i18n/Vendor/missing/locale_code.csv.** 

For example generate missing nl_NL strings:

```
php bin/magento experius_missingtranslations:collect --output app/i18n/experius/missing/nl_NL.csv --magento --locale nl_NL
```

## Translations to database

In addition to gathering missing translations this module also supports database translation (formerly known as inline translation)

This makes it possible for merchants to edit any translation in the adminpanel of Magento 2.

Gathering the translations happens nightly.
03:13 AM server time, all existing csv translations are added to the database.
03:23 AM server time, all missing translations found are added to the database.
This is done on global scope for all locales that are used in atleast one storeview.

Manually gathering the translations (and adding them to the database) is possible.
This can be done by one of the following two Console commands:
```
php bin/magento experius_missingtranslations:existing-translations-to-database --global --locale nl_NL
```

```
php bin/magento experius_missingtranslations:missing-translations-to-database --global --locale nl_NL
```
Herein --global is defined to save the translations for any storeview with the specified locale

To specify a specific store_id add the store ID parameter (--store [store_id])
WARNING: This is not recommended unless translations differ for the same language in separate storeviews
Example:
```
php bin/magento experius_missingtranslations:addtodatabase --store 1 --locale nl_NL
```

# TODO

For missing translations
- Add --vendor parameter to missing translation Console command
- Add --module parameter to missing translation Console command

For database translations
- Add flag to translation table database (user_defined); if user edit's a translation, user_defined is flagged as true.
- Add --force update for every entry that is not flagged as user_defined; to update csv changes into database.
