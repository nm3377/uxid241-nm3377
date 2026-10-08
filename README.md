# UXID 241 Cookbook 

This is the repository for the UXID 241 cookbook project. All work on the project will be done here.

## AI Use 

DrexelEDU ChatGPT was used for all AI assistance unless otherwise stated. 

9/27: To understand how to set up my repository within the XAMPP folders and where the file I work on should be located.

10/8: 

- To understand the function of type="search" and id="q" in my code, as code was working fine without them. 

- Catch an error I was getting with my success variable, ended up being a simple typo, missing the $.

- Removed code that was not doing anything. There were no full lines of code that didn't have a function, but I was able to remove the value ="" I had on lines 110 and 112 that was not doing anything, and rework a line that was written as <?php if ($recipe_name && $email !='' && $success) : ?> down to just <?php if ($success) : ?>, which worked because I had already built these parameters for success in the PHP at the top of the page. 