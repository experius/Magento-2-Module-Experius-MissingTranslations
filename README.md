Mage2 Module Experius MissingTranslations
====================

Add a CLI command to Collect missing translations in specified folder or the entire Magento 2 Root and add an Admin Grid to display and update or add database/inline translations `(Stores > Translations > Database/Inline Translations)`


   ``experius/module-missingtranslations``
   
 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Configuration](#markdown-header-configuration)
 - [Additional Information](#markdown-header-additional-information)
 - [Todo](#markdown-header-todo)

- - -

# Main Functionalities

 - Recommended Minimum Usage
 - Missing Translations
 - Translations to database (Existing and Missing)

## Recommended Minimum Usage

 - Use the command to generate the CSV File with Missing Translations for your Locales 
   - **CSV File for Missing Translations**
 - Use the command to manually collect and import Existing & Missing Translations for your Locales
   - **Manually - Collect and Import**
 - *Optional* Enable the Cronjobs in the configuration so the Database Translations will be up to date with New Modules
   - **Cronjob - Collect and Import** and **Configuration**


## Missing Translations
### CSV File for Missing Translations

It is possible to generate a CSV file which contains the original string and an empty string.

#### Command
This CSV file can be generated through the following command:

```
php bin/magento experius_missingtranslations:collect [-m|--magento] [-l|--locale="..."] [-s|--store="..."] [directory]
```

Use the command like this:

```
php bin/magento experius_missingtranslations:collect --magento --locale nl_NL
```

#### Usage 1 - Transform Fully Translated File to Language Pack

This file can be used by a **Translation Agency** to complete the translations for the webshop.

After the **Translation Agency** has fully translated the csv file it is possible to transform it to a language pack.

This can be done by removing the suffix `missing` and eventually transform it to a language pack by adding a `language.xml` and a `registration.php`


#### Usage 2 - Translate Missing Strings Through the Admin with the CSV File

Besides transforming the file to a language pack it is possible to add new translations through the admin interface, which can be found under `Stores > Translations > Database / Inline`

**only if you generated it to a file with the following filename a file in app/i18n/Vendor/missing/locale_code.csv. (Example:  app/i18n/Vendor/missing/nl_NL.csv.)** 


#### Usage 3 - Import the Missing Translations into the Database*
For this functionality see **Translations to database (Existing and Missing)**



## Translations to database (Existing and Missing)

In addition to gathering missing translations this module also supports database translation (formerly known as inline translate)

This makes it possible for merchants to edit any translation in the Magento Admin Panel.

### Cronjob - Collect and Import
Collecting and importing the translations happens nightly when it is enabled **by default this functionality is disabled**
This is done on global scope for all locales that are used in at least one storeview (based on the configured locales).

#### Existing CSV Translations
*03:13 AM server time, all existing csv translations are added to the database.*

This functionality Imports the Existing CSV Translations into the Database then the Original String will differ from the Translated String.


#### Missing Translations
*03:23 AM server time, all missing translations found are added to the database.*

This functionality Imports the Missing Translations into the Database then the Original String will be equal to the Translated String.


### Manually - Collect and Import
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

# Configuration

 - Define Vendor which is used for generating the Missing Translations CSV Files
   - `(Stores > Settings > Configuration > General > General > Locale Options > Language vendor for missing translations)`
 - Enable the Collect and Import for Existing Translations
   - `(Stores > Settings > Configuration > General > General > Locale Options > Existing translations cron enabled)`
 - Enable the Collect and Import for Missing Translations
   - `(Stores > Settings > Configuration > General > General > Locale Options > Missing translations cron enabled)`

# TODO

For database translations
- Add flag to translation table database (user_defined); if user edit's a translation, user_defined is flagged as true.
- Add --force update for every entry that is not flagged as user_defined; to update csv changes into database.
