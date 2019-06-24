#
# SPF CHECKER

SPF CHECKER it a very simple application that scan domains and save their spf into a file.

FIRST YOU NEED TO INSTALL THIS COMPONENT OR NOTHING WILL WORK

**yum -y install bind-utils;**

after that place your spf folder in your html directory and give the folder that permission by this command: **chmod 755 -R YOUR DIRECTORY**

then configure the path to htpwd in htaccess or simply remove it the default login and password is admin:12345

![](https://i.imgur.com/EA033Tr.png)

You can also check domains manually:
 
![](https://i.imgur.com/i7mstoD.png)

**tested in centos/apache/php7.3**
