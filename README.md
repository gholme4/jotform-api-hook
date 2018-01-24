
# Jotform API Voyager Hook

This is a hook for voyager that integrates with a Jotform account to view form data


## Installing the hook

You can use the artisan command below to install this hook

```bash
php artisan hook:install jotform-api-hook
```

## Configuration
Go to the admin tab on the settings page and enter your Jotform API key in the `Jotform API Key` field.


## API

#### Filtering Excel exports
When exporting form submissions, you can make modifications to the spreadsheet object before it is exported via a callback. The callback accepts the spreadsheet object, an array of data that populates the spreadsheet, and the corresponding form's title. Sample code is below:

```
use JotformApiHook\JotformApiHookServiceProvider;

...

if (class_exists('\JotformApiHook\JotformApiHookServiceProvider'))
{

    JotformApiHookServiceProvider::filterExportedSpreadsheet(function($spreadsheet, $sheetData, $formTitle) {
    	
    	// Logic to change spreadsheet data ...

        return $spreadsheet;
    });
}

...

```