#! /bin/bash
# A modification of Dean Clatworthy's deploy script as found here: https://github.com/deanc/wordpress-plugin-git-svn
# The difference is that this script lives in the plugin's git repo & doesn't require an existing SVN repo.
 
# main config
PLUGINSLUG="retreat-booking-guru-connect/"
CURRENTDIR=`pwd`
MAINFILE="rs-connect.php" # this should be the name of your main php file in the wordpress plugin
 
# git config
GITPATH="$CURRENTDIR/" # this file should be in the base of your git repository
 
# svn config
SVNPATH="/tmp/$PLUGINSLUG" # path to a temp SVN repo. No trailing slash required and don't add trunk.
SVNURL="http://plugins.svn.wordpress.org/$PLUGINSLUG/" # Remote SVN repo on wordpress.org, with no trailing slash

# Let's begin...
echo ".........................................."
echo 
echo "Preparing to deploy wordpress plugin"
echo "MAKE SURE YOU ARE RUNNING THIS FROM WITHIN THE DOCKER CONTAINER AND ARE IN THE CORRECT FOLDER"
echo
echo ".........................................."
echo 

echo -e "Enter your wordpress username: \c"
read SVNUSER
 
# Check version in readme.txt is the same as plugin file after translating both to unix line breaks to work around grep's failure to identify mac line breaks
NEWVERSION1=`grep "^Stable tag:" $GITPATH/readme.txt | awk -F' ' '{print $NF}'`
echo "readme.txt version: $NEWVERSION1"
echo "$GITPATH$MAINFILE"
NEWVERSION2=`grep "Version:" $GITPATH$MAINFILE | awk -F' ' '{print $NF}'`
echo "$MAINFILE version: $NEWVERSION2"

pwd
echo $GITPATH
cd $GITPATH
echo -e "Enter a commit message for this new version: \c"
read COMMITMSG
 
echo 
echo "Creating local copy of SVN repo ..."
svn co $SVNURL $SVNPATH
 
echo "Exporting the HEAD of master from git to the trunk of SVN"
echo $SVNPATH

echo "Ignoring github specific files and deployment script"
svn propset svn:ignore "deploy.sh
README.md
.git
.gitignore" "$SVNPATH/trunk/"
 
echo "Changing directory to SVN and committing to trunk"
cp -R $GITPATH/. $SVNPATH/trunk/
cd $SVNPATH/trunk/

# Add all new files that are not set to be ignored
echo "Adding any newly created files not set to be ignored. It is safe to disregard any 'svn add' parameter errors if no new files were created"
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add

echo "Committing changes to SVN repository"
svn commit --username=$SVNUSER -m "$COMMITMSG"

echo "Removing temporary directory $SVNPATH"
rm -fr $SVNPATH/
 
echo "*** FIN ***"