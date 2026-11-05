# UXID 241 Cookbook 

This is the repository for the UXID 241 cookbook project. Through this web app, users can search through a vast catalog of recipes, and even upload their own. All work on the project will be done in this repository.

# How it works! 

This project uses PHP, MySQL, and mysqli. XAMPP or MAMP is required to run the project locally.

1. Download or clone this repository and place it inside the web server's htdocs directory.
2. Start Apache and MySQL through XAMPP or MAMP.
3. Open phpMyAdmin through localhost.
4. Import uxid241_cookbook.sql. The schema creates the uxid241_cookbook database, creates the recipes table, and adds the seed recipe data.
5. Copy config.example.php to a new file named config.php.
6. Update the database settings in config.php to match your local MySQL configuration. The application expects the database name uxid241_cookbook.
7. When in localhost, follow the path cookbook/uxid241-nm3377 to open the project. 

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

- Help convert my Figma mockup of the visual layout into CSS 