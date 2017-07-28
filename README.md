#    Markyt, free annotation software

   Markyt is a **Web-based multi-purpose annotation tool**. With this annotation tool you can annotate from simple documents (without any formatting) to HTML documents. **Documents can have embedded: images, diagrams, videos, etc. With all power of HTML**.

This annotation tool implements the annotation project life cycle and is able to manage interactive and multi-user projects. Novelty lays on the annotation quality assessment tool and the **annotation tracking system**, which support systematic and on-demand annotation amendment **agreement analysis**.

##### This annotation tool allow you to:
* obtaining different annotation statistics, **like the annotation agreement between annotators for a document**, and more.
* **print and export the annotated text**.
* ask questions about the different types of annotation.
* **import your Word documents** or the like to annotate them. Thanks to technology CKEditor.

#####    Markyt was developed with:
* CakePHP framework
* Rangy Library
* jQuery Amchart Plugin
* And another open source libraries as CKEditor, Jquery, bootstrap...




#####    Markyt is cross browser compatible:

* Chrome (Recommended)
* Firefox (Recommended)
* IE9+
* Opera
* Safari

##### How to run    Markyt:
In order to install   Markyt you need to install:

* Apache
* PHP 5.6
* Java 8
* Mysql 5.6

-Donwload  Markyt
-The folder  Markyt should be placed in the directory of apache /www.
-Add permissions readable, writable and executable to  Markyt folder.
-Edit **app/Config/core.php** and change Security.salt and Security.cipherSeed. Alter the code according to your preferences.
-Edit **app/Config/database.php** and set the user account to be used in accessing the database. For example, a MySQL account with user "server" and the password "server" would look like:

```php
public $default = array ( 
'datasource' => 'Database / Mysql',
'persistent' => false, 
'host' => 'localhost', 
'login' => 'server', 
'password' => 'server', 
'database' => 'marky', 
'prefix' =>'' );
```


-You need to modify the file .htcacess in  Markyt directory as follows:
```sh
 Options Indexes FollowSymLinks MultiViews 
 AllowOverride All # HERE PUT None, change to All 
 Order allow, deny # allow from all
```

-Then run the script marky.sql.
Finally, you have to access the  Markyt's web and create the first administration account. 
In case of error, you can consult the official cakePHP website https://cakephp.org/

#### you can acces to a realtime demo at: http://www.markyt.org/annotationDemo
