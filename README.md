# Gravity-Forms-tracking
Force GF forms to use AJAX and pass data to Google Tag Manager for tracking.

## How it works
1. Manipulate AJAX value for $form_args with 'gform_form_args' filter.
2. Add GTML traching script to AJAX forms $confirmation 'gform_confirmation'.

## Usage
Download the plugin and activate it.

## Translating with WP Cli
1. Go to theme folder with SSH: `cd wp-content/plugins/avidly-gf-tracking`
2. Create or update POT file: `wp i18n make-pot ./ languages/avidly-gf-tracking.pot`
3. Open your PO file(s) with PoEdit & select from toolbar: Translation -> Update from POT file...
4. Make your translations changes & save files
