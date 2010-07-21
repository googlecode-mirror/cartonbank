Authors: KeytwoWhy
Author URI: http://www.keytwo.net
Version: 0.9.5
Plugin Name: Lazyest Gallery
Plugin URI: http://lazyest.keytwo.net
Description: Easy Gallery management plugin for Wordpress 2.0

License:
========
	Copyright (C) 2004 Nicholas Bruun Jespersen
		(For questions join discussion on www.lazyboy.dk)
	Copyright (C) 2005 - 2006 Valerio Chiodino
		(For questions join discussion on board.keytwo.net)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Lazyest Gallery Plugin:
=======================
Welcome to the Lazyest Gallery plugin. The motive for making this plugin was
to offer the countless users of the Wordpress blog a chance to get a simple,
easy-to-install gallery plugin, with as little management as possible.

Looking to Flickr and others I found them either to complicated to install,
or having a pricetag danglig from the 20 mb + marker. So .. I wanted the user
to be able to run this plugin as hasslefree as possible and with their own web
hosting as size limit..

Second, I wanted the plugin to read folders with image files. With this feature
the use could just point to the given gallery directory and have instant access to
images in that folder and subfolders.
		-- Lazyboy (Edited by Keytwo)

Download:
=========
The Lazyest Gallery Plugin can be downloaded here:
http://lazyest.keytwo.net/

		#================#
		## INSTRUCTIONS ##
		#================#

How to install:
===============
0. If you have a previous version of Lazyest Gallery we reccomend to follow
   the "How to upgrade" section.
1. Unpack the archive to your wordpress plugin dir ( /wp-content/plugins/ ) AND
   be sure that it will be placed inside a folder named "lazyest-gallery".
2. Create a folder for the gallery where albums will be placed if you haven't
	one already, better in the WordPress root folder. *
3. Activate plugin in the Wordpress backend.
4. Go in the new Options -> Lazyest-Gallery page of the Admin panel and adjust the
   configuration to suit your preferences.
5. Create a new page and write [[gallery]] in the content, save and it's done.

* Be sure to not place images directly here inside. Use nested folder instead;
this is how Lazyest Gallery menages albums. Note also that this is not the plugin
folder, it is the folder where album's images will be placed.

How to uninstall:
=================
1. Go to your Lazyest Gallery Admin panel and click on "Delete Options" button
2. Deactivate plugin from your WordPress Admin panel
3. Remove the "lazyest-gallery" folder from your plugins folder
   (usually /wp-content/plugins)

How to upgrade:
===============
1. Go in your admin panel and deactivate previous version of Lazyest Gallery.
2. [a] Delete "lazyest-gallery.php" file from "/wp-content/plugins/".
3. Backup eventual modified "lazyest-style.css" file.
4. Unpack archive to your WordPress plugin dir ( /wp-content/plugins/ ) overwriting
   existing files and folders.
5. [b] Remove the "lazyest-index.php" file from your WordPress root folder AND from
   your current theme folder (from 0.9.3 you no more need it).
6. Activate plugin the WordPress backend.
7. Go in the new Option -> Lazyest-Gallery page of the Admin panel and adjust the
   configuration to suit your preferences and BE SURE to setup eventual new options.
8. Modify or create your gallery page writing [[gallery]] on it (in the content,
   not in the title space), save and it's done.

[a] OPTIONAL: If upgrading from 0.3 since 0.8.3 to 0.9.*
[b] OPTIONAL: If upgrading from 0.3 since 0.9.2 to 0.9.5

Tips and triks:
===============
a. If you want icons to be displayed for any folders, just put inside the
   folder an image (jpg, gif or png) with same name of the folder.
