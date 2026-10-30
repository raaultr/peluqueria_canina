import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('click', (event) => {
	const toggle = event.target.closest('[data-password-toggle]');
	if (!toggle) {
		return;
	}

	const input = document.getElementById(toggle.getAttribute('aria-controls'));
	if (!(input instanceof HTMLInputElement)) {
		return;
	}

	const isVisible = input.type === 'password';
	input.type = isVisible ? 'text' : 'password';
	toggle.setAttribute('aria-pressed', String(isVisible));
	toggle.setAttribute('aria-label', isVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
});

const initializedAdminLists = new WeakSet();
const initializeAdminLists = () => {
	document.querySelectorAll('[data-admin-list]').forEach((view) => {
		if (initializedAdminLists.has(view)) {
			return;
		}
		initializedAdminLists.add(view);

	const rows = [...view.querySelectorAll('[data-admin-row]')];
	const search = view.querySelector('[data-admin-search]');
	const filterPanel = view.querySelector('[data-admin-filters-panel]');
	const filterToggle = view.querySelector('[data-admin-filters-toggle]');
	const filterSelects = [...view.querySelectorAll('[data-admin-filter]')];
	const dateFrom = view.querySelector('[data-admin-date-from]');
	const dateTo = view.querySelector('[data-admin-date-to]');
	const pageSizeSelect = view.querySelector('[data-admin-page-size]');
	const previousButton = view.querySelector('[data-admin-previous]');
	const nextButton = view.querySelector('[data-admin-next]');
	const emptyState = view.querySelector('[data-admin-empty]');
	const itemName = view.dataset.adminItemName || 'registros';
	let currentPage = 1;
	let pageSize = Number(pageSizeSelect?.value || 10);
	const syncStateActions = () => {
		const selectedState = view.querySelector('[data-admin-filter="estado"]')?.value || 'todos';
		view.querySelectorAll('[data-admin-state-link]').forEach((link) => {
			const linkUrl = new URL(link.href);
			linkUrl.searchParams.set('estado', selectedState);
			link.href = linkUrl.toString();
		});
		view.querySelectorAll('[data-admin-status-form]').forEach((form) => {
			const formUrl = new URL(form.action);
			formUrl.searchParams.set('estado', selectedState);
			form.action = formUrl.toString();
		});
	};

	const updateTable = () => {
		const query = search?.value.trim().toLocaleLowerCase() || '';
		const selectedFilters = Object.fromEntries(filterSelects.map((select) => [select.dataset.adminFilter, select.value]));
		const filteredRows = rows.filter((row) => {
			const matchesSearch = row.textContent.toLocaleLowerCase().includes(query);
			const matchesFilters = Object.entries(selectedFilters).every(([name, value]) => !value || value === 'todos' || row.getAttribute(`data-filter-${name}`) === value);
			const createdAt = row.getAttribute('data-filter-created-at') || '';
			const matchesDateRange = (!dateFrom?.value || createdAt >= dateFrom.value) && (!dateTo?.value || createdAt <= dateTo.value);

			return matchesSearch && matchesFilters && matchesDateRange;
		});
		const pageCount = Math.max(1, Math.ceil(filteredRows.length / pageSize));
		currentPage = Math.min(currentPage, pageCount);
		const firstRow = (currentPage - 1) * pageSize;
		const visibleRows = new Set(filteredRows.slice(firstRow, firstRow + pageSize));

		rows.forEach((row) => {
			row.hidden = !visibleRows.has(row);
		});

		if (emptyState) {
			emptyState.hidden = filteredRows.length > 0;
			const emptyMessage = emptyState.querySelector('td');
			if (emptyMessage) {
				emptyMessage.textContent = rows.length ? view.dataset.adminEmptyFiltered : view.dataset.adminEmptyAll;
			}
		}

		const lastRow = Math.min(firstRow + visibleRows.size, filteredRows.length);
		const countText = filteredRows.length === rows.length
			? `${filteredRows.length} ${itemName}`
			: `${filteredRows.length} de ${rows.length} ${itemName}`;
		const paginationText = filteredRows.length
			? `Mostrando ${firstRow + 1}–${lastRow} de ${filteredRows.length} ${itemName}`
			: `Mostrando 0 de ${rows.length} ${itemName}`;
		const count = view.querySelector('[data-admin-count]');
		const paginationSummary = view.querySelector('[data-admin-pagination-summary]');
		const pageIndicator = view.querySelector('[data-admin-page-indicator]');

		if (count) {
			count.textContent = countText;
		}
		if (paginationSummary) {
			paginationSummary.textContent = paginationText;
		}
		if (pageIndicator) {
			pageIndicator.textContent = `${currentPage} / ${pageCount}`;
		}
		if (previousButton) {
			previousButton.disabled = currentPage <= 1;
		}
		if (nextButton) {
			nextButton.disabled = currentPage >= pageCount;
		}
	};

	search?.addEventListener('input', () => {
		currentPage = 1;
		updateTable();
	});
	filterSelects.forEach((select) => select.addEventListener('change', () => {
		currentPage = 1;
		if (select.dataset.adminFilter === 'estado') {
			syncStateActions();
		}
		updateTable();
	}));
	[dateFrom, dateTo].filter(Boolean).forEach((input) => input.addEventListener('change', () => {
		currentPage = 1;
		updateTable();
	}));
	pageSizeSelect?.addEventListener('change', () => {
		pageSize = Number(pageSizeSelect.value);
		currentPage = 1;
		updateTable();
	});
	filterToggle?.addEventListener('click', () => {
		if (!filterPanel) {
			return;
		}

		filterPanel.hidden = !filterPanel.hidden;
		filterToggle.setAttribute('aria-expanded', String(!filterPanel.hidden));
		view.querySelector('[data-admin-filter-chevron]')?.classList.toggle('rotate-180', !filterPanel.hidden);
	});
	view.querySelector('[data-admin-reset]')?.addEventListener('click', () => {
		if (search) {
			search.value = '';
		}
		filterSelects.forEach((select) => {
			select.value = select.dataset.adminFilter === 'estado' ? 'todos' : '';
		});
		syncStateActions();
		if (dateFrom) {
			dateFrom.value = '';
		}
		if (dateTo) {
			dateTo.value = '';
		}
		currentPage = 1;
		updateTable();
	});
	previousButton?.addEventListener('click', () => {
		currentPage -= 1;
		updateTable();
	});
	nextButton?.addEventListener('click', () => {
		currentPage += 1;
		updateTable();
	});

	updateTable();
});
};

initializeAdminLists();
document.addEventListener('turbo:load', initializeAdminLists);

const adminModal = document.querySelector('[data-admin-modal]');
if (adminModal) {
	const modalContent = adminModal.querySelector('[data-admin-modal-content]');
	const closeModal = () => window.location.assign(adminModal.dataset.closeUrl);
	const focusableElements = [...(modalContent?.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled])') || [])];

	modalContent?.querySelector('input:not([type="hidden"]), select, textarea, button')?.focus();
	adminModal.querySelector('[data-modal-backdrop]')?.addEventListener('click', closeModal);
	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeModal();
			return;
		}
		if (event.key !== 'Tab' || focusableElements.length === 0) {
			return;
		}

		const firstElement = focusableElements[0];
		const lastElement = focusableElements[focusableElements.length - 1];
		if (event.shiftKey && document.activeElement === firstElement) {
			event.preventDefault();
			lastElement.focus();
		} else if (!event.shiftKey && document.activeElement === lastElement) {
			event.preventDefault();
			firstElement.focus();
		}
	});
}
