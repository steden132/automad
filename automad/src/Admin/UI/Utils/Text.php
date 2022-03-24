<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2016-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin\UI\Utils;

use Automad\Core\Debug;
use Automad\Core\Parse;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Text class provides all methods related to the text modules used in the UI.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Text {
	/**
	 * Array of UI text modules.
	 */
	private static $modules = null;

	/**
	 * Return the requested text module.
	 *
	 * @param string $key
	 * @return string The requested text module
	 */
	public static function get(string $key) {
		self::parseModules();

		if (isset(Text::$modules[$key])) {
			return Text::$modules[$key];
		}
	}

	/**
	 * Return the modules as object to be used in heredoc strings.
	 *
	 * @return object The modules array as object
	 */
	public static function getObject() {
		self::parseModules();

		return (object) self::$modules;
	}

	/**
	 * Parse the text modules file and store all modules in Text::$modules.
	 * In case AM_FILE_UI_TRANSLATION is defined, the translated text modules
	 * will be merged into Text:$modules.
	 */
	private static function parseModules() {
		if (self::$modules) {
			return false;
		}

		Text::$modules = Parse::dataFile(AM_FILE_UI_TEXT_MODULES);

		if (AM_FILE_UI_TRANSLATION) {
			$translationFile = AM_BASE_DIR . AM_FILE_UI_TRANSLATION;

			if (is_readable($translationFile)) {
				$translation = Parse::dataFile($translationFile);

				if (is_array($translation)) {
					Text::$modules = array_merge(Text::$modules, $translation);
				}
			}
		}

		array_walk(Text::$modules, function (&$item) {
			$item = Str::markdown($item, true);
			// Remove all line breaks to avoid problems when using text modules in JS notify.
			$item = str_replace(array("\n", "\r"), '', $item);
		});

		Debug::log('Parsed text modules');
	}
}