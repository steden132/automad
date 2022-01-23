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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App } from '../core/app';
import { create } from '../core/create';
import { waitForPendingRequests } from '../core/request';
import { query } from '../core/utils';
import { BaseComponent } from './Base';

type ViewName = 'Page' | 'System' | 'Shared' | 'Home' | 'Packages';

type Views = {
	[name in ViewName]: string;
};

const getViewSlug = (dashboard: string): ViewName => {
	const regex = new RegExp(`^${dashboard}\/`, 'i');

	return (window.location.pathname.replace(regex, '') as ViewName) || 'Home';
};

export const viewMap: Views = {
	Page: 'am-page',
	System: 'am-system',
	Shared: 'am-shared',
	Home: 'am-home',
	Packages: 'am-packages',
};

/**
 * The root app component.
 *
 * @extends BaseComponent
 */
export class RootComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['dashboard'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();
	}

	/**
	 * Init the root component.
	 */
	private async init(): Promise<void> {
		await App.bootstrap(this);

		this.updateView();
	}

	/**
	 * Update the root component.
	 */
	async updateView(): Promise<void> {
		await App.updateState();

		const slug = getViewSlug(this.elementAttributes.dashboard);
		const view = await create(viewMap[slug], [], {}).init();

		await waitForPendingRequests();

		this.innerHTML = '';
		this.appendChild(view);

		setTimeout(() => {
			query('html').scrollTop = 0;
		}, 0);
	}
}

customElements.define('am-root', RootComponent);