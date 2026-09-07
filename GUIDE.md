This document provides a strictly granular manual to guide you through installing, testing, and deploying the Remita Checkout plugin for Craft Commerce.

---

Step 1: Prerequisites & Requirements

- PHP: 8.1 or higher
- Composer: 2.x
- Craft CMS: 4.0.0+ or 5.0.0+
- Craft Commerce: 4.0.0+ or 5.0.0+
- A valid Remita API Secret Key and Base URL.

---

Step 2: Installing the Plugin Locally for Testing

1. Open your main Craft CMS project directory in your terminal.
2. Place this plugin folder (which contains this guide, the `src` directory, and `composer.json`) somewhere accessible. For example, place it in a `plugins/craft-remita-payment` directory inside your Craft project.
3. Open your main Craft CMS project's `composer.json` and add the local path to the `repositories` array (so Composer knows where to find the local plugin before it is published to the internet):
```json
    "repositories": [
        {
            "type": "path",
            "url": "plugins/craft-remita-payment",
            "options": {
                "symlink": true
            }
        }
    ]
```
4. Run the following command to require the plugin locally:
```bash
composer require remita/craft-remita-payment:@dev
```
5. Install the plugin into Craft CMS via the command line (or you can install it via the Control Panel Settings > Plugins):
```bash
php craft plugin/install remita-payment
```

---

Step 3: Configuration & Testing

1. Access your Craft CMS Control Panel and navigate to Commerce > System Settings > Gateways.
2. Click New Gateway and select Remita Checkout from the dropdown list.
3. Input your secretKey exactly as provided by Remita.
4. Input your baseUrl exactly as provided (e.g., your ngrok URL for local testing or the Remita QA/Production API URL).
5. Click Save.
6. To test the integration, navigate to your Craft Commerce frontend storefront.
7. Add an item to your cart and proceed to checkout.
8. When you select Remita Checkout and click Pay, Craft Commerce will seamlessly redirect you to the secure Remita payment page. 
9. Complete the payment. You will automatically bounce straight back to your storefront, and the plugin will verify the transaction natively with the Remita API!

---

Step 4: Publishing and Deployment

Method A: Local/Private Deployment
For custom implementations where you do not want to publish the plugin to the public Craft Plugin Store:
1. Commit the plugin repository code to a private Git repository (e.g. GitLab or a GitHub private repo).
2. In the target Craft site's `composer.json`, add the repository under the repositories block:
```json
   "repositories": [
       {
           "type": "vcs",
           "url": "git@github.com:your-organization/craft-remita-payment.git"
       }
   ]
```
3. Run the composer require command:
```bash
composer require remita/craft-remita-payment:dev-main
```
4. Access the control panel and install the plugin under Settings > Plugins.

Method B: Public Release via Packagist and Craft Plugin Store
To publish the plugin so that any Craft CMS developer can install it directly from their Control Panel:
1. Commit and push all stable changes to your public GitHub repository.
2. Tag a stable release using git (e.g. `git tag -a v1.0.0 -m "Initial release"`).
3. Submit your repository URL to Packagist.org.
4. Log into the Craft Console Developer Portal, click Add Plugin, input your repository URL (`remita/craft-remita-payment`), and submit it for review. 
5. Once approved by the Craft CMS team, your plugin will be listed publicly on the Plugin Store and can be installed by anyone using a simple `composer require remita/craft-remita-payment` command!
