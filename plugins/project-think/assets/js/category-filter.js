/**
 * Project Category Filter - Frontend JavaScript
 * Provides real-time AJAX filtering with progressive enhancement
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

            // If no term selected (All Projects)
            if (!termId) {
                filterProjects(queryLoop, null, taxonomy, filterContainer, url);
            } else {
                filterProjects(queryLoop, termId, taxonomy, filterContainer, url);
            }
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

                // Filter projects
                if (termId === '0') {
                    filterProjects(queryLoop, null, taxonomy, filterContainer, url);
                } else {
                    filterProjects(queryLoop, termId, taxonomy, filterContainer, url);
                }
            });
        });
    }

    /**
     * Filter projects using WordPress REST API
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

        // Build REST API URL
        let apiUrl = '/wp-json/wp/v2/posts?_embed=true&per_page=12';

        if (termId) {
            apiUrl += '&' + taxonomy + '=' + termId;
        }

        // Fetch filtered posts
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(function (posts) {
            // Update the post list
            renderPosts(posts, postList, queryLoop);

            // Hide pagination during filtered view (or update it)
            if (pagination) {
                pagination.style.display = posts.length > 12 ? '' : 'none';
            }

            // Update browser history (optional - for back button support)
            if (window.history && window.history.pushState) {
                const newUrl = termId ? fallbackUrl : window.location.pathname;
                window.history.pushState({ termId: termId }, '', newUrl);
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
     * Render posts into the Query Loop
     */
    function renderPosts(posts, postList, queryLoop) {
        if (!posts || posts.length === 0) {
            postList.innerHTML = '<li class="wp-block-post" style="width: 100%; text-align: center; padding: 40px;">' +
                '<p style="font-size: 1.2em; color: #666;">No projects found for this category.</p>' +
                '</li>';
            return;
        }

        // Get the first post item as a template
        const firstPost = postList.querySelector('.wp-block-post');
        if (!firstPost) {
            console.error('No post template found');
            return;
        }

        // Clone the structure
        const templateStructure = analyzePostStructure(firstPost);

        // Clear current posts
        postList.innerHTML = '';

        // Create new post items
        posts.forEach(function (post) {
            const postItem = createPostItem(post, templateStructure);
            postList.appendChild(postItem);
        });
    }

    /**
     * Analyze the structure of a post item
     */
    function analyzePostStructure(postElement) {
        return {
            hasImage: !!postElement.querySelector('.wp-block-post-featured-image'),
            hasTitle: !!postElement.querySelector('.wp-block-post-title'),
            hasExcerpt: !!postElement.querySelector('.wp-block-post-excerpt'),
            hasDate: !!postElement.querySelector('.wp-block-post-date'),
            hasTerms: !!postElement.querySelector('.wp-block-post-terms'),
            classList: Array.from(postElement.classList)
        };
    }

    /**
     * Create a post item element
     */
    function createPostItem(post, structure) {
        const li = document.createElement('li');
        structure.classList.forEach(function (className) {
            li.classList.add(className);
        });

        let html = '';

        // Featured Image
        if (structure.hasImage && post._embedded && post._embedded['wp:featuredmedia']) {
            const media = post._embedded['wp:featuredmedia'][0];
            const imageUrl = media.media_details && media.media_details.sizes && media.media_details.sizes.medium
                ? media.media_details.sizes.medium.source_url
                : media.source_url;

            html += '<div class="wp-block-post-featured-image">' +
                '<a href="' + escapeHtml(post.link) + '">' +
                '<img src="' + escapeHtml(imageUrl) + '" alt="' + escapeHtml(post.title.rendered) + '" loading="lazy" />' +
                '</a>' +
                '</div>';
        }

        // Title
        if (structure.hasTitle) {
            html += '<h2 class="wp-block-post-title">' +
                '<a href="' + escapeHtml(post.link) + '">' + post.title.rendered + '</a>' +
                '</h2>';
        }

        // Date
        if (structure.hasDate) {
            const date = new Date(post.date);
            html += '<div class="wp-block-post-date">' +
                '<time datetime="' + post.date + '">' + date.toLocaleDateString() + '</time>' +
                '</div>';
        }

        // Categories/Terms
        if (structure.hasTerms && post._embedded && post._embedded['wp:term']) {
            const categories = post._embedded['wp:term'][0] || [];
            if (categories.length > 0) {
                html += '<div class="wp-block-post-terms">';
                categories.forEach(function (term, index) {
                    if (index > 0) html += ', ';
                    html += '<a href="' + escapeHtml(term.link) + '">' + escapeHtml(term.name) + '</a>';
                });
                html += '</div>';
            }
        }

        // Excerpt
        if (structure.hasExcerpt && post.excerpt) {
            html += '<div class="wp-block-post-excerpt">' +
                '<p class="wp-block-post-excerpt__excerpt">' + post.excerpt.rendered + '</p>' +
                '</div>';
        }

        li.innerHTML = html;
        return li;
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function (event) {
        if (event.state && event.state.termId !== undefined) {
            // Reload the page to show the correct filtered state
            window.location.reload();
        }
    });

})();
