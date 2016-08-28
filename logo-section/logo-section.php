<div class="logo-section">
	<div class="logo-section__text">
		<h3 class="logo-section__title"><?= $customInputs['logo-text']; ?></h3>
	</div>
	<div class="logo-section__logos-container">
		<?php
			foreach( $uniquelyRandomLogos as $randomLogoNumber ){
				$randomLogo = $loopingInputs[$randomLogoNumber]; ?>
				<div class="logo-section__logo">
		    		<?php srcSet( imagePath( $site_path ) . $randomLogo['logo'], $randomLogo['company'], 150, 84, 'fit' ); ?>
				</div>
			<?php } ?>
	</div>
</div>
