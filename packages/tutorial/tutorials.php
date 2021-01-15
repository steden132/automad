<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>	
	<@ elements/navbar.php @>
	<section class="section">		
		<@ elements/content.php @>
	</section>
	<section class="section">
		<div class="columns is-multiline is-8 is-variable">
			<@ newPagelist { 
				type: 'children',
				excludeHidden: false 
			} @>
			<@ foreach in pagelist @>
				<div class="column is-one-third">
					<hr />
					<div class="field is-size-4 has-text-weight-bold">
						@{ title }
					</div>
					<div class="field is-size-6">@{ textTeaser | 150 }</div>
					<a href="@{ url }" class="button is-light">More</a>
				</div>
			<@ end @>
		</div>
	</section>
<@ elements/footer.php @>