b. If you want categories to be displayed in your sidebar ad this line to your
   "sidebar.php": <?php if (function_exists(lg_list_folder) lg_list_folders("title"); ?>
   You can use whatever title you want.
c. If you want a random image to be displayed in your sidebar ad this line to
   your "sidebar.php": <?php if (function_exists(lg_random_image) lg_random_image("title"); ?>
   You can use whatever title you want.
d. If you want to use link an image to your post you can use this syntax:
    [[Image:image/name.jpg|alignment|width|height|Caption or description]]
    Note that:
    - every field but "Image" are optional
    - alignment: must be left, right or center or leave it blank
    - width: must be an integer value
    - hight: must be an integer value, you can leave it blank
    - If caption is provided this will override the xml one
e. If you want to use Lightbox or Thickbox plugin with LG, you have to install it.
   Once done new options will be displayed in the side of the admin page.
f. Crop system need cache for thumbs enabled to work. Be sure of what you
   are doing.
g. [EXPERIMENTAL] If you want to protect a folder you can do it by increase the level of
   the folder. Though it is not safe, infact this folder is still directly browsable. It
   is strongly recomended to fill an .htaccess to avoid this.

Files in this archive:
======================
lazyest-gallery/lazyest-admin-form.php - The Lazyest Gallery admin pannel form file.
lazyest-gallery/lazyest-admin.php - The Lazyest Gallery admin pannel.
lazyest-gallery/lazyest-cache.php - The cache system file.
lazyest-gallery/lazyest-dirs.php - The showDirs() file.
lazyest-gallery/lazyest-exinfos.php - The exif info file.
lazyest-gallery/lazyest-filemanager.php - The Lazyest Gallery file manager file.
lazyest-gallery/lazyest-gallery.php - The main plugin file.
lazyest-gallery/lazyest-img.php - The image generator file.
lazyest-gallery/lazyest-parser.php - The Lazyest Gallery xml parser
lazyest-gallery/lazyest-popup.php - The popup template file
lazyest-gallery/lazyest-slides.php - The showSlides() file.
lazyest-gallery/lazyest-style.css - The Lazyest Gallery style sheet.
lazyest-gallery/lazyest-styleditor.php - The style editor file.
lazyest-gallery/lazyest-thumbnailer.php - The post's thumbs file.
lazyest-gallery/lazyest-thumbs.php - the showThumbs() file.
lazyest-gallery/lazyest-thumbs-style.css - Post's thumbs style sheet.
lazyest-gallery/lazyest-wizard-form.php - The wizard handler
lazyest-gallery/lazyest-wizard.php - The wizard form
lazyest-gallery/readme.txt - This file.
lazyest-gallery/exifer/exif.php - exif main file.
lazyest-gallery/exifer/thumbs.php - exif thumbs file.
lazyest-gallery/exifer/makers/canon.php - Cannon support for exIF
lazyest-gallery/exifer/makers/fujifilm.php - Fujifilm support for exIF
lazyest-gallery/exifer/makers/gps.php - GPS support for exIF
lazyest-gallery/exifer/makers/nikon.php - Nikon support for exIF
lazyest-gallery/exifer/makers/olympus.php - Olympus support for exIF
lazyest-gallery/exifer/makers/sanyo.php - Sanyo support for exIF
lazyest-gallery/images/folders.png - Folders icon
lazyest-gallery/images/powered_bg.gif - Powered background image

Have fun, and happy viewing.

		#=============#
		## CHANGELOG ##
		#=============#

Gallery current features:
=========================
version 0.9.5 (by Keytwo)
- Added option to enable/disable Microsoft wizard upload tool
- Added resample quality option for jpegs
- Added Thickbox support
- Added disable link to fullsize image view option
- Added gallery quickbutton in write pages
- Rework: now you can insert text in your gallery page's content
- Rework: popup windows now closes on click
- Rework: list folder in sidebar now shows subfolders too
- Rework: install now try to determine gallery URI
- Rework: styles customization has been emproved
- Rework: Microsoft wizar upload password is now encrypted
- Bugfix: list folder in sidebar now avoid to print everything but folders
- Bugfix: folers' icons were not resized in thumbs view
- Bugfix: differents links around the gallery
- Bugfix: translated message are now shown correctly
- Bugfix: now foreign chars are supported
- Bugfix: minor

version 0.9.4 (by Keytwo)
- Added remove folder
- Added create folder
- Added rename folder
- Added support for germans chars (thanks pufaxx)
- Added userleveled filemanager
- Added Microsoft wizard upload tool
- Rework: file manager
- Rework: random sidebar image
- Rework: now Lazyest Gallery is on toplevel menu
- Bugfix: sometime random images were not shown
- Bugfix: upper level images were not shown
- Bugfix: no-png icons are now correctly displayed
- Bugfix: XHTML uri for random thumbs icons fixed
- Bugfix: minor

version 0.9.3 (by Keytwo)
- Added support for differents Lightbox plugins
- Added forced Lightbox support (Deprecated)
- Added new version's "spy"
- Added cache cleaning for each folder
- Added upload feature (Austin Matzko plugins adapded)
- Added level protected folders feature
- Added slide's Lighbox support
- Removed: index file (no more needed)
- Rework: smartlinks (now similar to the wiki syntax)
- Bugfix: international characters in xml file
- Bugfix: minor

version 0.9.2 (by Keytwo)
- Added support for WP-Lightbox
- Added folders caption
- Added style Sheet editor
- Added crop system for cache system (cotrib: dodo)
- Added image deletion
- Rework: captions system
- Rework: full size pics with same name of folder
- Rework: inline styles moved to CSS
- Misc: Cleaned and Commented the code
- Misc: Admin page is now XHTML 1.0 Transitional compiliant
- Misc: All files are now in the same folder (but lazyest-index.php)
- Bugfix: subfolder's icons
- Bugfix: smartlinks

version 0.9.1-beta (by Keytwo)
- Permalinks bug fixes

version 0.9.0 (by Keytwo)
- Minor bug fixes

version 0.9.0-beta (by Keytwo)
- Splitted to differents files
- Improved some performance
- LG is now included into a page
- Post's smart links implemented
- Minor bug fixes

version 0.8.3 (by Keytwo)
- No changes from beta

version 0.8.3-beta (by Keytwo)
- Bug fixes

version 0.8.2 (by Keytwo)
- Wordpress 2.0 extra features
- Subcategories icon bug fixed
- Captions not refresh bug fixed
- Slide's cache system implemented
- Cache system improved
- Gallery is now "centred"
- You can now localize Lazyest Gallery in your own Language
- ExIF Data always displayed bug fix
- Minor bug fix

version 0.8.1 (by Keytwo)
- Thumb cache bug fix
- Minor bug fix

version 0.8 (by Keytwo)
- Compatibility with Wordpress 2.0
- Sorting of files and folder
- Shortcut to Home Page
- Shortcut to Lazyest Admin (admin only)
- Shortcut to Captions section (admin only)
- New Captions System implemented (Using XML instead of .ini)
- Now you can use links in the captions
- GD Library Infos in Admin Panel added
- Easyer ExIF management
- Sidebar: lg_list_folders($title) added
- Sidebar: lg_random_image($title) added
- Delete and Reset Options buttons added to Admin Panel
- Code cleaned
- Minor bug fix

version 0.7.1 (by Keytwo)
- Layout bug Fix

version 0.7 (by Keytwo)
- Minor bug fix

version 0.7-beta (by Keytwo)
- Switch for ExIF Data implemented
- Switch for Captions implemented
- Global Gallery width implemented
- Code cleaned
- Minor bug fix

version 0.7 (by Keytwo)
- Switch for ExIF Data implemented
- Switch for Captions implemented
- Global Gallery width implemented
- Code cleaned
- Minor bug fix

version 0.6 (by Keytwo)
- Improved ExIF Management system
- Adopted Jan831 heavy coding
- Admin panel hard coding
- No tables will be now inserted into your DB
- Minor bug fix

version 0.5 (by Keytwo)
- Never released

version 0.4.2_r4 (by Jan831)
- Caption/title per image
- Pagination number of thumbnails per page can be configurated
- Thumbnails with the original filename, instead of the md5-hash
- Users can set choose an image to be shown with each folder:
  random image, folder icon or nothing
- Number of images in each folder is shown in the album-list
- Some extra security-fixes

version 0.4.2_r3 (by Keytwo)
- Fixed thumnails and slide view
- Missing trailing slash in cache folder's name does not invoke eachtime cache
  creation
- Now jpgs and gifs for the categories icons work
- Minor bug fix

version 0.4.2_r2 (by Keytwo)
- Admin Pannel under "Options"

version 0.4.2_r1 (by Keytwo)
- Display the date the picture was taken, and any comments in it (while
  possible)
- New (good) thumbnail cacheing system (you can turn it on or off)
- You can choose in how many columns display your gallery folders
- You can choose in how many columns display thumbnails
- You can use category icons
- Align left, align right for side menu now working
- You can disable side menu from lazy-gallery.php

version 0.4
- Fixed IE float bug with pre/next.
- Fixed error showing images in Gallery root.

version 0.3
- Easy install.
- Handles JPG, GIF and PNG image files.
- Show images in the current gallery folder.
- Show subfolders to the gallery folder, giving access to tree-like structure.
- Thumbs view (size can be modified).
- Slide view with previous/next feature (size can be modified).

		#========#
		## TODO ##
		#========#

Features to come:
=================
- Password securing folders.
- Upload feature, zip upload (zip unpack upon upload).
- AJAX Technology for some part of the project
- Comments for each image
- Counter for each image

		#===========#
		## CREDITS ##
		#===========#

Code Contribution:
==================
Jan Marie - Jan831
Alfred Aubì
VaamYob - www.xyooj.com
White2001
Stuardo -StR- Rodríguez
Wolfram Riedel - www.wolframswebworld.de
Florian Engelhardt
Igor Kroutikov - www.ikrout.com
dodozhang21 - pure-essence.net
Austin Matzko - www.ilfilosofo.com (upload plugin)

Moral Support:
==============
Eddie - www.eddieslab.org
Lazyest Gallery Community - board.keytwo.net

Technical Consultancy:
======================
Kain - www.kuht.it
r3g-ik - www.elbao.it