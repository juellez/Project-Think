/**
 * Project Category Filter - Frontend JavaScript
 * Provides real-time filtering by showing/hiding existing posts
 */

(function () {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        const filterContainer = document.querySelector('.project-category-filter');
        if (!filterContainer) return;

        const queryLoop = document.querySelector('.wp-block-query');
        if (!queryLoop) {
            console.warn('No Query Loop block found - filter will use standard page navigation');
            return;
        }

        const taxonomy = filterContainer.getAttribute('data-taxonomy') || 'category';
        const displayStyle = filterContainer.getAttribute('data-display-style') || 'buttons';

        // Initialize based on display style
        if (displayStyle === 'dropdown') {
            initDropdownFilter(filterContainer, queryLoop, taxonomy);
        } else {
            initLinkFilter(filterContainer, queryLoop, taxonomy);
        }
    }

    /**
     * Initialize dropdown-style filter
     */
    function initDropdownFilter(filterContainer, queryLoop, taxonomy) {
        const select = filterContainer.querySelector('.project-category-filter__select');
        if (!select) return;

        select.addEventListener('change', function (e) {
            const termId = e.target.value;
            const selectedOption = e.target.options[e.target.selectedIndex];
            const url = selectedOption.getAttribute('data-url');

            filterProjects(queryLoop, termId, taxonomy, filterContainer, url);
        });
    }

    /**
     * Initialize link/button-style filter
     */
    function initLinkFilter(filterContainer, queryLoop, taxonomy) {
        const links = filterContainer.querySelectorAll('[data-filter-link]');
        if (!links.length) return;

        links.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const termId = link.getAttribute('data-term-id');
                const url = link.getAttribute('href');

                // Update active state
                links.forEach(function (l) {
                    l.classList.remove('is-active');
                });
                link.classList.add('is-active');

                filterProjects(queryLoop, termId, taxonomy, filterContainer, url);
            });
        });
    }

    /**
     * Filter projects by showing/hiding posts
     */
    function filterProjects(queryLoop, termId, taxonomy, filterContainer, fallbackUrl) {
        const loadingIndicator = filterContainer.querySelector('.project-category-filter__loading');
        const postList = queryLoop.querySelector('.wp-block-post-template');
        const pagination = queryLoop.querySelector('.wp-block-query-pagination');

        if (!postList) {
            console.error('Could not find post template in Query Loop');
            window.location.href = fallbackUrl;
            return;
        }

        // Show loading state
        if (loadingIndicator) {
            loadingIndicator.style.display = 'flex';
        }
        postList.style.opacity = '0.5';

        // If "All" is selected, show everything
        if (!termId || termId === '0') {
            const allPosts = postList.querySelectorAll('.wp-block-post');
            allPosts.forEach(function(post) {
                post.style.display = '';
            });

            // Remove any no-results message
            const noResultsEl = postList.querySelector('.no-results-message');
            if (noResultsEl) {
                noResultsEl.remove();
            }

            if (pagination) {
                pagination.style.display = '';
            }

            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
            postList.style.opacity = '1';

            // Update history
            if (window.history && window.history.pushState) {
                window.history.pushState({ termId: null }, '', window.location.pathname);
            }
            return;
        }

        // Get filtered post IDs using REST API
        const apiUrl = '/wp-json/wp/v2/posts?_fields=id&per_page=100&categories=' + termId;

        console.log('Filtering by category:', termId);
        console.log('API URL:', apiUrl);

        fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(function (posts) {
            console.log('Filtered posts from API:', posts);

            const filteredIds = posts.map(function(p) { return parseInt(p.id); });
            console.log('Filtered post IDs:', filteredIds);

            const allPosts = postList.querySelectorAll('.wp-block-post');
            console.log('Total posts in DOM:', allPosts.length);

            let visibleCount = 0;

            // Show/hide posts based on category
            allPosts.forEach(function(postEl) {
                const postId = extractPostIdFromElement(postEl);
                console.log('Post element ID:', postId, 'Should show:', filteredIds.includes(postId));

                if (postId && filteredIds.includes(postId)) {
                    postEl.style.display = '';
                    visibleCount++;
                } else {
                    postEl.style.display = 'none';
                }
            });

            console.log('Visible posts after filtering:', visibleCount);

            // If no posts visible, show message
            if (visibleCount === 0) {
                // Create and show no results message
                const noResults = document.createElement('li');
                noResults.className = 'wp-block-post no-results-message';
                noResults.style.cssText = 'width: 100%; grid-column: 1 / -1; text-align: center; padding: 40px;';
                noResults.innerHTML = '<p style="font-size: 1.2em; color: #666;">No projects found for this category.</p>';
                postList.appendChild(noResults);
            } else {
                // Remove any existing no-results message
                const noResultsEl = postList.querySelector('.no-results-message');
                if (noResultsEl) {
                    noResultsEl.remove();
                }
            }

            // Hide pagination during filtered view
            if (pagination) {
                pagination.style.display = 'none';
            }

            // Update browser history
            if (window.history && window.history.pushState) {
                window.history.pushState({ termId: termId }, '', fallbackUrl);
            }

            // Scroll to results
            queryLoop.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(function (error) {
            console.error('Error filtering projects:', error);
            // Fallback to page navigation if AJAX fails
            window.location.href = fallbackUrl;
        })
        .finally(function () {
            // Hide loading state
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
            postList.style.opacity = '1';
        });
    }

    /**
     * Extract post ID from WordPress post element
     * WordPress adds classes like "post-201"
     */
    function extractPostIdFromElement(element) {
        // Try class names - look for "post-{ID}" pattern
        const classes = element.className.split(' ');
        for (let i = 0; i < classes.length; i++) {
            const className = classes[i];

            // Match pattern: post-{number}
            if (/^post-\d+$/.test(className)) {
                const id = parseInt(className.replace('post-', ''));
                if (!isNaN(id)) {
                    return id;
                }
            }
        }

        // Try id attribute
        if (element.id && /^post-\d+$/.test(element.id)) {
            return parseInt(element.id.replace('post-', ''));
        }

        // Try data attribute
        if (element.getAttribute('data-post-id')) {
            return parseInt(element.getAttribute('data-post-id'));
        }

        console.warn('Could not extract post ID from element:', element);
        return null;
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function (event) {
        window.location.reload();
    });

})();
