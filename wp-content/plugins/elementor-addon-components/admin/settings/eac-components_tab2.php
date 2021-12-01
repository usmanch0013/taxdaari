<div id="tab-2" style="display: block;">
	<form action="" method="POST" id="eac-form-features" name="eac-form-features">
		<div class="eac-settings-tabs">
			<table class="eac-features-table">
				<tbody>
					<tr>
						<th><?php _e("Balises Dynamiques", 'eac-components'); ?><a href="https://elementor-addon-components.com/elementor-dynamic-tags/" target="_blank"><span class="eac-admin-help"></span></a></th>
						<td>
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="dynamic-tag" name="dynamic-tag" <?php checked(1, $this->get_settings_features['dynamic-tag'], true) ?>>
								<div><div></div></div>
							</label>
						</td>
						
						<th><?php _e("ACF Balises Dynamiques", 'eac-components'); ?><a href="https://elementor-addon-components.com/how-to-integrate-and-use-acf-fields-with-elementor/" target="_blank"><span class="eac-admin-help"></span></a></th>
						<td>
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="acf-dynamic-tag" name="acf-dynamic-tag" <?php checked(1, $this->get_settings_features['acf-dynamic-tag'], true) ?>>
								<div><div></div></div>
							</label>
						</td>
						
						<th><?php _e("CSS Personnalisé", 'eac-components'); ?><a href="https://elementor-addon-components.com/elementor-custom-css/" target="_blank"><span class="eac-admin-help"></span></a></th>
						<td>
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="custom-css" name="custom-css" <?php checked(1, $this->get_settings_features['custom-css'], true) ?>>
								<div><div></div></div>
							</label>
						</td>
						
						<th><?php _e("Attributs Personnalisés", 'eac-components'); ?><a href="https://elementor-addon-components.com/add-your-custom-attributes-with-elementor/" target="_blank"><span class="eac-admin-help"></span></a></th>
						<td>
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="custom-attribute" name="custom-attribute" <?php checked(1, $this->get_settings_features['custom-attribute'], true) ?>>
								<div><div></div></div>
							</label>
						</td>
					</tr>
					
					<tr>
						<th><?php _e("ACF Page d'Options", 'eac-components'); ?><a href="https://elementor-addon-components.com/how-to-add-acf-options-page-in-elementor/" target="_blank"><span class="eac-admin-help"></span></a></th>
						<td>
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="acf-option-page" name="acf-option-page" <?php checked(1, $this->get_settings_features['acf-option-page'], true) ?>>
								<div><div></div></div>
							</label>
						</td>
						<th><?php _e("Sticky Élément", 'eac-components'); ?><a href="https://elementor-addon-components.com/use-sticky-scrolling-effect-with-elementor/" target="_blank"><span class="eac-admin-help"></span></a></th>
						<td>
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="element-sticky" name="element-sticky" <?php checked(1, $this->get_settings_features['element-sticky'], true) ?>>
								<div><div></div></div>
							</label>
						</td>
						<th><?php _e("Lien Élément", 'eac-components'); ?><a href="https://elementor-addon-components.com/add-link-to-a-section-column-using-elementor/" target="_blank"><span class="eac-admin-help"></span></a></th>
						<td>
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="element-link" name="element-link" <?php checked(1, $this->get_settings_features['element-link'], true) ?>>
								<div><div></div></div>
							</label>
						</td>
						<th><?php _e("ALT Attribut", 'eac-components'); ?><a href="https://elementor-addon-components.com/add-external-image-for-elementor/#improve-your-seo-with-the-dynamic-tag-external-image" target="_blank"><span class="eac-admin-help"></span></a></th>
						<td>
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="alt-attribute" name="alt-attribute" <?php checked(1, $this->get_settings_features['alt-attribute'], true) ?>>
								<div><div></div></div>
							</label>
						</td>
					</tr>
					<tr>
						<th><?php _e("ACF JSON", 'eac-components'); ?><a href="#/"><span class="eac-admin-help acf-json"></span></a></th>
						<td colspan="8">
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="acf-json" name="acf-json" <?php checked(1, $this->get_settings_features['acf-json'], true) ?>>
								<div><div></div></div>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="eac-saving-box">
				<input id="eac-sumit" type="submit" value="<?php _e('Enregistrer les modifications', 'eac-components'); ?>">
				<div id="eac-features-saved"><?php _e('Réglages enregistrés', 'eac-components'); ?></div>
				<div id="eac-features-notsaved"><?php _e('Erreur lors de la sauvegarde...', 'eac-components'); ?></div>
			</div>
		</div>
	</form>
</div>