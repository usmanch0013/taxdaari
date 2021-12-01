<?php
/*******************************************************************************
* File with setting callbacks
*******************************************************************************/

/**
* Generates a settings form
* @param void
* @return HTML
**/
function clarity_admin_settings_page(){
  if (!current_user_can('administrator')) {
    return;
  }else {
	update_option("display_clarity_notice", false);
    ?>
      <div class="clarity_submenu_page">
        <h1>Clarity Settings</h1>
        <form action="options.php" method="post">
          <?php
            settings_fields('clarity_settings_fields');
            do_settings_sections('clarity_settings');
            ?>
            <?php
            submit_button();
          ?>
        </form>
      </div>
    <?php
  }
}

/**
* Displays settings section
* @param void
* @return HTML
**/
function clarity_section_project_id_callback(){
?>
  <div class="clarity_submenu_page_container">
    <p>Before you can start learning how people are using your site, we need to take a few more steps.</p>
    <h3>Instructions</h3>
      <ol>
        <li>If you don't already have a project on Clarity, create a project <a href="https://clarity.microsoft.com/projects?snpf=1">here</a>.</li>
        <li>Click on your project, and find the Wordpress installation guide under "Install tracking code on third-party platforms" in setup.</li>
        <li>Copy your project id from the Wordpress installation guide.</li>
        <li>Paste in the project id in the input box below.</li>
        <?php
        ?>
      </ol>
  </div>
<?php
}

/**
* Generates a settings input for introducing the project ID
* @param void
* @return HTML
**/
function clarity_settings_field_project_id_callback($args){
  $p_id_option = get_option('clarity_project_id', clarity_project_id_default_value());
  ?>
    <input 
      type="text"
      name="clarity_project_id"
      value="<?= $p_id_option;  ?>"
      pattern="[0-9a-z]+"
      title="Clarity project id should only contain numbers and letters.">
  <?php

}

/**
* Generates a settings form
* @param void
* @return HTML
**/
function clarity_project_id_default_value(){
  $default_value = '';
  return $default_value;
}
