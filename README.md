1. Prerequisite
   1. Installed Liam3 (normally on an other server) - https://github.com/BPMspaceUG/liam3.git
   2. URL and Port of the Liam3 installation
   3. Generated machine token to access
   4. Git client installed on machine
2. TODO
   1. "git clone https://github.com/BPMspaceUG/liam3_client.git"
   2. copy /inc/api.EXAMPLE_secret.inc.php to /inc/api.secret.inc.php and set: 
        1. $headers[] = 'Cookie: token=[ENTER TOKEN HERE]';
        2. $url = "[ENTER URL HERE]";
     
## Important notes for Sub-Systems
The entry-URL to Authenticate is https://URL-TO-LIAM3-Client/LIAM3_Client_login.php
This is important and has to be defined in every Sub-System.
For Example in the project SQMS, the config should look like this:
```
define('API_URL_LIAM', 'http://localhost/liam3-client/LIAM3_Client_login.php'); // URL from Authentication-Service -> returns a JWT-Token
define('AUTH_KEY', 'xxxxxxsecretkeyxxxxxx'); // Shared AuthKey which has to be known by the Authentication-Service
```
    
