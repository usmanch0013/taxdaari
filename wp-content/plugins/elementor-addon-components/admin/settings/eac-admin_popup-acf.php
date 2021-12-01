<?php

?>
<!-- The modal / dialog box, hidden somewhere near the footer -->
<div id="eac-dialog_acf-json" class="hidden" style="max-width:800px">
  <h3><?php _e('Dossier ACF-JSON', 'eac-components'); ?></h3>
  <p><?php _e("Cette fonctionnalité va créer le dossier 'acf-json' dans le plugin s'il n'existe pas dans le thème courant.", "eac-components"); ?></p>
  <p><?php _e("Les groupes et les champs ACF seront enregistrés localement dans ce dossier au format JSON.", "eac-components"); ?></p>
  <p><?php _e("L'idée est similaire à la mise en cache, et à la fois accélère considérablement ACF et permet le contrôle de version sur vos paramètres de champ.", "eac-components"); ?></p>
  <p><?php _e("Dossier '/includes/acf' du plugin.", "eac-components"); ?></p>
  <p><?php _e("Consulter la <a href='https://www.advancedcustomfields.com/resources/local-json/' target='_autre'>documentation officielle</a>", "eac-components"); ?></p>
</div>
<?php