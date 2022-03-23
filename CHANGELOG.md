# v1.3.3
## 03/22/22

1. [](#improved)
    * Custom HTTP Status code [#28](https://github.com/getgrav/grav-plugin-maintenance/pull/28)
    * Updated language codes to support Italian
1. [](#bugfix)
    * if auto-escape twig template [#36](https://github.com/getgrav/grav-plugin-maintenance/issues/36)

# v1.3.2
## 12/10/2018

1. [](#bugfix)
    * if `allow_login` is `false` don't allow a logged in user to skip maintenance page

# v1.3.1
## 02/17/2017

1. [](#new)
    * Added spanish translation
1. [](#bugfix)
    * Hide the forgot button [#17](https://github.com/getgrav/grav-plugin-maintenance/issues/17)

# v1.3.0
## 01/24/2017

1. [](#new)
    * Added event `getMaintenancePage` to allow theme or plugin to override maintenance page
    * Added event `onMaintenancePage` which replaces `onPageInitialized` event
1. [](#bugfix)
    * Fixed plugin not working with Twig setting `Autoescape variables` set to `Yes`
    * Fixed `Login access` setting having no effect
    * Prevent other plugins from overriding maintenance page by blocking `onPage(s)Initialized` events

# v1.2.1
## 09/06/2016

1. [](#improved)
    * Added Romanian translation
1. [](#bugfix)
    * Fix Login form not appearing [#11](https://github.com/getgrav/grav-plugin-maintenance/issues/11)

# v1.2.0
## 07/14/2016

1. [](#new)
    * Allow translation of the maintenance login form

# v1.1.0
## 05/03/2016

1. [](#new)
    * Added `zh-hk`, `zh-cn`, and `de` translations

# v1.0.2
## 01/06/2016

1. [](#bugfix)
    * Fixed the default placeholder for the maintenance page route in Admin
1. [](#new)
    * Added `fr` translation

# v1.0.1
## 09/01/2015

1. [](#new)
    * Added `login` plugin dependency

# v1.0.0
## 09/01/2015

1. [](#new)
    * ChangeLog started...
