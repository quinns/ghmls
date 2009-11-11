$Id: README.txt,v 1.2 2009/06/01 19:38:59 jpetso Exp $

Transformations -
A framework for generic data transformation pipelines.


SHORT DESCRIPTION
-----------------
This module transforms data.
It doesn't care which data, and it doesn't care how.
It just makes it possible to define operations on whatever kind of data,
and lets you create pipelines in order to wire up those operations.

This module is an API.
It also ships with a default user interface (Transformations UI),
but is designed to be used seemlessly from within your code.
It's modular and does not pull in unnecessary code or undesired pages.

This module is pluggable.
It's relatively easy to code new operations, and autoloads any of those
when they ship with other modules. Add operations to your favorite module
now, without any impact in case Transformations isn't enabled!

This module is complex, and object-oriented.
If you're afraid of classes and objects in PHP, run away now.
On the other hand, Transformation's architecture makes it possible to
centralize Drupal dependencies in specific places, which is going
to lower the porting effort between Drupal versions.

This module can do lots in principle, but little in particular.
It's equipped to power stuff like import/export functionalities,
deployment, or Yahoo! Pipes, but essentially it's what you make of it.

Finally, this module is still unfinished and work in progress.
Some parts of the API, and hopefully the UI too, might still change
a lot. Don't expect it to do anything advanced at this point.
Instead, come and join the project and help to realize its potential!


AUTHOR
------
Jakob Petsovits ("jpetso", http://drupal.org/user/56020)


THANKS TO
---------
Klaus Furtmüller and Josef Küng for initiating and guiding this project.
Gerhard and Christa Petsovits for making all of this possible in the first place.
