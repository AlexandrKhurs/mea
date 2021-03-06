Mass Edit Anything (MEA) project
https://github.com/AlexandrKhurs/mea

NOTICE OF LICENSE
This source file is distributed under GNU General Public License (GPL 3.0).
Please refer to https://opensource.org/licenses/GPL-3.0 for more information.

DISCLAIMER
This source code is distributed "as is", with no warranty expressed or implied, 
and no guarantee for accuracy or applicability to any purpose. 

author Alexander Khurs <alexandr[dot]khurs[at]gmail[dot]com>
license https://opensource.org/licenses/GPL-3.0  GNU General Public License (GPL 3.0)
-------------------------------------------------------------------------------------

PURPOSES:

adds the following bulk operaions to your PrestaShop 1.6

	1. Products list:
		- add selected products to specified category
		- move selected products to specifies category
			behaviour details:
				- removes from all categories;
				- adds to specified; 
				- sets product's default category to specified;
		- remove selected products from specified category
			behaviour details:
				- can't remove from the only (last) directory - error will be generated; 
				- if specified category is default for product - set default category to 
				  FIRST of remaining categories;
			
	2. Categories list
		- move selected categories under specifies category
	

INSTALLATION:
	1. 	Look the SOURCE directory 
		   for now it contains following files:
				- AdminCategoriesController.php 
				- AdminProductsController.php
				- Category.php
		   ...but there could be more futher.
			
	2. 	Check your OVERRIDE/CONTROLLERS/ADMIN folder: 
		   <path-to-prestashop-root>/override/controllers/admin/
		   
		And check your OVERRIDE/CLASSES folder
		   <path-to-prestashop-root>/override/controllers/admin/
		   
		ATTENTION! 
		your ONLY can continue installation if theese directories on your server
		DOES NOT COUNTAIN ANY OF FILES listed above and distributed in SOURCE folder.

		Otherwise - DONT INSTALL THIS SCRIPT!
		I repeat: DONT REWRITE EXISTING FILES to avoid breaking your existing functionnality!

		You are alerted now, and your next steps at your own risk.
	   
	3. 	Copy files from SOURCE directory:
		- AdminCategoriesController.php and AdminProductsController.php
		  copy to your <path-to-prestashop-root>/override/controllers/admin/
	  	- Category.php
		  copy to your <path-to-prestashop-root>/override/classes/
	
	4.  	manually delete class_index.php file located in <path-to-prestashop-root>/cache directory.
	
	5. 	Test it
		ATTENTION!
		This is highli recommended to TEST functionality first, using TEST VERSION OF YOUR SHOP, 
		or the TEST PRODUCTS in your production shop. Reliability of the script depends on your store 
		settings and a custom functionality installed in your store, and I cant guarantee 
		that it will work correctly despite all yours customization.
		So I repeat - Test it first!
	
		a. go to your prestashop admin panel
		b. go to Catalog -> Products
		c. check some products in list (set checkboxes at left of products)
		   (your only can use this if you have more than one product in list)
	    d. open "Bulk actions" list at the bottom of your product list
		e. chose one of actions ("add to category", "move to category", "delete from category")
		f. after page reloaded, at the top of your product list choose desired category, 
		   then click "Proceed"
		g. check result
		
		h. do the same with all other add/remove/move actions
		
		i. do the same with in your Categories list
		
	6.  Check this project page from time to time :) 
	    Project should grow to a complete module that extends finctionality of any list in Prestashop,  
		could be installed a regular way, without manually copying files, and doesnt use overrides.
		
		
-------------------------------------------------------------------------------------
		
	   
	   
	   
		
		
		
 
