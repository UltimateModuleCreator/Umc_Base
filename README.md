**Notice**:   
The work for this is currenly on hold until further notice due to lack of capable resources.  
In other words, magento 2 changes too much too fast and I don't have much time available to keep up with it.  
But I promise that at one point I will start it again.  
Feel free to fill the [issues](https://github.com/UltimateModuleCreator/Umc_Base/issues) section with what you find, so I will have a starting point when I pick this up again.  

**Description**

The `Umc_Base` module is the main module of the Ultimate Module Creator for Magento 2.   

What is Ultimate Module Creator For Magento 2? It's the Magento 2 module [for this](https://github.com/tzyganu/UMC1.9).  
This main module allows you to create the backend CRUD for your own custom entity flat or tree.
Others modules for different functionalities (frontend, API, Catalog relation, ...) will follow.

**Installation**

After installing magento 2, run these commands:

 - `composer config repositories.ultimatemodulecreator-umc-base git git@github.com:UltimateModuleCreator/Umc_Base.git`
 - `sudo composer require ultimatemodulecreator/umc-base:dev-master`
 - `php bin/magento setup:upgrade`

