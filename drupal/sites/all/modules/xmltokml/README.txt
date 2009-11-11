XML to KML allows you to generate KML data from existing XML documents such as
RSS feeds. This KML data can be downloaded by visitors and opened in
Google Earth™, allowing your site's news to be displayed geographically.


Installation
------------
Unpack the Innovation News module to your site's module directory
(e.g. /sites/all/modules) as you would unpack any other Drupal module. After you
unpack the module, you may enable it by navigating to Administer > Site Building
> Modules (admin/build/modules).


Usage
-----
After you install XML to KML, navigate to Administration > Site Configuration >
XML to KML (admin/settings/xmltokml). Here, you will be able to provide the
address of an XML file (an RSS feed or any other XML data). After the file is
loaded and analyzed, you will be able to specify which XML elements contain
important data. When you have chosen your settings, click the "Save" button.
You will be brought to a page which lists the location of the input XML file and
the loation of the output KML file. The KML file will be dynamically-generated
based on the data which is in the input XML file when the KML file is loaded.

Visitors will be able to download this KML file by either direct URL or the
"KML Output" link provided in the Navigation menu. This file can be opened in
Google Earth™ to display your site's news geographically.

To edit your data settings, navigate again to Administration >
Site Configuration > XML to KML (admin/settings/xmltokml) and click the "Edit"
button.

To specify a new input XML file, navigate to Administration >
Site Configuration > XML to KML (admin/settings/xmltokml) and click the
"Delete" button. You will then be able to start over, choosing a new XML file
and new data settings.


0.5 Limitations
---------------
XML to KML 0.5 was developed specifically for use in the Innovation News
Installation Profile. Because integration with the profile was our foremost
goal, it may be less than flexible for users using it independently.
Specifically, XML to KML 0.5:
  - Can only convert one XML file to one KML file at a time
  - Requires that all important data (Title, Body, etc) be contained in sibling
    elements

If there is a greater interest in XML to KML as a standalone module in the
future, we will hopefully be able to dedicate much more time to these features
and any features which are requested.


Uninstallation
--------------
If you have no intention of using this module again and would like to remove
any traces which it has left behind, you can completely uninstall it. Doing so
will clear all settings which you have chosen for this module. Please be aware
of this before you proceed.

To uninstall this or any other module, first disable it via Drupal's "Modules"
page Administer > Site Building > Modules (admin/build/modules). After it has
been disabled, click "Uninstall" on the same page. Check the module off and then
click "Uninstall".


Credits
-------
XML to KML was written by Mark Gambrell of RIT's Collaboratorium. His software
was used at RIT's first Innovation Festival, showcasing innovation in news
display.

Drupal integration was developed for use in Innovation News, a web-to-print
newspaper which has been developed by the Open Publishing Lab at the Rochester
Institute of Technology. Innovation News has been imagined and fostered by the
creative minds of Matthew Bernius and Patricia Albanese. Drupal integration was
written by John Karahalis of the OPL.

RIT Collaboratorium: http://casci-web.rit.edu/~collaboratorium/
Open Publishing Lab: http://opl.cias.rit.edu/
Innovation News Installation Profile: http://drupal.org/project/innovationnewsprofile
Matthew Bernius: http://opl.cias.rit.edu/content/matthew-bernius
Patricia Albanese (Pitkin): http://opl.cias.rit.edu/content/patricia-albanese-pitkin
John Karahalis: http://opl.cias.rit.edu/content/john-karahalis
