## 3.1.0 (2020-10-22)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/3.1.0)

*  [FEATURE] [BACI-154] Added strict_types=1 and added License *(Lewis Voncken)*


## 3.0.1 (2020-10-21)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/3.0.1)

*  [REFACTOR] [BACI-157] Removed setup_version from module.xml *(Lewis Voncken)*


## 3.0.0 (2020-10-21)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/3.0.0)

*  [FEATURE] [BACI-157] Converted Schema Setup Scripts to db_schema.xml *(Lewis Voncken)*


## 2.1.13 (2020-10-15)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.13)

*  [REFACTOR] [BACI-123] solved errors based on php code sniffer *(Lewis Voncken)*


## 2.1.12 (2020-10-15)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.12)

*  [REFACTOR] Removed unused code or added suppression when unused code is allowed and applied phpcs fixes *(Lewis Voncken)*


## 2.1.11 (2020-03-18)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.11)

*  [BUGFIX] - Check file exists before processing *(Ruben Panis)*


## 2.1.10 (2019-11-26)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.10)

*  Make sure JS translations keep working after import *(Arnoud Beekman)*


## 2.1.9 (2019-10-07)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.9)

*  [BUGFIX] "context" argument breaks adminhtml page in Magento 2.3.3. *(Boris van Katwijk)*


## 2.1.8 (2019-05-27)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.8)

*  [BUGFIX] Fixed module not working on adminurl's other than 'storemanager' *(Rens Wolters)*


## 2.1.7 (2019-01-11)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.7)

*  [TASK] Added additional Filters and added a getter so a Plugin can be written *(Lewis Voncken)*
*  [TASK] Refine Filter *(Lewis Voncken)*
*  [TASK] Added custom admin url support *(Lewis Voncken)*


## 2.1.6 (2018-12-05)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.6)

*  [BUGFIX] Fixed acl not being properly defined for controllers *(René Schep)*


## 2.1.5 (2018-11-29)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.5)

*  Added check function createPhrase *(thokiller)*
*  added instance check Phrase *(thokiller)*
*  Fixed indent on file Factory.php *(thokiller)*
*  fixed indent on generator.php *(thokiller)*


## 2.1.4 (2018-06-18)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.4)

*  [TASK] added dependency sequence for Magento_Translation in module.xml *(Jeroen Coppelmans)*
*  [TASK] added Experius_Core to module.xml sequence as well *(Jeroen Coppelmans)*
*  [TASK] removed Experius Core from sequence in module.xml *(Jeroen Coppelmans)*


## 2.1.3 (2018-05-15)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.3)

*  [TASK] Applied CodeQuality fixes *(Lewis Voncken)*
*  [BUGFIX] Solved incorrect dependency injection *(Lewis Voncken)*


## 2.1.2 (2018-05-02)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.2)

*  [DOCS] Updated the README.md with clear information *(Lewis Voncken)*


## 2.1.1 (2018-04-09)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.1)

*  [TASK] [issue-3] Now the folder is automatically created *(Lewis Voncken)*


## 2.1.0 (2018-02-26)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.1.0)

*  [FEATURE] Added system xml (configuration) to enable nightly translation gathering cronjobs. Default is off, must be enabled to gather new translations on global scope every night. *(Boris van Katwijk)*
*  [FEATURE] Added update function of all js-translation.json files. This function is triggered for all themes with a specific locale, on saving a translation in the missing translations section. This enables real-time updates of javascript translations for each locale active in a Magento 2 installation. *(Boris van Katwijk)*
*  [BUGFIX] If file exists was not being checked in putting js-translation.json file on translation save. Deployed version number was not being updated resulting in translations not coming through in the frontend with all cache one (full_page). *(Boris van Katwijk)*


## 2.0.0 (2018-02-21)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/2.0.0)

