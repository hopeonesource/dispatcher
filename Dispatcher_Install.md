# Dispatcher Install

Here we will install our dependencies for the Dispatcher module!

## Pre-reqs

Checkout our [Drupal 8 install tutorial!](./Drupal8_install.md)

### SMS Requirements

Simply use `composer require drupal/sms drupal/sms_twilio` in your app directory to install the two dependencies for our work. 

First the SMS Framework. This contains sophisticated actions that will help our work, like queueing of messages and making using 'gateways' easier (more on this in a second). 

Second, Twilio SDK will be used as the 'gateway' linked to the SMS Framework. In short, a gateway in the sms framework context is a module that will handle interactions with a vendor (in our case Twilio!) like actually sending SMS messages.

### Extend

Now in the extend portion of the drupal site, you should see options for SMS Framework. If the options are greyed out, then something went wrong in the composer section.

Enable all fields in SMS Framework.

Secondly, enable the `Twilio for SMS Framework` under `Extend -> Other`.

**NOTE**: you will receive warnings about needing to enable Telephone and Dynamic Entity Reference, simply say yes and continue! 

### Configure - Add the Twilio Gateway

Right now we have setup the SMS Framework but we must hook the Twilio Gateway to it!

Go to `Configure -> SMS Framework -> Gateways`. Click `Add Gateway`. **MAKE SURE** that the `machine_name` of this gateway ends up being `twilio`. This is important for integrating with the Hope One Source Dispatcher module. Select Twilio from the Gateway drop down (if you do not see Twilio as a Gateway option, something went wrong when installing it).

Finally, save the gateway. Then if you refresh and scroll to the bottom, you will have a form to enter your Twilio account credentials. Talk to your Hope One Source contact to get access!

**SANITY CHECK**: You have now installed SMS Devel, a module that helps with testing the SMS Framework. Now we can make sure SMS Framework is working here! Go to `Configure -> Development -> Test SMS`. Text yourself!

### HOS Dispatcher - Install

Finally, we are ready to incorporate the Dispatcher.

There are many ways to do this, but the idea is simple. We need to add the code in the Dispatcher repo to the `/modules` folder in our project. 

An easy way is to download the source code as a zip: [https://github.com/hopeonesource/dispatcher/archive/master.zip](https://github.com/hopeonesource/dispatcher/archive/master.zip), unzip the folder on your laptop (should unzip to dispatcher-master), and move that folder to your Drupal 8 project's `/modules` folder. For example, my dispatcher was places in `/Users/ryanaubrey/Sites/lando-d8/modules`, where `lando-d8` was the project I created!

Now navigate in your Drupal site to `Extend` and you should see a new section called `HOS -> Hos sms dispatcher`! Select this option and install it to enable the module.

### HOS Dispatcher - Test

Great, now let's test the dispatcher. Simply open the file `dispatcher.module` from the dispatcher folder we just added. Replace the `$$$$$$` with your number, just the ten digit number without spaces, parenthesis, dashes, etc. will work great.

Save that file. Once the changes are saved, we can go to `Configure -> Cron` and hit the button `Run Cron`. This will execute the code in the `dispatcher.module` which will send an SMS message to your number!