# ProForm
Please see the License section at the end of this file. By using ProForm, you are agreeing to this license.

## Documentation
Please see the full documentation at in proform_user_guide.pdf:
  https://github.com/airways/ProForm/blob/master/proform_user_guide.pdf

## Installation
Installing ProForm is similar to installing any third-party Module into ExpressionEngine.

### Installation Steps
Follow these steps to install ProForm:

 0. This add-on REQUIRES ProLib to be in your third_party directory. Get it here:
       https://github.com/airways/ProLib

 1. (For old purchased ZIP file release version) Download the ZIP file from the location where you purchased it.

 2. (For old purchased ZIP file release version) Extract the ZIP file.

 3. Move directories:
 
 <b>For git checkout / git ZIP file:</b>
 
 NOTE: _These steps do <b>not</b> apply to the old purchased ZIP file download, only to a git checkout and the new git ZIP file builds which are the only ones available via GitHub._
    
   a. Check out or copy files from the repo directly to [doc_root]/system/expressionengine/third_party/proform
   
   b. Move or create a symlink from [doc_root]/system/expressionengine/third_party/proform/themes to [doc_root]/themes/third_party/proform
   
   c. Check out or copy files from the the ProLib repo directly to [doc_root]/system/expressionengine/third_party/prolib
   
   d. Move or create a symlink from [doc_root]/system/expressionengine/third_party/prolib/themes to [doc_root]/themes/third_party/prolib
 
<b>For old purchased ZIP file build:</b>
   
  NOTE: _These steps do <b>not</b> apply to the git ZIP file download, only to the old purchased ZIP file builds which are not available via GitHub._
   
   There are four folders within the extracted folder from the ZIP file. Copy each one to the matching location
   within your ExpressionEngine installation:
   
   [extracted_folder]/system/expressionengine/third_party/proform → [doc_root]/system/expressionengine/third_party/proform
        
   [extracted_folder]/system/expressionengine/third_party/prolib → [doc_root]/system/expressionengine/third_party/prolib
        
   [extracted_folder]/themes/third_party/proform → [doc_root]/themes/third_party/proform
        
   [extracted_folder]/themes/third_party/prolib → [doc_root]/themes/third_party/prolib
        
   
 4. Visit your control panel, usually located at:
        http://example.com/system/

 5. Click on Add-ons > Modules in the main menu.

 6. Within the list of Modules you will find an entry for ProForm, click on the Install link next to this entry.

 7. You will be taken to a Package Settings screen, listing entries for ProForm's components. Make sure that all
   components have Install selected.

 8. Click Submit to finish installation.

 9. After the module is installed, you can find a link to it's main page in the list at Add-ons > Modules.

10. On the main ProForm page, you may want to click the + Add link in the menu bar. This will add a main menu
   item for ProForm so you can get it to more easily.

You may now use the control panel page for ProForm to being managing forms.


## License

Copyright (c)2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016.
Isaac Raway and MetaSushi, LLC. All rights reserved.

You may use this software under a commercial license, if you have one,
or under the GPL v3 contained in LICENSE, in which case you MUST
comply with all GPL requirements.

This source is commercial software. Use of this software requires a
site license for each domain it is used on. Use of this software or any
of its source code without express written permission in the form of
a purchased commercial or other license is prohibited.

THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
PARTICULAR PURPOSE.

As part of the license agreement for this software, all modifications
to this source must be submitted to the original author for review and
possible inclusion in future releases. No compensation will be provided
for patches, although where possible we will attribute each contribution
in file revision notes. Submitting such modifications constitutes
assignment of copyright to the original author (Isaac Raway and
MetaSushi, LLC) for such modifications. If you do not wish to assign
copyright to the original author, your license to  use and modify this
source is null and void. Use of this software constitutes your agreement
to this clause.

## Credits
ProForm uses icons from the famfamfam Silk icon set: http://www.famfamfam.com/lab/icons/silk/
