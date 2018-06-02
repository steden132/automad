<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	
	<div class="uk-button-dropdown" data-uk-dropdown>
		<button class="uk-button">
			<@ if @{ ?filter } @>
				@{ ?filter }
			<@ else @>
				@{ labelShowAll | def ('Show All') }
			<@ end @>
		</button>
		<div class="uk-dropdown uk-dropdown-small uk-text-left">
			<ul class="uk-nav uk-nav-dropdown">
				<li>
					<# Also reset pagination! #>
					<a href="?<@ queryStringMerge { filter: false, page: 1 } @>">
						<@ if not @{ ?filter } @>
							<i class="uk-icon-circle"></i>
						<@ else @>
							<i class="uk-icon-circle-thin"></i>
						<@ end @>
						&nbsp;@{ labelShowAll | def ('Show All') }
					</a>
				</li>
				<@ foreach in filters @>
					<li>
						<a href="?<@ queryStringMerge { filter: @{ :filter }, page: 1 } @>">
							<@ if @{ ?filter } = @{ :filter } @>
								<i class="uk-icon-circle"></i>
							<@ else @>
								<i class="uk-icon-circle-thin"></i>
							<@ end @>
							&nbsp;@{ :filter }
						</a>
					</li>
				<@ end @>
			</ul>
		</div>
	</div>