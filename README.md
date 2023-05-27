# Best Betting Apps WordPress Plugin

The **Best Betting Apps** plugin is a WordPress plugin that allows you to display data from a JSON file using a shortcode. This plugin provides a convenient way to showcase a list of betting apps on your website. It includes features for sorting the apps based on different criteria and displaying relevant information such as bonuses, features, and play options.

## Installation

1. Download the plugin zip file from the [Best Betting Apps Plugin page](https://github.com/Li8ning/best-betting-apps).
2. Log in to your WordPress admin panel.
3. Go to **Plugins > Add New**.
4. Click on the **Upload Plugin** button and choose the downloaded zip file.
5. Click **Install Now** and then activate the plugin.

## Usage

The Best Betting Apps plugin provides a shortcode that you can use to display the list of apps. To use the shortcode, simply add `[best_betting_apps]` to any post, page, or widget where you want to display the apps list.

You can also customize the sorting order by providing the `sorting` attribute to the shortcode. The possible values for the `sorting` attribute are:

- `a` (default): Sorts the apps by natural order.
- `0`: Sorts the apps in ascending order based on their position.
- `1`: Sorts the apps in descending order based on their position.

Example usage: `[best_betting_apps sorting="1"]`

## Styling

The plugin includes a default stylesheet that you can modify to match your website's design. If you want to make changes to the stylesheet, you can find it at `assets/css/best-betting-apps.css` in the plugin directory.

Additionally, the plugin uses the Font Awesome library to display star ratings and icons. The Font Awesome stylesheet is loaded from a CDN.

## License
This plugin is licensed under the GPLv2 or later.

## JSON Data

The plugin retrieves the app data from a JSON file located at `data.json` in the plugin directory. The JSON data should have the following structure:

```json
{
  "toplists": [
    {
      "position": 1,
      "logo": "path/to/app-logo.png",
      "brand_id": "app-id",
      "info": {
        "bonus": "App Bonus",
        "rating": 4,
        "features": ["Feature 1", "Feature 2", "Feature 3"]
      },
      "play_url": "https://app-play-url.com",
      "terms_and_conditions": "App terms and conditions"
    },
  ]
}
