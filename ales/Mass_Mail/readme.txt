Welcome to phpMassMailer(phpMM)

Free download from http://www.phpclasses.net
Version 0.8, 23-08-2007  

About the program 

Many sites send regular e-mail newsletters to their subscribers. Usually the subscriber records with their e-mail addresses are stored in a database.

This programm can automate the process of extracting the user records from databases to generate and send newsletters to many subscribers.

It supports many types of different databases. So it can be used to address the newsletter delivery needs of many types of sites.

There is also sub-class that can import a list of e-mail addresses to the database.

This programm for Unix/Windows system and PHP4 (or higest).  

Functions for some databases are not checked up, but included in the program.
If they are necessary for you, remove in them comments.

By default the program sends the message through function mail (). 

How to send the message from a file:
  1. To create a file of a format *.txt
  2. Everyone e-mail should be since a new line

Example of table MySQL:

   CREATE TABLE mymail (
     id INT NOT NULL AUTO_INCREMENT,
     mail varchar(30),
     PRIMARY KEY(id) );
   INSERT INTO mymail(mail) VALUES ('mysql@list.ru');

Example of table PGSQL:

    CREATE TABLE mymail (
     mail_no oid,
     mail text
   );

   INSERT INTO mymail(mail) VALUES ('pg@rambler.ru');


Example of table Firebird:

   To create GENERATOR, TRIGGER.

   CREATE TABLE MYMAIL (
     MAIL_NO  INTEGER,
     MAIL     VARCHAR(10)
    );

Please, report bugs...

Romanovsky Maxim,  romanovsky_m@yahoo.com  



