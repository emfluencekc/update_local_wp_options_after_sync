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
LOCAL_PATH="Users/local_user/your_lando_project.wordpress"
LOCAL_DB_NAME="wordpress"
LOCAL_DB_USER="wordpress"
LOCAL_DB_PASSWORD="wordpress"
LOCAL_DB_HOST="127.0.0.1"
LOCAL_PORT="####" # for example "52005"

# Misc variables for line breaks and color
LINE="\n#############"
COLOR_YELLOW='\033[0;33m'
COLOR_RESET='\033[0m'

# SSH into remote server and archive database
ssh $REMOTE_SERVER "cd $REMOTE_PATH && 
    mysqldump -u $REMOTE_USER_NAME --password=\"$REMOTE_MYSQL_PASSWORD\" -h $REMOTE_HOST_NAME --port=$REMOTE_PORT $REMOTE_DB_NAME > $FILE_NAME";

echo "${COLOR_YELLOW}$LINE\nArchived database${COLOR_RESET}\n";

scp $REMOTE_SERVER:$REMOTE_PATH/$FILE_NAME .;

echo "${COLOR_YELLOW}$LINE\nCopied $FILE_NAME from $REMOTE_SITE_NAME to $LOCAL_PATH${COLOR_RESET}\n";

ssh $REMOTE_SERVER "cd $REMOTE_PATH && rm $FILE_NAME";

echo "${COLOR_YELLOW}$LINE\nDeleted $FILE_NAME from $REMOTE_SITE_NAME${COLOR_RESET}\n";

lando db-import $FILE_NAME;

echo "${COLOR_YELLOW}$LINE\nImported $FILE_NAME into local database${COLOR_RESET}\n";

# Download the script to update wp_options table, change permissions, and navigate to it
git clone https://github.com/emfluencekc/update_local_wp_options_after_sync.git;
chmod -R 755 update_local_wp_options_after_sync;
cd update_local_wp_options_after_sync;

# Run script to update wp_options table
php update_local_wp_options_after_sync.php --from=$REMOTE_SITE_NAME --to=$LOCAL_SITE_NAME --db_name=$LOCAL_DB_NAME --db_user=$LOCAL_DB_USER --db_password=$LOCAL_DB_PASSWORD --db_host=$LOCAL_DB_HOST --db_port=$LOCAL_PORT

# Go back a level and delete script that was just downloaded
cd ..;
rm -r update_local_wp_options_after_sync;

echo "${COLOR_YELLOW}\n$LINE\nUpdated the wp_options table by replacing all references of $REMOTE_SITE_NAME with $LOCAL_SITE_NAME${COLOR_RESET}\n";
```
