<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\GUI\Views;

use Automad\Core\Str;
use Automad\GUI\Components\Header\BlockSnippetArrays;
use Automad\GUI\Components\Header\EditorTextModules;
use Automad\GUI\Components\Modal\About;
use Automad\GUI\Components\Modal\AddPage;
use Automad\GUI\Controllers\Themelist;
use Automad\GUI\Controllers\User;
use Automad\GUI\Views\Elements\Navbar;
use Automad\GUI\Views\Elements\Sidebar;
use Automad\GUI\Utils\Text;
use Automad\GUI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The base for all dashboard views.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

abstract class View {


	/**
	 *	Define whether a navbar and sidebar exist.
	 */

	protected $hasNav = True;


	/**
	 *	The Automad object.
	 */

	protected $Automad = NULL;


	/**
	 *	The Automad object.
	 */

	protected $Themelist = NULL;


	/**
	 *	This property stores a simple helper to output expression in strings.
	 */

	protected $fn;


	/**
	 *	Render body.
	 *
	 *	@return string the rendered items
	 */

	abstract protected function body();


	/**
	 *	Get the title for the dashboard view.
	 *
	 *	@return string the rendered items
	 */

	abstract protected function title();


	/**
	 *	The page constructor.
	 */

	public function __construct() {
		
		$this->Automad = UICache::get();
		$this->Themelist = new Themelist();
		$this->fn = function ($expression) {
			return $expression;
		};

	}


	/**
	 *	Render a dashboard page.
	 *
	 *	@return string the rendered dashboard
	 */

	public function render() {

		$fn = $this->fn;

		$versionSanitized = Str::sanitize(AM_VERSION);
		$push = '';

		if ($this->hasNav) {
			$push = 'am-sidebar-push';
		}

		return <<< HTML
			<!DOCTYPE html>
			<html lang="en" class="am-dashboard">
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="robots" content="noindex">
				<meta 
				name="viewport" 
				content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"
				>
				<title>{$fn($this->title())}</title>
				<link href="{$fn(AM_BASE_URL)}/automad/gui/dist/favicon.ico?v=$versionSanitized" rel="shortcut icon" type="image/x-icon" />
				<link href="{$fn(AM_BASE_URL)}/automad/gui/dist/libs.min.css?v=$versionSanitized" rel="stylesheet">
				<link href="{$fn(AM_BASE_URL)}/automad/gui/dist/automad.min.css?v=$versionSanitized" rel="stylesheet">
				<script type="text/javascript" src="{$fn(AM_BASE_URL)}/automad/gui/dist/libs.min.js?v=$versionSanitized"></script>
				<script type="text/javascript" src="{$fn(AM_BASE_URL)}/automad/gui/dist/automad.min.js?v=$versionSanitized"></script>
				{$fn(BlockSnippetArrays::render())}
				{$fn(EditorTextModules::render())}
			</head>
			<body>
				{$fn($this->navbar())}
				{$fn($this->sidebar())}
				<div class="$push am-navbar-push uk-container uk-container-center">
					{$fn($this->body())}
					{$fn($this->footer())}
				</div>
				<div id="am-no-js" class="uk-animation-fade">
					<div class="uk-container uk-container-center uk-margin-large-top">
						<div class="uk-container-center uk-width-medium-1-2">
							<div class="uk-alert uk-alert-danger">
								{$fn(Text::get('error_no_js'))}
							</div>
						</div>
					</div>
				</div>
			</body>
			</html>
HTML;

	}


	/**
	 *	Render a navbar.
	 *
	 *	@return string the rendered navbar
	 */

	protected function navbar() {

		if ($this->hasNav) {
			return Navbar::render($this->Automad);
		}
		

	}


	/**
	 *	Render a sidebar.
	 *
	 *	@return string the rendered sidebar
	 */

	protected function sidebar() {

		if ($this->hasNav) {
			return Sidebar::render($this->Automad);
		}
		

	}


	/**
	 *	Render footer in case a user is logged in.
	 *
	 *	@return string the rendered footer
	 */

	private function footer() {

		if (!$this->Automad) {
			return false;
		}

		$fn = $this->fn;

		return <<< HTML
			<div class="am-footer uk-position-bottom">
				<a 
				href="#am-about-modal" 
				class="uk-button uk-button-mini uk-text-muted" 
				data-uk-modal
				>
					Automad &mdash; Version {$fn(AM_VERSION)}
				</a>
			</div>
			{$fn(About::render('am-about-modal'))}
			{$fn(AddPage::render($this->Automad, $this->Themelist))}
HTML;

	}


}