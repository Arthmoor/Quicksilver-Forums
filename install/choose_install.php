	 <form action='<?php echo $qsf->self; ?>' method='get'>
	  <div class='article'>
	   <div class='title'>Choose Installation Type:</div>

           <span class='field'><input id='install' type='radio' name='mode' value='new_install' checked='checked' /><label for='install'>New Installation</label></span>
           <span class='form'>            
            This can be used to install a fresh copy of Quicksilver Forums, either brand new or while wiping out a previous installation.
           </span>
           <p class='line'></p>

           <span class='field'><input id='upgrade' type='radio' name='mode' value='upgrade' /><label for='upgrade'>Upgrade Existing Forum</label></span>
           <span class='form'>
            Used to upgrade an existing installation to the latest version of Quicksilver Forums.
           </span>
           <p class='line'></p>

           <span class='field'><input id='convert' type='radio' name='mode' value='convert' /><label for='convert'>Convert From Another Package</label></span>
           <span class='form'>
            If you are running an existing forum, but wish to switch to Quicksilver Forums while preserving your data, use this option.
           </span>
           <p class='line'></p>

           <div style='text-align:center'><input type='submit' name='submit' value='Continue' /></div>
	  </div>
         </form>