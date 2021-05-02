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
 *	Copyright (c) 2020-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\Blocks;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The embed block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Embed extends Paragraph {


	/**	
	 *	Render a embed block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		$attr = <<< HTML
				scrolling="no"
				frameborder="no"
				allowtransparency="true"
				allowfullscreen="true"
HTML;

		if ($data->service == 'twitter') {

			$url = Core\Str::stripStart($data->embed, 'https://twitframe.com/show?url=');
			$html = <<< HTML
					<blockquote class="twitter-tweet tw-align-center" style="visibility: hidden;">
						<a href="$url"></a>
					</blockquote>
					<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
HTML;

		} else if (!empty($data->width)) {

			$paddingTop = $data->height / $data->width * 100;
			$html = <<< HTML
					<div style="position: relative; padding-top: $paddingTop%;">
						<iframe 
						src="$data->embed"
						$attr
						style="position: absolute; top: 0; width: 100%; height: 100%;"
						>
						</iframe>
					</div>
HTML;

		} else {

			$html = <<< HTML
					<iframe 
					src="$data->embed"
					height="$data->height"
					$attr
					style="width: 100%;"
					>
					</iframe>
HTML;

		}

		if (!empty($data->caption)) {
			$html .= "<figcaption>$data->caption</figcaption>";
		}

		$class = self::classAttr();

		return "<am-embed $class><figure>$html</figure></am-embed>";

	}


}