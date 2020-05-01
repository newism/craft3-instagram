# Instagram plugin for Craft CMS 3.x

Instagram plugin to connect elements to the Instagram Basic API via access tokens and the basic display api.

## Requirements

This plugin requires Craft CMS 3.4.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project: `cd /path/to/project`
2. Then tell Composer to load the plugin: `composer require newism/craft3-instagram`
3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Instagram.

## Instagram Plugin Overview

This plugin creates a new field type that generates an Instagram API access token which allows you to query for an Instagram users media.

This plugin is different from other Instagram plugins becuase it:

* Uses the (https://developers.facebook.com/docs/instagram-basic-display-api)[Instagram Basic Display API] to fetch user media
* Is implemented as a field type, not a global setting.

### Why a fieldtype? 

Fieldtypes can be assigned to any element including Entries, Globals, Users. This gives you multiple use cases eg:

* Entries: Allow business listings entries to connect their own Instagram account
* Globals: Create a global field and connect your site Instagram account
* Users: Allow users to connect their own instagram account

## Configuring Instagram Plugin

To configure this plugin you'll need the 'OAuth Redirect URI
' and 'Deauthorize Callback URL' found in the plugin settings. 
 
Next follow the first three steps of the [Getting Started documention](https://developers.facebook.com/docs/instagram-basic-display-api/getting-started):

1. Create a Facebook App
2. Configure Instagram Basic Display
3. Add an Instagram Test User

Once you've created the Facebook App you will be provided with a App Id and App Secret. Enter these values in the plugin settings. Note: It's a good idea to store these values as [environment configuration](https://docs.craftcms.com/v3/config/environments.html).

## Using Instagram

### Creating an Access Token

Once you've configured the plugin you'll need to create a new field and assign it to an Element. To test I recommend creating a new 'Instagram' field and assign it to a Global Set.

Edit the Global set and click "Connect Instagram" in the field you created. Make sure the user you're trying to connect is one of the testers setup in Step 3. of the getting started documentation. 

If all goes well you should be asked to authorise your Facebook App, a long life access token will be created and displayed in your custom field.

Save the global set.

## Fetching user media

Once you have a token you can fetch the connected users media. In your template paste the following (change `entry.instagramToken` to the name of your global field.

    {# Check if a token is set #}
    {% if entry.instagramToken %}
        {# Fetch the user media #}
        {% set userMedia = craft.instagram.fetchUserMedia(entry.instagramToken) %}
        {# Check userMedia was returned #}
        {% if userMedia.data|default() | length %}
            {# Loop over the user media #}
            {% for entry in userMedia.data %}
                {# Output the image #} 
                <img src="{{ entry.media_url }}" alt="{{ entry.caption|default() }}" />
            {% endfor %}
        {% endif %}
    {% endif %}

The output of `craft.instagram.fetchUserMedia(entry.instagramToken)` matches the instagram API: 

    {
      "data": [
        {
          "id": "17895695668004550",
          "caption": ""
        },
        {
          "id": "17899305451014820",
          "caption": ""
        },
        {
          "id": "17896450804038745",
          "caption": ""
        },
        {
          "id": "17881042411086627",
          "caption": ""
        }
      ],
      "paging": {
        "cursors": {
          "after": "MTAxN...",
          "before": "NDMyN..."
          },
        "next": "https://graph.faceb..."
      }
    }
    
### Options

`craft.instagram.fetchUserMedia(token, [options])` takes two parameters:

1. token (required): the access token saved in the field
2. options (optional): an array with two possible keys:
    * cache: the number of seconds to cache the request. The default value is `null` (no caching). Following Yii's standards `0` is infinity.
    * fields: a comma delimited list of fields. The default value is `caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username,children`.
    
The fields option can contain any of the following:

| Field | Description |
| :---- | :---------- |
| caption | The Media's caption text. Not returnable for Media in albums. |
| id | The Media's ID. |
| media_type | The Media's type. Can be `IMAGE`, `VIDEO`, or `CAROUSEL_ALBUM`. |
| media_url | The Media's URL. |
| permalink | The Media's permanent URL. |
| thumbnail_url | The Media's thumbnail image URL. Only available on VIDEO Media. |
| timestamp | The Media's publish date in ISO 8601 format. |
| username | The Media owner's username. |
| children | The children if `media_type` is `CAROUSEL_ALBUM`

### Caching

It's recommend that you wrap the `craft.instagram.fetchUserMedia()` method in a craft {% cache %} tag or use some other type of template caching like Blitz.

## Instagram Roadmap

Some things to do, and ideas for potential features:

* [ ] Release it
* [ ] Implement deauthorize callbacks
* [ ] Check PHP 7.3 & 7.2 compatibility

Brought to you by [Leevi Graham](https://newism.com.au)
