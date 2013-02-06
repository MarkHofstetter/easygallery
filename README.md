EasyGallery
===========
EasyGallery is a smart and simple way to present photos on your own website without a complex installation or administration. Just upload and everything is there.

How to use
----------
#### Installation
1. Extract and copy to your Webserver
2. Make the `PICTURES/` folder writeable (`chmod 775`)
3. Copy your images to `PICTURES/` folder

#### What do I need to run EasyGallery?
You need a Webserver with at least PHP 4 installed. Furthermore the GD library is required for thumbnail creation. You can check your PHP version by calling `<?php phpinfo(); ?>`.

#### GD library is not installed on my server.
Unfortunately you will have to generate your thumbnails manually before using the gallery. Add a `thumbnails/` directory to each of your album folders and for each `PICTUREXX.jpg` create a thumbnail `tn_PICTUREXX.jpg` with 140x140px size. Many image processing programs provide the possibility to automate this process, like for example Irfanview.

#### My image folders are located in another folder.
Change the $root_dir variable in html/gallery.php.


Changelog
---------
##### 09.02.2013 --- Version 3.0-BETA-0 released
Complete rework of the code, attempt to catch up 4 years of web development

Google Analytics integration. You can now add Google Analytics by setting the Tracking ID in index.php

Google Maps Integration for geolocated Images (EXIF)

Integration of _mustache_ templating engine, separation of content and logic

Changed to _fancybox_ lightbox implementation

Integration of _smaller_ in order to minify js 

_____________
##### 09.06.2009 --- Version 2.1.1 released
Code cleanup and refactoring

##### 04.04.2009 --- Version 2.1.0 released
Switch to Slimbox instead of Litebox.

_____________
##### 09.06.2007 --- Version 2.0.11 released
Some minor fixes and polishing the code.

_____________
##### 09.06.2007 --- Version 2.0.4 released
Automatic thumbnail generation.

Change from Lightbox to Lytebox.
##### 08.05.2007 --- Version 2.0.3 released
Some minor fixes and polishing the code.
 
##### 01.05.2007 --- Version 2.0.2 BETA released
lightbox update (<a href="http://www.huddletogether.com/projects/lightbox2/">http://www.huddletogether.com/projects/lightbox2/</a>).

#####25.04.2006 --- Version 2.0.1 released
`important security update`: In all former verisons &quot;A remote attacker could exploit this vulnerability using the 'ordner'  parameter to execute script in a victim's Web browser within the  security context of the hosting Web site, allowing the attacker to  steal the victim's cookie-based authentication credentials.&quot; ( <a href="http://xforce.iss.net/xforce/xfdb/25943">http://xforce.iss.net/xforce/xfdb/25943</a> ).

#####16.04.2006 --- Version 2.0.0 released
complete layout is now based on layers (if you have problems with the positioning of the layers use EasyGallery 1.xx)

dynamic hover effects with overlapping in thumbnail view

comment function has been extendet to author comments, that appear as tooltips

style.css has been included

#####16.04.2006 --- Versions 1.16 &amp; 2.0.0 split