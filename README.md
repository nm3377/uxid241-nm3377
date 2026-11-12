# UXID 241 Cookbook 

This project is a searchable online cookbook built with PHP, MySQL, HTML, and CSS. Recipes are stored in a MySQL database and displayed as cards on the homepage, where users can search the cookbook and open individual recipes to view their full information. The project also includes an authentication system with hashed passwords and session handling. Administrators can access a protected Manage Recipes area where they can create, edit, and delete recipes.

## How to Run Locally 

This project requires XAMPP or MAMP with Apache and MySQL running.

Download or clone this repository and place the project folder inside your local web server's htdocs directory.

Start Apache and MySQL through XAMPP or MAMP.

Open phpMyAdmin through localhost.

Import uxid241_cookbook.sql. The SQL file creates the uxid241_cookbook database, creates the required tables, and loads the seed data.

Copy config.example.php and rename the copy to config.php.

Update the values in config.php to match your local MySQL configuration:

return [
  'db_host' => 'localhost',
  'db_port' => 'YOUR_PORT',
  'db_name' => 'uxid241_cookbook',
  'db_user' => 'YOUR_USERNAME',
  'db_pass' => 'YOUR_PASSWORD',
  'db_charset' => 'utf8mb4',
];

Open the project through localhost in your browser. For example, if the repository is stored at htdocs/cookbook/uxid241-nm3377, navigate to:

localhost/cookbook/uxid241-nm3377/

The project must be served through XAMPP or MAMP rather than opening the PHP files directly.

## Admin login

The cookbook includes a session-protected admin area for managing recipes. The seeded administrator account can log in using:

Email: [ENTER SEEDED ADMIN EMAIL HERE]
Password: [ENTER SEEDED ADMIN PASSWORD HERE]

After logging in as an administrator, use Manage Recipes in the navigation to access the protected admin area. From there, an administrator can create, edit, and delete recipes.

Passwords are not stored in plaintext in the database. The database stores password hashes generated with password_hash(), and login attempts are verified with password_verify().

## Beta feature: Search

The beta feature implemented for this project is Search.

Use the search field on the homepage to search the cookbook. Search terms are compared against recipe content in the database, and matching recipes are displayed as recipe cards. Clicking a recipe card opens that recipe's individual page.

The search query uses a prepared SQL statement with LIKE parameters so user-supplied search values are not directly interpolated into SQL.

## AI Use 

DrexelEDU ChatGPT was used for all AI assistance unless otherwise stated. 

9/27: To understand how to set up my repository within the XAMPP folders and where the file I work on should be located.

10/8: 

- To understand the function of type="search" and id="q" in my code, as code was working fine without them. 

- Catch an error I was getting with my success variable, ended up being a simple typo, missing the $.

- Removed code that was not doing anything. There were no full lines of code that didn't have a function, but I was able to remove the value ="" I had on lines 110 and 112 that was not doing anything, and rework a line that was written as <?php if ($recipe_name && $email !='' && $success) : ?> down to just <?php if ($success) : ?>, which worked because I had already built these parameters for success in the PHP at the top of the page. 

10/20:

- Figure out why I was receiving a warning of an undefined array. Ended up being an error with line 6 not calling on the database for anything other than title, description, and step 1. 

- Verified that all requirements according to canvas were met. They were. 

10/30-10/31

- Refresh on how implode/explode work in order to integrate them while changing how the steps system works, information to include in config.example.php, and confirm necessary updates to the README.md file. 

11/4

- To understand how to build the login/registration function of the site. 

- Help convert my Figma mockup of the visual layout into CSS.

11/11 

- Flesh out the code and help understand the logic behind the new PHP files needed for CRUD requirements (manage_recipes, edit_recipe, delete_recipe, recipe).

- Aid in fixing login system to require emails, not just usernames. 

- Basic bugfixing/determining root cause of errors. 

- Verify that all content needed for Beta review is present in the code. 