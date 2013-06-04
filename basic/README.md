

# Basic Example
Here's a basic example on how to subscribe people via ajax and populate them to your Sendicate account.


## Configure
You'll need to set up two things before test this code:
- List ID: Add your List ID in subscription.php file, line 21.
- API token: Add your in subscription.php line 24.

### API Token
You will find your API token within your Sendicate Account, under: Manage / Account / API Token.


## Required fields
The only field required is the Email filed. If you don't fill in the name, 'Guest Subscribed' will be used.
You can change this in subscription.php line 31.
Default Subscriber name is:
	$_subscriber_name = 'Guest Subscriber';


## Adding new fields
In order to add more fields you'll need to change:
- index.html: to add the new field.
- js/post-handler.js: to retrieve the value and send it via ajax.
- subscription.php: to ask for that field and send it to Sendicate's API.

Code is commented to give an idea of what you need to do to achieve this. Later, some examples using dropdowns will be published.


## Debug
By default API won't be logged.
If you want to enable this, go to subscription.php file at line 91 and set to true that parameter.
Like so:
	$_sendicate->setDebug(true);


## Error loggin
This example setups a couple directives to log errors within the project root folder under 'errors.log' file.
It's a plain text file, you can open this file with any text processor software.
If you don't need this, go to subscription.php file and remove/comment lines 8-14.