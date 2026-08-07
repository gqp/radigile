import './bootstrap';

/**
 * Wires a search <input> to live-update a results container via AJAX,
 * without a full page reload. Debounced, and only fires once the term is
 * empty (reset) or at least `minChars` long.
 *
 * @param {string} inputId - id of the <input> rendered by <x-search-box id="...">
 * @param {string} resultsId - id of the container wrapping the swappable results partial
 * @param {object} [options]
 * @param {number} [options.minChars=2]
 * @param {number} [options.debounceMs=300]
 * @param {Function} [options.onSwap] - called after new HTML is inserted, e.g. to reapply a view-mode toggle
 */
window.initLiveSearch = function initLiveSearch(inputId, resultsId, options = {}) {
    const input = document.getElementById(inputId);
    const results = document.getElementById(resultsId);
    if (!input || !results) return;

    const minChars = options.minChars ?? 2;
    const debounceMs = options.debounceMs ?? 300;
    const baseUrl = results.dataset.liveUrl;
    const paramName = input.name;
    const pageParam = input.dataset.pageParam || 'page';

    let debounceTimer;

    input.addEventListener('input', function () {
        const term = input.value.trim();
        if (term.length > 0 && term.length < minChars) {
            return;
        }

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            const url = new URL(baseUrl, window.location.origin);
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.forEach((value, key) => url.searchParams.set(key, value));

            if (term) {
                url.searchParams.set(paramName, term);
            } else {
                url.searchParams.delete(paramName);
            }
            url.searchParams.delete(pageParam);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((response) => response.text())
                .then((html) => {
                    results.innerHTML = html;
                    if (options.onSwap) {
                        options.onSwap();
                    }
                    window.history.replaceState({}, '', url);
                })
                .catch(() => {
                    // Live search is a progressive enhancement — on failure,
                    // the existing <form method="GET"> submit still works.
                });
        }, debounceMs);
    });
};
