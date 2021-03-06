![Advanced](https://github.com/DenzelCode/Advanced/blob/master/project/public/assets/images/advanced.png?raw=true)

## What's Advanced?

Advanced Micro Framework - Create more by doing less

Advanced Micro Framework is a PHP Object Oriented framework that make your life easier, advanced provides you a whole bunch of functionalities that you will not have the need of write a lot of code. It is already predefined so you only have to create your project and use the internal libraries of Advanced such as the authentications (login/register), In-code routing by controllers, SQL automatization (generate queries and execute them in a safe way with just PHP code), and more.

Get started with Advanced and see what else it can provide you.

[Get started](https://github.com/DenzelCode/Advanced/wiki/Get-started)

## Discussion/Help

-   [Discord](https://discord.gg/T7PsB5z)
-   [StackOverflow](https://stackoverflow.com/tags/advanced-framework)

## Sample projects

[Advanced-Samples](https://github.com/DenzelCode/Advanced-Samples)

## Documentation

[Click here to go to the documentation](https://github.com/DenzelCode/Advanced/wiki/Get-started)

## Installation

Composer:

```
mkdir new-folder

cd new-folder

composer create-project denzelcode/advanced .

cd project/public

php -S localhost:8000
```

**THIS IS IMPORTANT**
If Advanced file system is not working properly remember to add writing and reading permissions to the directory where you want to install the framework!

Linux/Mac OS (Unix):

```
sudo chmod -R 777 new-folder
```

Windows:

```
CACLS new-folder /e /p Everyone:R
CACLS new-folder /e /p Everyone:W
```

## Requirements

-   PHP 7.2.0
-   Apache Server

Xampp [Windows](https://www.apachefriends.org/xampp-files/7.4.2/xampp-windows-x64-7.4.2-0-VC15-installer.exe) | [Linux](https://www.apachefriends.org/xampp-files/7.4.2/xampp-linux-x64-7.4.2-0-installer.run) | [OS X](https://www.apachefriends.org/xampp-files/7.4.2/xampp-osx-7.4.2-0-vm.dmg), **IIS** or **Hosting**

Postdata: To use **IIS** you are required to set the /project/public path as the root directory.

## Contributors

-   Denzel Code (Advanced main developer)
-   Soull Darknezz
-   mamazu

## Licensing information

This project is licensed under LGPL-3.0. Please see the [LICENSE](/LICENSE) file for details.

## Donations

-   [PayPal](https://paypal.me/DenzelGiraldo)

## Contribution

Anyone who wants to contribute to the project can do it, this is an independent project that plans to become a community.
