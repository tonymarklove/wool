# Wool - PHP in Sheep's Clothing

Wool is a PHP framework and base application intended for rapid construction of new projects. It is not currently in a releasable state, but many of the key features are in a reasonable condition.

I haven't worked on it for a few years now, since I stopped writing PHP in my day job. But there are enough interesting features contained here that I'm open sourcing the code by the popular demand of my friends.

**Do not expect to be able to run the code without modification.** 

## Framework level

### 1. Core database layer
No ORM. You write SQL, it gets parsed into a syntax tree allowing you to combine and chain parts of your query together. Very useful if you have a base query and you want to add a `limit` and `offset` easily, for example. But there is no limit to the transformations you can apply.

More importantly, the parsing of the query allows us to determine the source table for each selected column, even across joins. So a simple `$row->save()` can automatically save back to multiple DB tables, automatically providing a transaction, and with all the pre-save and post-save callbacks you would expect from an ORM.

### 2. MVC
Nothing too special here nowadays, but a standard MVC layer is provided to organsie your code. A nice clean directory structure is provided, following the Rails "convention over configuration" appraoch.

### 3. Database / schema builder
You defined the database schema with a series of YAML files. The real database is then automatically built and kept up-to-date for you. These schemas try to support a wide range of database features. Foreign keys and contraints are automatically added for you in the correct places, and other contraints can be added manually. These are enforced at a database level where possible, but reported back nicely in the code so that you can easily handle failures.

Schemas also support the creation of stored procedures for many simple operations. For example you might want to specify that a shopping cart total is the sum of all cart line totals. Schemas allow you to do this declaratively without writing any code. A stored procedure is created for you, enforcing this rule at the database level. (A side benefit of this is the ability to change values in the database and having totals recalculate, without having to run through your app code.)

Having all of this declarative information about the database allows us to do some nice things, such as...

### 4. Auto-generated admin interface
We build an data-table based view of all your database tables automatically for you. For many parts of your application this is a nice simple default view of your data.

All parts of these default interfaces can be overridden to provide custom functionality. From smallest change to biggest, you can modify: row view, table view, page view, and whole controller code.

### 5. Themes, styling, and live preview
Built in support for themes. Themes can be defined using a YAML file with colour and image variables that get hooked into the CSS system for the front-end.

A designer can create this YAML file and even provide alpha blended thumbnails as layers for a preview. The admin interface will present you with a colour picker for each colour variable and composite a live preview image from the layers.

### 6. Cron plugin system
Drop PHP scripts into a directory and they will automatically get run on the schedule you specify within.

## Application level

### Installer
Customizable installer which checks for required PHP modules and system settings, then installs app for you. A developer using the framework can make their app easy to install for the end user.

### Image uploading and processing
As an example of overriding the auto-admin-interface, the base application comes with a custom image uploading module. These images can be used in the CMS or other parts of the site.

The custom image module allows you to crop and resize images as you upload them. Using the file upload API we can display a live preview of the image to the user (with drag-and-drop from your desktop) while the upload is happening in the background. Then we send any resize and crop commands to the server, for it to process later.

Images can also be processed when they are viewed. Using query string parameters you can request a variety of transformations to be applied, from simple resizing to overlaying of text.

### CMS with inline editing
The CMS is partly a framework feature. It is designed as the core system for getting and content onto a page. Modules can be built and registered with the system. Then using the admin interface you can use a grid system to place modules on pages. You also get inline-, WYSIWYG-editing on the front-end of the site.

So, for example, you can create a page with some introductionary text at the top and drag-and-drop an e-commerce store module onto the page below. Then visit the front end as an administrator and double-click to edit the introductionary text.

The code for this needs some finishing off, but was working reasonably close to how I wanted it.

### E-commerce
Support for a basic e-commerce shop is in progress. Not complete in the current codebase.

### Forum
Another example module. A basic forum is mostly complete, if I remember correctly.
