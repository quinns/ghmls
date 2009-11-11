//ELMS: Network Manager - Define a network of sites that you manage and monitor / modify settings from a central one
//Copyright (C) 2009  The Pennsylvania State University
//
//Bryan Ollendyke
//bto108@psu.edu
//
//Keith D. Bailey
//kdb163@psu.edu
//
//12 Borland
//University Park,  PA 16802
//
//This program is free software; you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation; either version 2 of the License,  or
//(at your option) any later version.

//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.

//You should have received a copy of the GNU General Public License along
//with this program; if not,  write to the Free Software Foundation,  Inc.,
//51 Franklin Street,  Fifth Floor,  Boston,  MA 02110-1301 USA.

-----------------
Required Modules
-----------------
None at this time

-----------------
Installation
-----------------
  *Download from drupal.org
  *Place in your modules folder of choice and then activate it at admin/build/modules
  *Make sure your DB user account with access to the current Drupal site has the ability to connect to your information_schema table.  This is critical so the module can fetch a list of available databases.
  *Go to admin/user/permissions to configure who can access the reports as well as cross network functions
  *Now you need to define your network.  Go to admin/settings/network_manager and define what databases are Drupal ones you wish to connect to and centrally manage. NOTE: The DB user with access to the site you're installing this on needs to have access rights to all the sites (at a DB layer) you are about to connect.
  *You're now ready to do far more in far less time
  
-----------------
Known Issues
-----------------
  None at this time
-----------------
Compatability
-----------------
  Shared Hosting setups will have issue with this module if they're even able to handle it (most will not be able to).  This really is intended for large scale implementations

-----------------
Notes
-----------------
  All Feedback is very much appreciated; Just don't let your boss know that you're using it ;)