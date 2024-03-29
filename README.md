# update_local_wp_options_after_sync
Script that updates wp_options table of all references of production url to your local url

## Shell script that can archives prod sql, imports into local, and accesses this script to update wp-options table
Just change the remote and local variable name values.

```sh
#!/bin/sh

# Set remote variables
REMOTE_SITE_NAME="remote.com"
REMOTE_PATH="/home/remote/html"
REMOTE_SERVER="remote.myftpupload.com"
REMOTE_USER_NAME="remote_user"
REMOTE_MYSQL_PASSWORD="remote_pw"
REMOTE_HOST_NAME="remote_host_name.com"
REMOTE_PORT="####" # for example "3312"
REMOTE_DB_NAME="remote_db_name"
FILE_NAME=$(date '+%Y-%m-%d-%H-%M-%S'_archive.sql)

# Set local variables (can be found in lando info)
LOCAL_SITE_NAME="local.lndo.site"

# Misc variables for line breaks and color
LINE="\n#############"
COLOR_YELLOW='\033[0;33m'
COLOR_RESET='\033[0m'

# SSH into remote server and archive database
ssh $REMOTE_SERVER "cd $REMOTE_PATH && 
    mysqldump -u $REMOTE_USER_NAME --password=\"$REMOTE_MYSQL_PASSWORD\" -h $REMOTE_HOST_NAME --port=$REMOTE_PORT $REMOTE_DB_NAME > $FILE_NAME";

echo "${COLOR_YELLOW}$LINE\nArchived database on $REMOTE_SITE_NAME${COLOR_RESET}\n";

scp $REMOTE_SERVER:$REMOTE_PATH/$FILE_NAME .

echo "${COLOR_YELLOW}$LINE\nCopied $FILE_NAME from $REMOTE_SITE_NAME to $LOCAL_SITE_NAME${COLOR_RESET}\n";

ssh $REMOTE_SERVER "cd $REMOTE_PATH && rm $FILE_NAME";

echo "${COLOR_YELLOW}$LINE\nDeleted $FILE_NAME from $REMOTE_SITE_NAME${COLOR_RESET}\n";

lando wp db import $FILE_NAME;

echo "${COLOR_YELLOW}$LINE\nImported $FILE_NAME into $LOCAL_SITE_NAME database${COLOR_RESET}\n";

lando wp search-replace --all-tables $REMOTE_SITE_NAME $LOCAL_SITE_NAME

echo "${COLOR_YELLOW}\n$LINE\nUpdated the wp_options table by replacing all references of $REMOTE_SITE_NAME with $LOCAL_SITE_NAME${COLOR_RESET}\n";
```
