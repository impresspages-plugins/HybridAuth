# HybridAuth

Social login for ImpressPages. 

## Features

Supports login to ImpressPages via Facebook, Google and GitHub. Uses HybridAuth library from http://hybridauth.sourceforge.net/
Adds social login icons to user login and registration forms.

## Requirements

`HybridAuth` plugin requires `User` plugin installed. Download `User` plugin from https://github.com/impresspages-plugins/User.
To see the effects of the plugin you need to drag login, registration or logout widgets to your page.

## Installing

1. Upload `HybridAuth` directory to your website's `Plugin` directory.
2. Login to the administration area. Go to `Plugins` panel, locate `HybridAuth` plugin and click `activate` button.
3. Fill plugin configuration panel with your application OAuth IDs and secrets. See instructions below on how to do this.

### Setting up login via Facebook 
1. Login to Facebook, and open https://developers.facebook.com/apps page. 
2. Click `Create New App` button, and go thru the wizard to create your application. 
3. Copy APP ID and APP secret, open ImpressPages HybridAuth plugin settings, and paste them.
4. Click `Setttings` menu, `Add Platform` button, click `Website`. Specify your Site URL, e.g. http://www.example.com

### Setting up login via Google
1. Login to Google developer console https://console.developers.google.com/project
2. Click `CREATE PROJECT` button. Enter your project name and click `Create` button.
3. Click on your new project in project list.
3. Click `APIs & auth` menu, then click `Credentials` menu.
4. Click `CREATE NEW CLIENT ID` button.
5. Use `Web application` type. 
6. Type your ImpressPages website address in `AUTHORIZED JAVASCRIPT ORIGINS` field, e.g. http://www.example.com
7. In `AUTHORIZED REDIRECT URI` field, enter callback URL, e.g. http://www.example.com/?pa=HybridAuth.callback&hauth.done=Google
8. Copy `Client ID` and `Client secret` values, open ImpressPages HybridAuth plugin settings, and paste them.

### Setting up login via GitHub
1. Login to your GitHub account. Click `Account settings` button.
2. Click `Applications` menu, then click `Register New Application` button.
3. Enter `Application name` (e.g., `My App`) and `Homepage URL` (e.g., http://www.example.com)
4. Enter `Authorization callback URL`, e.g. http://www.example.com/?pa=HybridAuth.callback&hauth.done=GitHub
5. Click `Register Application` button.
6. Copy `Client ID` and `Client Secret` values, open ImpressPages HybridAuth plugin settings, and paste them.
7. To be able to login via GitHub, user must enter `Email (will be public)` field on GitHub account profile.

## Usage

As a widget:
1. Log in to ImpressPages administration page and edit your page content.
2. Drag `HybridAuth` widget from ImpressPages widgets toolbar to your page content area.
3. In the popup window, select the checkboxes to specify which icons are displayed.
3. Open your page as a user (e.g., by clicking `Preview` button).
4. Click on a social network icon to login to ImpressPages.

As a part of user registration form
1. Log in to ImpressPages administration page and edit the page content.
2. Drag `User login` or `User registration` widget to the content area. As a result, social icons are displayed.
3. Click on social icon to login.
