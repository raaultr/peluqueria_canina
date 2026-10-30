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

document.querySelectorAll('[data-mascotas-view]').forEach((view) => {
	const rows = [...view.querySelectorAll('[data-mascota-row]')];
	const search = view.querySelector('[data-mascotas-search]');
	const filterPanel = view.querySelector('[data-mascotas-filters-panel]');
	const filterToggle = view.querySelector('[data-mascotas-filters-toggle]');
	const filterSelects = [...view.querySelectorAll('[data-mascotas-filter]')];
	const pageSizeSelect = view.querySelector('[data-mascotas-page-size]');
	const previousButton = view.querySelector('[data-mascotas-previous]');
	const nextButton = view.querySelector('[data-mascotas-next]');
	const emptyState = view.querySelector('[data-mascotas-empty]');
	let currentPage = 1;
	let pageSize = Number(pageSizeSelect?.value || 10);

	const updateTable = () => {
		const query = search?.value.trim().toLocaleLowerCase() || '';
		const selectedFilters = Object.fromEntries(filterSelects.map((select) => [select.dataset.mascotasFilter, select.value]));
		const filteredRows = rows.filter((row) => {
			const matchesSearch = row.textContent.toLocaleLowerCase().includes(query);
			const matchesSex = !selectedFilters.sexo || row.dataset.mascotaSexo === selectedFilters.sexo;
			const matchesSize = !selectedFilters.tamano || row.dataset.mascotaTamano === selectedFilters.tamano;
			const matchesPhoto = !selectedFilters.foto || row.dataset.mascotaFoto === selectedFilters.foto;

			return matchesSearch && matchesSex && matchesSize && matchesPhoto;
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
				emptyMessage.textContent = rows.length ? 'No se encontraron mascotas con estos filtros.' : 'Todavía no hay mascotas registradas.';
			}
		}

		const lastRow = Math.min(firstRow + visibleRows.size, filteredRows.length);
		const countText = filteredRows.length === rows.length
			? `${filteredRows.length} mascotas`
			: `${filteredRows.length} de ${rows.length} mascotas`;
		const paginationText = filteredRows.length
			? `Mostrando ${firstRow + 1}–${lastRow} de ${filteredRows.length} mascotas`
			: `Mostrando 0 de ${rows.length} mascotas`;
		const count = view.querySelector('[data-mascotas-count]');
		const paginationSummary = view.querySelector('[data-mascotas-pagination-summary]');
		const pageIndicator = view.querySelector('[data-mascotas-page-indicator]');

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
		view.querySelector('[data-mascotas-filter-chevron]')?.classList.toggle('rotate-180', !filterPanel.hidden);
	});
	view.querySelector('[data-mascotas-reset]')?.addEventListener('click', () => {
		if (search) {
			search.value = '';
		}
		filterSelects.forEach((select) => {
			select.value = '';
		});
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
