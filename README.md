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

Gathering the translations happens nightly at 03:13 AM for the global scope.
Manually gathering the translations (and adding them to the database) is possible.
This can be done by using the following Console command:
```
php bin/magento experius_missingtranslations:addtodatabase --global --locale nl_NL
```
Herein --global is defined to save the translations for any storeview with the specified locale

To include missing translations add the --include-missing parameter.
This will only work if missing translations have been previously gathered.
```
php bin/magento experius_missingtranslations:addtodatabase --global --locale nl_NL --include-missing
```

To specify a specific store_id add the --store [store_id] parameter
NOTE: This is not recommended unless translations differ for the same language for each storeview
Example:
```
php bin/magento experius_missingtranslations:addtodatabase --store 1 --locale nl_NL
```


## Additional information

Nothing here at the moment

# TODO

For missing translations
- Add --vendor parameter to missing translation Console command
- Add --module parameter to missing translation Console command

For database translations
- Add flag to translation database; if user edit's a translation, user_defined is flagged as true.
- Add --force update for every entry that is not flagged as user_defined; to update csv changes into database.
