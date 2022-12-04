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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { discover } from 'dropzone';
import {
	App,
	classes,
	create,
	debounce,
	fire,
	getFormData,
	html,
	listen,
	queryAll,
	requestAPI,
	Routes,
} from '../../core';
import { FieldResults, FileResults, KeyValueMap } from '../../types';
import { BaseComponent } from '../Base';

/**
 * Render the inner content for a given field in the results card.
 *
 * @param fieldResultsArray
 * @returns the rendered field result
 */
const renderFieldResults = (fieldResultsArray: FieldResults[]) => {
	return fieldResultsArray.reduce((output, fieldResults): string => {
		return html`
			${output}
			<div
				class="${classes.flex} ${classes.flexColumn} ${classes.flexGap}"
			>
				<small class="${classes.textMuted}">
					${fieldResults.field}
				</small>
				<div>${fieldResults.context}</div>
			</div>
		`;
	}, '');
};

/**
 * Render a card for all results for a given file.
 *
 * @param fileResults
 * @returns the rendered file card
 */
const renderFileCard = (fileResults: FileResults) => {
	const { path, fieldResultsArray, url } = fileResults;
	const fields: string[] = fieldResultsArray.reduce(
		(fields, fieldResults): string[] => {
			return fields.concat([fieldResults.field]);
		},
		[]
	);

	return html`
		<div class="${classes.card} ${classes.cardList}">
			<div
				class="${classes.flex} ${classes.flexBetween} ${classes.flexAlignCenter}"
			>
				<am-link
					target="${path.match(/^\/shared\//i)
						? Routes.shared
						: `${Routes.page}?url=${url}`}"
					class="${classes.textPrimary} ${classes.iconText}"
				>
					<i class="bi bi-file-earmark-text"></i>
					<span>${url || App.text('sharedTitle')}</span>
				</am-link>
				<am-checkbox
					name="${path}"
					value="${fields.join()}"
					checked
				></am-checkbox>
			</div>
			${renderFieldResults(fieldResultsArray)}
		</div>
	`;
};

/**
 * The delete users form.
 *
 * @extends BaseComponent
 */
export class SearchFormComponent extends BaseComponent {
	/**
	 * Get the api attribute already before attributes are observed.
	 */
	protected get api(): string {
		return 'Search/searchReplace';
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.flex, classes.flexGap, classes.flexColumn);

		const { searchBar, search, isRegex, isCaseSensitive, replace } =
			this.createForm();
		const { replaceButton, checkAll, unCheckAll } = this.createButtons();
		const resultsContainer = create(
			'section',
			[classes.flex, classes.flexColumn, classes.flexGap],
			{},
			this
		);

		const performRequest = async (replaceData: KeyValueMap = {}) => {
			const data: KeyValueMap = Object.assign(
				{
					searchValue: search.value,
					isRegex: isRegex.checked,
					isCaseSensitive: isCaseSensitive.checked,
				},
				replaceData
			);

			const response = await requestAPI(this.api, data);

			this.createResults(response, resultsContainer);
		};

		const performSearch = debounce(async () => {
			const url = new URL(window.location.href);

			url.searchParams.set('search', search.value);
			window.history.replaceState(null, null, url);

			performRequest();
		}, 200);

		const performReplace = () => {
			const files = JSON.stringify(getFormData(resultsContainer));

			performRequest({
				replaceValue: replace.value,
				replaceSelected: true,
				files,
			});
		};

		listen(searchBar, 'input', performSearch, 'input');
		listen(replaceButton, 'click', performReplace);

		const toggle = (state: boolean) => {
			queryAll('input', resultsContainer).forEach(
				(checkbox: HTMLInputElement) => {
					checkbox.checked = state;
					fire('input', checkbox);
				}
			);
		};

		listen(checkAll, 'click', () => {
			toggle(true);
		});

		listen(unCheckAll, 'click', () => {
			toggle(false);
		});

		performSearch();
	}

	/**
	 * Create the list of results.
	 *
	 * @returns the created results list
	 */
	private createResults(response: KeyValueMap, container: HTMLElement): void {
		const fileResultsArray: FileResults[] = response.data;

		if (fileResultsArray) {
			container.innerHTML = fileResultsArray.reduce(
				(output, fileResults): string => {
					return html`${output} ${renderFileCard(fileResults)}`;
				},
				''
			);

			return;
		}

		container.innerHTML = html`
			<am-alert
				icon="slash-circle"
				text="searchNoResults"
				type="danger"
			></am-alert>
		`;
	}

	/**
	 * Create all form inputs.
	 *
	 * @returns the created form input elements
	 */
	private createForm(): KeyValueMap {
		const searchParams = new URLSearchParams(window.location.search);

		const searchBar = create(
			'div',
			[classes.flex, classes.flexGap],
			{},
			this
		);
		const search = create(
			'input',
			[classes.input],
			{
				type: 'text',
				name: 'searchValue',
				value: searchParams.get('search') || '',
				placeholder: App.text('searchPlaceholder'),
			},
			searchBar
		);
		const isRegex = create(
			'input',
			[],
			{
				type: 'checkbox',
				name: 'isRegex',
				title: App.text('searchIsRegex'),
			},
			searchBar
		);
		const isCaseSensitive = create(
			'input',
			[],
			{
				type: 'checkbox',
				name: 'isCaseSensitive',
				title: App.text('searchIsCaseSensitive'),
			},
			searchBar
		);
		const replace = create(
			'input',
			[classes.input],
			{
				type: 'text',
				name: 'replaceValue',
				placeholder: App.text('searchReplacePlaceholder'),
			},
			this
		);

		return {
			searchBar,
			search,
			isRegex,
			isCaseSensitive,
			replace,
		};
	}

	/**
	 * Create all button.
	 *
	 * @returns the created button elements
	 */
	private createButtons(): KeyValueMap {
		const wrapper = create(
			'div',
			[classes.flex, classes.flexBetween],
			{},
			this
		);

		const replaceButton = create(
			'span',
			[classes.button, classes.buttonPrimary],
			{},
			wrapper
		);

		replaceButton.innerHTML = App.text('searchReplaceSelected');

		const toggles = create(
			'div',
			[classes.flex, classes.flexGap],
			{},
			wrapper
		);

		const checkAll = create('span', [classes.button], {}, toggles);

		checkAll.innerHTML = html`
			<am-icon-text
				icon="check-circle"
				text="${App.text('searchReplaceCheckAll')}"
			></am-icon-text>
		`;

		const unCheckAll = create('span', [classes.button], {}, toggles);

		unCheckAll.innerHTML = html`
			<am-icon-text
				icon="circle"
				text="${App.text('searchReplaceUncheckAll')}"
			></am-icon-text>
		`;

		return {
			replaceButton,
			checkAll,
			unCheckAll,
		};
	}
}

customElements.define('am-search-form', SearchFormComponent);