SetUp Notes:

For more info view bindinator/documentation/index.php

Below is the default install procedure:

   1. Unpack the tgz to a location of your choice (tar -xvzpf bindinator-VERSION.tgz)
   2. From the command line run (as root or use sudo)
      - cd bindinator; sudo php ./install.php
   3. Follow the on screen prompts and fill in the data asked for.
      - Information you will need is:
      -- webroot path (where you would like the php files etc. to live),
      -- Bindinator Data dir (default is /var/bindinator),
      -- MYSQL username, password and db name (if your DB Type needs that)
      -- Apache User (user who apache runs as).
   4. Configure Apache AUTH to allow access to the files / virtualhost etc. - At least BASIC auth is suggested as the logged in name is used for logging as well as your use for access control.
   5. [OPTIONAL] If you wish Bindinator to do more than generate the files you will need to set up SSH key auth between the user apache runs as and a user on the remote system that can RNDC and write over existing zone files on the DNS server. You will also need to edit the updateDNS.bash script in the utilities folder.

