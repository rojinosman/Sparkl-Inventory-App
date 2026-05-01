# Sparkl Bulk Platform
## PHP > Main Platform Components


### **Application Base Page:**     
> /index.php

<dl>       
 <dd>  <strong>Main param of note:    </strong></dd>
 <dd> <strong>?loc</strong> represents the main page  <strong>eg:</strong>   index.php?<strong>?loc=dashboard</strong>  would render dashboard.php into index.php </dd>
    
<dd>All user facing content is rendered through this <strong>index.php</strong> chasis page <strong>(see index.php line #200)</strong>  </dd>
</dl> 
      
### **Data Interaction & Primary Function Logic:** 
> **/_user-core.php**   (included in every page)

<dl>      
 <dd>This functionality available site-wide as <strong>$USR->{function}</strong> (see below for example)       </dd>      

  <dd><strong>Dynamic DB Interaction:</strong></dd>
  </dl>
  
  - ``` $USR->insertDynamic($tablename,$values) ```
  this will insert into any table (no setup or instantiation needed) by passing the table name and an array with keys matching field names ...  **eg:**      
   ```php
    $values = array();
    $values('firstname') = 'frank';
    $values('lastname') = 'jones';
    $values('email') = 'jones@hailmary.com';
    $tablename = 'users';
    $insid = $USR->insertDynamic($tablename,$values);
   ```
<dl>       
 <dd>Thats it!, the above would insert a record in the user table for frank - and $insid would be returned as the inserted record id (or false on failure)   
Also, no cleanup necessary; no close or other resource mgmt needed ... </dd>   </dl>
  
  - ``` $USR->updateDynamic($id,$tablename,$values,$overrideidfield='') ``` - update any db table by passing:      
          $id: value of record identifier - by default this is the 'id'      
          $tablename: table to update     
          $values: array with keys matching fields (identical to insertDynamic above)           
          $overrideidfield: OPTIONAL - needed only if the value of **$id** corresponds to a specific field in the DB other than the 'id'     
     
  <dl>       
 <dd>Again - no addtl mgmt needed.   So, continuing the example above, to update frank's record to add his company name using the <strong>$insid</strong> returned from <strong> $USR->insertDynamic</strong>  above:   
 </dd>  </dl>   

 
  ```php
    $values = array();
    $values('company_name') = 'Franks Fajitas';
    $tablename = 'users';
    $updid = $USR->updateDynamic($insid,$tablename,$values)
```

<dl>       
 <dd>Updated! - Or, if you didnt have the **$insid**, you could use his email by leveraging param #4 <strong>eg:</strong>        </dd></dl>    
 
```php
    $franksemail = 'jones@hailmary.com';
    $values = array();
    $values('company_name') = 'Franks Fajitas';
    $tablename = 'users';
    $updid = $USR->updateDynamic($franksemail,$tablename,$values,**'email'**)
```   
**... you get the idea...**     
Similarly, delete and get are very similarly dynamic:      
  - ``` $USR->deleteDynamic ``` - execute one or multiple deletion commands in one call  
  - ``` $USR->getDynResults ``` - retrieve simple or complex queries using this function 

**The page mentioned below _process_form.php uses these functions in nearly every block - so, this would be a good place to look for examples...**

### **Form Processing & Ajax Functionality:** 
> /_process_form.php
    
This is standalone and accessed almost primarily via Ajax from **/includes/all_scripts.php**   
   
**There are many good examples of how to use ``` $USR->insertDynamic ``` and ``` $USR->updateDynamic ``` in this page**   
     
### **Common JS (with some dynamically created JS>from>PHP):** 
>/includes/all_scripts.php    

  **noteworthy functions:**
  - notify users: uNotify(...)
  - Ajax Submit: dynSubmit(...)
  - Ajax Processing: execAjax()
       
### **External or Additional Includes** 
>/includes/vendorscripts.php


### **Basic login access protection:** 
> /_logout_enforce.php

This is included in index.php so only logged in users can access any existing page as a baseline    

To allow certain types of users and not others (see report_totals.php for how to use this), you would use:        
>/_logout_usertype_enforce.php   

This is included in index.php so only logged in users can access any existing page as a baseline    
  
### **CSS:** 
> /assets/custom_overrides.css

and if too messy / much to process, the last file written to the chasis page is :
>/assets/custom_final.css - so this has the final word on styling  ;)

  
   