*  [FEATURE] - Added command to add all csv translated strings to database for easy editing. Including option to include previously collected missing translations *(Ruben Panis)*
*  [FEATURE] - Enabled translation grid inline editing for faster translating *(Ruben Panis)*
*  [FEATURE] - Added column to check if translation differs from original string *(Ruben Panis)*
*  [FEATURE] Added "different" as database column in the translation table to add filtering / sorting in adminhtml. Added minor messaging for clear console script progression. *(Boris van Katwijk)*
*  [TASK] Syntax / licence only commit. *(Boris van Katwijk)*
*  [FEATURE] Cronjob added and small restructure *(Boris van Katwijk)*
*  [TASK] Updated readme. *(Boris van Katwijk)*
*  [FEATURE] Second pass for database style translations for merchant *(Boris van Katwijk)*
*  [BUGFIX] Removed not in use observers. *(Boris van Katwijk)*
*  [BUGFIX] Locale was changed on translation save, resolved this issue. *(Boris van Katwijk)*


## 1.6.0 (2018-01-30)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.6.0)

*  [TASK] Disabled the Translate String Button *(Lewis Voncken)*


## 1.5.0 (2018-01-30)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.5.0)

*  [TASK] Updated labels and added notices *(Lewis Voncken)*


## 1.4.0 (2018-01-30)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.4.0)

*  [FEATURE] Only Display Available Languages *(Lewis Voncken)*
*  [TASK] Removed console.log *(Lewis Voncken)*
*  [TASK] Updated the README.md *(Lewis Voncken)*


## 1.3.10 (2017-10-06)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.10)

*  Update README.md *(Mr. Lewis)*


## 1.3.9 (2017-06-12)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.9)

*  [BUGFIX] Solved problem with new string without missing strings file *(Lewis Voncken)*


## 1.3.8 (2017-06-06)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.8)

*  [TASK] README.md update *(Lewis Voncken)*


## 1.3.7 (2017-06-06)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.7)

*  [BUGFIX] Solved problem with empty translations in frontend and added option to translate string which is already translated *(Lewis Voncken)*


## 1.3.6 (2017-03-23)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.6)

*  [TASK] Added custom delimiter for console command *(Lewis Voncken)*


## 1.3.5 (2017-03-14)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.5)

*  [TASK] Updated README.md and change vendor to lower case *(Lewis Voncken)*


## 1.3.4 (2017-03-14)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.4)

*  [TASK] changed the missing translations csv file *(Lewis Voncken)*


## 1.3.3 (2017-03-07)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.3)

*  [TASK] Exclude UnitTests and magento2-base/dev *(Lewis Voncken)*


## 1.3.2 (2017-03-07)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.2)

*  [BUGFIX] Solved compile error Extra parameters passed to parent construct: $coreRegistry. *(Lewis Voncken)*


## 1.3.1 (2017-02-28)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.1)

*  [TASK] Columns update and Bugfix Edit Save *(Lewis Voncken)*


## 1.3.0 (2017-02-28)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.3.0)

*  [FEATURE] Add Missing Translation *(Lewis Voncken)*
*  [FEATURE] Translate a Missing Translation *(Lewis Voncken)*
*  [TASK] Updated the README.md for the new feature *(Lewis Voncken)*


## 1.2.0 (2017-02-17)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.2.0)

*  [FEATURE] Added Database translations UI compnonent *(Lewis Voncken)*
*  [TASK] Updated the setActiveMenu item *(Lewis Voncken)*
*  [TASK] setActiveMenu item for grid overview *(Lewis Voncken)*
*  [TASK] Updated README.md *(Lewis Voncken)*


## 1.1.2 (2017-02-17)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.1.2)

*  [TASK] Added extra todo to the readme *(Lewis Voncken)*


## 1.1.1 (2017-02-17)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.1.1)

*  [TASK] Updated the README.md for correct uses with packs *(Lewis Voncken)*


## 1.1.0 (2017-02-16)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.1.0)

*  [TASK] Changed the License *(Lewis Voncken)*
*  [TASK] Changed logic to search by locale code and store id is optional for specific inline translations *(Lewis Voncken)*
*  [TASK] Updated the README with updated CLI command *(Lewis Voncken)*
*  [TASK] Reset if statement of foundTranslation check *(Lewis Voncken)*


## 1.0.0 (2017-02-16)

[View Release](git@github.com:experius/Magento-2-Module-Experius-MissingTranslations.git/commits/tag/1.0.0)

*  [TASK] Initial Commit *(Lewis Voncken)*
*  [TASK] Refactor - removed Objectmanager uses from the Console Command *(Lewis Voncken)*


